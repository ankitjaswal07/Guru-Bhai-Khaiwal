<?php
/** Native theme controls replace unlicensed ACF PRO options/repeaters. */
defined( 'ABSPATH' ) || exit;
function gbk_defaults(): array {
    return array(
        'hero_assurance_one' => 'Source-reviewed records', 'hero_assurance_two' => 'Hindi & English',
        'hero_strip' => 'Daily schedules | Historical archives | Transparent sources | No predictions',
        'desk_label' => 'The information desk', 'desk_note' => 'जानकारी, ज़िम्मेदारी के साथ।',
        'principle_one' => 'Check the source', 'principle_two' => 'Keep the context', 'principle_three' => 'Leave gaps visible',
        'footer_explore' => 'Explore', 'footer_contact' => 'Stay informed',
        'footer_source_text' => 'Published records include their source. Missing records stay missing.',
        'footer_tagline' => 'Independent. Informational. Source-reviewed.',
        'contact_empty' => 'Contact details have not yet been published.',
        'navy' => '#142536', 'gold' => '#e5b85c', 'paper' => '#f6f5f1', 'heading_font' => 'sans',
        'eyebrow' => 'SCHEDULES · RECORDS · INFORMATION', 'monogram' => 'GB',
        'hero_title' => 'Clear information.\nA reliable record.',
        'hero_text' => 'Welcome to Guru Bhai Khaiwal. Explore daily schedules and source-reviewed historical records, in one simple, accessible place.',
        'hero_button' => 'Explore the archives', 'schedule_button' => 'View daily schedule',
        'notice' => 'For information only. No betting, predictions or financial transactions.',
        'email' => '', 'phone' => '', 'address' => '', 'social_one' => '', 'social_two' => '',
        'social_one_label' => 'Social channel', 'social_two_label' => 'Community updates',
        'archive_page' => 0, 'seo_description' => 'Guru Bhai Khaiwal: daily informational schedules and source-reviewed historical archives. Browse records by game, date and month.',
        'footer_text' => 'An independent informational archive. Historical records do not predict future outcomes. We do not offer betting or payment services.',
        'about_text' => 'Information, with context. Guru Bhai Khaiwal brings schedules and historical records into a clear, easy-to-browse archive. Every published record requires a source and an editorial review. When information is missing or disputed, we leave it unpublished rather than fill the gap.',
        'contact_text' => 'Have a question about the archive or spotted a record that needs checking? Please include the game name, date and a source link when requesting a correction.',
    );
}
function gbk_option( string $key ) { return get_theme_mod( 'gbk_' . $key, gbk_defaults()[ $key ] ?? '' ); }
function gbk_section_defaults(): array {
    return array(
        'hero' => array( 'Welcome', '' ),
        'schedule' => array( 'Your daily schedule', 'दैनिक समय सारणी · All times follow the website timezone.' ),
        'latest' => array( 'Latest published records', 'A clear view of the most recently dated, source-reviewed records.' ),
        'daily' => array( 'Today & yesterday', 'Published records only. A missing value is not a zero.' ),
        'current_month' => array( 'This month, at a glance', 'Explore the current month’s historical record.' ),
        'previous_month' => array( 'The previous month', 'A month-by-month view, with source links for each published value.' ),
        'archive' => array( 'Find a historical record', 'Select a game, month or exact date to explore the archive.' ),
        'about' => array( 'Built around clarity.', 'ABOUT GURU BHAI KHAIWAL' ),
        'contact' => array( 'Let’s keep the record clear.', 'CONTACT & CORRECTIONS' ),
        'faq' => array( 'A little more clarity', 'Answers to common questions about schedules, sources and missing records.' ),
        'blog' => array( 'Notes from the archive', 'Information, updates and editorial notes.' ),
    );
}
function gbk_sanitize_bool( $value ): bool { return (bool) $value; }
function gbk_sanitize_font( $value ): string { return in_array( $value, array( 'sans', 'serif' ), true ) ? $value : 'sans'; }
add_action( 'customize_register', function ( $wp_customize ) {
    $wp_customize->add_panel( 'gbk', array( 'title' => 'Guru Bhai Khaiwal', 'priority' => 30, 'description' => 'Edit branding and homepage sections. Site title, tagline, logo and favicon are in Site Identity. FAQ questions use the FAQs menu; articles use Posts.' ) );
    $wp_customize->add_section( 'gbk_brand', array( 'title' => 'Brand, contact & SEO', 'panel' => 'gbk' ) );
    $labels = array( 'navy' => 'Primary navy', 'gold' => 'Warm gold accent', 'paper' => 'Page background', 'heading_font' => 'Heading typography', 'eyebrow' => 'Hero eyebrow', 'monogram' => 'Fallback logo initials', 'hero_title' => 'Hero title (use a line break)', 'hero_text' => 'Hero introduction', 'hero_button' => 'Archive button label', 'schedule_button' => 'Schedule button label', 'notice' => 'Information notice', 'email' => 'Public contact email', 'phone' => 'Public telephone', 'address' => 'Contact / address text', 'social_one' => 'First social URL', 'social_two' => 'Second social URL', 'social_one_label' => 'First social label', 'social_two_label' => 'Second social label', 'archive_page' => 'Archive page', 'seo_description' => 'Default / homepage SEO description', 'footer_text' => 'Footer notice', 'about_text' => 'About text', 'contact_text' => 'Contact introduction' );
    foreach ( gbk_defaults() as $key => $default ) {
        $type = in_array( $key, array( 'navy', 'gold', 'paper' ), true ) ? 'color' : ( str_contains( $key, 'text' ) || in_array( $key, array( 'hero_title', 'seo_description', 'notice' ), true ) ? 'textarea' : 'text' );
        $sanitize = 'textarea' === $type ? 'sanitize_textarea_field' : 'sanitize_text_field';
        if ( 'color' === $type ) { $sanitize = 'sanitize_hex_color'; }
        if ( 'email' === $key ) { $type = 'email'; $sanitize = 'sanitize_email'; }
        if ( in_array( $key, array( 'social_one', 'social_two' ), true ) ) { $type = 'url'; $sanitize = 'esc_url_raw'; }
        if ( 'archive_page' === $key ) { $type = 'dropdown-pages'; $sanitize = 'absint'; }
        if ( 'heading_font' === $key ) { $type = 'select'; $sanitize = 'gbk_sanitize_font'; }
        $wp_customize->add_setting( 'gbk_' . $key, array( 'default' => $default, 'sanitize_callback' => $sanitize ) );
        $control = array( 'label' => $labels[ $key ] ?? ucwords( str_replace( '_', ' ', $key ) ), 'section' => 'gbk_brand', 'type' => $type, 'choices' => array( 'sans' => 'System sans-serif', 'serif' => 'Editorial serif' ) );
        if ( 'color' === $type ) {
            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'gbk_' . $key, $control ) );
        } else {
            $wp_customize->add_control( 'gbk_' . $key, $control );
        }
    }
    $i = 0;
    foreach ( gbk_section_defaults() as $key => $defaults ) {
        ++$i; $section = 'gbk_section_' . $key;
        $wp_customize->add_section( $section, array( 'title' => sprintf( '%02d · %s', $i, $defaults[0] ), 'panel' => 'gbk' ) );
        foreach ( array( 'enabled' => array( true, 'checkbox', 'gbk_sanitize_bool', 'Show this section' ), 'order' => array( $i * 10, 'number', 'absint', 'Display order (lowest first)' ), 'title' => array( $defaults[0], 'text', 'sanitize_text_field', 'Section heading' ), 'description' => array( $defaults[1], 'textarea', 'sanitize_textarea_field', 'Section description' ) ) as $field => $config ) {
            $id = 'gbk_' . $key . '_' . $field;
            $wp_customize->add_setting( $id, array( 'default' => $config[0], 'sanitize_callback' => $config[2] ) );
            $wp_customize->add_control( $id, array( 'label' => $config[3], 'section' => $section, 'type' => $config[1] ) );
        }
        $wp_customize->add_setting( 'gbk_' . $key . '_image', array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
        $wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'gbk_' . $key . '_image', array( 'label' => 'Optional section image (set alt text in Media)', 'section' => $section, 'mime_type' => 'image' ) ) );
    }
} );
