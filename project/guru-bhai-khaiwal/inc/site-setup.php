<?php
/** Explicit, repeatable starter-content creation. Never replaces existing pages/options. */
defined('ABSPATH')||exit;
function gbk_seed_page_meta(int $id,string $name,$value): void {
    $keys=array('page_sections'=>'field_s20_page_page_sections','result_chart_shortcode'=>'field_s20_pageresults_result_chart_shortcode','page_intro'=>'field_s20_pageresults_page_intro');
    $key=$keys[$name]??'';
    // Resolve real registered keys; original groups/keys remain authoritative.
    foreach($GLOBALS['s20_field_groups']??array() as $group)foreach($group['fields']??array() as $field)if(($field['name']??'')===$name)$key=$field['key'];
    if($key&&function_exists('update_field'))update_field($key,$value,$id);
    else {update_post_meta($id,$name,$value);if($key){update_post_meta($id,'_'.$name,$key);$pending=get_post_meta($id,'_gbk_pending_acf',true)?:array();$pending[$name]=array($key,$value);update_post_meta($id,'_gbk_pending_acf',$pending);}}
}
add_action('admin_init',function(){
    if(!current_user_can('manage_options')||!function_exists('update_field'))return;
    foreach(get_posts(array('post_type'=>'page','post_status'=>'any','numberposts'=>100,'meta_key'=>'_gbk_pending_acf')) as $p){foreach(get_post_meta($p->ID,'_gbk_pending_acf',true) as $name=>$v){if(get_post_meta($p->ID,$name,true)===$v[1])update_field($v[0],$v[1],$p->ID);}delete_post_meta($p->ID,'_gbk_pending_acf');}
});
function gbk_install_starter_pages(): array|WP_Error {
    if(!current_user_can('manage_options'))return new WP_Error('permission','Administrator access required.');
    if(!class_exists('SRM_Games'))return new WP_Error('plugin','Activate Results Manager first.');
    $catalog=json_decode(file_get_contents(S20_DIR.'/inc/data/starter-content.json'),true);
    $summary=array('created'=>0,'existing'=>0,'failed'=>0,'pages'=>array());
    foreach(SRM_Games::get_all_games() as $g){
        $name=$g->event_name_secondary?:$g->event_name;
        $catalog['pages']['game-'.$g->slug]=array('title'=>$name.' रिज़ल्ट चार्ट','template'=>'template-game-results.php','game_id'=>(int)$g->id,'shortcode'=>'[srm_monthly_results game_id="'.(int)$g->id.'"]','sections'=>array(array('heading'=>$name.' की जानकारी','content'=>'<p>यहाँ '.$name.' के उपलब्ध रिज़ल्ट महीने और तारीख के अनुसार देख सकते हैं। ऊपर दिए फ़िल्टर से महीना बदलें। समय-सारणी वेबसाइट की स्थानीय समय-सेटिंग के अनुसार है।</p><p>किसी तारीख पर खाली स्थान होने का अर्थ है कि उस दिन का प्रकाशित रिकॉर्ड उपलब्ध नहीं है। पुराना रिज़ल्ट आने वाले परिणाम की गारंटी नहीं देता।</p>'),array('heading'=>'चार्ट कैसे पढ़ें?','content'=>'<p>बाईं ओर तारीख और उसके सामने गेम का रिज़ल्ट दिखता है। दो अंकों के नंबर में शुरुआती शून्य भी परिणाम का हिस्सा है। रिकॉर्ड में गलती दिखे तो संपर्क पेज पर गेम का नाम और तारीख बताएं।</p>')));
    }
    $game_map=get_option('gbk_game_pages',array());
    foreach(array('pages'=>'page','posts'=>'post') as $collection=>$type)foreach($catalog[$collection] as $slug=>$item){
        $front_id=absint(get_option('page_on_front'));
        $existing=('page'===$type&&'home'===$slug&&$front_id&&'page'===get_post_type($front_id)&&'publish'===get_post_status($front_id))?get_post($front_id):get_page_by_path($slug,OBJECT,$type);
        if($existing){$id=$existing->ID;$summary['existing']++;}
        else{
            $content='';if('post'===$type)foreach($item['sections'] as $section)$content.='<h2>'.esc_html($section['heading']).'</h2>'.$section['content'];
            $id=wp_insert_post(array('post_type'=>$type,'post_status'=>'publish','post_name'=>$slug,'post_title'=>$item['title'],'post_content'=>$content,'post_excerpt'=>$item['excerpt']??''),true);
            if(is_wp_error($id)){$summary['failed']++;continue;}$summary['created']++;
            update_post_meta($id,'_gbk_starter_version',S20_VERSION);
            if('page'===$type){
                $sections=array_map(static fn($r)=>array('acf_fc_layout'=>'full_content','active'=>1)+$r,$item['sections']??array());
                gbk_seed_page_meta($id,'page_sections',$sections);
                if(!empty($item['template']))update_post_meta($id,'_wp_page_template','page-templates/'.$item['template']);
                if(!empty($item['shortcode']))gbk_seed_page_meta($id,'result_chart_shortcode',$item['shortcode']);
                if(!empty($item['game_id']))update_post_meta($id,'_gbk_game_id',$item['game_id']);
            }
        }
        if('page'===$type){$summary['pages'][$slug]=$id;if(!empty($item['game_id'])&&(int)get_post_meta($id,'_gbk_game_id',true)===$item['game_id'])$game_map[$item['game_id']]=$id;}
    }
    update_option('gbk_game_pages',$game_map,false);
    if(!get_option('page_on_front')){update_option('show_on_front','page');update_option('page_on_front',$summary['pages']['home']);}
    $locations=get_theme_mod('nav_menu_locations',array());
    foreach(array('primary'=>array('home','games','results','chart','blog','faq','contact'),'footer'=>array('about','blog','faq','contact')) as $location=>$slugs){
        if(!empty($locations[$location]))continue;
        $menu=wp_get_nav_menu_object('GBK Hindi '.$location);$menu_id=$menu?$menu->term_id:wp_create_nav_menu('GBK Hindi '.$location);
        if(is_wp_error($menu_id))continue;
        $items=wp_get_nav_menu_items($menu_id)?:array();$linked=array_map(static fn($p)=>(int)$p->object_id,$items);
        foreach($slugs as $slug)if(!in_array($summary['pages'][$slug],$linked,true))wp_update_nav_menu_item($menu_id,0,array('menu-item-object-id'=>$summary['pages'][$slug],'menu-item-object'=>'page','menu-item-type'=>'post_type','menu-item-status'=>'publish'));
        $locations[$location]=$menu_id;
    }
    set_theme_mod('nav_menu_locations',$locations);
    update_option('gbk_starter_content_version',S20_VERSION,false);
    return $summary;
}
add_action('admin_menu',function(){add_theme_page('Hindi content setup','Hindi content setup','manage_options','gbk-site-setup','gbk_site_setup_screen');});
function gbk_site_setup_screen(): void {
    if(!current_user_can('manage_options'))return;
    echo '<div class="wrap"><h1>Guru Bhai Khaiwal — Hindi content setup</h1><p>Create the requested pages, one chart page per game and three Hindi starter articles. New pages/articles are published. Existing content, menus, front page, settings and game schedules are preserved.</p><p>Page content: Pages → Edit → Page Display Settings → Page Sections. Add text, image or shortcode sections. Homepage section order and text: Guru Bhai Khaiwal → Homepage. Requires licensed ACF PRO for the visual fields.</p>';
    $result=get_transient('gbk_setup_'.get_current_user_id());if($result){echo '<div class="notice notice-info"><p>'.esc_html($result).'</p></div>';delete_transient('gbk_setup_'.get_current_user_id());}
    echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="gbk_setup_pages">';wp_nonce_field('gbk_setup_pages');
    echo '<p><label><input type="checkbox" name="confirm" value="1" required> Create and publish missing Hindi starter pages/articles (no result values are fabricated or published).</label></p>';submit_button('Create missing Hindi pages');echo '</form><h2>Historical results</h2><p>The unchanged 523-row dataset is bundled with the plugin. Import it as drafts, then review records before publishing.</p><a class="button" href="'.esc_url(admin_url('admin.php?page=srm-history-setup')).'">Open historical import &amp; review</a></div>';
}
add_action('admin_post_gbk_setup_pages',function(){
    if(!current_user_can('manage_options'))wp_die('Permission denied.','',array('response'=>403));
    if('POST'!==($_SERVER['REQUEST_METHOD']??''))wp_die('POST required.','',array('response'=>405));
    check_admin_referer('gbk_setup_pages');if('1'!==($_POST['confirm']??''))wp_die('Explicit confirmation required.');
    $r=gbk_install_starter_pages();set_transient('gbk_setup_'.get_current_user_id(),is_wp_error($r)?$r->get_error_message():sprintf('Created %d; preserved existing %d; failed %d. Existing pages are never overwritten.',$r['created'],$r['existing'],$r['failed']),90);wp_safe_redirect(admin_url('themes.php?page=gbk-site-setup'));exit;
});
add_shortcode('gbk_game_directory',function(){
    if(!class_exists('SRM_Games'))return s20_shortcode_fallback();$html='<div class="gbk-game-directory">';
    foreach(SRM_Games::get_active_games() as $g){$url=SRM_Reference_Tables::game_url((int)$g->id);if(!$url)continue;$html.='<a class="gbk-directory-link" href="'.esc_url($url).'"><strong>'.esc_html($g->event_name_secondary?:$g->event_name).'</strong><span>'.esc_html($g->event_name.' · '.SRM_Games::format_time($g->scheduled_time)).'</span></a>';}
    return $html.'</div>';
});
