<?php
/** Intentional retention policy: uninstall never deletes archives or schedules. */
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;
// Back up and remove tables manually only when data retention policy permits.
