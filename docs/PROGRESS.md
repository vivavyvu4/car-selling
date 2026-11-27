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

### Kiểm tra
- GitHub Actions sẽ chạy static validation sau commit.
- Môi trường agent hiện không có Docker daemon nên chưa thể chạy WordPress stack end-to-end tại đây.
- Chưa kiểm thử trình duyệt, email SMTP, backup/restore hoặc production hosting.

### Dữ liệu / cấu hình còn thiếu
- Logo, tên thương mại, hotline, Zalo, email và địa chỉ thật của đại lý.
- Catalog xe và ảnh được phép sử dụng.
- SMTP sender và email nhận lead.
- Domain, hosting, SSL và tài khoản production.

### Lưu ý
- Dealer Starter là theme mặc định để clone repo có thể chạy không cần license Flatsome.
- Lead inbox hiện cho Dealer Sales/Shop Manager xem toàn bộ lead; chưa có phân quyền theo từng nhân viên.
- Chưa tạo sản phẩm hoặc giá giả.
