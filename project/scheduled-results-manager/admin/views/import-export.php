<?php
defined('ABSPATH')||exit;
if(!SRM_Access::current_user_has_full_access())wp_die('Permission denied.','',array('response'=>403));
echo '<div class="wrap srm-admin-wrap"><h1 class="srm-page-title"><span class="dashicons dashicons-database-import"></span> Import / Export</h1><div class="srm-box">';
SRM_Review_Desk::csv();
echo '</div></div>';
