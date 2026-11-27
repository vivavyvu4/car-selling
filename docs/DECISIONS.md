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
PHP 8.3 / Apache, MariaDB 11.4, WP-CLI cài WordPress, WooCommerce, Rank Math và nội dung cơ bản. Named volumes giữ database và runtime; custom plugin/theme mount từ repo.

## 6. CRM
MVP lưu lead trong WordPress để giảm dịch vụ cần cấu hình. Có thể tích hợp HubSpot khi cần automation hoặc pipeline nâng cao.

## 7. Bảo mật
Không commit secrets. Production bắt buộc HTTPS, backup và cập nhật. Form có validate, consent, honeypot và rate limit. Cần chính sách quyền riêng tư trước khi thu lead thật.
