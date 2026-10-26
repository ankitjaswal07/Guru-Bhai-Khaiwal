<?php
defined('ABSPATH')||exit;
if(!SRM_Access::current_user_can_access_plugin())wp_die('Permission denied.','',array('response'=>403));
$date=sanitize_text_field(wp_unslash($_GET['date']??SRM_Results::get_site_date()));
if(!SRM_Results::valid_date($date))$date=SRM_Results::get_site_date();
$games=SRM_Games::get_active_games();$full=SRM_Access::current_user_has_full_access();
?>
<div class="wrap srm-admin-wrap">
<h1 class="srm-page-title"><span class="dashicons dashicons-edit"></span> Daily Results</h1>
<form method="get" class="srm-date-selector"><input type="hidden" name="page" value="srm-results"><label for="srm-result-date">Date (<?php echo esc_html(wp_timezone_string()); ?>)</label> <input id="srm-result-date" type="date" name="date" value="<?php echo esc_attr($date); ?>" max="<?php echo esc_attr(SRM_Results::get_site_date()); ?>"> <button class="button">Load Date</button></form>
<p>Each record needs its original source name and URL. Only a full results manager can mark Verified, confirm review and publish. Imports and operator entries remain drafts. Empty rows are skipped by Save All. Disable a game or change a record to Pending instead of deleting history.</p>
<div class="srm-bulk-actions"><button type="button" id="srm-save-all" class="button button-primary">Save All</button> <?php if($full): ?><button type="button" id="srm-publish-all" class="button">Publish Reviewed Rows</button><?php endif; ?> <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=srm-monthly')); ?>">Monthly Results</a></div>
<div id="srm-ajax-message" role="status" aria-live="polite"></div>
<div class="srm-daily-scroll"><table class="widefat striped srm-daily-table"><thead><tr><th>Game / time</th><th>Result</th><th>Source & verification</th><th>Status & review</th><th>Actions</th></tr></thead><tbody>
<?php foreach($games as $game):
$r=SRM_Results::get_result((int)$game->id,$date,true);$prefix='result-'.$game->id;
?>
<tr data-event-id="<?php echo absint($game->id); ?>" data-result-date="<?php echo esc_attr($date); ?>">
<td><strong><?php echo esc_html($game->event_name); ?></strong><p lang="hi"><?php echo esc_html($game->event_name_secondary??''); ?></p><?php echo esc_html(SRM_Games::format_time($game->scheduled_time)); ?></td>
<td><label for="<?php echo esc_attr($prefix); ?>-value" class="screen-reader-text">Result for <?php echo esc_html($game->event_name); ?></label><input id="<?php echo esc_attr($prefix); ?>-value" name="result_value" class="srm-result-input" value="<?php echo esc_attr($r->result_value??''); ?>" maxlength="50" autocomplete="off" style="width:100px"><input type="hidden" name="expected_hash" value="<?php echo esc_attr($r?SRM_Results::record_hash($r):''); ?>"></td>
<td><label>Original source game name<input name="original_game_name" value="<?php echo esc_attr($r->original_game_name??$game->event_name); ?>" maxlength="191"></label><label>Public source URL<input type="url" name="source_url" value="<?php echo esc_attr($r->source_url??''); ?>" placeholder="https://" maxlength="2000"></label><label>Private source notes<textarea name="verification_notes" rows="2" maxlength="4000"><?php echo esc_textarea($r->verification_notes??''); ?></textarea></label></td>
<td><label>Publication<select name="result_status" <?php disabled(!$full); ?>><?php foreach(array('pending'=>'Pending / draft','published'=>'Published') as $value=>$label): ?><option value="<?php echo esc_attr($value); ?>" <?php selected($r->result_status??get_option('srm_default_result_status','pending'),$value); ?>><?php echo esc_html($label); ?></option><?php endforeach; ?></select></label>
<label>Verification<select name="verification_status" <?php disabled(!$full); ?>><?php foreach(array('unverified','verified','disputed') as $value): ?><option value="<?php echo esc_attr($value); ?>" <?php selected($r->verification_status??'unverified',$value); ?>><?php echo esc_html(ucfirst($value)); ?></option><?php endforeach; ?></select></label>
<?php if($full): ?><label><input type="checkbox" name="confirm_review" value="1"> I checked this source and approve this value.</label><?php else: ?><small>Draft-only operator access</small><?php endif; ?></td>
<td><button type="button" class="button button-primary srm-save-single">Save</button><p class="srm-row-message" role="status"></p></td>
</tr><?php endforeach; ?>
</tbody></table></div></div>
