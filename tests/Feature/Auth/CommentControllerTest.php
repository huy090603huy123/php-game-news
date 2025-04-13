<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase; // Reset DB sau mỗi test
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User; // Import User model
use App\Models\Post;  // Import Post model
use App\Models\Comment; // Import Comment model (nếu cần assert database)

class CommentControllerTest extends TestCase
{
  

    /**
     * Người dùng đã xác thực có thể thêm bình luận thành công.
     *
     * @test
     */
    public function authenticated_user_can_add_comment_successfully(): void
    {
        // 1. Chuẩn bị (Arrange)
        $user = User::factory()->create(); // Tạo user giả
        $post = Post::factory()->create(); // Tạo post giả

        $commentData = [
            'the_comment' => 'Đây là một bình luận hợp lệ.',
            'post_title' => $post->title, // Sử dụng title của post đã tạo
        ];

        // 2. Hành động (Act)
        // Giả lập user đã đăng nhập và gửi request POST
        $response = $this->actingAs($user)->postJson('/binh-luan', $commentData);

        // 3. Khẳng định (Assert)
        $response->assertStatus(200) // Hoặc status code phù hợp nếu bạn dùng resource controller (201 Created)
                 ->assertJson([
                     'success' => 1,
                     'message' => 'Bạn đã bình luận thành công !',
                     'errors' => [], // Không có lỗi
                     // Kiểm tra một phần cấu trúc của result nếu cần
                     'result' => [
                         'the_comment' => $commentData['the_comment'],
                         'user_id' => $user->id,
                         'post_id' => $post->id,
                     ]
                 ]);

        // Khẳng định thêm là comment đã được lưu vào database
        $this->assertDatabaseHas('comments', [
            'the_comment' => $commentData['the_comment'],
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }

    /**
     * Test validation thất bại nếu thiếu 'the_comment'.
     *
     * @test
     */
    public function add_comment_fails_validation_if_comment_is_missing(): void
    {
        // 1. Chuẩn bị
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $commentData = [
            // 'the_comment' => 'Thiếu trường này',
            'post_title' => $post->title,
        ];

        // 2. Hành động
        $response = $this->actingAs($user)->postJson('/binh-luan', $commentData);

        // 3. Khẳng định
        $response->assertStatus(200) // Code của bạn trả về 200 ngay cả khi lỗi validation
                 ->assertJson([
                     'success' => 0,
                     'message' => 'Không thể bình luận',
                     // Code của bạn chỉ lấy lỗi đầu tiên của 'the_comment'
                     // 'errors' => '...', // Cần kiểm tra message lỗi cụ thể từ validation
                 ])
                 ->assertJsonStructure(['success', 'message', 'errors']); // Đảm bảo cấu trúc JSON đúng

        // Kiểm tra xem message lỗi có chứa thông báo 'required' không
        // Lưu ý: cách lấy lỗi trong code gốc hơi lạ (`first('the_comment')`)
        // Nếu validation của Laravel trả về mảng lỗi, bạn cần điều chỉnh assert này
        $response->assertJsonFragment(['errors' => 'The the comment field is required.']); // Hoặc message tiếng Việt tương ứng nếu bạn đã cấu hình
    }

     /**
     * Test validation thất bại nếu 'the_comment' quá ngắn.
     *
     * @test
     */
    public function add_comment_fails_validation_if_comment_is_too_short(): void
    {
        // 1. Chuẩn bị
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $commentData = [
            'the_comment' => 'ngan', // < 5 ký tự
            'post_title' => $post->title,
        ];

        // 2. Hành động
        $response = $this->actingAs($user)->postJson('/binh-luan', $commentData);

        // 3. Khẳng định
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => 0,
                     'message' => 'Không thể bình luận',
                 ])
                 ->assertJsonStructure(['success', 'message', 'errors']);

        // Kiểm tra message lỗi có chứa thông báo 'min:5' không
        $response->assertJsonFragment(['errors' => 'The the comment must be at least 5 characters.']); // Hoặc message tiếng Việt
    }

    /**
     * Test validation thất bại nếu thiếu 'post_title'.
     *
     * @test
     */
    public function add_comment_fails_validation_if_post_title_is_missing(): void
    {
        // 1. Chuẩn bị
        $user = User::factory()->create();
        // Không cần tạo Post vì title bị thiếu

        $commentData = [
            'the_comment' => 'Bình luận hợp lệ nhưng thiếu title post.',
            // 'post_title' => 'Thiếu trường này',
        ];

        // 2. Hành động
        $response = $this->actingAs($user)->postJson('/binh-luan', $commentData);

        // 3. Khẳng định
        // *** Lưu ý quan trọng: ***
        // Code gốc của bạn có thể hoạt động không như mong đợi ở đây.
        // Nó chỉ lấy lỗi $validated->errors()->first('the_comment').
        // Nếu 'the_comment' hợp lệ nhưng 'post_title' thiếu, 'errors' trong JSON response có thể là null hoặc rỗng.
        // Test này kiểm tra hành vi *hiện tại* của code.
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => 0,
                     'message' => 'Không thể bình luận',
                     'errors'  => null, // Hoặc [] tùy thuộc vào việc first() trả về gì khi không có lỗi cho key đó
                 ]);
                 // ->assertJsonMissingPath('errors.post_title'); // Không có lỗi cụ thể cho post_title trong response JSON
    }

    /**
     * Test thất bại nếu không tìm thấy Post với title cung cấp.
     *
     * @test
     */
    public function add_comment_fails_if_post_not_found(): void
    {
         // 1. Chuẩn bị
        $user = User::factory()->create();
        // KHÔNG tạo Post trùng title

        $commentData = [
            'the_comment' => 'Bình luận hợp lệ cho post không tồn tại.',
            'post_title' => 'Title Của Post Không Có Trong DB',
        ];

        // 2. Hành động
        $response = $this->actingAs($user)->postJson('/binh-luan', $commentData);

        // 3. Khẳng định
        // Code gốc sẽ bị lỗi "Trying to get property 'id' of non-object" hoặc tương tự
        // vì $post sẽ là null. Unit test nên bắt lỗi này hoặc code gốc cần được sửa.
        // Trong trường hợp này, Laravel thường trả về status 500 Internal Server Error.
        $response->assertStatus(500);

        // Nếu bạn sửa code gốc để xử lý trường hợp $post == null, bạn sẽ cần cập nhật assert này, ví dụ:
        // $response->assertStatus(404) // Not Found
        //          ->assertJson([
        //              'success' => 0,
        //              'message' => 'Không tìm thấy bài viết',
        //              'errors' => [],
        //          ]);
    }

    /**
     * Test người dùng chưa xác thực không thể bình luận.
     *
     * @test
     */
    public function unauthenticated_user_cannot_add_comment(): void
    {
        // 1. Chuẩn bị
        $post = Post::factory()->create();
        $commentData = [
            'the_comment' => 'Người lạ cố gắng bình luận.',
            'post_title' => $post->title,
        ];

        // 2. Hành động
        // KHÔNG dùng actingAs()
        $response = $this->postJson('/binh-luan', $commentData);

        // 3. Khẳng định
        // Thông thường, middleware 'auth' sẽ chặn và redirect hoặc trả về lỗi 401/403
        // Nếu API, thường là 401 Unauthorized
        $response->assertStatus(401); // Hoặc 403 Forbidden tùy cấu hình middleware
    }
}