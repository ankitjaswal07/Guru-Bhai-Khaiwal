<?php
/** Neutral new-install defaults. Never overwrite saved site content. */
defined( 'ABSPATH' ) || exit;
function s20_brand_defaults() {
    return array(
        'shortcode_empty_behavior'=>'message', 'shortcode_empty_message'=>'The informational archive is not available. Please check back later.',
        'site_title' => 'Guru Bhai Khaiwal', 'load_google_fonts' => 0, 'header_marquee_scroll'=>0,
        'site_phone' => '', 'site_whatsapp' => '', 'site_email' => '', 'site_telegram' => '',
        'hero_heading' => 'Guru Bhai Khaiwal', 'hero_description' => 'Daily schedules and source-reviewed historical records. / दैनिक समय-सारणी और ऐतिहासिक रिकॉर्ड।',
        'hero_pill_text' => 'Information only · Missing results remain blank · No predictions',
        'header_marquee_text' => 'Guru Bhai Khaiwal · Schedules follow the website timezone · Check the source and update date for each published record.',
        'games_heading' => 'Daily Game Schedule / दैनिक समय-सारणी', 'games_subheading' => 'Guru Bhai Khaiwal',
        'games_timing_heading' => 'Scheduled local times', 'games_show_payment' => 0, 'payment_enabled' => 0,
        'payment_text' => '', 'payment_phone' => '', 'payment_tagline' => '', 'payment_closing_heading' => '',
        'contact_direct_heading' => 'Contact the editorial team', 'contact_direct_description' => 'Report a correction or ask about a published source.',
        'contact_direct_name' => 'Guru Bhai Khaiwal', 'contact_us_name' => 'Guru Bhai Khaiwal',
        'contact_direct_phone_text' => '', 'contact_direct_phone' => '', 'contact_direct_whatsapp' => '', 'contact_direct_note' => '',
        'contact_us_phone_text' => '', 'contact_us_phone' => '', 'contact_us_whatsapp' => '', 'contact_us_note' => '',
        'contact_us_description' => 'Questions about schedules, sources or corrections are welcome.',
        'promo_enabled' => 0, 'contact_direct_enabled' => 0, 'contact_us_enabled' => 0,
        'blog_heading' => 'Latest Updates', 'blog_description' => 'Editorial updates, source notes and corrections.',
        'faq_home_description' => 'How schedules, sources and publication review work.', 'faq_description' => 'How schedules, sources and publication review work.',
        'footer_tagline' => 'Schedules & source-reviewed records', 'footer_description' => 'An informational archive. Records are published after editorial source review; completeness and accuracy are not guaranteed.',
        'footer_age_text' => 'INFORMATION ONLY', 'copyright_text' => '© {year} Guru Bhai Khaiwal. All rights reserved.',
        'games_shortcode' => '[srm_active_games]', 'live_status_shortcode' => '[srm_live_status]',
        'today_results_shortcode' => '[srm_today_results]', 'yesterday_results_shortcode' => '[srm_yesterday_results]',
        'today_yesterday_shortcode' => '[srm_today_yesterday_results]', 'current_month_results_shortcode' => '[srm_full_month_results]',
        'previous_month_results_shortcode' => '[srm_previous_month_results]', 'result_chart_shortcode' => '[gbk_archive]',
        'plugin_admin_url' => 'admin.php?page=srm-dashboard', 'seo_description' => 'Guru Bhai Khaiwal: editable daily schedules and source-reviewed historical records. Information only; no predictions or wagering.',
    );
}
