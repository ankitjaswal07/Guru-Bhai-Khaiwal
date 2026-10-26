<?php
/** Original combined layout: Game | Game Time | Yesterday | Today. Match by stable ID. */
defined('ABSPATH') || exit;
$show_time='yes'===$atts['show_time'];$show_secondary='yes'===$atts['show_secondary'];
$title=$atts['title']?:'आज और कल का रिज़ल्ट';
?>
<div class="srm-wrap srm-today-yesterday-results">
<?php if($atts['title']): ?><h3 class="srm-table-title srm-combined-title"><?php echo esc_html($title); ?></h3><?php endif; ?>
<div class="srm-table-responsive" role="region" tabindex="0" aria-label="<?php echo esc_attr($title); ?>">
<table class="srm-table srm-combined-results-table">
<caption class="screen-reader-text"><?php echo esc_html($title); ?></caption>
<thead><tr><th scope="col">गेम / Game</th><?php if($show_time): ?><th scope="col">समय / Time</th><?php endif; ?>
<?php foreach($dates as $label=>$date): ?><th scope="col"><span><?php echo esc_html($label); ?></span><small class="srm-date-label"><?php echo esc_html($date->format('d/m/Y')); ?></small></th><?php endforeach; ?></tr></thead>
<tbody><?php foreach($games as $game): ?>
<tr><th scope="row" class="srm-event-col"><span class="srm-event-primary"><?php $url=SRM_Reference_Tables::game_url((int)$game->id);if($url): ?><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($game->event_name); ?></a><?php else:echo esc_html($game->event_name);endif; ?></span>
<?php if($show_secondary&&$game->event_name_secondary): ?><span class="srm-event-secondary-fe" lang="hi"><?php echo esc_html($game->event_name_secondary); ?></span><?php endif; ?></th>
<?php if($show_time): ?><td class="srm-time-col"><?php echo esc_html(SRM_Games::format_time($game->scheduled_time)); ?></td><?php endif; ?>
<?php foreach($dates as $date):$r=$values[$date->format('Y-m-d')][$game->id]??null; ?><td class="srm-result-col"><?php if($r): ?><span class="srm-result-published"><?php echo esc_html($r->result_value); ?></span><?php else: ?><span class="srm-result-empty" aria-label="रिज़ल्ट उपलब्ध नहीं">—</span><?php endif; ?></td><?php endforeach; ?></tr>
<?php endforeach;if(!$games): ?><tr><td colspan="<?php echo count($dates)+1+(int)$show_time; ?>">कोई सक्रिय गेम नहीं है।</td></tr><?php endif; ?>
</tbody></table></div></div>
