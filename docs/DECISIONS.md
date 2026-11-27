# Quyết định kỹ thuật

## 1. WordPress thay cho Nuxt + Spring Boot
Website phục vụ sale và marketing, không phải dự án học tập hoặc SaaS. Ưu tiên triển khai nhanh, CMS có sẵn và ít công bảo trì.

## 2. WooCommerce làm catalog
Dùng sản phẩm, danh mục, taxonomy, gallery và giá số. Tắt purchasable/cart/checkout để tập trung nhận yêu cầu báo giá.

## 3. Dealer Site Core
Plugin riêng giữ nghiệp vụ đại lý: thương hiệu, thông tin xe, thông số động, lead form/inbox và thông tin liên hệ. Không fork WooCommerce hoặc sửa WordPress core.

## 4. Dealer Starter là theme mặc định
Theme tự xây nhẹ dùng local và có thể dùng production. Flatsome là lựa chọn bổ sung khi đại lý có license; không commit parent theme thương mại.

## 5. Docker Compose + WP-CLI
- PHP 8.3 / Apache.
- MariaDB 11.4.
- WP-CLI cài WordPress, WooCommerce, Rank Math và seed nội dung cơ bản.
- Named volumes giữ database và runtime; custom plugin/theme được bind mount từ repo.
- .env ở repository root chỉ cung cấp biến môi trường cho Docker Compose.

## 6. Chạy local không Docker
- WordPress core được cài riêng trong Laragon/XAMPP web root.
- Repository chỉ lưu custom plugin/theme và project docs/scripts.
- Copy wp-content/plugins/dealer-site-core và wp-content/themes/dealer-starter từ repository sang WordPress installation.
- Database connection được WordPress installer lưu trong wp-config.php.
- Không cần .env, Docker Desktop hoặc docker compose cho cách chạy này.
- Hướng dẫn cụ thể ở docs/LOCAL_SETUP.md.

## 7. CRM
MVP lưu lead trong WordPress để giảm dịch vụ cần cấu hình. Có thể tích hợp HubSpot khi cần automation hoặc pipeline nâng cao.

## 8. Phân quyền
- Administrator quản lý toàn website.
- Dealer Sales kế thừa capability của WooCommerce Shop Manager, thêm quyền lead.
- Shop Manager cũng được cấp quyền lead.
- Các role này có edit_private_dealer_leads để chỉnh sửa lead có post_status private.
- Dealer Sales và Shop Manager hiện xem được toàn bộ lead; chưa có ownership/assignment filter.

## 9. Bảo mật
Không commit secrets. Production bắt buộc HTTPS, backup và cập nhật thường xuyên. Form có validate, consent, honeypot và rate limit. Cần chính sách quyền riêng tư trước khi thu lead thật.


## Demo content
- Dữ liệu demo được tạo bằng WP-CLI; không commit database dump hoặc uploads.
- Seed đánh dấu sản phẩm/bài viết/ảnh để chạy lại không tạo bản trùng.
- Sản phẩm dùng giá 0 nội bộ và display mode “Liên hệ báo giá”; giá không hiển thị ra khách.
- SVG vector được lưu trong theme, copy vào Media Library khi seed.
- Không điền thông tin liên hệ giả; chính sách bảo mật hiển thị rõ là nội dung mẫu.
- remove-demo-content.php chỉ xóa record có marker demo; nội dung trang chỉ làm trống nếu hash vẫn khớp bản seed.
