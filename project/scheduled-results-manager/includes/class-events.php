<?php
/**
 * Games Management Class
 *
 * @package ScheduledResultsManager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class SRM_Games
 */
class SRM_Games {

    /**
     * Get all active events ordered by display_order then scheduled_time.
     *
     * @return array<object>
     */
    public static function get_active_games(): array {
        global $wpdb;
        $table = SRM_Database::events_table();
        $query = "SELECT * FROM {$table} WHERE status = 1";

        if ( SRM_Access::should_enforce_user_game_restrictions() ) {
            $allowed = SRM_Access::get_user_allowed_game_ids();
            if ( empty( $allowed ) ) {
                return array();
            }
            $placeholders = implode( ',', array_fill( 0, count( $allowed ), '%d' ) );
            $query       .= " AND id IN ({$placeholders})";
            $query       = $wpdb->prepare( $query, $allowed );
        }

        $query .= " ORDER BY display_order ASC, scheduled_time ASC";

        return $wpdb->get_results( $query ) ?: array();
    }

    /**
     * Get all events (active and inactive).
     *
     * @return array<object>
     */
    public static function get_all_games(): array {
        global $wpdb;
        $table = SRM_Database::events_table();
        $query = "SELECT * FROM {$table}";

        if ( SRM_Access::should_enforce_user_game_restrictions() ) {
            $allowed = SRM_Access::get_user_allowed_game_ids();
            if ( empty( $allowed ) ) {
                return array();
            }
            $placeholders = implode( ',', array_fill( 0, count( $allowed ), '%d' ) );
            $query      .= " WHERE id IN ({$placeholders})";
            $query       = $wpdb->prepare( $query, $allowed );
        }

        $query .= " ORDER BY display_order ASC, scheduled_time ASC";

        return $wpdb->get_results( $query ) ?: array();
    }

    /**
     * Get a single event by ID.
     *
     * @param int $id Event ID.
     * @return object|null
     */
    public static function get_game( int $id ): ?object {
        global $wpdb;
        $table = SRM_Database::events_table();
        $game  = $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $id )
        );

        if ( ! $game ) {
            return null;
        }

        if ( ! SRM_Access::should_enforce_user_game_restrictions() ) {
            return $game;
        }

        if ( ! SRM_Access::user_can_access_game( (int) $game->id ) ) {
            return null;
        }

        return $game;
    }

    /**
     * Get event by slug.
     *
     * @param string $slug
     * @return object|null
     */
    public static function get_game_by_slug( string $slug ): ?object {
        global $wpdb;
        $table = SRM_Database::events_table();
        return $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM {$table} WHERE slug = %s", $slug )
        );
    }

    /**
     * Create a new event.
     *
     * @param array<string,mixed> $data
     * @return int|WP_Error New event ID or error.
     */
    public static function create_game( array $data ): int|WP_Error {
        if ( ! SRM_Access::current_user_has_full_access() ) { return new WP_Error( 'forbidden', 'Permission denied.' ); }
        global $wpdb;

        $table = SRM_Database::events_table();
        $now   = current_time( 'mysql', true );

        if(mb_strlen($data['event_name']??'')>150 || mb_strlen($data['event_name_secondary']??'')>150)return new WP_Error('name','Names must be at most 150 characters.');
        // Validate required fields
        if ( empty( $data['event_name'] ) ) {
            return new WP_Error( 'missing_name', __( 'Game name is required.', 'scheduled-results-manager' ) );
        }
        if ( empty( $data['scheduled_time'] ) ) {
            return new WP_Error( 'missing_time', __( 'Scheduled time is required.', 'scheduled-results-manager' ) );
        }

        // Validate and sanitize time
        $time = self::sanitize_time( $data['scheduled_time'] );
        if ( ! $time ) {
            return new WP_Error( 'invalid_time', __( 'Invalid time format.', 'scheduled-results-manager' ) );
        }

        $base = sanitize_title( $data['event_name'] );
        if ( ! $base || strlen( $base ) > 140 ) { $base = 'game-' . substr( hash( 'sha256', $data['event_name'] ), 0, 12 ); }
        $slug = self::generate_unique_slug( $base );

        $result = $wpdb->insert(
            $table,
            array(
                'event_name'           => sanitize_text_field( $data['event_name'] ),
                'event_name_secondary' => ! empty( $data['event_name_secondary'] ) ? sanitize_text_field( $data['event_name_secondary'] ) : null,
                'slug'                 => $slug,
                'scheduled_time'       => $time,
                'display_order'        => absint( $data['display_order'] ?? 0 ),
                'status'               => isset( $data['status'] ) ? ( $data['status'] ? 1 : 0 ) : 1,
                'created_at'           => $now,
                'updated_at'           => $now,
            ),
            array( '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s' )
        );

        if ( false === $result ) {
            return new WP_Error( 'db_error', __( 'Database error while creating game.', 'scheduled-results-manager' ) );
        }

        return (int) $wpdb->insert_id;
    }

    /**
     * Update an existing event.
     *
     * @param int                 $id   Event ID.
     * @param array<string,mixed> $data Updated fields.
     * @return bool|WP_Error
     */
    public static function update_game( int $id, array $data ): bool|WP_Error {
        if(mb_strlen($data['event_name']??'')>150||mb_strlen($data['event_name_secondary']??'')>150)return new WP_Error('name','Names must be at most 150 characters.');
        if ( ! SRM_Access::current_user_has_full_access() ) { return new WP_Error( 'forbidden', 'Permission denied.' ); }
        global $wpdb;

        $table = SRM_Database::events_table();
        $now   = current_time( 'mysql', true );

        if ( empty( $data['event_name'] ) ) {
            return new WP_Error( 'missing_name', __( 'Game name is required.', 'scheduled-results-manager' ) );
        }
        if ( empty( $data['scheduled_time'] ) ) {
            return new WP_Error( 'missing_time', __( 'Scheduled time is required.', 'scheduled-results-manager' ) );
        }

        $time = self::sanitize_time( $data['scheduled_time'] );
        if ( ! $time ) {
            return new WP_Error( 'invalid_time', __( 'Invalid time format.', 'scheduled-results-manager' ) );
        }

        $result = $wpdb->update(
            $table,
            array(
                'event_name'           => sanitize_text_field( $data['event_name'] ),
                'event_name_secondary' => ! empty( $data['event_name_secondary'] ) ? sanitize_text_field( $data['event_name_secondary'] ) : null,
                'scheduled_time'       => $time,
                'display_order'        => absint( $data['display_order'] ?? 0 ),
                'status'               => isset( $data['status'] ) ? ( $data['status'] ? 1 : 0 ) : 1,
                'updated_at'           => $now,
            ),
            array( 'id' => $id ),
            array( '%s', '%s', '%s', '%d', '%d', '%s' ),
            array( '%d' )
        );

        if ( false === $result ) {
            return new WP_Error( 'db_error', __( 'Database error while updating game.', 'scheduled-results-manager' ) );
        }

        return true;
    }

    /**
     * Delete an event and its results.
     *
     * @param int $id Event ID.
     * @return bool
     */
    public static function delete_game( int $id ): bool {
        // Non-destructive replacement: retain historical references.
        return false;
    }

    /**
     * Duplicate an event.
     *
     * @param int $id Source event ID.
     * @return int|WP_Error New event ID or error.
     */
    public static function duplicate_game( int $id ): int|WP_Error {
        $event = self::get_game( $id );
        if ( ! $event ) {
            return new WP_Error( 'not_found', __( 'Game not found.', 'scheduled-results-manager' ) );
        }

        return self::create_game( array(
            'event_name'           => $event->event_name . ' (Copy)',
            'event_name_secondary' => $event->event_name_secondary,
            'scheduled_time'       => $event->scheduled_time,
            'display_order'        => (int) $event->display_order + 1,
            'status'               => 0, // Duplicated events start as inactive
        ) );
    }

    /**
     * Toggle event status.
     *
     * @param int $id Event ID.
     * @return bool|WP_Error
     */
    public static function toggle_status( int $id ): bool|WP_Error {
        if ( ! SRM_Access::current_user_has_full_access() ) { return new WP_Error( 'forbidden', 'Permission denied.' ); }
        global $wpdb;

        $event = self::get_game( $id );
        if ( ! $event ) {
            return new WP_Error( 'not_found', __( 'Game not found.', 'scheduled-results-manager' ) );
        }

        $new_status = $event->status ? 0 : 1;
        $result     = $wpdb->update(
            SRM_Database::events_table(),
            array( 'status' => $new_status, 'updated_at' => current_time( 'mysql' ) ),
            array( 'id' => $id ),
            array( '%d', '%s' ),
            array( '%d' )
        );

        return false !== $result;
    }

    /**
     * AJAX: Save event display order.
     */
    public static function ajax_save_order(): void {
        check_ajax_referer( 'srm_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_scheduled_results' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'scheduled-results-manager' ) ) );
        }

        $order = isset( $_POST['order'] ) ? array_map( 'absint', (array) $_POST['order'] ) : array();

        global $wpdb;
        $table = SRM_Database::events_table();
        $now   = current_time( 'mysql', true );

        foreach ( $order as $position => $event_id ) {
            $wpdb->update(
                $table,
                array( 'display_order' => $position, 'updated_at' => $now ),
                array( 'id' => $event_id ),
                array( '%d', '%s' ),
                array( '%d' )
            );
        }

        wp_send_json_success( array( 'message' => __( 'Order saved.', 'scheduled-results-manager' ) ) );
    }

    /**
     * AJAX: Toggle event status.
     */
    public static function ajax_toggle_status(): void {
        check_ajax_referer( 'srm_admin_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_scheduled_results' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'scheduled-results-manager' ) ) );
        }

        $event_id = absint( $_POST['event_id'] ?? 0 );
        $result   = self::toggle_status( $event_id );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        wp_send_json_success( array( 'message' => __( 'Game status updated.', 'scheduled-results-manager' ) ) );
    }

    /**
     * Format a TIME db value to display format.
     *
     * @param string $time  Time in HH:MM:SS.
     * @param string $format PHP date format.
     * @return string
     */
    public static function format_time( string $time, string $format = '' ): string {
        if ( empty( $format ) ) {
            $format = get_option('srm_time_format',get_option( 'time_format', 'g:i A' ));
        }
        $date = DateTimeImmutable::createFromFormat( '!H:i:s', $time, wp_timezone() );
        return $date ? wp_date( $format, $date->getTimestamp(), wp_timezone() ) : $time;
    }

    /**
     * Sanitize a time string to HH:MM:SS.
     *
     * @param string $time
     * @return string|false
     */
    public static function sanitize_time( string $time ): string|false {
        // Accept HH:MM or HH:MM:SS
        $time = trim( sanitize_text_field( $time ) );

        // Handle 12-hour input e.g. "01:10 PM"
        if ( preg_match( '/^(\d{1,2}):(\d{2})\s*(AM|PM)$/i', $time, $m ) ) {
            $timestamp = strtotime( $time );
            if ( (int) $m[1] < 1 || (int) $m[1] > 12 || (int) $m[2] > 59 || ! $timestamp ) {
                return false;
            }
            return date( 'H:i:s', $timestamp );
        }

        // Handle 24-hour input e.g. "13:10" or "13:10:00"
        if ( preg_match( '/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $time, $m ) ) {
            $h = (int) $m[1];
            $i = (int) $m[2];
            $s = isset( $m[3] ) ? (int) $m[3] : 0;

            if ( $h < 0 || $h > 23 || $i < 0 || $i > 59 || $s < 0 || $s > 59 ) {
                return false;
            }
            return sprintf( '%02d:%02d:%02d', $h, $i, $s );
        }

        return false;
    }

    /**
     * Generate a unique slug.
     *
     * @param string $base
     * @return string
     */
    private static function generate_unique_slug( string $base ): string {
        global $wpdb;
        $table  = SRM_Database::events_table();
        $slug   = $base;
        $suffix = 1;

        while ( $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE slug = %s", $slug ) ) ) {
            $slug = $base . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }
}
