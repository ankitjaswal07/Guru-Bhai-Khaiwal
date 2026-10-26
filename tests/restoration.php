<?php
/** Contract/API capture only: this does NOT load or emulate ACF PRO's editor. */
wp_set_current_user(1);
require_once ABSPATH.'wp-admin/includes/template.php';
$n=0;
function restored_assert($condition,$label){global $n;if(!$condition)throw new Exception('FAIL '.$label);++$n;echo 'PASS '.$label."\n";}
// Explicitly scoped capture helpers run only in this PHP test request, never in preview/plugin files.
function acf_add_local_field_group($group){$GLOBALS['captured_groups'][$group['key']]=$group;}
function acf_add_options_page($page){$GLOBALS['captured_pages'][$page['menu_slug']]=$page;return $page;}
function acf_add_options_sub_page($page){return acf_add_options_page($page);}
function field_contract($groups){$out=array();$walk=function($fields)use(&$walk,&$out){foreach($fields as $field){if(isset($field['key']))$out[$field['key']]=array($field['name']??'',$field['type']);if(isset($field['sub_fields']))$walk($field['sub_fields']);foreach($field['layouts']??array() as $layout){$out[$layout['key']]=array($layout['name'],'layout');$walk($layout['sub_fields']);}}};foreach($groups as $g)$walk($g['fields']);return $out;}
$z=new ZipArchive();$z->open('/tmp/original-theme.zip');$source=$z->getFromName('satta20/inc/acf-fields.php');$z->close();
eval(str_replace('<?php','namespace OriginalACFCapture; function s20_icon_choices($all=true){return \\s20_icon_choices($all);}',$source));
\OriginalACFCapture\s20_register_acf_fields();$original=field_contract($GLOBALS['captured_groups']);$original_groups=array_keys($GLOBALS['captured_groups']);$GLOBALS['captured_groups']=array();
do_action('acf/init');$updated=field_contract($GLOBALS['captured_groups']);
restored_assert(!array_diff_key($original,$updated),'all '.count($original).' supplied ACF field keys retained');
foreach($original as $key=>$contract)restored_assert($updated[$key]===$contract,'field name/type compatibility '.$key);
restored_assert(!array_diff($original_groups,array_keys($GLOBALS['captured_groups'])),'all original field groups retained');
foreach(array('general','header','results','contact','payment','faq','footer','social','shortcodes','homepage','styles') as $page)restored_assert(isset($GLOBALS['captured_pages']['s20-'.$page]),'ACF page registration s20-'.$page);
restored_assert($GLOBALS['captured_pages']['s20-homepage']['post_id']==get_option('page_on_front'),'Homepage options target existing front-page post ID');
restored_assert(count(s20_style_schema())>=50,'comprehensive Theme Styles controls registered');
restored_assert(s20_style_value('accent','red;}</style><script>bad</script>')==='#f5c62f','style color injection rejected');
restored_assert(s20_style_value('container_width',99999)===1920,'style width clamped');
restored_assert(s20_style_value('body_font','url(javascript:x)')==='system','font uses allowlist');
update_option('options_style_accent','#123456');restored_assert(str_contains(s20_style_css(),'--gbk-style-accent:#123456'),'saved style value reaches frontend CSS');delete_option('options_style_accent');
update_option('options_site_phone','');restored_assert(s20_get_option('site_phone','unwanted fallback')==='','deliberately empty field stays empty');delete_option('options_site_phone');
update_option('options_faq_items',0);restored_assert(s20_get_repeater('faq_items',s20_default_faq_items())===array(),'cleared repeater never reseeded by rendering');delete_option('options_faq_items');
restored_assert(s20_get_option('payment_enabled',true)===false,'informational payment restriction enforced');
$before=SRM_Settings::get_all();SRM_Settings::save(array('srm_header_bg'=>'#224466','srm_font_size'=>99,'srm_current_game_enabled'=>1,'srm_next_game_enabled'=>1,'srm_allow_editing'=>1,'srm_countdown_enabled'=>1,'srm_delete_on_uninstall'=>1));
restored_assert(str_contains(SRM_Settings::get_css_vars(),'#224466')&&get_option('srm_font_size')==28,'plugin styling validates and reaches CSS');
restored_assert(!get_option('srm_delete_on_uninstall'),'uninstall erasure cannot be enabled');
foreach($before as $k=>$v){if($k!=='timezone')update_option('srm_'.$k,$v);}
restored_assert(SRM_Results::get_stats()['total_events']===14,'dashboard stats adapter works');
// Real views rendered under actual WordPress; no ACF UI involved.
foreach(array('dashboard','events','results','monthly','settings','import_export') as $view){$_GET=array();ob_start();(new SRM_Admin())->{'page_'.$view}();$html=ob_get_clean();restored_assert(str_contains($html,'srm-admin-wrap'),'restored admin view '.$view);}
$_GET=array();
restored_assert(str_contains(do_shortcode('[srm_live_status]'),'data-srm-panel="current_game"')&&str_contains(do_shortcode('[srm_live_status]'),'data-srm-panel="next_game"'),'combined current/next panels');
update_option('srm_next_game_enabled','0');restored_assert(do_shortcode('[srm_next_game]')==='','next-game setting honored');update_option('srm_next_game_enabled','1');
update_option('srm_current_game_enabled','0');restored_assert(do_shortcode('[srm_current_game]')==='','current-game setting honored');update_option('srm_current_game_enabled','1');
$operator=wp_insert_user(array('user_login'=>'restoration-operator','user_pass'=>wp_generate_password(),'role'=>'subscriber'));
SRM_Access::save_user_allowed_games($operator,array(1));wp_set_current_user($operator);
$draft=array('result_value'=>'09','original_game_name'=>'TEST ONLY','source_url'=>'https://example.com/test','result_status'=>'pending','verification_status'=>'unverified');
restored_assert(true===SRM_Results::save_result(1,'2026-08-26',$draft),'assigned operator can submit assigned draft');
restored_assert(is_wp_error(SRM_Results::save_result(2,'2026-08-26',$draft)),'assigned operator cannot write another game');
restored_assert(SRM_Results::query(array(),true)['total']===1,'operator private query restricted to assignments');
wp_set_current_user(1);update_option('srm_allow_editing','0');restored_assert(is_wp_error(SRM_Results::save_result(1,'2026-08-26',$draft)),'editing lock enforced by repository');update_option('srm_allow_editing','1');
update_option('srm_result_format','any');restored_assert(true===SRM_Results::save_result(1,'2026-08-26',array_merge($draft,array('result_value'=>'AB12'))),'original alphanumeric option honored safely');
update_option('srm_result_format','numeric');restored_assert(is_wp_error(SRM_Results::save_result(1,'2026-08-26',array_merge($draft,array('result_value'=>'AB12')))),'numeric-only option enforced');
update_option('srm_time_format','H:i');restored_assert(SRM_Games::format_time('01:00:00')==='01:00','original time-format option honored');delete_option('srm_time_format');
global $wpdb;$wpdb->query('DELETE FROM '.SRM_Database::results_table());require_once ABSPATH.'wp-admin/includes/user.php';wp_delete_user($operator);
echo "RESTORATION: $n contract/integration assertions passed. ACF PRO UI is NOT tested by these capture functions.\n";
