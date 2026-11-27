<?php
/**
 * Remove demo-marked products, posts and media. Run explicitly:
 * wp eval-file scripts/remove-demo-content.php
 * Main pages, menu and vehicle categories remain.
 */
if(!defined('ABSPATH'))exit;
$records=get_posts(array('post_type'=>array('product','post'),'post_status'=>'any','posts_per_page'=>-1,'fields'=>'ids','meta_key'=>'_dealer_demo_record','meta_value'=>1));
$removed=0;foreach($records as $id)if(wp_delete_post((int)$id,true))$removed++;
$media=get_posts(array('post_type'=>'attachment','post_status'=>'inherit','posts_per_page'=>-1,'fields'=>'ids','meta_key'=>'_dealer_demo_asset'));$removed_media=0;
foreach($media as $attachment){$attachment=(int)$attachment;$terms=get_terms(array('taxonomy'=>'product_cat','hide_empty'=>false));if(!is_wp_error($terms))foreach($terms as $term)if((int)get_term_meta($term->term_id,'thumbnail_id',true)===$attachment){delete_term_meta($term->term_id,'thumbnail_id');delete_term_meta($term->term_id,'_dealer_demo_thumbnail');}if(wp_delete_attachment($attachment,true))$removed_media++;}
$brands=get_terms(array('taxonomy'=>'dealer_vehicle_brand','hide_empty'=>false));if(!is_wp_error($brands))foreach($brands as $brand)if(get_term_meta($brand->term_id,'_dealer_demo_seeded',true)&&(int)$brand->count===0)wp_delete_term($brand->term_id,'dealer_vehicle_brand');
$pages=get_posts(array('post_type'=>'page','post_status'=>'any','posts_per_page'=>-1,'meta_key'=>'_dealer_demo_page_seeded'));$cleared=0;
foreach($pages as $p){$expected=(string)get_post_meta($p->ID,'_dealer_demo_page_hash',true);$current=hash('sha256',(string)$p->post_content);if($expected!==''&&hash_equals($expected,$current)){wp_update_post(array('ID'=>$p->ID,'post_content'=>''));$cleared++;}delete_post_meta($p->ID,'_dealer_demo_page_seeded');delete_post_meta($p->ID,'_dealer_demo_page_hash');}
delete_option('dealer_demo_content_enabled');
WP_CLI::success(sprintf('Removed %d demo products/articles and %d demo images; cleared unchanged sample content on %d pages. Pages, menu and categories remain.',$removed,$removed_media,$cleared));
