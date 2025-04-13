<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UpdateUserFeatureTest extends TestCase
{

    protected function setUp(): void  
    {
        parent::setUp();

        // Đảm bảo role 'admin' luôn tồn tại để test vượt qua middleware 'check_permissions'
        Role::firstOrCreate(['name' => 'admin']);
    }

    private function createAdminUser(): User
    {
        return User::factory()->create([
            'role_id' => Role::where('name', 'admin')->first()->id
        ]);
    }

    /** @test TC01 */
    public function test_update_user_without_password_and_without_avatar()
    {
        $user = $this->createAdminUser();

        $response = $this->actingAs($user)->put(route('admin.users.update', $user), [
            'name' => 'Updated Name',
            'email' => $user->email,
            'role_id' => $user->role_id,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name'
        ]);
    }

    /** @test TC02 */
    public function test_update_user_without_password_but_with_avatar()
    {
        Storage::fake('public');
        $user = $this->createAdminUser();
        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($user)->put(route('admin.users.update', $user), [
            'name' => 'Updated',
            'email' => $user->email,
            'role_id' => $user->role_id,
            'image' => $file,
        ]);

        $response->assertStatus(302);
        $this->assertTrue(Storage::disk('public')->exists('images/' . $file->hashName()));
    }

    /** @test TC03 */
    public function test_update_user_with_password_without_avatar()
    {
        $user = $this->createAdminUser();

        $response = $this->actingAs($user)->put(route('admin.users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
        ]);

        $response->assertStatus(302);
        $user->refresh();
        $this->assertTrue(Hash::check('new_password', $user->password));
    }

    /** @test TC04 */
    public function test_update_user_with_password_and_avatar()
    {
        Storage::fake('public');
        $user = $this->createAdminUser();
        $file = UploadedFile::fake()->image('avatar.png');

        $response = $this->actingAs($user)->put(route('admin.users.update', $user), [
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'password' => 'secure123',
            'password_confirmation' => 'secure123',
            'image' => $file
        ]);

        $response->assertStatus(302);
        $this->assertTrue(Storage::disk('public')->exists('images/' . $file->hashName()));

        $user->refresh();
        $this->assertTrue(Hash::check('secure123', $user->password));
    }
}
