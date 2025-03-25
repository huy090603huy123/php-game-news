🎮 Game News - Dự án Laravel Tin Tức Game

🚀 Giới thiệu

Game News là một dự án web được xây dựng bằng Laravel nhằm cung cấp tin tức mới nhất về game, đánh giá, hướng dẫn chơi và các thông tin liên quan đến cộng đồng game thủ.

🛠️ Công nghệ sử dụng

📂 Cấu trúc thư mục

Dự án tuân theo cấu trúc chuẩn của Laravel:

📌 app/        - Chứa các model, controller, middleware, services xử lý logic chính của ứng dụng.
📌 bootstrap/  - Chứa file `app.php`, giúp bootstrap ứng dụng Laravel.
📌 config/     - Chứa file cấu hình như database, auth, mail...
📌 database/   - Chứa migrations, seeders, factories để quản lý dữ liệu.
📌 lang/       - Chứa các file dịch ngôn ngữ (đa ngôn ngữ cho dự án).
📌 public/     - Chứa tài nguyên công khai như ảnh, CSS, JS.
📌 resources/  - Chứa blade templates (giao diện), SCSS, JavaScript.
📌 routes/     - Chứa định nghĩa các route (web.php, api.php...).
📌 storage/    - Chứa logs, cache, file tải lên.
📌 tests/      - Chứa các test tự động cho dự án.
📌 vendor/     - Chứa các package do Composer cài đặt.

🎯 Các tính năng chính

✔ Quản lý tin tức: Thêm, sửa, xóa bài viết.
✔ Danh mục game: Phân loại tin tức theo các tựa game hoặc thể loại game.
✔ Tìm kiếm & Lọc: Cho phép người dùng tìm kiếm tin tức theo từ khóa.
✔ Bình luận & Thảo luận: Người dùng có thể đăng nhập và bình luận về các bài viết.
✔ Quản lý người dùng: Phân quyền Admin, Editor và User.
✔ Hệ thống đăng nhập & đăng ký: Hỗ trợ xác thực qua email.

🏗️ Cài đặt

⚙️ Yêu cầu hệ thống

✔ PHP >= 8.0
✔ Composer
✔ MySQL hoặc PostgreSQL
✔ Node.js (nếu sử dụng frontend JavaScript framework)
✔ Docker (nếu sử dụng Laravel Sail)

📌 Hướng dẫn cài đặt

# Clone dự án
git clone https://github.com/your-repo/game-news.git
cd game-news

# Cài đặt dependencies
composer install
npm install

# Cấu hình file .env
cp .env.example .env

# Tạo khóa ứng dụng
php artisan key:generate

# Chạy migration và seed dữ liệu
php artisan migrate --seed

# Chạy ứng dụng
php artisan serve
# Truy cập http://127.0.0.1:8000

# (Tùy chọn) Chạy với Docker
./vendor/bin/sail up -d
# Truy cập http://localhost

📖 Hướng dẫn sử dụng

📌 Truy cập trang chủ để xem tin tức mới nhất.
📌 Đăng nhập để đăng bình luận và lưu bài viết yêu thích.
📌 Admin có thể quản lý bài viết và người dùng từ trang Dashboard.

🤝 Đóng góp

💡 Nếu bạn muốn đóng góp, vui lòng fork dự án và gửi pull request. Chúng tôi rất hoan nghênh sự đóng góp từ cộng đồng! 🚀

📜 License

📜 Dự án này được phát hành dưới giấy phép MIT.
