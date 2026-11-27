<?php
/**
 * Plugin Name: Dealer Site Core
 * Description: Vehicle catalog fields, dealership settings and quote-lead inbox.
 * Version: 0.2.0
 * Requires at least: 6.5
 * Requires PHP: 8.1
 * Text Domain: dealer-site-core
 */
if (! defined('ABSPATH')) {
    exit;
}
define('DEALER_SITE_CORE_VERSION', '0.2.0');
define('DEALER_SITE_CORE_PATH', plugin_dir_path(__FILE__));
define('DEALER_SITE_CORE_URL', plugin_dir_url(__FILE__));

require_once DEALER_SITE_CORE_PATH . 'includes/class-dealer-settings.php';
require_once DEALER_SITE_CORE_PATH . 'includes/class-dealer-products.php';
require_once DEALER_SITE_CORE_PATH . 'includes/class-dealer-leads.php';

add_action('init', array('Dealer_Products', 'register_brand_taxonomy'));
add_action('init', array('Dealer_Leads', 'register_post_type'));
add_action('admin_init', array('Dealer_Settings', 'register_settings'));
add_action('admin_menu', array('Dealer_Settings', 'register_menu'));

if (class_exists('WooCommerce')) {
    add_action('add_meta_boxes_product', array('Dealer_Products', 'add_meta_box'));
    add_action('save_post_product', array('Dealer_Products', 'save_meta'), 10, 2);
    add_action('admin_enqueue_scripts', array('Dealer_Products', 'enqueue_admin_assets'));
    add_filter('woocommerce_get_price_html', array('Dealer_Products', 'filter_price_html'), 20, 2);
    add_filter('woocommerce_is_purchasable', '__return_false', 99);
    add_filter('woocommerce_variation_is_purchasable', '__return_false', 99);
    add_action('wp', array('Dealer_Products', 'disable_cart_actions'));
    add_action('template_redirect', array('Dealer_Products', 'redirect_cart_pages'));
    add_action('woocommerce_single_product_summary', array('Dealer_Products', 'render_specs'), 26);
    add_action('woocommerce_single_product_summary', array('Dealer_Products', 'render_quote_box'), 38);
} else {
    add_action('admin_notices', array('Dealer_Products', 'woocommerce_notice'));
}

add_action('rest_api_init', array('Dealer_Leads', 'register_routes'));
add_action('add_meta_boxes_dealer_lead', array('Dealer_Leads', 'add_meta_box'));
add_action('save_post_dealer_lead', array('Dealer_Leads', 'save_admin_meta'), 10, 2);
add_filter('manage_dealer_lead_posts_columns', array('Dealer_Leads', 'admin_columns'));
add_action('manage_dealer_lead_posts_custom_column', array('Dealer_Leads', 'render_admin_column'), 10, 2);
add_shortcode('dealer_quote_form', array('Dealer_Leads', 'render_form'));
add_shortcode('dealer_contact_buttons', array('Dealer_Settings', 'contact_buttons'));

register_activation_hook(__FILE__, static function (): void {
    Dealer_Leads::register_post_type();
    Dealer_Products::register_brand_taxonomy();
    $caps = array(
        'read_dealer_lead', 'edit_dealer_lead', 'edit_dealer_leads',
        'edit_others_dealer_leads', 'edit_published_dealer_leads',
        'read_private_dealer_leads', 'publish_dealer_leads',
        'delete_dealer_lead', 'delete_dealer_leads', 'delete_others_dealer_leads',
        'delete_private_dealer_leads', 'delete_published_dealer_leads',
    );
    $admin = get_role('administrator');
    if ($admin) {
        foreach ($caps as $cap) {
            $admin->add_cap($cap);
        }
    }
    $sales = get_role('dealer_sales');
    if (! $sales) {
        add_role('dealer_sales', 'Dealer Sales', array_merge(array('read' => true), $caps));
    } else {
        foreach ($caps as $cap) {
            $sales->add_cap($cap);
        }
    }
    flush_rewrite_rules();
});
register_deactivation_hook(__FILE__, static function (): void {
    flush_rewrite_rules();
});
