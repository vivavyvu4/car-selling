# Tiến trình dự án

## 2026-11-27 — Commit 1: Khởi tạo nền tảng WordPress
### Đã thực hiện
- Khởi tạo repository car-selling.
- Chọn WordPress + WooCommerce làm nền tảng catalog và marketing.
- Thêm Docker Compose cho WordPress, MariaDB và WP-CLI setup tự động.
- Tự động cài WordPress, WooCommerce và Rank Math SEO khi chạy local lần đầu.
- Tạo theme miễn phí Dealer Starter để website chạy local mà không cần license Flatsome.
- Tạo plugin Dealer Site Core: thương hiệu xe, thông tin model/tình trạng/bảo hành/giá, thông số động, catalog mode, form báo giá và lead inbox.
- Form yêu cầu đồng ý liên hệ, có honeypot và rate limit; lưu UTM, landing page và referrer.
- Cấu hình hotline, Zalo, email và địa chỉ đại lý.
- Tạo trang cơ bản và danh mục xe bằng WP-CLI script.
- Thêm script start cho Windows/macOS/Linux và tài liệu tiến trình.

## 2026-11-27 — Commit 2: Rà soát trải nghiệm local và phân quyền
### Đã cập nhật
- Tạo trang Tin tức và gán làm WordPress Posts page để menu không trỏ tới URL chưa tồn tại.
- Cấu hình múi giờ Asia/Ho_Chi_Minh, tiền tệ VND, 0 chữ số thập phân, đơn vị kg/cm và quốc gia VN cho WooCommerce.
- Điều chỉnh menu Dealer Website để nhân viên sale có quyền truy cập lead inbox.
- Tạo role Dealer Sales dựa trên capabilities của WooCommerce Shop Manager, bổ sung quyền quản lý lead nhưng không cấp manage_options.
- Giữ trang cấu hình đại lý chỉ dành cho Administrator.
- Giữ lại UTM/landing/referrer sau khi form gửi thành công và reset.
- Đặt executable bit cho scripts/start.sh.
- Thêm GitHub Actions workflow kiểm tra PHP, JavaScript, shell và Docker Compose.
- Cập nhật README, NEXT_STEPS và quyết định kỹ thuật.

## 2026-11-27 — Commit 3: Quyền chỉnh sửa lead private
- Bổ sung edit_private_dealer_leads cho Administrator, Shop Manager và Dealer Sales để nhân viên có thể mở/sửa lead private do form công khai tạo.
- Cập nhật plugin version 0.2.2.

## 2026-11-27 — Commit 4: Hướng dẫn cài đặt có chú thích và local non-Docker
- Làm rõ Copy-Item .env.example .env tạo file .env ngay tại repository root, cùng cấp với docker-compose.yml; không tạo file ở thư mục WordPress.
- Ghi rõ .env chỉ dành cho Docker Compose; local Laragon/XAMPP dùng wp-config.php, không cần .env.
- Tạo docs/LOCAL_SETUP.md với hướng dẫn Windows Laragon Quick App và XAMPP.
- Phân biệt repository source (C:\projects\car-selling) và WordPress core folder (C:\laragon\www\car-selling-local).
- Thêm hướng dẫn copy plugin/theme, cài WP-CLI, cài WooCommerce/Rank Math, chạy seed script và cập nhật source sau git pull.
- Bổ sung comment giải thích cho từng lệnh PowerShell và Docker trong README.
- Bổ sung index.php fallback cho Dealer Starter để theme có template index cần thiết khi kích hoạt.
- Ghi rõ yêu cầu PHP/database và các bước kiểm tra version local.

## Kiểm tra tự động
GitHub Actions run cho commit 9de542f0509f99935ef839be2235cb34b7128e88 đã hoàn tất thành công:
- PHP syntax: success.
- JavaScript syntax: success.
- Shell syntax: success.
- Docker Compose config: success.

Run: https://github.com/vivavyvu4/car-selling/actions/runs/45956996925

Commit tiếp theo sẽ chạy lại GitHub Actions cho các thay đổi mới.

## Chưa kiểm chứng
- Môi trường agent không có Docker daemon nên chưa chạy được WordPress stack end-to-end.
- Chưa integration test trên WordPress/WooCommerce thật.
- Chưa kiểm thử Laragon/XAMPP trực tiếp trên máy Windows.
- Chưa kiểm thử trình duyệt, email SMTP, backup/restore hoặc SEO structured data trên domain production.

## Dữ liệu / cấu hình còn thiếu
- Logo, tên thương mại, hotline, Zalo, email và địa chỉ thật của đại lý.
- Catalog xe và ảnh được phép sử dụng.
- SMTP sender và email nhận lead.
- Domain, hosting, SSL và tài khoản production.

## Lưu ý
- Dealer Starter là theme mặc định để clone repo có thể chạy không cần license Flatsome.
- Dealer Sales và Shop Manager hiện xem được toàn bộ lead; chưa có phân lead theo từng nhân viên.
- Chưa tạo sản phẩm hoặc giá giả.


## 2026-11-27 — Commit 5: Bộ dữ liệu DEMO toàn website
- Seed 10 sản phẩm xe mẫu trải đều 5 danh mục, 4 thương hiệu hư cấu.
- Seed 4 bài viết và các danh mục tin tức.
- Tạo/nâng cấp trang Giới thiệu, Liên hệ và mẫu Chính sách bảo mật.
- Tạo menu chính và gán ảnh đại diện cho sản phẩm, danh mục, bài viết.
- Thêm 5 ảnh vector xe theo nhóm và 1 ảnh hero đội xe; seed vào Media Library.
- Thêm banner DEMO MODE, cảnh báo trên nội dung sản phẩm/bài viết; không điền hotline/email/Zalo/địa chỉ giả, giá bán không hiển thị.
- Bổ sung layout trang chủ, trang tin tức, CSS responsive và script remove-demo-content.php.

### Kiểm thử và giới hạn
- GitHub Actions sẽ chạy lại trên commit này để kiểm tra cú pháp PHP/JavaScript/shell và Docker Compose.
- Chưa có Docker daemon để chạy WordPress/WooCommerce end-to-end; chưa kiểm thử Laragon/XAMPP hoặc giao diện trình duyệt.
- Ảnh trong repo là vector minh họa, không phải ảnh chụp xe thật.
- Chính sách bảo mật là mẫu nội dung, cần rà soát trước khi thu thập dữ liệu thật.
