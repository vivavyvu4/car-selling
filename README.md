# Car Selling — Dealer Website

Website marketing/catalog cho đại lý ô tô đa thương hiệu, xây trên WordPress + WooCommerce. Repository chứa custom plugin, theme, Docker Compose và tài liệu local.

## Chọn cách chạy local

Có hai cách độc lập:

1. **Docker:** ít bước cài đặt thủ công nhất; WordPress, PHP và MariaDB chạy trong container.
2. **Không Docker:** dùng Laragon (khuyến nghị trên Windows) hoặc XAMPP/local Apache + PHP + MySQL/MariaDB. WordPress core được cài riêng, sau đó copy plugin/theme từ repository vào WordPress.

Hướng dẫn chi tiết:
- docs/LOCAL_SETUP.md — chạy trực tiếp local không Docker.

## Cách 1 — Chạy bằng Docker

### Yêu cầu
- Docker Desktop đang chạy.
- Docker Compose v2.
- Git.

### Windows — PowerShell

Mở PowerShell và chạy lần lượt:

    # Tạo thư mục cha để chứa các project source code (chỉ cần làm một lần).
    New-Item -ItemType Directory -Force C:\projects | Out-Null

    # Chuyển vào thư mục cha vừa tạo.
    Set-Location C:\projects

    # Clone repository từ GitHub về máy.
    git clone https://github.com/vivavyvu4/car-selling.git

    # Đi vào thư mục repository; đây là thư mục chứa docker-compose.yml và .env.example.
    Set-Location .\car-selling

    # Copy file mẫu .env.example thành .env NGAY TRONG THƯ MỤC car-selling.
    # Lệnh này không tạo file trong thư mục WordPress và không sửa .env.example.
    Copy-Item -Path .\.env.example -Destination .\.env

    # Mở file .env để thay các password mẫu trước khi khởi động.
    notepad .\.env

    # Chạy script khởi động Docker Compose.
    .\scripts\start.ps1

    # Theo dõi log cài WordPress, WooCommerce và Rank Math trong lần chạy đầu.
    docker compose logs -f setup

### macOS / Linux

    # Clone repository vào thư mục hiện tại.
    git clone https://github.com/vivavyvu4/car-selling.git

    # Đi vào thư mục project, nơi có docker-compose.yml và .env.example.
    cd car-selling

    # Tạo file .env cùng cấp với docker-compose.yml từ file mẫu.
    cp .env.example .env

    # Mở .env bằng editor của bạn và đổi password local trước khi chạy.
    nano .env

    # Cấp quyền thực thi cho script khởi động (chỉ cần làm một lần).
    chmod +x scripts/start.sh

    # Khởi động các container.
    ./scripts/start.sh

    # Theo dõi tiến trình setup lần đầu.
    docker compose logs -f setup

### Vì sao cần lệnh Copy-Item?

Ví dụ nếu clone vào C:\projects\car-selling, lệnh:

    Copy-Item -Path .\.env.example -Destination .\.env

sẽ tạo file:

    C:\projects\car-selling\.env

File này nằm cạnh docker-compose.yml, không nằm trong wp-content hay trong thư mục WordPress core. .env chỉ chứa cấu hình cho Docker Compose; không commit file này lên Git.

Nếu không tự tạo .env, script start.ps1 cũng tự copy .env.example khi chạy lần đầu. Tuy nhiên, nên copy và chỉnh password trước khi khởi động.

### Địa chỉ và tài khoản mặc định

- Website: http://localhost:8080
- Admin: http://localhost:8080/wp-admin
- Username mặc định: dealeradmin
- Password: giá trị WP_ADMIN_PASSWORD trong .env trên máy của bạn.

Chỉ dùng password local cho môi trường phát triển. Không dùng password mẫu trên production.

### Lệnh Docker hữu ích

    # Xem log của toàn bộ service.
    docker compose logs -f

    # Dừng container nhưng giữ nguyên database và dữ liệu WordPress.
    docker compose down

    # Chạy lại tác vụ setup nếu lần cài plugin bị gián đoạn do mất mạng.
    docker compose run --rm setup

    # XÓA database và toàn bộ dữ liệu WordPress local trong named volumes.
    # Chỉ chạy khi chắc chắn không cần dữ liệu cũ hoặc đã backup.
    docker compose down -v

## Cách 2 — Chạy trực tiếp local, không Docker

Dùng Laragon hoặc XAMPP để chạy Apache, PHP và MySQL/MariaDB trực tiếp trên máy. Khi dùng cách này:
- Không chạy docker compose.
- Không cần tạo file .env.
- WordPress lưu thông tin database trong wp-config.php.
- Repository car-selling giữ source custom; thư mục WordPress core là một thư mục riêng.

Xem hướng dẫn từng bước và các lệnh PowerShell có comment tại docs/LOCAL_SETUP.md.

## Bộ dữ liệu DEMO có sẵn

Lần setup đầu sẽ tạo 10 sản phẩm xe trải đều 5 danh mục, 4 bài viết, trang Giới thiệu/Liên hệ/Chính sách, menu và ảnh minh họa. Tên xe, thương hiệu, model và thông số đều hư cấu; giá hiển thị ở chế độ “Liên hệ báo giá”, không tạo giá bán giả. Không có hotline, email, Zalo hoặc địa chỉ giả trong cấu hình đại lý.

Ảnh xe là vector SVG minh họa được lưu trong theme tại wp-content/themes/dealer-starter/assets/demo-vehicles và được seed vào Media Library. Header hiển thị DEMO MODE cho đến khi dữ liệu mẫu được gỡ.

Để xóa sản phẩm, bài viết và ảnh mẫu, chạy lệnh chủ động:

    # Docker: chạy WP-CLI trong container setup và xóa các record demo.
    docker compose run --rm --entrypoint wp setup eval-file /project/scripts/remove-demo-content.php --allow-root

    # Laragon/XAMPP: dùng WP-CLI đã cài và chỉ định đường dẫn WordPress + script trong repository.
    php $wpCli --path="$wpRoot" eval-file "$repo\scripts\remove-demo-content.php"

Cleanup chỉ làm trống nội dung trang nếu nội dung vẫn trùng với bản demo đã seed, tránh xóa chỉnh sửa thủ công. Không public website khi chưa thay nội dung, hình ảnh và thông tin đại lý bằng dữ liệu đã xác minh.

## Thành phần
- WordPress image PHP 8.3 + Apache (Docker mode).
- MariaDB 11.4 (Docker mode).
- WP-CLI cài site tự động.
- WooCommerce quản lý catalog, danh mục, thuộc tính và ảnh.
- Rank Math SEO quản lý metadata, sitemap và schema.
- Dealer Site Core chứa chức năng riêng của đại lý.
- Dealer Starter là theme responsive, không cần license thương mại.

## Quản trị sản phẩm
Vào **Sản phẩm → Thêm mới**:
1. Nhập tên xe và mô tả.
2. Chọn danh mục và thương hiệu.
3. Tải ảnh đại diện và album WooCommerce.
4. Nhập model, tình trạng và bảo hành trong hộp **Thông tin xe**.
5. Thêm các dòng thông số kỹ thuật phù hợp.
6. Nhập giá số trong dữ liệu sản phẩm nếu muốn lọc/sắp xếp theo giá.
7. Chọn cách hiển thị giá: liên hệ, giá từ hoặc giá cụ thể.
8. Lưu nháp, xem trước và xuất bản sau khi xác minh dữ liệu.

Không cần nhập thông số không áp dụng cho mẫu xe.

## Người dùng và phân quyền
- Administrator: toàn quyền website và cấu hình đại lý.
- Dealer Sales: có quyền quản lý sản phẩm và lead, không có quyền cài plugin hoặc thay đổi cấu hình toàn website.
- WooCommerce Shop Manager: cũng được cấp quyền truy cập lead.
- Tạo người dùng tại Users → Add New và chọn role phù hợp.

Hiện tại Dealer Sales và Shop Manager có thể xem toàn bộ lead. Phân lead theo từng nhân viên chưa nằm trong MVP.

## Nhận yêu cầu báo giá
- Shortcode [dealer_quote_form] hiển thị form; trên trang sản phẩm tự nhận sản phẩm hiện tại.
- Shortcode [dealer_contact_buttons] hiển thị hotline, Zalo và email.
- Lead lưu trong Dealer Website → Khách hàng tiềm năng.
- Lead lưu thông tin liên hệ, nội dung, xe quan tâm, UTM và nguồn truy cập.
- Email dùng wp_mail; cần cấu hình SMTP trước production.

## Cấu hình đại lý
Vào Dealer Website → Cấu hình đại lý để nhập tên, hotline, link Zalo, email nhận lead và địa chỉ. Chỉ Administrator được sửa phần cấu hình này.

Không public website khi thông tin đại lý, thông số hoặc giá chưa xác minh.

## Flatsome (tùy chọn)
Local mặc định dùng Dealer Starter nên không cần license Flatsome để chạy. Nếu đại lý có license, có thể cài parent theme Flatsome riêng và phát triển child theme tương ứng. Không commit theme thương mại vào Git.

## SEO/marketing trước khi public
Cấu hình Site Title, logo, favicon, Rank Math, sitemap, canonical, schema; viết nội dung riêng cho từng xe và danh mục; tối ưu ảnh/ALT; kết nối Search Console, GA4, GTM; kiểm tra UTM, sự kiện gửi form, mobile, structured data và chính sách quyền riêng tư. Xác minh quyền sử dụng ảnh, thông số và tên thương hiệu.

## Production
Docker Compose trong repo chỉ dành cho local development. Hosting nên đáp ứng baseline WordPress: PHP 8.3+, MariaDB 10.11+ hoặc MySQL 8.4+, HTTPS. Dùng backup, staging, SMTP, cập nhật bảo mật và tài khoản theo vai trò.

## Ghi chú dự án
- docs/PROGRESS.md — Nhật ký tiến trình và kiểm thử.
- docs/NEXT_STEPS.md — Các bước tiếp theo.
- docs/DECISIONS.md — Quyết định kỹ thuật.
- docs/LOCAL_SETUP.md — Hướng dẫn chạy local bằng Laragon/XAMPP, không Docker.
