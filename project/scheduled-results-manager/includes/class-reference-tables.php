<?php
/** Supplied Satta20 table contracts, backed exclusively by public-gated reads. */
defined('ABSPATH') || exit;
class SRM_Reference_Tables {
    public static function game_url(int $id): string {
        $map=get_option('gbk_game_pages',array());$page=absint($map[$id]??0);
        return $page && 'publish'===get_post_status($page) ? (string)get_permalink($page) : '';
    }
    public static function schedule(array $atts=array()): string {
        $atts=shortcode_atts(array('show_time'=>'yes','show_secondary'=>'yes','title'=>'गेम की समय-सारणी','ul_class'=>''),$atts);
        $games=SRM_Games::get_active_games();ob_start();include SRM_PLUGIN_DIR.'templates/active-games.php';return (string)ob_get_clean();
    }
    public static function daily(array $atts=array(),string $tag=''): string {
        $atts=shortcode_atts(array('show_time'=>'yes','show_secondary'=>'yes','title'=>''),$atts);
        $today=current_datetime();$yesterday=$today->modify('-1 day');
        $games=SRM_Games::get_active_games();$values=array();
        foreach(array($yesterday,$today) as $date){foreach(SRM_Results::get_results_for_date($date->format('Y-m-d')) as $row){$values[$date->format('Y-m-d')][$row->event_id]=$row;}}
        $dates='srm_today_yesterday_results'===$tag ? array('कल का रिज़ल्ट'=>$yesterday,'आज का रिज़ल्ट'=>$today) : ('srm_yesterday_results'===$tag?array('कल का रिज़ल्ट'=>$yesterday):array('आज का रिज़ल्ट'=>$today));
        ob_start();include SRM_PLUGIN_DIR.'templates/today-yesterday-results.php';return (string)ob_get_clean();
    }
    public static function monthly(array $atts=array(),string $tag=''): string {
        $previous=str_contains($tag,'previous');$base=current_datetime()->modify('first day of this month');
        if($previous)$base=$base->modify('-1 month');
        $prefix=$previous?'srm_previous_':'srm_';
        foreach(array('game_id','month','year') as $key)if(isset($_GET[$prefix.$key])&&!is_scalar($_GET[$prefix.$key]))return '<p role="alert">कृपया सही फ़िल्टर चुनें।</p>';
        // Explicit shortcode game ID is a constraint, not a default that a URL can widen.
        $fixed=absint($atts['game_id']??$atts['event_id']??0);
        $selected_game_id=$fixed?:absint($_GET[$prefix.'game_id']??0);
        $month=(int)($_GET[$prefix.'month']??$atts['month']??$base->format('n'));
        $year=(int)($_GET[$prefix.'year']??$atts['year']??$base->format('Y'));
        if($month<1||$month>12||$year<1900||$year>2100)return '<p role="alert">कृपया सही महीना और वर्ष चुनें।</p>';
        $atts=shortcode_atts(array('title'=>'','show_filter'=>'yes'),$atts);
        $events=SRM_Games::get_active_games();$filter_options=$events;
        if($selected_game_id)$events=array_values(array_filter($events,static fn($g)=>(int)$g->id===$selected_game_id));
        if($fixed)$filter_options=$events;
        $date=new DateTimeImmutable(sprintf('%04d-%02d-01',$year,$month),wp_timezone());
        $days=(int)$date->format('t');$date_format=get_option('srm_date_format','d/m/Y');
        $month_label=self::month_name($month).' '.$year;$data=SRM_Results::get_monthly_results($year,$month);
        $instance=wp_unique_id('srm-month-');
        ob_start();include SRM_PLUGIN_DIR.'templates/monthly-results.php';return (string)ob_get_clean();
    }
    public static function month_name(int $month): string {
        return array(1=>'जनवरी','फरवरी','मार्च','अप्रैल','मई','जून','जुलाई','अगस्त','सितंबर','अक्टूबर','नवंबर','दिसंबर')[$month]??'';
    }
}
