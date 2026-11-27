<?php
if (! defined('ABSPATH')) exit;
add_action('after_setup_theme', static function (): void {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array('height' => 80, 'width' => 240, 'flex-height' => true, 'flex-width' => true));
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script'));
    add_theme_support('woocommerce');
    register_nav_menus(array('primary' => __('Menu chính', 'dealer-starter'), 'footer' => __('Menu chân trang', 'dealer-starter')));
});
add_action('wp_enqueue_scripts', static function (): void {
    wp_enqueue_style('dealer-starter-style', get_stylesheet_uri(), array(), wp_get_theme()->get('Version'));
});
add_filter('body_class', static function (array $classes): array {
    $classes[] = 'dealer-starter-site';
    return $classes;
});
