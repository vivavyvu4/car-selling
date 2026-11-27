<?php
/**
 * Idempotent first-run content setup.
 * No fictitious vehicles, prices or contact details are created.
 */
if (! defined('ABSPATH')) {
    exit;
}

$pages = array(
    array('slug' => 'trang-chu', 'title' => 'Trang chủ', 'content' => ''),
    array('slug' => 'xe', 'title' => 'Danh mục xe', 'content' => '[products limit="12" columns="3" paginate="true"]'),
    array('slug' => 'tin-tuc', 'title' => 'Tin tức', 'content' => ''),
    array('slug' => 'lien-he', 'title' => 'Liên hệ', 'content' => '<h2>Liên hệ tư vấn</h2>[dealer_contact_buttons][dealer_quote_form]'),
    array('slug' => 'chinh-sach-bao-mat', 'title' => 'Chính sách bảo mật', 'content' => '<p>Vui lòng cập nhật chính sách bảo mật và mục đích sử dụng thông tin khách hàng trước khi website hoạt động chính thức.</p>'),
);

$page_ids = array();
foreach ($pages as $page) {
    $existing = get_page_by_path($page['slug'], OBJECT, 'page');
    if ($existing instanceof WP_Post) {
        $page_ids[$page['slug']] = (int) $existing->ID;
        continue;
    }
    $page_id = wp_insert_post(array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_title' => $page['title'],
        'post_name' => $page['slug'],
        'post_content' => $page['content'],
    ), true);
    if (! is_wp_error($page_id)) {
        $page_ids[$page['slug']] = (int) $page_id;
    }
}

if (! empty($page_ids['trang-chu'])) {
    update_option('show_on_front', 'page');
    update_option('page_on_front', $page_ids['trang-chu']);
}
if (! empty($page_ids['xe'])) {
    update_option('woocommerce_shop_page_id', $page_ids['xe']);
}
if (! empty($page_ids['tin-tuc'])) {
    update_option('page_for_posts', $page_ids['tin-tuc']);
}

update_option('blogname', getenv('WP_SITE_TITLE') ?: get_option('blogname'));
update_option('blogdescription', 'Website giới thiệu sản phẩm và tư vấn mua xe');
update_option('WPLANG', 'vi');
update_option('timezone_string', 'Asia/Ho_Chi_Minh');

update_option('woocommerce_currency', 'VND');
update_option('woocommerce_price_num_decimals', 0);
update_option('woocommerce_currency_pos', 'right_space');
update_option('woocommerce_default_country', 'VN');
update_option('woocommerce_weight_unit', 'kg');
update_option('woocommerce_dimension_unit', 'cm');

$categories = array(
    'xe-du-lich' => 'Xe du lịch',
    'xe-tai' => 'Xe tải',
    'xe-dau-keo' => 'Xe đầu kéo',
    'xe-khach' => 'Xe khách',
    'xe-chuyen-dung' => 'Xe chuyên dụng',
);

foreach ($categories as $slug => $name) {
    if (! term_exists($slug, 'product_cat')) {
        wp_insert_term($name, 'product_cat', array('slug' => $slug));
    }
}

WP_CLI::success('Starter pages, Vietnamese settings and vehicle categories are ready. No demo vehicles or fake contact data were created.');
