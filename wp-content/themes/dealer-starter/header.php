<?php
if (! defined('ABSPATH')) exit;
$name = class_exists('Dealer_Settings') ? Dealer_Settings::get('dealer_name', get_bloginfo('name')) : get_bloginfo('name');
$phone = class_exists('Dealer_Settings') ? Dealer_Settings::get('hotline') : '';
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="dealer-topbar"><div class="dealer-container dealer-topbar-inner">
<span><?php echo esc_html($name); ?> · Tư vấn mua xe đa thương hiệu</span>
<?php if ($phone) : ?><a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>">Hotline: <?php echo esc_html($phone); ?></a><?php else : ?><span>Cập nhật hotline tại Dealer Website → Cấu hình đại lý</span><?php endif; ?>
</div></div>
<header class="dealer-header"><div class="dealer-container dealer-header-inner">
<a class="dealer-brand" href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php if (has_custom_logo()) { the_custom_logo(); } else { echo esc_html($name); } ?></a>
<nav class="dealer-nav" aria-label="Menu chính">
<?php wp_nav_menu(array('theme_location' => 'primary', 'container' => false, 'fallback_cb' => static function (): void {
    echo '<ul><li><a href="' . esc_url(home_url('/')) . '">Trang chủ</a></li>';
    echo '<li><a href="' . esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/xe/')) . '">Danh mục xe</a></li>';
    echo '<li><a href="' . esc_url(home_url('/tin-tuc/')) . '">Tin tức</a></li>';
    echo '<li><a href="' . esc_url(home_url('/lien-he/')) . '">Liên hệ</a></li></ul>';
})); ?>
</nav>
<div class="dealer-header-cta"><a class="dealer-button dealer-button-primary" href="<?php echo esc_url($phone ? 'tel:' . preg_replace('/[^0-9+]/', '', $phone) : home_url('/lien-he/')); ?>">Gọi tư vấn</a></div>
</div></header>
