# Việc tiếp theo

## Ưu tiên 1 — Chạy và kiểm thử local
1. Chọn một trong hai cách: Docker theo README hoặc Laragon/XAMPP theo docs/LOCAL_SETUP.md.
2. Kiểm tra WordPress, WooCommerce, Dealer Site Core, Dealer Starter và Rank Math đã active.
3. Tạo một sản phẩm thử nội bộ (không public) và kiểm tra thương hiệu, ảnh, thông số, giá và trang chi tiết.
4. Gửi form thử, kiểm tra lead, trạng thái, ghi chú và UTM.
5. Kiểm tra tài khoản Administrator, Dealer Sales và Shop Manager.
6. Cấu hình SMTP rồi kiểm tra email thông báo.
7. Ghi lại lỗi theo môi trường cụ thể (Docker hay Laragon/XAMPP).

## Ưu tiên 2 — Hoàn thiện trải nghiệm sale
1. Rút gọn màn hình thêm sản phẩm.
2. Thêm preset thông số theo loại xe.
3. Hoàn thiện thương hiệu, danh mục và bộ lọc.
4. Thêm nhân bản sản phẩm và import/export.
5. Bổ sung gán lead, lịch sử chăm sóc và nhắc gọi lại.
6. Cân nhắc plugin CRM bên ngoài nếu đại lý cần pipeline nâng cao.

## Ưu tiên 3 — Marketing
1. Nhập logo, hotline, Zalo, email, địa chỉ và thông tin pháp lý thật.
2. Tạo nội dung giới thiệu đại lý và chính sách bảo mật hoàn chỉnh.
3. Cấu hình Rank Math, sitemap, canonical và schema.
4. Kết nối Search Console, GA4 và Tag Manager.
5. Kiểm tra UTM, event gửi lead, trang cảm ơn, tốc độ và responsive.
6. Xác minh quyền sử dụng ảnh, thông số và tên thương hiệu.

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
- Phân lead tự động theo chi nhánh/nhân viên.
- Zalo OA/API hoặc tổng đài.
- Đa ngôn ngữ.
