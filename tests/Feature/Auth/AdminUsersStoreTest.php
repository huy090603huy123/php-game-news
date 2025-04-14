<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;

class AdminUsersStoreTest extends TestCase
{
    //use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Lấy user admin từ database qua email cụ thể
        $adminUser = User::where('email', 'huy050903huy@gmail.com')->first();

        // $adminUser = \App\Models\User::firstOrCreate(
        //     ['email' => 'huy050903huy@gmail.com'],
        //     [
        //         'name'     => 'Admin Huy',
        //         'password' => bcrypt('12345678'),
        //         // Giả sử role_id = 1 tương ứng với tài khoản admin
        //         'role_id'  => 1,
        //     ]
        // );

        // Giả lập đăng nhập với tài khoản admin
        $this->actingAs($adminUser, 'web');
    }

    /** @test */
    public function it_creates_a_user_with_image()
    {
        // Test case 1: Có hình ảnh
        Storage::fake('public');
        //Role::create(['id' => 2, 'name' => 'User']);

        $this->withoutMiddleware(\Illuminate\Auth\Middleware\Authenticate::class);

        $data = [
            'name' => 'Nhat Huy',
            'email' => 'huy0509huy@gmail.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'role_id' => 2,
            'image' => UploadedFile::fake()->image('profile.jpg', 300, 300),
        ];
        

        $response = $this->post('/admin/users', $data);

        $response->assertRedirect('/admin/users/create');
        $response->assertSessionHas('success', 'Thêm tài khoản thành công.');
        $this->assertDatabaseHas('users', ['name' => 'Nhat Huy', 'email' => 'huy0509huy@gmail.com']);

        // Sử dụng assertTrue để kiểm tra xem tệp có tồn tại
        $this->assertTrue(Storage::disk('public')->exists('storage/images/profile.jpg'));
    }

    /** @test */
    public function it_creates_a_user_without_image()
    {
        // Test case 2: Không có hình ảnh
        //Role::create(['id' => 2, 'name' => 'User']);

        $this->withoutMiddleware(\Illuminate\Auth\Middleware\Authenticate::class);

        $data = [
            'name' => 'Nguyen Van B',
            'email' => 'example2@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => 1,
        ];

        $response = $this->post('/admin/users', $data);

        $response->assertRedirect('/admin/users/create');
        $response->assertSessionHas('success', 'Thêm tài khoản thành công.');
        $this->assertDatabaseHas('users', ['name' => 'Nguyen Van B', 'email' => 'example2@gmail.com']);
    }
}
