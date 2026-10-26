<?php
/** Authenticated POST-only adapters for the supplied dashboard. */
defined('ABSPATH')||exit;
class SRM_Admin_Actions {
    public static function ajax(): void {
        if('POST'!==($_SERVER['REQUEST_METHOD']??''))wp_send_json_error(array('message'=>'POST required.'),405);
        if(!SRM_Access::current_user_can_access_plugin())wp_send_json_error(array('message'=>'Permission denied.'),403);
        check_ajax_referer('srm_admin_nonce','nonce');
        $data=wp_unslash($_POST);$action=sanitize_key($data['action']??'');$id=absint($data['event_id']??0);
        if('srm_save_result'===$action){
            $result=SRM_Results::save_result($id,sanitize_text_field($data['result_date']??''),$data);
        }else{
            if(!SRM_Access::current_user_has_full_access())wp_send_json_error(array('message'=>'Full results-manager permission required.'),403);
            if('srm_toggle_game'===$action){$result=SRM_Games::toggle_status($id);}
            elseif('srm_save_event_order'===$action){
                $ids=array_values(array_unique(array_map('absint',(array)($data['order']??array()))));$result=true;
                foreach($ids as $position=>$game){$existing=SRM_Games::get_game($game);$saved=$existing?SRM_Games::update_game($game,array_merge((array)$existing,array('display_order'=>$position))):new WP_Error('game','Unknown game.');if(is_wp_error($saved)){$result=$saved;break;}}
            }else{$result=new WP_Error('action','Unknown action.');}
        }
        if(is_wp_error($result))wp_send_json_error(array('message'=>$result->get_error_message()),400);
        $row='srm_save_result'===$action?SRM_Results::get_result($id,$data['result_date'],true):null;
        wp_send_json_success(array('message'=>'Saved.','hash'=>$row?SRM_Results::record_hash($row):''));
    }
}
