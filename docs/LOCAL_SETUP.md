# Chạy local trực tiếp, không dùng Docker

Tài liệu này hướng dẫn chạy project trên Windows bằng **Laragon** (khuyến nghị) hoặc XAMPP. Cách này chạy Apache, PHP và MySQL/MariaDB trực tiếp trên máy, không dùng container.

## 1. Phân biệt hai thư mục

Khi chạy không Docker, cần có hai thư mục độc lập:

| Thư mục | Vai trò | Ví dụ |
|---|---|---|
| Repository source | Chứa code custom của dự án, README, scripts và docs | C:\projects\car-selling |
| WordPress installation | Chứa WordPress core như wp-admin, wp-includes, wp-config.php | C:\laragon\www\car-selling-local |

**Không trỏ Apache vào thư mục repository car-selling**, vì repository này không chứa WordPress core. Ta sẽ copy custom plugin/theme từ repository sang thư mục wp-content của WordPress installation.

## 2. Kiểm tra yêu cầu phần mềm

Theo khuyến nghị WordPress hiện tại, môi trường nên có:
- PHP 8.3 trở lên.
- MySQL 8.4 trở lên hoặc MariaDB 10.11 trở lên.
- Apache hoặc Nginx; bật rewrite module nếu dùng pretty permalink.

Tham khảo: https://wordpress.org/about/requirements/

Cần có Git để clone source và Internet để cài WordPress, WooCommerce, Rank Math.

## 3. Cách A — Dùng Laragon (khuyến nghị trên Windows)

### Bước 1: Cài Laragon và khởi động service

1. Tải Laragon từ https://laragon.org/download/.
2. Cài Laragon, ví dụ vào C:\laragon.
3. Mở Laragon và bấm **Start All** để khởi động Apache và MySQL.
4. Kiểm tra version PHP và database đang chọn trong Laragon. Nếu chưa đạt mức ở phần 2, hãy đổi sang package version phù hợp trước khi cài WordPress.

Tài liệu Laragon: https://laragon.org/docs/install

### Bước 2: Tạo một WordPress site riêng trong Laragon

Cách nhanh nhất:

1. Mở Laragon.
2. Chọn **Menu → Quick app → WordPress**.
3. Nhập tên site: car-selling-local.
4. Laragon sẽ tạo thư mục site trong C:\laragon\www, chuẩn bị database và tải WordPress.
5. Mở URL Laragon tạo, thường là http://car-selling-local.test.
6. Hoàn tất trình cài WordPress nếu trình duyệt yêu cầu.

Laragon Quick App có thể tạo database, tải WordPress và thiết lập virtual host tự động. Xem https://laragon.org/docs/quick-start.

Nếu Quick App không có sẵn hoặc không hoạt động:
- Tải WordPress từ https://wordpress.org/download/.
- Giải nén nội dung WordPress vào C:\laragon\www\car-selling-local.
- Tạo database bằng Laragon Database/phpMyAdmin.
- Mở http://localhost/car-selling-local và hoàn thành WordPress installer.

### Bước 3: Clone repository source

Mở **PowerShell** (không bắt buộc mở trong thư mục Laragon):

    # Tạo thư mục C:\projects để lưu source code riêng, không đặt source vào thư mục WordPress core.
    New-Item -ItemType Directory -Force C:\projects | Out-Null

    # Chuyển vào thư mục chứa các project.
    Set-Location C:\projects

    # Clone repository từ GitHub.
    git clone https://github.com/vivavyvu4/car-selling.git

    # Đi vào repository vừa clone.
    Set-Location C:\projects\car-selling

    # Liệt kê file để xác nhận đang ở đúng repository root.
    # Bạn sẽ thấy README.md, docker-compose.yml, .env.example, wp-content, scripts và docs.
    Get-ChildItem

### Bước 4: Hoàn thành WordPress installer

Mở URL site do Laragon tạo, ví dụ http://car-selling-local.test.

Nếu đây là lần đầu cài WordPress, điền:
- Site title: tên tạm cho website.
- Username/password: tài khoản quản trị local của bạn.
- Email: email dùng cho môi trường local.

Thông tin database đã được Quick App chuẩn bị thì dùng thông tin đó. Nếu WordPress hỏi thủ công, xem database/user/password trong Laragon hoặc phpMyAdmin; không đoán password database.

Sau khi hoàn tất, kiểm tra:
- C:\laragon\www\car-selling-local\wp-config.php tồn tại.
- C:\laragon\www\car-selling-local\wp-admin tồn tại.
- Có thể đăng nhập vào /wp-admin.

### Bước 5: Copy plugin và theme từ repository vào WordPress

Đây là bước nối source code của dự án với WordPress installation.

Chạy trong PowerShell. Nếu bạn cài Laragon ở đường dẫn khác, sửa giá trị $wpRoot tương ứng.

    # $repo là nơi bạn clone source từ GitHub.
    $repo = "C:\projects\car-selling"

    # $wpRoot là thư mục WordPress core được Laragon Quick App tạo.
    # Thư mục này phải có wp-admin, wp-includes và wp-config.php.
    $wpRoot = "C:\laragon\www\car-selling-local"

    # Copy plugin riêng của dự án vào WordPress/wp-content/plugins.
    # Copy-Item copy cả thư mục dealer-site-core, không chỉ copy file PHP lẻ.
    Copy-Item -Path (Join-Path $repo "wp-content\plugins\dealer-site-core") -Destination (Join-Path $wpRoot "wp-content\plugins") -Recurse -Force

    # Copy theme Dealer Starter vào WordPress/wp-content/themes.
    Copy-Item -Path (Join-Path $repo "wp-content\themes\dealer-starter") -Destination (Join-Path $wpRoot "wp-content\themes") -Recurse -Force

    # Kiểm tra hai thư mục đã được copy thành công.
    Test-Path (Join-Path $wpRoot "wp-content\plugins\dealer-site-core\dealer-site-core.php")
    Test-Path (Join-Path $wpRoot "wp-content\themes\dealer-starter\style.css")

Hai lệnh Copy-Item trên sẽ tạo:
- C:\laragon\www\car-selling-local\wp-content\plugins\dealer-site-core\
- C:\laragon\www\car-selling-local\wp-content\themes\dealer-starter\

Nếu Test-Path trả về True cho cả hai đường dẫn thì source đã vào đúng chỗ.

### Bước 6: Cài WP-CLI để hoàn tất setup bằng lệnh

WP-CLI giúp cài plugin, bật theme và chạy script tạo trang/danh mục mà không cần thao tác từng mục trong Admin.

Tài liệu cài chính thức: https://make.wordpress.org/cli/handbook/guides/installing/

Trước tiên, mở PowerShell có thể chạy PHP. Nếu lệnh php chưa được nhận diện, thêm thư mục PHP của Laragon vào PATH cho phiên terminal hiện tại. Thay tên folder ví dụ bằng folder PHP thật có trong C:\laragon\bin\php\.

    # Ví dụ: thay php-8.3.xx... bằng đúng tên folder PHP đang cài trong Laragon.
    $phpDir = "C:\laragon\bin\php\php-8.3.xx-Win32-vs16-x64"

    # Thêm PHP vào PATH của cửa sổ PowerShell hiện tại.
    $env:Path = "$phpDir;$env:Path"

    # Kiểm tra PHP chạy được và đúng version yêu cầu.
    php --version

Tiếp theo tải WP-CLI:

    # Tạo thư mục riêng để lưu file WP-CLI.
    New-Item -ItemType Directory -Force C:\tools\wp-cli | Out-Null

    # Tải bản WP-CLI Phar chính thức về máy.
    Invoke-WebRequest -Uri "https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar" -OutFile "C:\tools\wp-cli\wp-cli.phar"

    # Khai báo đường dẫn file WP-CLI để các lệnh tiếp theo dùng lại.
    $wpCli = "C:\tools\wp-cli\wp-cli.phar"

    # Kiểm tra WP-CLI khởi chạy thành công.
    php $wpCli --info

Bây giờ chạy các lệnh cài đặt. Giữ nguyên hai biến $wpCli và $wpRoot đã khai báo ở trên:

    # Cài WooCommerce và kích hoạt ngay sau khi tải về.
    php $wpCli --path="$wpRoot" plugin install woocommerce --activate

    # Cài Rank Math SEO và kích hoạt.
    php $wpCli --path="$wpRoot" plugin install seo-by-rank-math --activate

    # Kích hoạt plugin nghiệp vụ đã copy từ repository.
    php $wpCli --path="$wpRoot" plugin activate dealer-site-core

    # Kích hoạt giao diện miễn phí Dealer Starter đã copy từ repository.
    php $wpCli --path="$wpRoot" theme activate dealer-starter

    # Tải và kích hoạt gói ngôn ngữ tiếng Việt cho WordPress.
    php $wpCli --path="$wpRoot" language core install vi --activate

    # Chạy script seed-site.php của repository.
    # Script tạo trang chủ, trang xe, tin tức, liên hệ, chính sách và danh mục xe.
    # Script có thể chạy lại để bổ sung nội dung thiếu; tạo 10 xe demo, 4 bài viết, ảnh vector, menu và trang nội dung mẫu.
    php $wpCli --path="$wpRoot" eval-file "$repo\scripts\seed-site.php"

    # Có thể chạy lại script seed để bổ sung record demo còn thiếu mà không nhân bản sản phẩm/bài viết.
    php $wpCli --path="$wpRoot" eval-file "$repo\scripts\seed-site.php"

    # Thiết lập permalink dạng /ten-bai-viet/ và cập nhật rewrite rules.
    php $wpCli --path="$wpRoot" rewrite structure '/%postname%/' --hard

    # Kiểm tra danh sách plugin đang bật.
    php $wpCli --path="$wpRoot" plugin list

    # Kiểm tra theme đang được sử dụng.
    php $wpCli --path="$wpRoot" theme list

Nếu các lệnh hoàn tất không báo lỗi, mở:
- Website: http://car-selling-local.test
- Admin: http://car-selling-local.test/wp-admin

Nếu Laragon không tạo virtual host, dùng URL tương ứng như http://localhost/car-selling-local.

### Bước 7: Cấu hình website sau khi cài

Trong Admin:
1. Vào **Dealer Website → Cấu hình đại lý**.
2. Nhập tên, hotline, Zalo URL, email nhận lead và địa chỉ thật.
3. Vào **Sản phẩm** để thêm một xe kiểm thử ở trạng thái nháp.
4. Kiểm tra danh mục, thương hiệu, ảnh, model, tình trạng, bảo hành và thông số.
5. Gửi thử form báo giá và kiểm tra **Dealer Website → Khách hàng tiềm năng**.

Email thông báo dùng wp_mail; email local có thể chưa gửi được nếu chưa cấu hình SMTP.

### Bước 8: Cập nhật source sau này

Khi có commit mới trên GitHub, thao tác theo thứ tự:
1. Pull source mới trong repository.
2. Copy lại plugin/theme vào WordPress installation để cập nhật source đang chạy.
3. Nếu có thay đổi database hoặc seed mới, đọc docs/PROGRESS.md và docs/NEXT_STEPS.md trước khi chạy migration/seed.

    # Đi vào repository source, không phải thư mục WordPress core.
    Set-Location C:\projects\car-selling

    # Lấy commit mới nhất từ main.
    git pull origin main

    # Copy lại plugin đã cập nhật vào WordPress installation.
    Copy-Item -Path (Join-Path $repo "wp-content\plugins\dealer-site-core") -Destination (Join-Path $wpRoot "wp-content\plugins") -Recurse -Force

    # Copy lại theme đã cập nhật vào WordPress installation.
    Copy-Item -Path (Join-Path $repo "wp-content\themes\dealer-starter") -Destination (Join-Path $wpRoot "wp-content\themes") -Recurse -Force

Nếu PowerShell báo biến $repo hoặc $wpRoot không tồn tại, khai báo lại hai biến ở Bước 5 trước khi chạy lệnh copy.

## 4. Cách B — Dùng XAMPP

Nếu đã quen XAMPP, các bước tương tự Laragon nhưng cần tự chuẩn bị WordPress core và database.

1. Cài XAMPP có PHP 8.3+ và MySQL 8.4+ hoặc MariaDB 10.11+.
2. Mở XAMPP Control Panel, Start Apache và MySQL.
3. Tạo thư mục WordPress, ví dụ C:\xampp\htdocs\car-selling-local.
4. Tải WordPress từ https://wordpress.org/download/ và giải nén toàn bộ nội dung vào thư mục trên.
5. Mở http://localhost/phpmyadmin, tạo database ví dụ car_selling_local, charset utf8mb4.
6. Mở http://localhost/car-selling-local và hoàn tất WordPress installer bằng thông tin database vừa tạo.
7. Trong các lệnh ở Bước 5, đặt lại WordPress root:

    # Đường dẫn WordPress core cài bằng XAMPP.
    $wpRoot = "C:\xampp\htdocs\car-selling-local"

8. Thêm PHP XAMPP vào PATH của PowerShell nếu lệnh php chưa chạy:

    # PHP thường nằm trong C:\xampp\php.
    $env:Path = "C:\xampp\php;$env:Path"

9. Tiếp tục từ Bước 5 — Copy plugin/theme và Bước 6 — Cài WP-CLI trong hướng dẫn Laragon.

Lưu ý: không phải bản XAMPP nào cũng đi kèm phiên bản database đáp ứng khuyến nghị WordPress. Hãy kiểm tra version MySQL/MariaDB trước khi sử dụng.

## 5. Những điểm khác biệt so với Docker

| Nội dung | Docker | Laragon / XAMPP |
|---|---|---|
| WordPress core | Nằm trong named volume của container | Nằm trong thư mục Laragon/XAMPP |
| PHP + Apache | Chạy trong container | Chạy trực tiếp trên Windows |
| Database | MariaDB container | MySQL/MariaDB local |
| .env | Dùng ở repository root cho Compose | Không cần |
| WordPress database config | Docker tự truyền qua environment | Lưu trong wp-config.php |
| Plugin/theme custom | Bind mount từ repository | Copy từ repository sang wp-content |
| Khởi động/dừng | docker compose up/down | Start/Stop Laragon hoặc XAMPP |

## 6. Xử lý lỗi thường gặp

### php is not recognized
PowerShell chưa tìm thấy PHP. Kiểm tra PHP đã cài chưa và thêm đúng thư mục PHP của Laragon/XAMPP vào PATH như Bước 6.

### Error establishing a database connection
Kiểm tra:
- MySQL/MariaDB đã Start trong Laragon/XAMPP chưa.
- Database name, username, password và host đã nhập đúng trong WordPress installer chưa.
- Database đã được tạo chưa.

### Không thấy theme Dealer Starter
Kiểm tra file sau có tồn tại:

    # Đường dẫn style.css phải nằm bên trong đúng thư mục theme.
    Test-Path "$wpRoot\wp-content\themes\dealer-starter\style.css"

Nếu trả về False, chạy lại lệnh Copy-Item theme tại Bước 5.

### Không thấy plugin Dealer Site Core
Kiểm tra file:

    # File chính của plugin phải tồn tại trong thư mục WordPress plugins.
    Test-Path "$wpRoot\wp-content\plugins\dealer-site-core\dealer-site-core.php"

Nếu trả về False, chạy lại lệnh Copy-Item plugin tại Bước 5.

### Website báo lỗi permalink / trang không tìm thấy
Chạy lại:

    # Tạo lại rewrite rules bằng WP-CLI.
    php $wpCli --path="$wpRoot" rewrite structure '/%postname%/' --hard

Nếu dùng Apache, kiểm tra module rewrite đã được bật.

### Không gửi được email lead
Đây là tình trạng có thể gặp ở local. Cấu hình SMTP plugin hoặc mail catcher trước khi kiểm thử email; việc lưu lead trong WordPress vẫn là bước kiểm tra chính.

### Cập nhật source nhưng website chưa đổi
Bạn cần copy lại thư mục plugin/theme từ repository sang WordPress installation. git pull chỉ cập nhật repository, không tự đồng bộ sang thư mục WordPress độc lập.

## 7. Ghi chú bảo mật

- Đây là môi trường local để phát triển, không public port ra Internet.
- Không tái sử dụng mật khẩu local cho production.
- Không đưa wp-config.php, database dump, file upload hoặc secrets vào Git.
- Trước khi chạy website thật, cần cấu hình HTTPS, SMTP, backup và chính sách quyền riêng tư.


### Xóa dữ liệu DEMO trước khi nhập catalog thật

    # Xóa các sản phẩm/bài viết còn được đánh dấu demo và ảnh demo trong Media Library.
    # Các trang lõi, menu và danh mục xe được giữ lại.
    # Nội dung trang chỉ được làm trống nếu vẫn trùng với bản demo seed, không đụng vào phần đã sửa tay.
    php $wpCli --path="$wpRoot" eval-file "$repo\scripts\remove-demo-content.php"

Lưu ý: cleanup có tính phá hủy đối với record còn mang marker DEMO. Nếu đã chỉnh sửa một sản phẩm demo thành dữ liệu thật, hãy sao lưu/tạo bản mới trước khi chạy lệnh.
