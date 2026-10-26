<?php
/**
 * User access control for specific games/results.
 *
 * @package ScheduledResultsManager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SRM_Access {
    public static function can_edit_game(int $id): bool {
        return self::current_user_has_full_access() || (current_user_can(self::capability()) && in_array($id,self::get_user_allowed_game_ids(),true));
    }


    /**
     * Capability used for users with game-specific access.
     */
    public static function capability(): string {
        return 'srm_manage_game_results';
    }

    /**
     * Check whether the current user has full plugin access.
     */
    public static function current_user_has_full_access(): bool {
        return current_user_can( 'manage_scheduled_results' ) || current_user_can( 'manage_options' );
    }

    /**
     * Check whether a user can access the plugin at all.
     */
    public static function current_user_can_access_plugin(): bool {
        return self::current_user_has_full_access() || current_user_can( self::capability() );
    }

    /**
     * Only enforce game-specific filtering in admin/backend contexts.
     * Frontend shortcodes must always show all games/results.
     */
    public static function should_enforce_user_game_restrictions(): bool {
        if ( ! is_user_logged_in() ) {
            return false;
        }

        if ( ! is_admin() ) {
            return false;
        }

        if ( self::current_user_has_full_access() ) {
            return false;
        }

        return current_user_can( self::capability() );
    }

    /**
     * Get the list of allowed event IDs for a user.
     *
     * @param int $user_id Optional user ID. Defaults to current user.
     * @return int[]
     */
    public static function get_user_allowed_game_ids( int $user_id = 0 ): array {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }

        if ( ! $user_id ) {
            return array();
        }

        $ids = get_user_meta( $user_id, 'srm_allowed_games', true );
        if ( empty( $ids ) ) {
            return array();
        }

        if ( is_array( $ids ) ) {
            $ids = array_map( 'intval', $ids );
        } else {
            $ids = array_filter( array_map( 'intval', preg_split( '/[\s,]+/', (string) $ids ) ?: array() ) );
        }

        return array_values( array_unique( array_filter( $ids ) ) );
    }

    /**
     * Check whether a given user can access a specific game.
     *
     * @param int $event_id Game ID.
     * @param int $user_id Optional user ID. Defaults to current user.
     */
    public static function user_can_access_game( int $event_id, int $user_id = 0 ): bool {
        if ( ! $event_id ) {
            return false;
        }

        if ( ! self::should_enforce_user_game_restrictions() ) {
            return true;
        }

        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }

        if ( ! $user_id ) {
            return false;
        }

        if ( self::current_user_has_full_access() || ( $user_id === get_current_user_id() && current_user_can( 'manage_scheduled_results' ) ) ) {
            return true;
        }

        $allowed = self::get_user_allowed_game_ids( $user_id );
        return in_array( $event_id, $allowed, true );
    }

    /**
     * Returns filtered IDs for a user.
     *
     * @param int[] $ids Original IDs.
     * @param int   $user_id Optional user ID. Defaults to current user.
     * @return int[]
     */
    public static function filter_allowed_game_ids( array $ids, int $user_id = 0 ): array {
        if ( ! $ids ) {
            return array();
        }

        if ( ! self::should_enforce_user_game_restrictions() ) {
            return array_values( array_unique( array_map( 'intval', $ids ) ) );
        }

        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }

        if ( self::current_user_has_full_access() || ( $user_id === get_current_user_id() && current_user_can( 'manage_scheduled_results' ) ) ) {
            return array_values( array_unique( array_map( 'intval', $ids ) ) );
        }

        $allowed = self::get_user_allowed_game_ids( $user_id );
        if ( empty( $allowed ) ) {
            return array();
        }

        $allowed_map = array_flip( $allowed );
        $filtered    = array();

        foreach ( $ids as $id ) {
            $event_id = (int) $id;
            if ( isset( $allowed_map[ $event_id ] ) ) {
                $filtered[] = $event_id;
            }
        }

        return $filtered;
    }

    /**
     * Save allowed games for a user.
     *
     * @param int   $user_id User ID.
     * @param int[] $game_ids Selected game IDs.
     */
    public static function save_user_allowed_games( int $user_id, array $game_ids ): void {
        $ids = array_values( array_unique( array_map( 'intval', $game_ids ) ) );
        update_user_meta( $user_id, 'srm_allowed_games', $ids );

        $user = get_user_by( 'id', $user_id );
        if ( ! $user ) {
            return;
        }

        if ( ! empty( $ids ) ) {
            $user->add_cap( self::capability() );
        } else {
            $user->remove_cap( self::capability() );
        }
    }
}
