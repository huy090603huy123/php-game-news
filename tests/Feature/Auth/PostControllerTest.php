<?php

// Correct namespace based on the directory structure: tests/Feature/Auth
namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile; // To fake file uploads
use Illuminate\Support\Facades\Storage; // To fake storage
use Tests\TestCase;
use App\Models\User; // Assuming User model namespace
use App\Models\Post;  // Assuming Post model namespace
use App\Models\Tag;   // Assuming Tag model namespace
use App\Models\Image; // Assuming Image model namespace

// The class name remains the same
class PostControllerTest extends TestCase
{
    use WithFaker;       // Use faker for dummy data

    protected $user;

    // Setup a user to authenticate requests
    protected function setUp(): void
    {
        parent::setUp();
        // Create a user using a factory (adjust if needed)
        $this->user = User::factory()->create();
        // You might need specific permissions/roles depending on your auth setup
        // If this controller is truly for ADMIN actions, the user created here
        // might need an 'admin' role/permission depending on your authorization logic.
    }

    /**
     * Helper to get valid post data.
     * Excludes fields handled separately like user_id, thumbnail, tags.
     * Adjust required fields based on your actual validation rules.
     */
    private function getValidPostData(array $overrides = []): array
    {
        return array_merge([
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraphs(3, true),
            // Add other required fields from your $rules here
        ], $overrides);
    }

    // --- Test Cases ---

    /** @test */
    public function it_can_store_a_post_without_thumbnail_or_tags()
    {
        $postData = $this->getValidPostData();

        // *** IMPORTANT: Make sure 'posts.store' and 'admin.posts.create' route names are correct ***
        $response = $this->actingAs($this->user)
                         ->post(route('posts.store'), $postData);

        // 1. Assert Redirect and Session Message
        // *** Adjust 'admin.posts.create' if the redirect route name is different ***
        $response->assertRedirect(route('admin.posts.create'));
        $response->assertSessionHas('success', 'Thêm bài viết thành công.');

        // 2. Assert Post was created in DB
        $this->assertDatabaseHas('posts', [
            'title' => $postData['title'],
            'content' => $postData['content'],
            'user_id' => $this->user->id,
        ]);

        // 3. Assert NO Image was created
        $this->assertDatabaseCount('images', 0);

        // 4. Assert NO Tags were created or linked
        $this->assertDatabaseCount('tags', 0);
        $this->assertDatabaseCount('post_tag', 0); // Assuming pivot table name
    }

    /** @test */
    public function it_can_store_a_post_with_a_thumbnail()
    {
        Storage::fake('public'); // Fake the 'public' disk
        $file = UploadedFile::fake()->image('thumbnail.jpg');
        // Storage::fake('public'); // Fake the 'public' disk
        // $file = UploadedFile::fake()->image('thumbnail.jpg', 600, 400); // Create a fake image
        $postData = $this->getValidPostData(['thumbnail' => $file]);

        $response = $this->actingAs($this->user)
                         ->post(route('posts.store'), $postData);

        // 1. Assert Redirect and Session Message
        $response->assertRedirect(route('admin.posts.create'));
        $response->assertSessionHas('success', 'Thêm bài viết thành công.');

        // 2. Assert Post was created
        $this->assertDatabaseHas('posts', [
            'title' => $postData['title'],
            'user_id' => $this->user->id,
        ]);
        $post = Post::where('title', $postData['title'])->firstOrFail();
        $disk = Storage::disk('public');
        // 4. Assert File was stored
        $expectedPath = 'images/' . $file->hashName();
        $disk->assertExists($expectedPath);
        // $expectedPath = 'images/' . $file->hashName();
        // Storage::disk('public')->assertExists($expectedPath);

        // 5. Assert Image record was created in DB
        $this->assertDatabaseHas('images', [
            // Adjust 'post_id' if your Image relationship uses different keys (e.g., morphs)
            'post_id'   => $post->id,
            'name'      => 'thumbnail.jpg',
            'extension' => 'jpg',
            'path'      => $expectedPath,
        ]);

        // 6. Assert NO Tags were created or linked
        $this->assertDatabaseCount('tags', 0);
        $this->assertDatabaseCount('post_tag', 0);
    }

    /** @test */
    public function it_can_store_a_post_with_tags()
    {
        $postData = $this->getValidPostData([
            'tags' => ' Laravel, PHP , testing ', // Comma-separated tags with extra spaces
        ]);

        $response = $this->actingAs($this->user)
                         ->post(route('posts.store'), $postData);

        // 1. Assert Redirect and Session Message
        $response->assertRedirect(route('admin.posts.create'));
        $response->assertSessionHas('success', 'Thêm bài viết thành công.');

        // 2. Assert Post was created
        $this->assertDatabaseHas('posts', ['title' => $postData['title']]);
        $post = Post::where('title', $postData['title'])->firstOrFail();

        // 3. Assert Tags were created (trimmed)
        $this->assertDatabaseHas('tags', ['name' => 'Laravel']);
        $this->assertDatabaseHas('tags', ['name' => 'PHP']);
        $this->assertDatabaseHas('tags', ['name' => 'testing']);
        $this->assertDatabaseCount('tags', 3);

        // 4. Assert Tags were linked to the post
        $tag1 = Tag::where('name', 'Laravel')->firstOrFail();
        $tag2 = Tag::where('name', 'PHP')->firstOrFail();
        $tag3 = Tag::where('name', 'testing')->firstOrFail();

        $this->assertDatabaseHas('post_tag', ['post_id' => $post->id, 'tag_id' => $tag1->id]);
        $this->assertDatabaseHas('post_tag', ['post_id' => $post->id, 'tag_id' => $tag2->id]);
        $this->assertDatabaseHas('post_tag', ['post_id' => $post->id, 'tag_id' => $tag3->id]);
        $this->assertDatabaseCount('post_tag', 3);

        // 5. Assert NO Image was created
        $this->assertDatabaseCount('images', 0);
    }

    /** @test */
    public function it_can_store_a_post_with_thumbnail_and_tags()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('featured.png');
        $postData = $this->getValidPostData([
            'thumbnail' => $file,
            'tags' => '  code, fun ',
        ]);

        $response = $this->actingAs($this->user)->post(route('posts.store'), $postData);

        // 1. Assert Redirect and Session Message
        $response->assertRedirect(route('admin.posts.create'));
        $response->assertSessionHas('success', 'Thêm bài viết thành công.');

        // 2. Assert Post was created
        $this->assertDatabaseHas('posts', ['title' => $postData['title']]);
        $post = Post::where('title', $postData['title'])->firstOrFail();

        // 3. Assert File was stored
        $expectedPath = 'images/' . $file->hashName();
        Storage::disk('public')->assertExists($expectedPath);

        // 4. Assert Image record was created
        $this->assertDatabaseHas('images', [
            'post_id'   => $post->id,
            'name'      => 'featured.png',
            'extension' => 'png',
            'path'      => $expectedPath,
        ]);

        // 5. Assert Tags were created
        $this->assertDatabaseHas('tags', ['name' => 'code']);
        $this->assertDatabaseHas('tags', ['name' => 'fun']);
        $this->assertDatabaseCount('tags', 2);

        // 6. Assert Tags were linked
        $tag1 = Tag::where('name', 'code')->firstOrFail();
        $tag2 = Tag::where('name', 'fun')->firstOrFail();
        $this->assertDatabaseHas('post_tag', ['post_id' => $post->id, 'tag_id' => $tag1->id]);
        $this->assertDatabaseHas('post_tag', ['post_id' => $post->id, 'tag_id' => $tag2->id]);
        $this->assertDatabaseCount('post_tag', 2);
    }

    /** @test */
    public function it_fails_validation_if_required_data_is_missing()
    {
        // Assuming 'title' is required in your controller's $rules
        $invalidData = $this->getValidPostData(['title' => '']); // Provide empty title

        $response = $this->actingAs($this->user)
                         ->post(route('posts.store'), $invalidData);

        // 1. Assert Session has validation errors for the 'title' field
        $response->assertSessionHasErrors('title');
        // You might also want to assert the redirect back: $response->assertRedirect();

        // 2. Assert NO Post, Image, or Tags were created
        $this->assertDatabaseCount('posts', 0);
        $this->assertDatabaseCount('images', 0);
        $this->assertDatabaseCount('tags', 0);
        $this->assertDatabaseCount('post_tag', 0);
    }

     /** @test */
    public function it_handles_empty_or_whitespace_tags_string_correctly()
    {
        // Test with completely empty string
        $postDataEmpty = $this->getValidPostData(['tags' => '']);
        $responseEmpty = $this->actingAs($this->user)->post(route('posts.store'), $postDataEmpty);

        $responseEmpty->assertRedirect(route('admin.posts.create'));
        $responseEmpty->assertSessionHas('success');
        $this->assertDatabaseHas('posts', ['title' => $postDataEmpty['title']]);
        $this->assertDatabaseCount('tags', 0); // No tags should be created
        $this->assertDatabaseCount('post_tag', 0); // No tags linked
        Post::where('title', $postDataEmpty['title'])->delete(); // Clean up

        // Test with only commas/spaces
        $postDataWhitespace = $this->getValidPostData(['tags' => ' , ,,  , ']);
        $responseWhitespace = $this->actingAs($this->user)->post(route('posts.store'), $postDataWhitespace);

        $responseWhitespace->assertRedirect(route('admin.posts.create'));
        $responseWhitespace->assertSessionHas('success');
        $this->assertDatabaseHas('posts', ['title' => $postDataWhitespace['title']]);

        // Check current behavior: Your code likely creates empty tags if only spaces/commas exist.
        // Consider modifying your controller:
        // $tags = array_filter(array_map('trim', explode(',', $request->input('tags'))));
        // if(!empty($tags)) { ... process tags ... }
        // Assuming the ideal behavior is NO empty tags:
        $this->assertDatabaseCount('tags', 0);
        $this->assertDatabaseCount('post_tag', 0);

        Post::where('title', $postDataWhitespace['title'])->delete(); // Clean up
    }

    /** @test */
    public function it_does_not_sync_tags_if_tags_input_is_null_or_not_provided()
    {
        $postData = $this->getValidPostData(); // 'tags' key is not present

        $response = $this->actingAs($this->user)
                         ->post(route('posts.store'), $postData);

        // 1. Assert Redirect and Session Message
        $response->assertRedirect(route('admin.posts.create'));
        $response->assertSessionHas('success', 'Thêm bài viết thành công.');

        // 2. Assert Post was created
        $this->assertDatabaseHas('posts', ['title' => $postData['title']]);

        // 3. Assert NO Tags were created or linked
        $this->assertDatabaseCount('tags', 0);
        $this->assertDatabaseCount('post_tag', 0);
    }
}