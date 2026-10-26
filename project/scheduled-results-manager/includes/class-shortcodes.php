<?php
/** Accessible informational components; legacy shortcode names are retained. */
defined( 'ABSPATH' ) || exit;
class SRM_Shortcodes {
    public static function register(): void {
        $map = array(
            'schedule' => array( 'srm_games_list', 'srm_active_games', 'srm_active_game_list', 'srm_active_games_list', 'srm_active_game' ),
            'daily' => array( 'srm_today_results', 'srm_yesterday_results', 'srm_today_yesterday_results' ),
            'monthly' => array( 'srm_monthly_results', 'srm_full_month_results', 'srm_previous_month_results', 'srm_previous_month_table' ),
            'archive' => array( 'srm_results_archive', 'gbk_archive', 'srm_game_history', 'srm_event_history' ),
            'latest' => array( 'gbk_latest_records', 'srm_latest_game_result', 'srm_current_result' ),
            'next' => array( 'srm_current_game', 'srm_current_game_name', 'srm_current_game_time', 'srm_next_game', 'srm_live_status' ),
        );
        foreach ( $map as $method => $tags ) {
            foreach ( $tags as $tag ) {
                add_shortcode( $tag, static fn( $atts = array(), $content = null, $name = '' ) => self::$method( is_array( $atts ) ? $atts : array(), $name ) );
            }
        }
    }
    public static function empty_message(): string {
        return '<div class="gbk-empty"><span aria-hidden="true">—</span><strong>' . esc_html__( 'अभी रिज़ल्ट उपलब्ध नहीं हैं', 'scheduled-results-manager' ) . '</strong><p>' . esc_html__( 'उपलब्ध रिज़ल्ट प्रकाशित होने पर यहाँ दिखेंगे। खाली स्थान का अर्थ शून्य नहीं है।', 'scheduled-results-manager' ) . '</p></div>';
    }
    public static function schedule( array $atts = array(), string $tag = '' ): string {
        return SRM_Reference_Tables::schedule($atts);
    }
    public static function next( array $atts = array(), string $tag = '' ): string {
        if('srm_live_status'===$tag){return '<div class="gbk-live-panels">'.self::next(array(),'srm_current_game').self::next(array(),'srm_next_game').'</div>';}
        $current = in_array( $tag, array( 'srm_current_game', 'srm_current_game_name', 'srm_current_game_time' ), true );
        if(!get_option($current?'srm_current_game_enabled':'srm_next_game_enabled','1'))return '';
        $next = SRM_Current_Event::occurrences()[ $current ? 'previous' : 'next' ];
        if ( ! $next ) { return '<p>No active schedule entries.</p>'; }
        if ( 'srm_current_game_name' === $tag && $next ) { return esc_html( $next['game']->event_name ); }
        if ( 'srm_current_game_time' === $tag && $next ) { return esc_html( SRM_Games::format_time( $next['game']->scheduled_time ) ); }
        return '<div class="gbk-next" data-srm-panel="'.($current?'current_game':'next_game').'"><span class="eyebrow">' . ( $current ? 'पिछला निर्धारित गेम' : 'अगला निर्धारित गेम' ) . '</span><h3>' . esc_html( $next['game']->event_name ) . '</h3><p lang="hi">' . esc_html( $next['game']->event_name_secondary ?? '' ) . '</p><strong>' . esc_html( SRM_Games::format_time( $next['game']->scheduled_time ) ) . '</strong><p class="srm-occurrence-date">' . esc_html( wp_date( 'D, j M Y', $next['at']->getTimestamp() ) . ' · ' . wp_timezone_string() ) . '</p>' . (!$current && get_option('srm_countdown_enabled','1') ? '<p class="srm-countdown" data-srm-until="'.esc_attr($next['at']->getTimestamp()).'" aria-label="Time until next scheduled entry"></p>' : '') . '<small>निर्धारित समय है; प्रकाशन समय अलग हो सकता है।</small></div>';
    }
    public static function table( array $rows ): string {
        if ( ! $rows ) { return self::empty_message(); }
        ob_start(); ?>
        <div class="gbk-table-wrap" role="region" aria-label="Historical records" tabindex="0"><table class="gbk-table"><caption>पुराने रिज़ल्ट</caption><thead><tr><th scope="col">Date</th><th scope="col">Game</th><th scope="col">Record</th></tr></thead><tbody>
        <?php foreach ( $rows as $r ) : ?>
            <tr><td><time datetime="<?php echo esc_attr( $r->result_date ); ?>"><?php echo esc_html( wp_date( get_option( 'srm_date_format', get_option('date_format') ), ( new DateTimeImmutable( $r->result_date, wp_timezone() ) )->getTimestamp() ) ); ?></time></td><th scope="row"><?php echo esc_html( $r->event_name ); ?><small lang="hi"><?php echo esc_html( $r->event_name_secondary ?? '' ); ?></small></th><td><strong class="gbk-result"><?php echo esc_html( $r->result_value ); ?></strong></td></tr>
        <?php endforeach; ?>
        </tbody></table></div>
        <?php return (string) ob_get_clean();
    }
    public static function daily( array $atts = array(), string $tag = '' ): string {
        return SRM_Reference_Tables::daily($atts,$tag);
    }
    public static function latest( array $atts = array(), string $tag = '' ): string { return self::table( SRM_Results::query( array( 'limit' => 6 ) )['rows'] ); }
    public static function monthly( array $atts = array(), string $tag = '' ): string {
        return SRM_Reference_Tables::monthly($atts,$tag);
    }
    public static function filters(): array {
        $args = array( 'game_id' => absint( $_GET['gbk_game'] ?? $_GET['srm_game_id'] ?? 0 ), 'page' => max( 1, absint( $_GET['gbk_page'] ?? 1 ) ) );
        $date = sanitize_text_field( wp_unslash( $_GET['gbk_date'] ?? '' ) );
        $month = sanitize_text_field( wp_unslash( $_GET['gbk_month'] ?? '' ) );
        if ( ! $month && isset( $_GET['srm_year'], $_GET['srm_month'] ) ) { $month = sprintf( '%04d-%02d', absint( $_GET['srm_year'] ), absint( $_GET['srm_month'] ) ); }
        if ( $date ) {
            if ( ! SRM_Results::valid_date( $date ) ) { return array( 'error' => 'Enter a valid date.' ); }
            $args['start'] = $args['end'] = $date;
        } elseif ( $month ) {
            if ( ! SRM_Results::valid_date( $month . '-01' ) ) { return array( 'error' => 'Enter a valid month.' ); }
            $args['start'] = $month . '-01';
            $args['end'] = ( new DateTimeImmutable( $args['start'], wp_timezone() ) )->format( 'Y-m-t' );
        }
        return $args;
    }
    public static function archive( array $atts = array(), string $tag = '' ): string {
        $args = self::filters();
        if ( ! empty( $atts['game_id'] ) || ! empty( $atts['event_id'] ) ) { $args['game_id'] = absint( $atts['game_id'] ?? $atts['event_id'] ); }
        if ( ! empty( $atts['year'] ) && ! empty( $atts['month'] ) && empty( $args['start'] ) ) {
            $start = sprintf( '%04d-%02d-01', (int) $atts['year'], (int) $atts['month'] );
            if ( SRM_Results::valid_date( $start ) ) { $args['start'] = $start; $args['end'] = ( new DateTimeImmutable( $start ) )->format( 'Y-m-t' ); }
        }
        ob_start(); ?>
        <form class="gbk-filters" method="get" action="<?php echo esc_url( get_permalink() ?: home_url( '/' ) ); ?>">
            <?php if ( ! get_option( 'permalink_structure' ) && is_page() ) : ?><input type="hidden" name="page_id" value="<?php echo absint( get_queried_object_id() ); ?>"><?php endif; ?>
            <label>Game<select name="gbk_game"><option value="0">All games</option><?php foreach ( SRM_Games::get_active_games() as $game ) : ?><option value="<?php echo absint( $game->id ); ?>" <?php selected( $args['game_id'] ?? 0, $game->id ); ?>><?php echo esc_html( $game->event_name ); ?></option><?php endforeach; ?></select></label>
            <label>Month<input type="month" name="gbk_month" value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $_GET['gbk_month'] ?? '' ) ) ); ?>"></label>
            <label>Exact date<input type="date" name="gbk_date" value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $_GET['gbk_date'] ?? '' ) ) ); ?>"></label>
            <button type="submit">Find records</button><a href="<?php echo esc_url( get_permalink() ?: home_url( '/' ) ); ?>">Reset</a>
        </form><p class="gbk-table-note">An exact date takes priority over month. Times use the website timezone.</p>
        <?php
        if ( isset( $args['error'] ) ) { echo '<p role="alert">' . esc_html( $args['error'] ) . '</p>'; }
        else {
            $data = SRM_Results::query( $args );
            echo self::table( $data['rows'] ); // Escaped by table().
            if ( $data['pages'] > 1 ) {
                echo '<nav class="gbk-pagination" aria-label="Record pages">' . wp_kses_post( paginate_links( array( 'base' => esc_url_raw( add_query_arg( 'gbk_page', '%#%' ) ), 'format' => '', 'current' => $args['page'], 'total' => $data['pages'] ) ) ) . '</nav>';
            }
        }
        return (string) ob_get_clean();
    }
}
