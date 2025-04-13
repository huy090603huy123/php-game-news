<?php

namespace App\Http\Controllers\Auth; // Đảm bảo namespace chính xác

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite; // Facade cho Socialite
use App\Models\User;                      // Model User
use Illuminate\Support\Facades\Auth;      // Facade cho Authentication
use Illuminate\Support\Facades\Hash;      // Facade để hash password (nếu cần)
use Illuminate\Support\Str;               // Facade để tạo chuỗi ngẫu nhiên (nếu cần password)
use Exception;                            // Để bắt ngoại lệ chung

class LoginWithGoogleController extends Controller
{
    /**
     * Chuyển hướng người dùng đến trang xác thực của Google.
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToGoogle()
    {
        // Chuyển hướng người dùng đến Google OAuth
        return Socialite::driver('google')->redirect();
    }

    /**
     * Lấy thông tin người dùng từ Google và xử lý đăng nhập/đăng ký.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback()
    {
        try {
            // Lấy thông tin người dùng từ Google thông qua Socialite
            // Lưu ý: Nếu người dùng từ chối cấp quyền, Socialite có thể ném ra Exception
            $googleUser = Socialite::driver('google')->user();

            // Tìm kiếm người dùng trong CSDL bằng google_id
            $user = User::where('google_id', $googleUser->getId())->first();

            if ($user) {
                // ---- Trường hợp 1: Người dùng đã tồn tại với google_id ----
                // Đăng nhập người dùng này vào hệ thống
                Auth::login($user, true); // Tham số thứ 2 là true để ghi nhớ đăng nhập

                // Chuyển hướng đến trang sau khi đăng nhập thành công (ví dụ: dashboard)
                // intended() sẽ chuyển hướng đến trang người dùng định truy cập trước khi đăng nhập
                return redirect()->intended('/dashboard');

            } else {
                // ---- Trường hợp 2: Người dùng chưa có google_id ----
                // Kiểm tra xem email nhận được từ Google đã tồn tại trong CSDL chưa
                // Điều này xử lý trường hợp người dùng đã đăng ký bằng email/password trước đó
                $existingUser = User::where('email', $googleUser->getEmail())->first();

                if ($existingUser) {
                    // ---- Trường hợp 2.1: Email đã tồn tại (tài khoản thường) ----
                    // Cập nhật google_id cho tài khoản hiện có này
                    $existingUser->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $existingUser->avatar ?? $googleUser->getAvatar() // Cập nhật avatar nếu chưa có
                        // Bạn có thể cập nhật thêm các thông tin khác nếu muốn
                    ]);

                    // Đăng nhập người dùng này
                    Auth::login($existingUser, true);
                    return redirect()->intended('/dashboard');

                } else {
                    // ---- Trường hợp 2.2: Người dùng hoàn toàn mới ----
                    // Tạo một tài khoản mới trong bảng users
                    $newUser = User::create([
                        'name' => $googleUser->getName(), // Lấy tên từ Google
                        'email' => $googleUser->getEmail(), // Lấy email từ Google
                        'google_id' => $googleUser->getId(), // Lưu google_id
                        'avatar' => $googleUser->getAvatar(), // Lưu URL avatar từ Google
                        'password' => null, // Đặt password là null vì đăng nhập qua Google
                        // Hoặc bạn có thể tạo một mật khẩu ngẫu nhiên nếu cột password không cho phép NULL:
                        // 'password' => Hash::make(Str::random(24)),
                        // 'email_verified_at' => now(), // Có thể coi email đã xác thực vì đến từ Google
                        // Gán vai trò (role) mặc định nếu hệ thống của bạn có phân quyền
                         'role_id' => 1,
                    ]);

                    // Đăng nhập người dùng mới tạo
                    Auth::login($newUser, true);
                    return redirect()->intended('/dashboard'); // Chuyển hướng đến dashboard
                }
            }

        } catch (Exception $e) {
            // ---- Xử lý lỗi ----
            // Ghi log lỗi để debug
            // report($e); // Tạm thời comment dòng này lại
        
            // Hiển thị lỗi chi tiết ngay lập tức
            dd($e); // Dump and die - Hiển thị toàn bộ thông tin về Exception
        
            // return redirect('/login')->with('error', '...'); // Lệnh này sẽ không chạy khi có dd() ở trên
        }
    }
}