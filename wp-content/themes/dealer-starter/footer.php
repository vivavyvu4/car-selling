<?php
if (! defined('ABSPATH')) exit;
$name = class_exists('Dealer_Settings') ? Dealer_Settings::get('dealer_name', get_bloginfo('name')) : get_bloginfo('name');
$address = class_exists('Dealer_Settings') ? Dealer_Settings::get('address') : '';
?>
<footer class="dealer-footer"><div class="dealer-container"><div class="dealer-footer-grid">
<div><h2><?php echo esc_html($name); ?></h2><p>Thông tin sản phẩm và tư vấn lựa chọn xe theo nhu cầu.</p><?php echo do_shortcode('[dealer_contact_buttons]'); ?></div>
<div><h2>Thông tin đại lý</h2><?php if ($address) : ?><p><?php echo nl2br(esc_html($address)); ?></p><?php else : ?><p>Cập nhật địa chỉ tại Dealer Website → Cấu hình đại lý.</p><?php endif; ?><p><a href="<?php echo esc_url(home_url('/chinh-sach-bao-mat/')); ?>">Chính sách bảo mật</a></p></div>
</div><div class="dealer-footer-bottom">© <?php echo esc_html(wp_date('Y')); ?> <?php echo esc_html($name); ?>. Xác nhận thông tin sản phẩm với đại lý trước khi giao dịch.</div></div></footer>
<?php wp_footer(); ?>
</body></html>
