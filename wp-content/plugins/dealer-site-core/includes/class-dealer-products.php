<?php
if (! defined('ABSPATH')) {
    exit;
}
final class Dealer_Products {
    public static function register_brand_taxonomy(): void {
        if (! post_type_exists('product') || taxonomy_exists('dealer_vehicle_brand')) {
            return;
        }
        register_taxonomy('dealer_vehicle_brand', array('product'), array(
            'labels' => array(
                'name' => __('Thương hiệu xe', 'dealer-site-core'),
                'singular_name' => __('Thương hiệu xe', 'dealer-site-core'),
                'search_items' => __('Tìm thương hiệu', 'dealer-site-core'),
                'all_items' => __('Tất cả thương hiệu', 'dealer-site-core'),
                'add_new_item' => __('Thêm thương hiệu mới', 'dealer-site-core'),
                'menu_name' => __('Thương hiệu', 'dealer-site-core'),
            ),
            'public' => true, 'hierarchical' => false, 'show_ui' => true,
            'show_admin_column' => true, 'show_in_rest' => true,
            'query_var' => true, 'rewrite' => array('slug' => 'hang-xe', 'with_front' => false),
        ));
    }

    public static function add_meta_box(): void {
        add_meta_box('dealer_vehicle_details', __('Thông tin xe', 'dealer-site-core'),
            array(__CLASS__, 'render_meta_box'), 'product', 'normal', 'high');
    }

    public static function render_meta_box(WP_Post $post): void {
        wp_nonce_field('dealer_save_vehicle_details', 'dealer_vehicle_nonce');
        $model = get_post_meta($post->ID, '_dealer_model', true);
        $condition = get_post_meta($post->ID, '_dealer_condition', true) ?: 'new';
        $warranty = get_post_meta($post->ID, '_dealer_warranty', true);
        $price_mode = get_post_meta($post->ID, '_dealer_price_mode', true) ?: 'contact';
        $specs = get_post_meta($post->ID, '_dealer_specs', true);
        $specs = is_array($specs) && $specs ? $specs : array(array('label' => '', 'value' => ''));
        ?>
        <div class="dealer-admin-fields">
            <p class="description"><?php esc_html_e('Điền thông tin phù hợp với dòng xe. Để trống thông số không áp dụng.', 'dealer-site-core'); ?></p>
            <div class="dealer-admin-grid">
                <p><label for="dealer_model"><strong>Model / phiên bản</strong></label>
                    <input class="widefat" type="text" id="dealer_model" name="dealer_model" value="<?php echo esc_attr($model); ?>" placeholder="Ví dụ: H7 4x2 400HP"></p>
                <p><label for="dealer_condition"><strong>Tình trạng xe</strong></label>
                    <select class="widefat" id="dealer_condition" name="dealer_condition">
                        <option value="new" <?php selected($condition, 'new'); ?>>Xe mới</option>
                        <option value="used" <?php selected($condition, 'used'); ?>>Xe đã qua sử dụng</option>
                        <option value="coming_soon" <?php selected($condition, 'coming_soon'); ?>>Sắp về</option>
                    </select></p>
                <p><label for="dealer_warranty"><strong>Bảo hành</strong></label>
                    <input class="widefat" type="text" id="dealer_warranty" name="dealer_warranty" value="<?php echo esc_attr($warranty); ?>" placeholder="Theo chính sách hãng"></p>
                <p><label for="dealer_price_mode"><strong>Cách hiển thị giá</strong></label>
                    <select class="widefat" id="dealer_price_mode" name="dealer_price_mode">
                        <option value="contact" <?php selected($price_mode, 'contact'); ?>>Liên hệ báo giá</option>
                        <option value="from" <?php selected($price_mode, 'from'); ?>>Giá từ</option>
                        <option value="exact" <?php selected($price_mode, 'exact'); ?>>Giá cụ thể</option>
                    </select></p>
            </div>
            <h3>Thông số kỹ thuật</h3>
            <p class="description">Ví dụ: động cơ, công suất, tải trọng, kích thước thùng, số chỗ, hộp số...</p>
            <table class="widefat dealer-specs-table">
                <thead><tr><th>Thông số</th><th>Giá trị</th><th>Thao tác</th></tr></thead>
                <tbody id="dealer-spec-rows">
                <?php foreach ($specs as $index => $spec) : ?>
                    <tr>
                        <td><input class="widefat" type="text" name="dealer_specs[<?php echo esc_attr((string) $index); ?>][label]" value="<?php echo esc_attr($spec['label'] ?? ''); ?>" placeholder="Ví dụ: Công suất"></td>
                        <td><input class="widefat" type="text" name="dealer_specs[<?php echo esc_attr((string) $index); ?>][value]" value="<?php echo esc_attr($spec['value'] ?? ''); ?>" placeholder="Ví dụ: 400 HP"></td>
                        <td><button type="button" class="button dealer-remove-spec">Xóa</button></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <p><button type="button" class="button button-secondary" id="dealer-add-spec">Thêm thông số</button></p>
        </div>
        <?php
    }

    public static function save_meta(int $post_id, WP_Post $post): void {
        if (! isset($_POST['dealer_vehicle_nonce']) ||
            ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['dealer_vehicle_nonce'])), 'dealer_save_vehicle_details')) {
            return;
        }
        if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || 'product' !== $post->post_type || ! current_user_can('edit_post', $post_id)) {
            return;
        }
        update_post_meta($post_id, '_dealer_model', isset($_POST['dealer_model']) ? sanitize_text_field(wp_unslash($_POST['dealer_model'])) : '');
        update_post_meta($post_id, '_dealer_warranty', isset($_POST['dealer_warranty']) ? sanitize_text_field(wp_unslash($_POST['dealer_warranty'])) : '');

        $condition = isset($_POST['dealer_condition']) ? sanitize_key(wp_unslash($_POST['dealer_condition'])) : 'new';
        if (! in_array($condition, array('new', 'used', 'coming_soon'), true)) $condition = 'new';
        update_post_meta($post_id, '_dealer_condition', $condition);

        $price_mode = isset($_POST['dealer_price_mode']) ? sanitize_key(wp_unslash($_POST['dealer_price_mode'])) : 'contact';
        if (! in_array($price_mode, array('contact', 'from', 'exact'), true)) $price_mode = 'contact';
        update_post_meta($post_id, '_dealer_price_mode', $price_mode);

        $raw_specs = isset($_POST['dealer_specs']) && is_array($_POST['dealer_specs']) ? wp_unslash($_POST['dealer_specs']) : array();
        $specs = array();
        foreach (array_slice($raw_specs, 0, 40) as $spec) {
            if (! is_array($spec)) continue;
            $label = isset($spec['label']) ? sanitize_text_field($spec['label']) : '';
            $value = isset($spec['value']) ? sanitize_text_field($spec['value']) : '';
            if ($label !== '' && $value !== '') $specs[] = array('label' => $label, 'value' => $value);
        }
        if ($specs) update_post_meta($post_id, '_dealer_specs', $specs);
        else delete_post_meta($post_id, '_dealer_specs');
    }

    public static function enqueue_admin_assets(string $hook): void {
        global $post_type;
        if (in_array($hook, array('post.php', 'post-new.php'), true) && $post_type === 'product') {
            wp_enqueue_script('dealer-site-core-admin', DEALER_SITE_CORE_URL . 'assets/js/admin-product.js', array(), DEALER_SITE_CORE_VERSION, true);
            wp_enqueue_style('dealer-site-core-admin', DEALER_SITE_CORE_URL . 'assets/css/frontend.css', array(), DEALER_SITE_CORE_VERSION);
        }
    }

    public static function filter_price_html(string $price, WC_Product $product): string {
        $mode = get_post_meta($product->get_id(), '_dealer_price_mode', true) ?: 'contact';
        if ($mode === 'contact' || $price === '') {
            return '<span class="dealer-contact-price">' . esc_html__('Liên hệ báo giá', 'dealer-site-core') . '</span>';
        }
        if ($mode === 'from') {
            return '<span class="dealer-contact-price">' . esc_html__('Từ', 'dealer-site-core') . ' ' . $price . '</span>';
        }
        return $price;
    }

    public static function render_specs(): void {
        global $product;
        if (! $product instanceof WC_Product) return;
        $specs = get_post_meta($product->get_id(), '_dealer_specs', true);
        $model = get_post_meta($product->get_id(), '_dealer_model', true);
        $condition = get_post_meta($product->get_id(), '_dealer_condition', true);
        $warranty = get_post_meta($product->get_id(), '_dealer_warranty', true);
        if (! is_array($specs)) $specs = array();
        if (! $specs && ! $model && ! $warranty) return;
        $conditions = array('new' => 'Xe mới', 'used' => 'Xe đã qua sử dụng', 'coming_soon' => 'Sắp về');
        ?>
        <section class="dealer-specifications" aria-labelledby="dealer-specifications-title">
            <h2 id="dealer-specifications-title">Thông tin xe</h2>
            <table><tbody>
            <?php if ($model) : ?><tr><th>Model / phiên bản</th><td><?php echo esc_html($model); ?></td></tr><?php endif; ?>
            <?php if ($condition) : ?><tr><th>Tình trạng</th><td><?php echo esc_html($conditions[$condition] ?? $condition); ?></td></tr><?php endif; ?>
            <?php if ($warranty) : ?><tr><th>Bảo hành</th><td><?php echo esc_html($warranty); ?></td></tr><?php endif; ?>
            <?php foreach ($specs as $spec) : ?><tr><th><?php echo esc_html($spec['label'] ?? ''); ?></th><td><?php echo esc_html($spec['value'] ?? ''); ?></td></tr><?php endforeach; ?>
            </tbody></table>
        </section>
        <?php
    }

    public static function render_quote_box(): void {
        echo '<section class="dealer-quote-box"><h2>Nhận tư vấn và báo giá</h2><p>Để lại thông tin, đại lý sẽ liên hệ tư vấn về mẫu xe này.</p>';
        echo do_shortcode('[dealer_quote_form]');
        echo '</section>';
    }

    public static function disable_cart_actions(): void {
        remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
    }

    public static function redirect_cart_pages(): void {
        if (function_exists('is_cart') && (is_cart() || is_checkout())) {
            $shop_url = wc_get_page_permalink('shop');
            wp_safe_redirect($shop_url ?: home_url('/'));
            exit;
        }
    }

    public static function woocommerce_notice(): void {
        if (current_user_can('activate_plugins')) {
            echo '<div class="notice notice-warning"><p>' . esc_html__('Dealer Site Core cần WooCommerce để quản lý danh mục xe.', 'dealer-site-core') . '</p></div>';
        }
    }
}
