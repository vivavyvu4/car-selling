# Việc tiếp theo

## Ưu tiên 1 — Kiểm thử local
1. Chạy Docker Desktop.
2. Chạy scripts/start.sh hoặc scripts/start.ps1.
3. Theo dõi docker compose logs -f setup đến khi setup hoàn tất.
4. Truy cập http://localhost:8080 và /wp-admin.
5. Kiểm tra WooCommerce, Dealer Site Core, Dealer Starter và Rank Math đã active.
6. Gửi thử form và xác nhận lead xuất hiện trong Admin.
7. Cấu hình SMTP rồi kiểm tra email thông báo.

## Ưu tiên 2 — Hoàn thiện trải nghiệm sale
1. Rút gọn màn hình thêm sản phẩm.
2. Thêm preset thông số theo loại xe.
3. Hoàn thiện thương hiệu, danh mục và bộ lọc.
4. Thêm nhân bản sản phẩm và import/export.
5. Bổ sung gán lead, lịch sử chăm sóc và nhắc gọi lại.

## Ưu tiên 3 — Marketing
1. Nhập logo, hotline, Zalo, email, địa chỉ và thông tin pháp lý thật.
2. Tạo trang giới thiệu, chính sách bảo mật và điều khoản liên hệ.
3. Cấu hình Rank Math, sitemap, canonical và schema.
4. Kết nối Search Console, GA4 và Tag Manager.
5. Kiểm tra UTM, event gửi lead, tốc độ và responsive.

## Ưu tiên 4 — Production
1. Chọn hosting PHP 8.3+, MariaDB 10.11+ hoặc MySQL 8.4+ và HTTPS.
2. Tạo staging và backup tự động.
3. Đặt secrets qua hosting, không dùng mật khẩu local.
4. Cấu hình SMTP, CDN/WAF và anti-spam.
5. Cài Flatsome license nếu muốn dùng; không commit theme thương mại.
6. Kiểm tra quyền riêng tư, thời gian lưu lead và quyền truy cập dữ liệu.
7. Chỉ chạy quảng cáo sau khi test form, tracking, mobile, SEO và backup.

## Ngoài MVP
- Đồng bộ HubSpot.
- Báo giá PDF.
- CRM/đơn hàng đầy đủ.
- Phân lead tự động theo chi nhánh.
- Zalo OA/API hoặc tổng đài.
- Đa ngôn ngữ.
