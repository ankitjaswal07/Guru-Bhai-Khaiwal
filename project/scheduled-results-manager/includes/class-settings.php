<?php
/**
 * Settings Class
 *
 * @package ScheduledResultsManager
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class SRM_Settings
 */
class SRM_Settings {

    public static function validated(string $key, $value) {
        if(!is_scalar($value))$value='';
        $ranges=array('srm_ajax_interval'=>array(10,300),'srm_font_size'=>array(10,28),'srm_header_font_size'=>array(10,28),'srm_border_width'=>array(0,5),'srm_cell_padding'=>array(4,40),'srm_mobile_font_size'=>array(10,24));
        if(isset($ranges[$key]))return max($ranges[$key][0],min($ranges[$key][1],(int)$value));
        if(preg_match('/(?:_bg|_color|_accent)$/',$key))return sanitize_hex_color((string)$value)?:'#1e293b';
        if('srm_result_format'===$key)return in_array($value,array('any','numeric'),true)?$value:'numeric';
        if('srm_default_result_status'===$key)return 'published'===$value?'published':'pending';
        return mb_substr(sanitize_text_field((string)$value),0,100);
    }
    /**
     * Save settings from POST data.
     *
     * @param array<string,mixed> $post
     * @return void
     */
    public static function save( array $post ): void {
        if(!SRM_Access::current_user_has_full_access())return;
        $fields = array(
            'srm_plugin_name'           => 'sanitize_text_field',
            'srm_date_format'           => 'sanitize_text_field',
            'srm_time_format'           => 'sanitize_text_field',
            'srm_result_format'         => 'sanitize_key',
            'srm_default_result_status' => 'sanitize_key',
            'srm_ajax_interval'         => 'absint',
            'srm_header_bg'             => 'sanitize_hex_color',
            'srm_header_color'          => 'sanitize_hex_color',
            'srm_table_bg'              => 'sanitize_hex_color',
            'srm_alt_row_color'         => 'sanitize_hex_color',
            'srm_border_color'          => 'sanitize_hex_color',
            'srm_text_color'            => 'sanitize_hex_color',
            'srm_card_bg'               => 'sanitize_hex_color',
            'srm_card_color'            => 'sanitize_hex_color',
            'srm_card_accent'           => 'sanitize_hex_color',
            'srm_font_size'             => 'absint',
            'srm_header_font_size'      => 'absint',
            'srm_border_width'          => 'absint',
            'srm_cell_padding'          => 'absint',
            'srm_mobile_font_size'      => 'absint',
        );

        $checkboxes = array(
            'srm_allow_editing',
            'srm_allow_deletion',
            'srm_countdown_enabled',
            'srm_current_game_enabled',
            'srm_next_game_enabled',
            'srm_delete_on_uninstall',
        );

        foreach ( $fields as $key => $sanitizer ) {
            if ( isset( $post[ $key ] ) ) {
                $value = self::validated($key, $post[$key]);
                update_option( $key, $value );
            }
        }

        foreach ( $checkboxes as $key ) {
            update_option( $key, !in_array($key,array('srm_allow_deletion','srm_delete_on_uninstall'),true) && isset( $post[ $key ] ) ? '1' : '0' );
        }
    }

    /**
     * Get all settings as array.
     *
     * @return array<string,mixed>
     */
    public static function get_all(): array {
        return array(
            'plugin_name'              => get_option( 'srm_plugin_name', 'Scheduled Results Manager' ),
            'date_format'              => get_option( 'srm_date_format', 'd/m/Y' ),
            'time_format'              => get_option( 'srm_time_format', 'h:i A' ),
            'result_format'            => get_option( 'srm_result_format', 'numeric' ),
            'default_result_status'    => get_option( 'srm_default_result_status', 'pending' ),
            'ajax_interval'            => (int) get_option( 'srm_ajax_interval', 30 ),
            'allow_editing'            => get_option( 'srm_allow_editing', '1' ),
            'allow_deletion'           => '0',
            'countdown_enabled'        => get_option( 'srm_countdown_enabled', '1' ),
            'current_game_enabled'    => get_option( 'srm_current_game_enabled', '1' ),
            'next_game_enabled'       => get_option( 'srm_next_game_enabled', '1' ),
            'delete_on_uninstall'      => '0',
            'header_bg'                => get_option( 'srm_header_bg', '#1e293b' ),
            'header_color'             => get_option( 'srm_header_color', '#ffffff' ),
            'table_bg'                 => get_option( 'srm_table_bg', '#ffffff' ),
            'alt_row_color'            => get_option( 'srm_alt_row_color', '#f8fafc' ),
            'border_color'             => get_option( 'srm_border_color', '#e2e8f0' ),
            'text_color'               => get_option( 'srm_text_color', '#1e293b' ),
            'card_bg'                  => get_option( 'srm_card_bg', '#1e293b' ),
            'card_color'               => get_option( 'srm_card_color', '#ffffff' ),
            'card_accent'              => get_option( 'srm_card_accent', '#f59e0b' ),
            'font_size'                => (int) get_option( 'srm_font_size', 14 ),
            'header_font_size'         => (int) get_option( 'srm_header_font_size', 13 ),
            'border_width'             => (int) get_option( 'srm_border_width', 1 ),
            'cell_padding'             => (int) get_option( 'srm_cell_padding', 10 ),
            'mobile_font_size'         => (int) get_option( 'srm_mobile_font_size', 12 ),
            'timezone'                 => wp_timezone_string(),
        );
    }

    /**
     * Output CSS variables block for frontend.
     *
     * @return string
     */
    public static function get_css_vars(): string {
        $s = self::get_all();
        foreach($s as $key=>$value){$s[$key]=self::validated('srm_'.$key,$value);}
        return sprintf(
            ':root{
                --srm-header-bg:%s;
                --srm-header-color:%s;
                --srm-table-bg:%s;
                --srm-alt-row:%s;
                --srm-border-color:%s;
                --srm-text-color:%s;
                --srm-card-bg:%s;
                --srm-card-color:%s;
                --srm-card-accent:%s;
                --srm-font-size:%spx;
                --srm-header-font-size:%spx;
                --srm-border-width:%spx;
                --srm-cell-padding:%spx;
                --srm-mobile-font-size:%spx;
            }',
            esc_attr( $s['header_bg'] ),
            esc_attr( $s['header_color'] ),
            esc_attr( $s['table_bg'] ),
            esc_attr( $s['alt_row_color'] ),
            esc_attr( $s['border_color'] ),
            esc_attr( $s['text_color'] ),
            esc_attr( $s['card_bg'] ),
            esc_attr( $s['card_color'] ),
            esc_attr( $s['card_accent'] ),
            esc_attr( (string) $s['font_size'] ),
            esc_attr( (string) $s['header_font_size'] ),
            esc_attr( (string) $s['border_width'] ),
            esc_attr( (string) $s['cell_padding'] ),
            esc_attr( (string) $s['mobile_font_size'] )
        );
    }
}
