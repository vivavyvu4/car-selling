# Tiến trình dự án

## 2026-11-27 — Khởi tạo nền tảng WordPress
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

### Đã kiểm tra tĩnh
- PHP syntax lint.
- JavaScript syntax check.
- Shell syntax check.
- YAML parse.
- Kiểm tra đường dẫn mount trong Compose.

### Chưa kiểm chứng
- Chưa chạy docker compose up vì môi trường làm việc không có Docker daemon.
- Chưa integration test trên WordPress/WooCommerce thật.
- Chưa kiểm thử SMTP, form trình duyệt hoặc SEO structured data trên URL thật.
- Chưa có license Flatsome hoặc thông tin đại lý/catalog chính thức.

### Nguyên tắc dữ liệu
Không tạo xe demo, giá bán hoặc thông tin liên hệ giả. Người quản trị phải nhập và xác minh dữ liệu thật trước khi xuất bản.
