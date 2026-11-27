<?php
if (! defined('ABSPATH')) exit;
get_header();
$shop = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/xe/');
?>
<main>
<section class="dealer-hero"><div class="dealer-container dealer-hero-inner">
<div><span class="dealer-eyebrow">Tư vấn xe đa thương hiệu</span><h1>Tìm chiếc xe phù hợp với nhu cầu của bạn</h1>
<p>Khám phá danh mục xe, xem thông tin sản phẩm và kết nối với tư vấn viên để nhận báo giá.</p>
<form class="dealer-search" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
<input type="search" name="s" placeholder="Nhập tên xe, hãng xe hoặc model..." aria-label="Tìm kiếm xe"><input type="hidden" name="post_type" value="product"><button type="submit">Tìm xe</button>
</form></div>
<div class="dealer-hero-art"><div><strong>Chọn xe đúng nhu cầu</strong><span>Thông tin rõ ràng · Tư vấn trực tiếp · Báo giá minh bạch</span></div></div>
</div></section>
<section class="dealer-section"><div class="dealer-container">
<div class="dealer-section-heading"><div><h2>Danh mục xe</h2><p>Lựa chọn nhóm xe bạn đang quan tâm.</p></div><a href="<?php echo esc_url($shop); ?>">Xem tất cả xe →</a></div>
<div class="dealer-category-grid">
<?php
$categories = array(
    array('xe-du-lich', 'Xe du lịch', 'Sedan, SUV, MPV, bán tải'),
    array('xe-tai', 'Xe tải', 'Tải nhẹ, tải trung, tải nặng'),
    array('xe-dau-keo', 'Xe đầu kéo', 'Đầu kéo và vận tải đường dài'),
    array('xe-khach', 'Xe khách', 'Xe khách và xe thương mại'),
    array('xe-chuyen-dung', 'Xe chuyên dụng', 'Ben, bồn, trộn và chuyên dùng'),
);
foreach ($categories as $category) :
    $term = get_term_by('slug', $category[0], 'product_cat');
    $url = $term && ! is_wp_error($term) ? get_term_link($term) : $shop;
?>
<a class="dealer-category-card" href="<?php echo esc_url($url); ?>"><strong><?php echo esc_html($category[1]); ?></strong><span><?php echo esc_html($category[2]); ?></span></a>
<?php endforeach; ?>
</div></div></section>
<section class="dealer-section dealer-section-muted"><div class="dealer-container">
<div class="dealer-section-heading"><div><h2>Sản phẩm mới cập nhật</h2><p>Danh sách xe được đại lý đăng tải gần đây.</p></div><a href="<?php echo esc_url($shop); ?>">Khám phá danh mục →</a></div>
<?php echo do_shortcode('[products limit="8" columns="4" orderby="date" order="DESC"]'); ?>
</div></section>
<section class="dealer-section"><div class="dealer-container">
<div class="dealer-section-heading"><div><h2>Đồng hành cùng bạn khi chọn xe</h2><p>Trao đổi rõ nhu cầu trước khi quyết định.</p></div></div>
<div class="dealer-benefits">
<article class="dealer-benefit"><h3>Tư vấn theo nhu cầu</h3><p>Trao đổi mục đích sử dụng, tải trọng, ngân sách và phương án phù hợp.</p></article>
<article class="dealer-benefit"><h3>Thông tin minh bạch</h3><p>Thông số, tình trạng và giá tham khảo được xác nhận trước giao dịch.</p></article>
<article class="dealer-benefit"><h3>Kết nối trực tiếp</h3><p>Gửi yêu cầu để đại lý liên hệ tư vấn và cung cấp thông tin cập nhật.</p></article>
</div><?php echo do_shortcode('[dealer_contact_buttons]'); ?>
</div></section>
</main>
<?php get_footer(); ?>
