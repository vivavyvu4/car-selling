<?php
if (! defined('ABSPATH')) {
    exit;
}

final class Dealer_Settings {
    private const OPTION = 'dealer_site_settings';

    public static function get(string $key, string $default = ''): string {
        $settings = get_option(self::OPTION, array());
        $value = is_array($settings) && isset($settings[$key]) ? $settings[$key] : $default;
        return is_string($value) ? $value : $default;
    }

    public static function register_menu(): void {
        add_menu_page(
            __('Dealer Website', 'dealer-site-core'),
            __('Dealer Website', 'dealer-site-core'),
            'edit_dealer_leads',
            'dealer-site-settings',
            array(__CLASS__, 'render_dashboard'),
            'dashicons-car',
            56
        );

        add_submenu_page(
            'dealer-site-settings',
            __('Cấu hình đại lý', 'dealer-site-core'),
            __('Cấu hình đại lý', 'dealer-site-core'),
            'manage_options',
            'dealer-site-config',
            array(__CLASS__, 'render_page')
        );
    }

    public static function register_settings(): void {
        register_setting('dealer_site_settings_group', self::OPTION, array('sanitize_callback' => array(__CLASS__, 'sanitize')));

        add_settings_section(
            'dealer_site_contact_section',
            __('Thông tin đại lý', 'dealer-site-core'),
            static function (): void {
                echo '<p>' . esc_html__('Nhập thông tin thật của đại lý. Các thông tin này được dùng trên nút liên hệ và biểu mẫu.', 'dealer-site-core') . '</p>';
            },
            'dealer-site-config'
        );

        $fields = array(
            'dealer_name' => __('Tên đại lý', 'dealer-site-core'),
            'hotline' => __('Hotline', 'dealer-site-core'),
            'zalo_url' => __('Liên kết Zalo', 'dealer-site-core'),
            'contact_email' => __('Email nhận yêu cầu', 'dealer-site-core'),
            'address' => __('Địa chỉ', 'dealer-site-core'),
        );

        foreach ($fields as $key => $label) {
            add_settings_field(
                'dealer_site_' . $key,
                $label,
                array(__CLASS__, 'render_field'),
                'dealer-site-config',
                'dealer_site_contact_section',
                array('key' => $key)
            );
        }
    }

    public static function sanitize($input): array {
        $input = is_array($input) ? $input : array();
        return array(
            'dealer_name' => isset($input['dealer_name']) ? sanitize_text_field($input['dealer_name']) : '',
            'hotline' => isset($input['hotline']) ? preg_replace('/[^0-9+\s().-]/', '', sanitize_text_field($input['hotline'])) : '',
            'zalo_url' => isset($input['zalo_url']) ? esc_url_raw($input['zalo_url']) : '',
            'contact_email' => isset($input['contact_email']) ? sanitize_email($input['contact_email']) : '',
            'address' => isset($input['address']) ? sanitize_textarea_field($input['address']) : '',
        );
    }

    public static function render_field(array $args): void {
        $key = isset($args['key']) ? sanitize_key($args['key']) : '';
        $settings = get_option(self::OPTION, array());
        $value = is_array($settings) && isset($settings[$key]) ? $settings[$key] : '';
        $name = self::OPTION . '[' . $key . ']';

        if ('address' === $key) {
            printf('<textarea class="large-text" rows="3" name="%1$s">%2$s</textarea>', esc_attr($name), esc_textarea($value));
            return;
        }

        $type = 'contact_email' === $key ? 'email' : ('zalo_url' === $key ? 'url' : 'text');
        printf('<input class="regular-text" type="%1$s" name="%2$s" value="%3$s">', esc_attr($type), esc_attr($name), esc_attr($value));
    }

    public static function render_dashboard(): void {
        if (! current_user_can('edit_dealer_leads')) {
            return;
        }
        $lead_url = admin_url('edit.php?post_type=dealer_lead');
        $product_url = admin_url('edit.php?post_type=product');
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Dealer Website', 'dealer-site-core'); ?></h1>
            <p><?php esc_html_e('Quản lý danh mục xe và các yêu cầu tư vấn từ khách hàng.', 'dealer-site-core'); ?></p>
            <div style="display:flex;flex-wrap:wrap;gap:12px;margin-top:20px">
                <a class="button button-primary" href="<?php echo esc_url($product_url); ?>"><?php esc_html_e('Quản lý sản phẩm', 'dealer-site-core'); ?></a>
                <a class="button button-primary" href="<?php echo esc_url($lead_url); ?>"><?php esc_html_e('Xem khách hàng tiềm năng', 'dealer-site-core'); ?></a>
                <?php if (current_user_can('manage_options')) : ?>
                    <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=dealer-site-config')); ?>"><?php esc_html_e('Cấu hình đại lý', 'dealer-site-core'); ?></a>
                <?php endif; ?>
            </div>
            <p style="margin-top:24px"><?php esc_html_e('Nhân viên sale có thể cập nhật sản phẩm và xử lý yêu cầu khách hàng. Chỉ quản trị viên được thay đổi cấu hình toàn website.', 'dealer-site-core'); ?></p>
        </div>
        <?php
    }

    public static function render_page(): void {
        if (! current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Cấu hình đại lý', 'dealer-site-core'); ?></h1>
            <p><?php esc_html_e('Cập nhật thông tin liên hệ chính thức trước khi website hoạt động.', 'dealer-site-core'); ?></p>
            <form method="post" action="options.php">
                <?php settings_fields('dealer_site_settings_group'); do_settings_sections('dealer-site-config'); submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public static function contact_buttons(): string {
        $phone = self::get('hotline');
        $zalo = self::get('zalo_url');
        $email = self::get('contact_email');
        $html = '<div class="dealer-contact-buttons">';
        if ($phone) {
            $tel = preg_replace('/[^0-9+]/', '', $phone);
            $html .= '<a class="dealer-button dealer-button-primary" href="tel:' . esc_attr($tel) . '">' . esc_html__('Gọi tư vấn', 'dealer-site-core') . ' · ' . esc_html($phone) . '</a>';
        }
        if ($zalo) {
            $html .= '<a class="dealer-button" href="' . esc_url($zalo) . '" target="_blank" rel="noopener noreferrer">' . esc_html__('Nhắn Zalo', 'dealer-site-core') . '</a>';
        }
        if ($email && is_email($email)) {
            $html .= '<a class="dealer-button" href="mailto:' . esc_attr($email) . '">' . esc_html__('Gửi email', 'dealer-site-core') . '</a>';
        }
        return $html . '</div>';
    }
}
