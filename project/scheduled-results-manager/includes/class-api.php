<?php
/** Read-only public endpoints. No draft or unverified values leave the repository. */
defined( 'ABSPATH' ) || exit;
class SRM_API {
    const NAMESPACE = 'srm/v1';
    public static function register_routes(): void {
        foreach ( array( 'games', 'events', 'today', 'monthly', 'current' ) as $route ) {
            register_rest_route( self::NAMESPACE, '/' . $route, array(
                'methods' => 'GET', 'permission_callback' => '__return_true',
                'callback' => function ( WP_REST_Request $request ) use ( $route ) {
                    switch ( $route ) {
                        case 'games': case 'events': $data = self::games(); break;
                        case 'current': $data = SRM_Current_Event::build_live_status_data(); break;
                        case 'today': $data = array( 'date' => wp_date( 'Y-m-d' ), 'results' => self::public_rows( SRM_Results::get_today_results() ) ); break;
                        default:
                            $year = (int) ( $request['year'] ?? wp_date( 'Y' ) );
                            $month = (int) ( $request['month'] ?? wp_date( 'n' ) );
                            $rows = SRM_Results::get_monthly_results( $year, $month );
                            foreach ( $rows as &$day ) { $day = self::public_rows( array_values( $day ) ); } unset( $day );
                            $grid = array();
                            $games = self::games();
                            $days = (int) ( new DateTimeImmutable( sprintf( '%04d-%02d-01', $year, $month ), wp_timezone() ) )->format( 't' );
                            for ( $day = 1; $day <= $days; ++$day ) {
                                $date = sprintf( '%04d-%02d-%02d', $year, $month, $day );
                                $values = array();
                                foreach ( $games as $game ) { $values[ $game['id'] ] = null; }
                                foreach ( $rows[ $date ] ?? array() as $record ) { $values[ $record['game_id'] ] = $record['result']; }
                                $grid[] = array( 'date' => $date, 'results' => $values );
                            }
                            $data = array( 'year' => $year, 'month' => $month, 'games' => $games, 'rows' => $grid, 'results' => $rows );
                    }
                    $response = new WP_REST_Response( $data );
                    $response->header( 'X-Robots-Tag', 'noindex' );
                    $response->header( 'Cache-Control', 'no-store' );
                    return $response;
                },
                'args' => array(
                    'year' => array( 'type' => 'integer', 'minimum' => 1900, 'maximum' => 2100 ),
                    'month' => array( 'type' => 'integer', 'minimum' => 1, 'maximum' => 12 ),
                ),
            ) );
        }
    }
    public static function games(): array {
        return array_map( static fn( $game ) => array( 'id' => (int) $game->id, 'name' => $game->event_name, 'name_secondary' => $game->event_name_secondary ?? '', 'time' => SRM_Games::format_time( $game->scheduled_time ), 'time_24' => $game->scheduled_time, 'display_order' => (int) $game->display_order ), SRM_Games::get_active_games() );
    }
    public static function public_rows( array $rows ): array {
        return array_map( static fn( $r ) => array( 'game_id' => (int) $r->event_id, 'game_name' => $r->event_name, 'date' => $r->result_date, 'result' => $r->result_value, 'status' => 'published', 'scheduled_time' => SRM_Games::format_time( $r->scheduled_time ), 'source_url' => $r->source_url, 'original_game_name' => $r->original_game_name, 'verification_status' => $r->verification_status, 'updated_at' => $r->updated_at, 'updated_timezone' => 'UTC' ), $rows );
    }
}
