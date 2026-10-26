<?php
/** Calendar-aware schedule service. Times are informational, not result promises. */
defined( 'ABSPATH' ) || exit;
class SRM_Current_Event {
    public static function occurrences( ?DateTimeImmutable $now = null ): array {
        $now = $now ? $now->setTimezone( wp_timezone() ) : current_datetime();
        $next = null; $previous = null;
        foreach ( SRM_Games::get_active_games() as $game ) {
            $today = new DateTimeImmutable( $now->format( 'Y-m-d' ) . ' ' . $game->scheduled_time, wp_timezone() );
            $future = $today > $now ? $today : $today->modify( '+1 day' );
            $past = $today <= $now ? $today : $today->modify( '-1 day' );
            if ( ! $next || $future < $next['at'] ) { $next = array( 'game' => $game, 'at' => $future ); }
            if ( ! $previous || $past > $previous['at'] ) { $previous = array( 'game' => $game, 'at' => $past ); }
        }
        return array( 'next' => $next, 'previous' => $previous );
    }
    public static function get_next_game(): ?object { return self::occurrences()['next']['game'] ?? null; }
    public static function get_current_game(): ?object { return self::occurrences()['previous']['game'] ?? null; }
    public static function is_next_game_tomorrow(): bool { $next = self::occurrences()['next']; return $next && $next['at']->format( 'Y-m-d' ) > wp_date( 'Y-m-d' ); }
    public static function get_next_game_timestamp(): int { return isset( self::occurrences()['next']['at'] ) ? self::occurrences()['next']['at']->getTimestamp() : 0; }
    public static function get_remaining_seconds(): int { return max( 0, self::get_next_game_timestamp() - time() ); }
    public static function build_live_status_data(): array {
        $data = array( 'timezone' => wp_timezone_string(), 'today_date' => wp_date( 'Y-m-d' ), 'server_ts' => time() );
        foreach ( self::occurrences() as $key => $item ) {
            $data[ $key . '_game' ] = $item ? array( 'id' => (int) $item['game']->id, 'name' => $item['game']->event_name, 'time' => SRM_Games::format_time( $item['game']->scheduled_time ), 'date' => $item['at']->format( 'Y-m-d' ), 'timestamp' => $item['at']->getTimestamp() ) : null;
        }
        $previous = self::occurrences()['previous'];
        $data['current_game'] = $data['previous_game'];
        if ( $previous && $data['current_game'] ) {
            $record = SRM_Results::get_result( (int) $previous['game']->id, $previous['at']->format( 'Y-m-d' ) );
            $data['current_game']['result'] = $record ? $record->result_value : null;
            $data['current_game']['status'] = $record ? 'published' : 'pending';
            $data['current_game']['source_url'] = $record ? $record->source_url : null;
        }
        $data['latest_game'] = $data['current_game'];
        $data['server_time'] = current_time( 'mysql' );
        if ( $data['next_game'] ) { $data['next_game']['is_tomorrow'] = self::is_next_game_tomorrow(); }
        return $data;
    }
}
