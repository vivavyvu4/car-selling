<?php
/**
 * Idempotent starter pages and labelled demo data.
 * Run: wp eval-file scripts/seed-site.php
 */
if (!defined('ABSPATH')) exit;
if (!class_exists('WooCommerce') || !class_exists('WC_Product_Simple')) WP_CLI::error('Activate WooCommerce before running this seed.');
Dealer_Products::register_brand_taxonomy();

function dealer_demo_image_id(string $file): int {
    $found=get_posts(array('post_type'=>'attachment','post_status'=>'inherit','posts_per_page'=>1,'fields'=>'ids','meta_key'=>'_dealer_demo_asset','meta_value'=>$file));
    if($found)return (int)$found[0];
    $source=get_theme_file_path('assets/demo-vehicles/'.$file);
    if(!file_exists($source)){WP_CLI::warning('Missing theme image: '.$file);return 0;}
    $up=wp_upload_dir();if(!empty($up['error'])){WP_CLI::warning($up['error']);return 0;}
    $rel='dealer-demo-images';$dir=trailingslashit($up['basedir']).$rel;
    if(!wp_mkdir_p($dir))return 0;
    $target=trailingslashit($dir).sanitize_file_name($file);
    if(!file_exists($target)&&!copy($source,$target))return 0;
    $id=wp_insert_attachment(array('post_mime_type'=>'image/svg+xml','post_title'=>sanitize_text_field($file),'post_status'=>'inherit','post_excerpt'=>'Ảnh vector minh họa DEMO, không phải ảnh xe đang bán.'),$target,0,true);
    if(is_wp_error($id)){WP_CLI::warning($id->get_error_message());return 0;}
    update_attached_file($id,$target);update_post_meta($id,'_dealer_demo_asset',$file);
    update_post_meta($id,'_wp_attachment_image_alt','Hình minh họa xe DEMO');
    update_post_meta($id,'_wp_attachment_metadata',array('width'=>800,'height'=>520,'file'=>$rel.'/'.sanitize_file_name($file),'sizes'=>array(),'image_meta'=>array()));
    return (int)$id;
}
function dealer_demo_page(array $p,array $legacy=array()): int {
    $old=get_page_by_path($p['slug'],OBJECT,'page');$new=!($old instanceof WP_Post);
    if(!$new&&!get_post_meta($old->ID,'_dealer_demo_page_seeded',true)&&trim((string)$old->post_content)!==''&&!in_array((string)$old->post_content,$legacy,true))return (int)$old->ID;
    $data=array('post_type'=>'page','post_status'=>'publish','post_title'=>$p['title'],'post_name'=>$p['slug'],'post_content'=>$p['content']);
    if($new)$id=wp_insert_post($data,true);else{$data['ID']=$old->ID;$id=wp_update_post($data,true);}
    if(is_wp_error($id)){WP_CLI::warning($id->get_error_message());return 0;}
    update_post_meta($id,'_dealer_demo_page_seeded',1);update_post_meta($id,'_dealer_demo_page_hash',hash('sha256',$p['content']));
    return (int)$id;
}
$images=array();
foreach(array('vehicle-suv.svg','vehicle-truck.svg','vehicle-tractor.svg','vehicle-bus.svg','vehicle-work.svg') as $f)$images[$f]=dealer_demo_image_id($f);

$cat_defs=array(
 'xe-du-lich'=>array('Xe du lịch','SUV, MPV và xe gia đình. Nội dung DEMO để thử giao diện.','vehicle-suv.svg'),
 'xe-tai'=>array('Xe tải','Xe tải thùng và xe tải ben. Thông số DEMO, cần xác minh trước khi sử dụng.','vehicle-truck.svg'),
 'xe-dau-keo'=>array('Xe đầu kéo','Đầu kéo và vận tải đường dài. Cấu hình chỉ mang tính minh họa.','vehicle-tractor.svg'),
 'xe-khach'=>array('Xe khách','Minibus và xe khách nhiều chỗ. Số chỗ là ví dụ DEMO.','vehicle-bus.svg'),
 'xe-chuyen-dung'=>array('Xe chuyên dụng','Xe ben và phương tiện chuyên dụng. Cấu hình là dữ liệu mẫu.','vehicle-work.svg')
);
$cat_ids=array();
foreach($cat_defs as $slug=>$c){
 $t=term_exists($slug,'product_cat');if(!$t)$t=wp_insert_term($c[0],'product_cat',array('slug'=>$slug));
 if(is_wp_error($t)){WP_CLI::warning($t->get_error_message());continue;}
 $id=is_array($t)?(int)$t['term_id']:(int)$t;$cat_ids[$slug]=$id;
 if(!get_term_meta($id,'_dealer_demo_seeded',true)||trim((string)get_term_field('description',$id,'product_cat'))===''){wp_update_term($id,'product_cat',array('description'=>$c[1]));update_term_meta($id,'_dealer_demo_seeded',1);}
 $img=(int)($images[$c[2]]??0);$current=(int)get_term_meta($id,'thumbnail_id',true);
 if($img&&(!$current||get_post_meta($current,'_dealer_demo_asset',true))){update_term_meta($id,'thumbnail_id',$img);update_term_meta($id,'_dealer_demo_thumbnail',$img);}
}
$brands=array();
foreach(array('Demo Mobility','Demo Commercial','Demo Transit','Demo Utility') as $name){
 $slug=sanitize_title($name);$t=term_exists($slug,'dealer_vehicle_brand');if(!$t)$t=wp_insert_term($name,'dealer_vehicle_brand',array('slug'=>$slug));
 if(!is_wp_error($t)){$id=is_array($t)?(int)$t['term_id']:(int)$t;$brands[$name]=$id;update_term_meta($id,'_dealer_demo_seeded',1);}
}
$pages=array(
 array('trang-chu','Trang chủ',''),
 array('xe','Danh mục xe','<p>Danh mục xe minh họa để xem trước cách hiển thị sản phẩm. Toàn bộ xe, ảnh và thông số trong môi trường này là DEMO.</p>'),
 array('tin-tuc','Tin tức',''),
 array('gioi-thieu','Giới thiệu','<div class="dealer-demo-notice"><strong>NỘI DUNG DEMO:</strong> Thay tên thương mại, lịch sử, năng lực và hình ảnh bằng thông tin đã được đại lý xác nhận.</div><h1 class="dealer-page-title">Đồng hành cùng bạn trong hành trình chọn xe</h1><p>Tra cứu thông tin xe rõ ràng, bắt đầu từ nhu cầu sử dụng và kết nối với đội ngũ tư vấn.</p><div class="dealer-info-grid"><article><h2>Tư vấn theo nhu cầu</h2><p>Làm rõ mục đích sử dụng, tải trọng, số hành khách và ngân sách.</p></article><article><h2>Thông tin dễ tra cứu</h2><p>Danh mục, ảnh, thông số và tình trạng xe được trình bày thống nhất.</p></article><article><h2>Kết nối rõ ràng</h2><p>Gửi yêu cầu để trao đổi cấu hình, hồ sơ và báo giá.</p></article></div><h2>Quy trình tư vấn tham khảo</h2><ol class="dealer-process-list"><li><strong>Tiếp nhận nhu cầu</strong><span>Ghi nhận dòng xe và thông tin liên hệ.</span></li><li><strong>Đề xuất lựa chọn</strong><span>Đối chiếu cấu hình phù hợp.</span></li><li><strong>Xác nhận thông tin</strong><span>Kiểm tra tình trạng, thông số và giá.</span></li><li><strong>Trao đổi tiếp theo</strong><span>Thống nhất lịch xem xe hoặc tư vấn.</span></li></ol>'),
 array('lien-he','Liên hệ','<div class="dealer-demo-notice"><strong>TRANG LIÊN HỆ DEMO:</strong> Chưa có hotline, Zalo, email hoặc địa chỉ thật. Cập nhật tại <strong>Dealer Website → Cấu hình đại lý</strong> trước khi public.</div><h1 class="dealer-page-title">Bạn đang tìm mẫu xe phù hợp?</h1><p>Để lại thông tin và nhu cầu. Biểu mẫu sẽ tạo yêu cầu tư vấn trong khu vực quản trị.</p><div class="dealer-contact-panel"><h2>Kênh liên hệ</h2><p>Hotline, Zalo, email và địa chỉ sẽ hiển thị sau khi cấu hình.</p>[dealer_contact_buttons]</div><h2>Gửi yêu cầu tư vấn</h2>[dealer_quote_form]'),
 array('chinh-sach-bao-mat','Chính sách bảo mật','<div class="dealer-demo-notice"><strong>MẪU THAM KHẢO — CHƯA PHẢI CHÍNH SÁCH PHÁP LÝ HOÀN CHỈNH.</strong> Cần rà soát theo pháp nhân, hoạt động xử lý dữ liệu và quy định áp dụng trước khi mở form công khai.</div><h1 class="dealer-page-title">Chính sách bảo mật</h1><p><strong>Cập nhật lần cuối:</strong> [Điền ngày cập nhật].</p><h2>1. Thông tin có thể được thu thập</h2><p>Biểu mẫu có thể ghi nhận họ tên, số điện thoại, email, nội dung liên hệ, mẫu xe quan tâm và nguồn truy cập.</p><h2>2. Mục đích sử dụng</h2><p>Phản hồi yêu cầu, tư vấn sản phẩm, quản lý lịch sử trao đổi theo phạm vi đã thông báo.</p><h2>3. Lưu trữ và bảo vệ</h2><p>Chủ website cần xác định thời hạn lưu trữ, người được quyền truy cập, biện pháp bảo vệ và sao lưu.</p><h2>4. Chia sẻ thông tin</h2><p>Không chia sẻ dữ liệu ngoài phạm vi đã thông báo, trừ khi có căn cứ phù hợp.</p><h2>5. Quyền của khách hàng</h2><p>Bổ sung cách khách hàng yêu cầu truy cập, chỉnh sửa hoặc đề nghị xử lý thông tin cá nhân.</p><h2>6. Đơn vị phụ trách</h2><p>Tên pháp lý: [Cập nhật]. Email: [Cập nhật]. Địa chỉ: [Cập nhật].</p>')
);
$legacy=array('trang-chu'=>array(''),'xe'=>array('[products limit="12" columns="3" paginate="true"]'),'tin-tuc'=>array(''),'lien-he'=>array('<h2>Liên hệ tư vấn</h2>[dealer_contact_buttons][dealer_quote_form]'),'chinh-sach-bao-mat'=>array('<p>Vui lòng cập nhật chính sách bảo mật và mục đích sử dụng thông tin khách hàng trước khi website hoạt động chính thức.</p>'));
$page_ids=array();foreach($pages as $p)$page_ids[$p[0]]=dealer_demo_page(array('slug'=>$p[0],'title'=>$p[1],'content'=>$p[2]),$legacy[$p[0]]??array(''));
if(!empty($page_ids['trang-chu'])){update_option('show_on_front','page');update_option('page_on_front',$page_ids['trang-chu']);}
if(!empty($page_ids['xe']))update_option('woocommerce_shop_page_id',$page_ids['xe']);
if(!empty($page_ids['tin-tuc']))update_option('page_for_posts',$page_ids['tin-tuc']);
if(!empty($page_ids['chinh-sach-bao-mat']))update_option('wp_page_for_privacy_policy',$page_ids['chinh-sach-bao-mat']);
update_option('dealer_demo_content_enabled',1);update_option('WPLANG','vi');update_option('timezone_string','Asia/Ho_Chi_Minh');
update_option('woocommerce_currency','VND');update_option('woocommerce_price_num_decimals',0);update_option('woocommerce_currency_pos','right_space');update_option('woocommerce_default_country','VN');update_option('woocommerce_weight_unit','kg');update_option('woocommerce_dimension_unit','cm');

$rows=array(
 array('DEMO-CAR-001','D-SUV 01','DEMO SUV 7 chỗ — D-SUV 01','xe-du-lich','Demo Mobility','vehicle-suv.svg','new','Minh họa: 36 tháng',array('Số chỗ'=>'7 chỗ (minh họa)','Nhiên liệu'=>'Xăng (minh họa)','Hộp số'=>'Tự động (minh họa)')),
 array('DEMO-CAR-002','D-MPV 02','DEMO MPV 7 chỗ — D-MPV 02','xe-du-lich','Demo Mobility','vehicle-suv.svg','used','Theo tình trạng — ví dụ',array('Số chỗ'=>'7 chỗ (minh họa)','Khoang hành lý'=>'Linh hoạt (minh họa)')),
 array('DEMO-TRK-001','D-TK 250','DEMO Xe tải thùng — D-TK 250','xe-tai','Demo Commercial','vehicle-truck.svg','new','Minh họa: 24 tháng',array('Tải trọng'=>'2.5 tấn (minh họa)','Dẫn động'=>'4x2 (minh họa)','Loại thùng'=>'Thùng kín (minh họa)')),
 array('DEMO-TRK-002','D-BEN 350','DEMO Xe tải ben — D-BEN 350','xe-tai','Demo Commercial','vehicle-work.svg','new','Minh họa: 24 tháng',array('Tải trọng'=>'3.5 tấn (minh họa)','Loại thùng'=>'Thùng ben (minh họa)')),
 array('DEMO-TRC-001','D-TR 420','DEMO Đầu kéo 4x2 — D-TR 420','xe-dau-keo','Demo Commercial','vehicle-tractor.svg','new','Theo cấu hình — ví dụ',array('Công suất'=>'420 HP (minh họa)','Cấu hình cầu'=>'4x2 (minh họa)')),
 array('DEMO-TRC-002','D-TR 460','DEMO Đầu kéo 6x4 — D-TR 460','xe-dau-keo','Demo Commercial','vehicle-tractor.svg','coming_soon','Cập nhật sau — demo',array('Công suất'=>'460 HP (minh họa)','Cấu hình cầu'=>'6x4 (minh họa)')),
 array('DEMO-BUS-001','D-BUS 16','DEMO Minibus 16 chỗ — D-BUS 16','xe-khach','Demo Transit','vehicle-bus.svg','new','Minh họa: 24 tháng',array('Số chỗ'=>'16 chỗ (minh họa)','Nhiên liệu'=>'Diesel (minh họa)')),
 array('DEMO-BUS-002','D-BUS 29','DEMO Xe khách 29 chỗ — D-BUS 29','xe-khach','Demo Transit','vehicle-bus.svg','used','Theo tình trạng — ví dụ',array('Số chỗ'=>'29 chỗ (minh họa)','Tình trạng'=>'Đã qua sử dụng (minh họa)')),
 array('DEMO-SPC-001','D-SPEC 01','DEMO Xe ben chuyên dụng — D-SPEC 01','xe-chuyen-dung','Demo Utility','vehicle-work.svg','new','Minh họa: 18 tháng',array('Ứng dụng'=>'Công trình (minh họa)','Cấu hình'=>'Ben tự đổ (minh họa)')),
 array('DEMO-SPC-002','D-REF 01','DEMO Xe tải bảo ôn — D-REF 01','xe-chuyen-dung','Demo Utility','vehicle-work.svg','new','Theo thiết bị — ví dụ',array('Thùng xe'=>'Thùng bảo ôn (minh họa)','Thiết bị lạnh'=>'Có (minh họa)'))
);
$made=0;
foreach($rows as $i=>$r){
 if(wc_get_product_id_by_sku($r[0]))continue;$cat=$cat_ids[$r[3]]??0;if(!$cat)continue;
 $specs=array();foreach($r[8] as $k=>$v)$specs[]=array('label'=>$k,'value'=>$v);
 $p=new WC_Product_Simple();$p->set_name($r[2]);$p->set_slug(sanitize_title($r[2]));$p->set_status('publish');$p->set_catalog_visibility('visible');
 $p->set_description('<p><strong>SẢN PHẨM DEMO — KHÔNG PHẢI XE ĐANG CHÀO BÁN.</strong></p><p>Model '.$r[1].' và thông số, bảo hành, hình ảnh là dữ liệu hư cấu để xem trước giao diện. Không dùng làm báo giá hoặc căn cứ kỹ thuật.</p>');
 $p->set_short_description('<p><strong>DỮ LIỆU DEMO.</strong> Mẫu xe minh họa cho trang danh sách và chi tiết.</p>');$p->set_sku($r[0]);$p->set_regular_price('0');$p->set_price('0');$p->set_manage_stock(false);$p->set_stock_status('instock');$p->set_reviews_allowed(false);$p->set_category_ids(array($cat));$p->set_image_id((int)($images[$r[5]]??0));$p->set_featured(in_array($i,array(0,2,4,6),true));
 try{$id=$p->save();}catch(Throwable $e){WP_CLI::warning($r[0].': '.$e->getMessage());continue;}if(!$id)continue;
 update_post_meta($id,'_dealer_model',$r[1]);update_post_meta($id,'_dealer_condition',$r[6]);update_post_meta($id,'_dealer_warranty',$r[7]);update_post_meta($id,'_dealer_price_mode','contact');update_post_meta($id,'_dealer_specs',$specs);update_post_meta($id,'_dealer_demo_record',1);
 update_post_meta($id,'_dealer_demo_price_note','Không hiển thị giá bán; cần nhập báo giá đã xác minh khi thay dữ liệu thật.');
 if(!empty($brands[$r[4]]))wp_set_object_terms($id,array((int)$brands[$r[4]]),'dealer_vehicle_brand',false);$made++;
}
$news=array('cam-nang'=>'Cẩm nang chọn xe','kinh-nghiem'=>'Kinh nghiệm sử dụng','tin-hoat-dong'=>'Tin hoạt động');$news_ids=array();
foreach($news as $slug=>$name){$t=term_exists($slug,'category');if(!$t)$t=wp_insert_term($name,'category',array('slug'=>$slug));if(!is_wp_error($t))$news_ids[$slug]=is_array($t)?(int)$t['term_id']:(int)$t;}
$articles=array(
 array('demo-xac-dinh-nhu-cau-truoc-khi-chon-xe','DEMO: 5 câu hỏi nên làm rõ trước khi chọn xe','cam-nang','vehicle-suv.svg','Bài viết mẫu về cách bắt đầu từ nhu cầu sử dụng.','<p>Chọn xe dễ hơn khi người mua xác định nhu cầu.</p><h2>Xe phục vụ công việc gì?</h2><p>Gia đình, chở hàng, vận tải hay công việc chuyên dụng?</p><h2>Tần suất và cung đường?</h2><p>Trao đổi về địa hình, quãng đường và nơi đỗ.</p><h2>Cần bao nhiêu chỗ hoặc tải trọng?</h2><p>Đối chiếu cấu hình thực tế với hồ sơ xe.</p><h2>Cần xác minh điều gì?</h2><p>Tình trạng, giấy tờ, lịch sử bảo dưỡng, bảo hành và điều kiện bàn giao.'),
 array('demo-thong-so-can-xem-khi-chon-xe-tai','DEMO: Những thông số cần chú ý khi tìm xe tải','cam-nang','vehicle-truck.svg','Bài viết mẫu hiển thị danh sách tin tức và ảnh đại diện.','<p>Làm rõ loại hàng, tuyến đường và điều kiện xếp dỡ.</p><h2>Tải trọng</h2><p>Đối chiếu tải trọng cho phép trên hồ sơ xe.</p><h2>Kích thước thùng</h2><p>Chọn kích thước phù hợp hàng hóa và điểm giao nhận.</p><h2>Động cơ, truyền động</h2><p>Trao đổi cấu hình theo tài liệu nhà sản xuất. Thông số minh họa không dùng đối chiếu xe thật.'),
 array('demo-checklist-xem-xe-truoc-khi-ban-giao','DEMO: Checklist tham khảo khi kiểm tra xe','kinh-nghiem','vehicle-tractor.svg','Danh sách kiểm tra mẫu trước khi bàn giao xe.','<p>Checklist gợi ý: đối chiếu số khung và giấy tờ; kiểm tra ngoại thất, kính, đèn, lốp; kiểm tra thiết bị an toàn; yêu cầu hướng dẫn vận hành và bảo dưỡng; ghi nhận hạng mục cần xử lý trước khi ký biên bản bàn giao. Với xe đã qua sử dụng hoặc xe chuyên dụng, nên có người có chuyên môn kiểm tra.</p>'),
 array('demo-quy-trinh-tiep-nhan-yeu-cau-tu-van','DEMO: Quy trình tiếp nhận yêu cầu tư vấn','tin-hoat-dong','vehicle-work.svg','Bài viết mẫu giới thiệu cách website tiếp nhận yêu cầu.','<p>Website mẫu cho phép gửi nhu cầu qua biểu mẫu và lưu yêu cầu trong khu vực quản trị.</p><h2>Tiếp nhận thông tin</h2><p>Nhân viên xem nội dung, mẫu xe quan tâm và nguồn truy cập.</p><h2>Liên hệ xác nhận</h2><p>Xác nhận nhu cầu trước khi trao đổi báo giá hoặc lịch xem xe.</p><h2>Cập nhật trạng thái</h2><p>Trạng thái và ghi chú lưu lại tiến độ. Đây là quy trình minh họa, không phải SLA thực tế.</p>')
);
$made_news=0;
foreach($articles as $a){
 if(get_page_by_path($a[0],OBJECT,'post') instanceof WP_Post)continue;
 $id=wp_insert_post(array('post_type'=>'post','post_status'=>'publish','post_name'=>$a[0],'post_title'=>$a[1],'post_excerpt'=>$a[4],'post_content'=>'<div class="dealer-demo-notice"><strong>BÀI VIẾT DEMO:</strong> Nội dung mẫu, cần biên tập và kiểm chứng trước khi public.</div>'.$a[5],'post_category'=>array($news_ids[$a[2]]??0),'meta_input'=>array('_dealer_demo_record'=>1)),true);
 if(is_wp_error($id)){WP_CLI::warning($id->get_error_message());continue;}$img=(int)($images[$a[3]]??0);if($img)set_post_thumbnail($id,$img);$made_news++;
}
$menu=wp_get_nav_menu_object('Menu chính');if(!$menu){$menu_id=wp_create_nav_menu('Menu chính');if(is_wp_error($menu_id)){$menu_id=0;WP_CLI::warning('Cannot create menu: '.$menu_id->get_error_message());}}else$menu_id=(int)$menu->term_id;
if($menu_id){$items=wp_get_nav_menu_items($menu_id);$exists=array();foreach((array)$items as $item)if($item->object==='page')$exists[]=(int)$item->object_id;
 foreach(array('trang-chu','xe','gioi-thieu','tin-tuc','lien-he') as $slug){$id=(int)($page_ids[$slug]??0);if(!$id||in_array($id,$exists,true))continue;wp_update_nav_menu_item($menu_id,0,array('menu-item-title'=>get_the_title($id),'menu-item-object'=>'page','menu-item-object-id'=>$id,'menu-item-type'=>'post_type','menu-item-status'=>'publish'));}
 $loc=get_theme_mod('nav_menu_locations',array());$loc['primary']=$menu_id;$loc['footer']=$menu_id;set_theme_mod('nav_menu_locations',$loc);
}
WP_CLI::success(sprintf('Demo ready: %d new vehicles, %d new articles, %d images. Safe to run again without duplicating seeded records.',$made,$made_news,count(array_filter($images))));
