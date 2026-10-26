<?php
/** Client-provided schedules only. No historical result seeding. */
defined( 'ABSPATH' ) || exit;
class SRM_Seed_Data {
    public static function seed_all(): void { self::seed_games(); }
    public static function seed_games(): void {
        global $wpdb;
        $table = SRM_Database::events_table();
        if ( (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" ) > 0 ) { return; }
        $games = array(
            array( 'Model Town', 'मॉडल टाउन', 'model-town', '12:40:00' ),
            array( 'Sadar Bazar', 'सदर बाजार', 'sadar-bazar', '13:25:00' ),
            array( 'Gwalior', 'ग्वालियर', 'gwalior', '14:25:00' ),
            array( 'Delhi Bazar', 'दिल्ली बाजार', 'delhi-bazar', '14:50:00' ),
            array( 'Punjab Matka', 'पंजाब मटका', 'punjab-matka', '15:40:00' ),
            array( 'Shri Ganesh', 'श्री गणेश', 'shri-ganesh', '16:25:00' ),
            array( 'Agra', 'आगरा', 'agra', '17:20:00' ),
            array( 'Faridabad', 'फरीदाबाद', 'faridabad', '17:55:00' ),
            array( 'Alwar', 'अलवर', 'alwar', '19:25:00' ),
            array( 'HR Matka', 'HR मटका', 'hr-matka', '20:15:00' ),
            array( 'Ghaziabad', 'गाज़ियाबाद', 'gaziabad', '21:20:00' ),
            array( 'HUDA City', 'हुडा सिटी', 'huda-city', '22:40:00' ),
            array( 'Gali', 'गली', 'gali', '23:25:00' ),
            array( 'Disawar', 'दिसावर', 'dishavar', '01:00:00' ),
        );
        foreach ( $games as $i => $game ) {
            $wpdb->insert( $table, array( 'event_name' => $game[0], 'event_name_secondary' => $game[1], 'slug' => $game[2], 'scheduled_time' => $game[3], 'display_order' => $i + 1, 'status' => 1, 'created_at' => current_time( 'mysql' ), 'updated_at' => current_time( 'mysql' ) ) );
        }
    }
}
