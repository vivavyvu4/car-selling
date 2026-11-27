# Car Selling — Dealer Website

Website marketing/catalog cho đại lý ô tô đa thương hiệu, xây trên WordPress + WooCommerce. Repository chứa custom plugin, theme, Docker Compose và tài liệu local.

## Chạy local
Yêu cầu: Docker Desktop đang chạy, Docker Compose v2 và Git.

### Windows
Mở PowerShell tại thư mục repo và chạy:
    
    .\scripts\start.ps1

### macOS / Linux

    chmod +x scripts/start.sh
    ./scripts/start.sh

Lần đầu, script tạo .env từ .env.example. Docker Compose tự khởi động MariaDB/WordPress, cài WordPress, WooCommerce, Rank Math, kích hoạt Dealer Site Core và Dealer Starter, rồi tạo trang và danh mục xe cơ bản.

Mặc định:
- Website: http://localhost:8080
- Admin: http://localhost:8080/wp-admin
- Username: dealeradmin
- Password: xem WP_ADMIN_PASSWORD trong .env local.

Chỉ dùng mật khẩu .env cho local; không dùng trên production.

Theo dõi cài đặt lần đầu:

    docker compose logs -f setup

Nếu setup lỗi do không tải được plugin từ WordPress.org, khôi phục mạng rồi chạy lại:

    docker compose run --rm setup

Dừng website nhưng giữ dữ liệu:

    docker compose down

Xóa cả database và WordPress local:

    docker compose down -v

Lệnh down -v xóa dữ liệu vĩnh viễn nếu chưa backup.

## Thành phần
- WordPress image PHP 8.3 + Apache.
- MariaDB 11.4.
- WP-CLI cài site tự động.
- WooCommerce quản lý catalog, danh mục, thuộc tính và ảnh.
- Rank Math SEO quản lý metadata, sitemap và schema.
- Dealer Site Core chứa chức năng riêng của đại lý.
- Dealer Starter là theme responsive không cần license thương mại.

## Quản trị sản phẩm
Vào Sản phẩm → Thêm mới:
1. Nhập tên xe và mô tả.
2. Chọn danh mục và thương hiệu.
3. Tải ảnh đại diện và album WooCommerce.
4. Nhập model, tình trạng và bảo hành trong hộp Thông tin xe.
5. Thêm các dòng thông số kỹ thuật phù hợp.
6. Nhập giá số trong dữ liệu sản phẩm nếu muốn lọc/sắp xếp theo giá.
7. Chọn cách hiển thị giá: liên hệ, giá từ hoặc giá cụ thể.
8. Lưu nháp, xem trước và xuất bản sau khi xác minh dữ liệu.

## Nhận yêu cầu báo giá
- Shortcode [dealer_quote_form] hiển thị form; trên trang sản phẩm tự nhận sản phẩm hiện tại.
- Shortcode [dealer_contact_buttons] hiển thị hotline, Zalo và email.
- Lead lưu trong Dealer Website → Khách hàng tiềm năng.
- Lead lưu thông tin liên hệ, nội dung, xe quan tâm, UTM và nguồn truy cập.
- Email dùng wp_mail; cần cấu hình SMTP trước production.

## Cấu hình đại lý
Vào Dealer Website → Cấu hình đại lý để nhập tên, hotline, link Zalo, email nhận lead và địa chỉ. Không public website khi thông tin đại lý, thông số hoặc giá chưa xác minh.

## Flatsome (tùy chọn)
Local mặc định dùng Dealer Starter nên không cần license Flatsome để chạy. Nếu đại lý có license, có thể cài parent theme Flatsome riêng và xây child theme tương ứng. Không commit parent theme thương mại vào Git.

## SEO/marketing trước khi public
Cấu hình Site Title, logo, favicon, Rank Math, sitemap, canonical, schema; viết nội dung riêng cho từng xe và danh mục; tối ưu ảnh/ALT; kết nối Search Console, GA4, GTM; kiểm tra UTM, sự kiện gửi form, mobile, structured data và chính sách quyền riêng tư. Xác minh quyền sử dụng ảnh, thông số và tên thương hiệu.

## Production
Docker Compose trong repo chỉ dành cho local. Hosting nên đáp ứng baseline WordPress: PHP 8.3+, MariaDB 10.11+ hoặc MySQL 8.4+, HTTPS. Dùng backup, staging, SMTP, cập nhật bảo mật và tài khoản theo vai trò.

## Ghi chú dự án
- docs/PROGRESS.md — Nhật ký tiến trình và kiểm thử.
- docs/NEXT_STEPS.md — Các bước tiếp theo.
- docs/DECISIONS.md — Quyết định kỹ thuật.
