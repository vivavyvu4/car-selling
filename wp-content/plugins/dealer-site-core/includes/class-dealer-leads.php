<?php
if (! defined('ABSPATH')) {
    exit;
}
final class Dealer_Leads {
    public static function register_post_type(): void {
        register_post_type('dealer_lead', array(
            'labels' => array(
                'name' => __('Khách hàng tiềm năng', 'dealer-site-core'),
                'singular_name' => __('Khách hàng tiềm năng', 'dealer-site-core'),
                'menu_name' => __('Khách hàng tiềm năng', 'dealer-site-core'),
                'edit_item' => __('Chi tiết khách hàng', 'dealer-site-core'),
            ),
            'public' => false, 'show_ui' => true, 'show_in_menu' => 'dealer-site-settings',
            'show_in_rest' => false, 'supports' => array('title'),
            'capability_type' => array('dealer_lead', 'dealer_leads'),
            'map_meta_cap' => true, 'has_archive' => false, 'rewrite' => false,
        ));
    }

    public static function register_routes(): void {
        register_rest_route('dealer/v1', '/leads', array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array(__CLASS__, 'create_lead'),
            'permission_callback' => '__return_true',
        ));
    }

    public static function render_form($atts = array()): string {
        $atts = shortcode_atts(array('product_id' => 0), $atts, 'dealer_quote_form');
        $product_id = absint($atts['product_id']);
        if (! $product_id && function_exists('is_product') && is_product()) $product_id = get_the_ID();
        if ($product_id && get_post_status($product_id) !== 'publish') $product_id = 0;

        wp_enqueue_script('dealer-site-core-public', DEALER_SITE_CORE_URL . 'assets/js/frontend.js', array(), DEALER_SITE_CORE_VERSION, true);
        wp_enqueue_style('dealer-site-core-public', DEALER_SITE_CORE_URL . 'assets/css/frontend.css', array(), DEALER_SITE_CORE_VERSION);
        wp_localize_script('dealer-site-core-public', 'DealerSiteCore', array(
            'endpoint' => esc_url_raw(rest_url('dealer/v1/leads')),
            'successMessage' => __('Cảm ơn bạn! Đại lý đã nhận được yêu cầu.', 'dealer-site-core'),
            'errorMessage' => __('Chưa thể gửi yêu cầu. Vui lòng thử lại hoặc gọi hotline.', 'dealer-site-core'),
        ));
        ob_start();
        ?>
        <form class="dealer-quote-form" data-dealer-quote-form>
            <div class="dealer-form-grid">
                <p><label>Họ tên *</label><input type="text" name="name" autocomplete="name" required maxlength="120"></p>
                <p><label>Số điện thoại *</label><input type="tel" name="phone" autocomplete="tel" required minlength="8" maxlength="20"></p>
                <p class="dealer-form-full"><label>Email (không bắt buộc)</label><input type="email" name="email" autocomplete="email" maxlength="190"></p>
                <p class="dealer-form-full"><label>Nội dung cần tư vấn</label><textarea name="message" rows="3" maxlength="2000" placeholder="Nhu cầu sử dụng xe, ngân sách hoặc phương án thanh toán..."></textarea></p>
                <p class="dealer-form-full dealer-consent-row"><label><input type="checkbox" name="consent" value="1" required> Tôi đồng ý để đại lý sử dụng thông tin này nhằm liên hệ tư vấn theo yêu cầu.</label></p>
            </div>
            <input type="hidden" name="product_id" value="<?php echo esc_attr((string) $product_id); ?>">
            <input type="hidden" name="source_url" value="<?php echo esc_url(get_permalink()); ?>">
            <input type="hidden" name="landing_url" value=""><input type="hidden" name="referrer" value="">
            <?php foreach (array('utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content') as $utm) : ?>
                <input type="hidden" name="<?php echo esc_attr($utm); ?>" value="">
            <?php endforeach; ?>
            <div class="dealer-form-honeypot" aria-hidden="true"><label>Để trống<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
            <button type="submit" class="dealer-button dealer-button-primary">Gửi yêu cầu báo giá</button>
            <p class="dealer-form-message" data-dealer-form-message role="status" aria-live="polite"></p>
        </form>
        <?php
        return (string) ob_get_clean();
    }

    public static function create_lead(WP_REST_Request $request): WP_REST_Response {
        $params = $request->get_json_params();
        $params = is_array($params) ? $params : array();
        if (! empty($params['website'])) return new WP_REST_Response(array('success' => true), 201);

        $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : 'unknown';
        $rate_key = 'dealer_lead_rate_' . md5(wp_hash($ip));
        $attempts = (int) get_transient($rate_key);
        if ($attempts >= 5) return new WP_REST_Response(array('message' => __('Bạn đã gửi quá nhiều yêu cầu. Vui lòng thử lại sau.', 'dealer-site-core')), 429);

        $name = isset($params['name']) ? sanitize_text_field($params['name']) : '';
        $phone = isset($params['phone']) ? sanitize_text_field($params['phone']) : '';
        $email = isset($params['email']) ? sanitize_email($params['email']) : '';
        $message = isset($params['message']) ? sanitize_textarea_field($params['message']) : '';
        if ($name === '' || ! preg_match('/^[0-9+().\s-]{8,20}$/', $phone) || empty($params['consent'])) {
            return new WP_REST_Response(array('message' => __('Vui lòng nhập họ tên, số điện thoại hợp lệ và xác nhận đồng ý liên hệ.', 'dealer-site-core')), 400);
        }
        if ($email !== '' && ! is_email($email)) return new WP_REST_Response(array('message' => __('Vui lòng nhập email hợp lệ.', 'dealer-site-core')), 400);
        set_transient($rate_key, $attempts + 1, HOUR_IN_SECONDS);

        $product_id = isset($params['product_id']) ? absint($params['product_id']) : 0;
        if ($product_id && (get_post_type($product_id) !== 'product' || get_post_status($product_id) !== 'publish')) $product_id = 0;
        $product_title = $product_id ? get_the_title($product_id) : '';
        $lead_id = wp_insert_post(array(
            'post_type' => 'dealer_lead', 'post_status' => 'private',
            'post_title' => sprintf('%s - %s', $name, $phone),
        ), true);
        if (is_wp_error($lead_id)) return new WP_REST_Response(array('message' => __('Chưa thể lưu yêu cầu. Vui lòng thử lại sau.', 'dealer-site-core')), 500);

        $meta = array(
            '_dealer_lead_name' => $name, '_dealer_lead_phone' => $phone,
            '_dealer_lead_email' => $email, '_dealer_lead_message' => $message,
            '_dealer_lead_consent_at' => current_time('mysql'),
            '_dealer_lead_product_id' => $product_id, '_dealer_lead_product_title' => $product_title,
            '_dealer_lead_source_url' => isset($params['source_url']) ? esc_url_raw($params['source_url']) : '',
            '_dealer_lead_landing_url' => isset($params['landing_url']) ? esc_url_raw($params['landing_url']) : '',
            '_dealer_lead_referrer' => isset($params['referrer']) ? esc_url_raw($params['referrer']) : '',
            '_dealer_lead_status' => 'new',
        );
        foreach (array('utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content') as $utm) {
            $meta['_dealer_lead_' . $utm] = isset($params[$utm]) ? sanitize_text_field($params[$utm]) : '';
        }
        foreach ($meta as $key => $value) update_post_meta($lead_id, $key, $value);
        self::send_notification((int) $lead_id, $meta);
        return new WP_REST_Response(array('success' => true, 'message' => __('Đại lý đã nhận được yêu cầu.', 'dealer-site-core')), 201);
    }

    private static function send_notification(int $lead_id, array $meta): void {
        $to = Dealer_Settings::get('contact_email', get_option('admin_email'));
        if (! is_email($to)) $to = get_option('admin_email');
        $subject = sprintf('[%s] Có yêu cầu tư vấn mới', get_bloginfo('name'));
        $body = "Website vừa nhận được yêu cầu tư vấn mới.\n\n";
        $body .= 'Họ tên: ' . ($meta['_dealer_lead_name'] ?? '') . "\n";
        $body .= 'Số điện thoại: ' . ($meta['_dealer_lead_phone'] ?? '') . "\n";
        $body .= 'Email: ' . ($meta['_dealer_lead_email'] ?? '') . "\n";
        $body .= 'Xe quan tâm: ' . ($meta['_dealer_lead_product_title'] ?? '') . "\n";
        $body .= 'Nội dung: ' . ($meta['_dealer_lead_message'] ?? '') . "\n";
        $body .= 'Trang gửi form: ' . ($meta['_dealer_lead_source_url'] ?? '') . "\n";
        $body .= 'Xem lead: ' . admin_url('post.php?post=' . $lead_id . '&action=edit') . "\n";
        wp_mail($to, $subject, $body);
    }

    public static function add_meta_box(): void {
        add_meta_box('dealer_lead_details', __('Thông tin khách hàng', 'dealer-site-core'), array(__CLASS__, 'render_meta_box'), 'dealer_lead', 'normal', 'high');
    }

    public static function render_meta_box(WP_Post $post): void {
        wp_nonce_field('dealer_save_lead_details', 'dealer_lead_nonce');
        $fields = array(
            'name' => 'Họ tên', 'phone' => 'Số điện thoại', 'email' => 'Email',
            'product_title' => 'Xe quan tâm', 'message' => 'Nội dung',
            'landing_url' => 'Trang truy cập đầu tiên', 'referrer' => 'Nguồn giới thiệu',
            'utm_source' => 'UTM Source', 'utm_medium' => 'UTM Medium',
            'utm_campaign' => 'UTM Campaign', 'utm_term' => 'UTM Term',
            'utm_content' => 'UTM Content', 'consent_at' => 'Thời điểm đồng ý liên hệ',
        );
        $status = get_post_meta($post->ID, '_dealer_lead_status', true) ?: 'new';
        ?>
        <table class="form-table"><tbody>
        <?php foreach ($fields as $key => $label) :
            $value = get_post_meta($post->ID, '_dealer_lead_' . $key, true);
            if ($value === '') continue;
        ?>
            <tr><th><?php echo esc_html($label); ?></th><td><?php echo esc_html($value); ?></td></tr>
        <?php endforeach; ?>
        <tr><th><label for="dealer_lead_status">Trạng thái tư vấn</label></th><td><select id="dealer_lead_status" name="dealer_lead_status">
            <?php foreach (self::statuses() as $key => $label) : ?><option value="<?php echo esc_attr($key); ?>" <?php selected($status, $key); ?>><?php echo esc_html($label); ?></option><?php endforeach; ?>
        </select></td></tr>
        <tr><th><label for="dealer_lead_notes">Ghi chú nội bộ</label></th><td><textarea class="large-text" rows="5" id="dealer_lead_notes" name="dealer_lead_notes"><?php echo esc_textarea(get_post_meta($post->ID, '_dealer_lead_notes', true)); ?></textarea></td></tr>
        </tbody></table>
        <?php
    }

    public static function save_admin_meta(int $post_id, WP_Post $post): void {
        if (! isset($_POST['dealer_lead_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['dealer_lead_nonce'])), 'dealer_save_lead_details')) return;
        if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || $post->post_type !== 'dealer_lead' || ! current_user_can('edit_post', $post_id)) return;
        $status = isset($_POST['dealer_lead_status']) ? sanitize_key(wp_unslash($_POST['dealer_lead_status'])) : 'new';
        if (array_key_exists($status, self::statuses())) update_post_meta($post_id, '_dealer_lead_status', $status);
        $notes = isset($_POST['dealer_lead_notes']) ? sanitize_textarea_field(wp_unslash($_POST['dealer_lead_notes'])) : '';
        update_post_meta($post_id, '_dealer_lead_notes', $notes);
    }

    public static function statuses(): array {
        return array(
            'new' => __('Mới', 'dealer-site-core'),
            'contacted' => __('Đã liên hệ', 'dealer-site-core'),
            'qualified' => __('Đang tư vấn', 'dealer-site-core'),
            'quoted' => __('Đã gửi báo giá', 'dealer-site-core'),
            'negotiating' => __('Đàm phán', 'dealer-site-core'),
            'won' => __('Thành công', 'dealer-site-core'),
            'lost' => __('Thất bại', 'dealer-site-core'),
        );
    }

    public static function admin_columns(array $columns): array {
        return array(
            'cb' => $columns['cb'] ?? '',
            'title' => __('Khách hàng', 'dealer-site-core'),
            'dealer_phone' => __('Số điện thoại', 'dealer-site-core'),
            'dealer_vehicle' => __('Xe quan tâm', 'dealer-site-core'),
            'dealer_status' => __('Trạng thái', 'dealer-site-core'),
            'date' => __('Ngày gửi', 'dealer-site-core'),
        );
    }

    public static function render_admin_column(string $column, int $post_id): void {
        if ($column === 'dealer_phone') {
            $phone = get_post_meta($post_id, '_dealer_lead_phone', true);
            echo $phone ? '<a href="tel:' . esc_attr(preg_replace('/[^0-9+]/', '', $phone)) . '">' . esc_html($phone) . '</a>' : '—';
        } elseif ($column === 'dealer_vehicle') {
            echo esc_html(get_post_meta($post_id, '_dealer_lead_product_title', true) ?: '—');
        } elseif ($column === 'dealer_status') {
            $status = get_post_meta($post_id, '_dealer_lead_status', true) ?: 'new';
            $labels = self::statuses();
            echo esc_html($labels[$status] ?? $status);
        }
    }
}
