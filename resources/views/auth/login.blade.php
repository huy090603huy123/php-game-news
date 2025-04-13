<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                {{-- Giả sử bạn có component logo này hoặc thay thế bằng img tag --}}
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <x-auth-validation-errors class="mb-4" :errors="$errors" />

         {{-- Hiển thị thông báo lỗi từ Socialite (nếu có) --}}
        @if (session('error'))
            <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" :value="__('Email')" />

                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            </div>

            <div class="mt-4">
                <x-label for="password" :value="__('Mật khẩu')" />

                <x-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="remember">
                    <span class="ml-2 text-sm text-gray-600">{{ __('Nhớ mật khậu') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                 {{-- Liên kết đăng ký và quên mật khẩu để ở đây hoặc di chuyển xuống dưới nút Google --}}
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('register') }}">
                    {{ __('Đăng ký tài khoản') }}
                </a>
                 @if (Route::has('password.request'))
                    <a class="ml-4 underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
                        {{ __('Quên mật khẩu?') }}
                    </a>
                @endif

                <x-button class="ml-3">
                    {{ __('Đăng nhập') }}
                </x-button>
            </div>

            {{-- ===== PHẦN THÊM VÀO ===== --}}
            <div class="flex items-center justify-center mt-4">
                 <span class="text-sm text-gray-600">Hoặc đăng nhập với</span>
            </div>

            <div class="mt-4">
                <a href="{{ route('google.redirect') }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{-- SVG Icon Google --}}
                    <svg class="w-5 h-5 mr-2 -ml-1" aria-hidden="true" focusable="false" data-prefix="fab" data-icon="google" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 488 512"><path fill="#EA4335" d="M488 261.8C488 403.3 381.5 512 244 512 109.8 512 0 402.2 0 256S109.8 0 244 0c46.4 0 88.4 14.1 124.9 38.2l-50.7 49.9C297.9 71.2 272.1 64 244 64 164.9 64 100.3 128.5 100.3 207.7c0 79.2 64.6 143.7 143.7 143.7 51.8 0 96.4-25.2 122.8-64.3H244v-68.9h236.1c2.3 12.7 3.9 24.9 3.9 41.4z"></path></svg>
                    Google
                </a>
                {{-- Bạn có thể thêm các nút đăng nhập mạng xã hội khác ở đây --}}
            </div>
             {{-- ===== KẾT THÚC PHẦN THÊM VÀO ===== --}}

        </form>
    </x-auth-card>
</x-guest-layout>