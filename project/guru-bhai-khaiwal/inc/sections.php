<?php
/** Presentation components. No game data or result values are stored in the theme. */
defined( 'ABSPATH' ) || exit;
function gbk_contact_links(): void {
    $email = sanitize_email( gbk_option( 'email' ) );
    $phone = preg_replace( '/[^0-9+]/', '', gbk_option( 'phone' ) );
    if ( $email ) { echo '<p><a href="' . esc_url( 'mailto:' . $email ) . '">' . esc_html( $email ) . '</a></p>'; }
    if ( $phone ) { echo '<p><a href="' . esc_url( 'tel:' . $phone ) . '">' . esc_html( gbk_option( 'phone' ) ) . '</a></p>'; }
    if ( gbk_option( 'address' ) ) { echo '<p>' . esc_html( gbk_option( 'address' ) ) . '</p>'; }
    foreach ( array( 'one', 'two' ) as $key ) {
        if ( gbk_option( 'social_' . $key ) ) { echo '<p><a href="' . esc_url( gbk_option( 'social_' . $key ) ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( gbk_option( 'social_' . $key . '_label' ) ) . ' ↗</a></p>'; }
    }
    if ( ! $email && ! $phone && ! gbk_option( 'address' ) ) { echo '<p class="muted">' . esc_html( gbk_option( 'contact_empty' ) ) . '</p>'; }
}
function gbk_faqs(): void {
    $faqs = new WP_Query( array( 'post_type' => 'gbk_faq', 'post_status' => 'publish', 'posts_per_page' => 50, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'ASC' ), 'no_found_rows' => true ) );
    echo '<div class="faq-list">';
    if ( ! $faqs->have_posts() ) { echo '<p>Frequently asked questions will be published here.</p>'; }
    while ( $faqs->have_posts() ) {
        $faqs->the_post();
        echo '<details><summary>' . esc_html( get_the_title() ) . '<span aria-hidden="true">+</span></summary><div class="faq-answer">'; the_content(); echo '</div></details>';
    }
    echo '</div>'; wp_reset_postdata();
}
function gbk_post_cards(): void {
    $posts = new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => 3, 'post_status' => 'publish', 'ignore_sticky_posts' => true, 'no_found_rows' => true ) );
    echo '<div class="post-grid">';
    if ( ! $posts->have_posts() ) { echo '<p class="muted">Editorial notes and informational articles will appear here when published.</p>'; }
    while ( $posts->have_posts() ) { $posts->the_post(); get_template_part( 'template-parts/post-card' ); }
    echo '</div>'; wp_reset_postdata();
}
function gbk_section( string $key ): void {
    $defaults = gbk_section_defaults()[ $key ];
    $title = get_theme_mod( 'gbk_' . $key . '_title', $defaults[0] );
    $description = get_theme_mod( 'gbk_' . $key . '_description', $defaults[1] );
    $image = absint( get_theme_mod( 'gbk_' . $key . '_image', 0 ) );
    if ( 'hero' === $key ) { ?>
        <section class="hero" id="hero" aria-label="<?php echo esc_attr( $title ); ?>"><div class="container hero-grid"><div class="hero-copy"><p class="eyebrow"><span class="gold-line"></span><?php echo esc_html( gbk_option( 'eyebrow' ) ); ?></p><h1><?php echo nl2br( esc_html( str_replace( '\n', "\n", gbk_option( 'hero_title' ) ) ) ); ?></h1><p class="hero-intro"><?php echo esc_html( gbk_option( 'hero_text' ) ); ?></p><?php if ( $description ) : ?><p><?php echo esc_html( $description ); ?></p><?php endif; ?><div class="hero-actions"><a class="button button-gold" href="<?php echo esc_url( gbk_archive_url() ); ?>"><?php echo esc_html( gbk_option( 'hero_button' ) ); ?> <span aria-hidden="true">↗</span></a><a class="text-link" href="#schedule"><?php echo esc_html( gbk_option( 'schedule_button' ) ); ?> ↓</a></div><div class="hero-assurance"><span>✓ <?php echo esc_html( gbk_option( 'hero_assurance_one' ) ); ?></span><span>✓ <?php echo esc_html( gbk_option( 'hero_assurance_two' ) ); ?></span></div></div><div class="hero-aside"><?php if ( $image ) { echo wp_get_attachment_image( $image, 'large', false, array( 'class' => 'hero-image', 'loading' => 'eager' ) ); } else { ?><div class="aside-top"><span class="tiny-square" aria-hidden="true"></span><span><?php echo esc_html( gbk_option( 'desk_label' ) ); ?></span><span aria-hidden="true">↗</span></div><?php gbk_component( '[srm_next_game]' ); ?><div class="aside-bottom"><span lang="hi"><?php echo esc_html( gbk_option( 'desk_note' ) ); ?></span><span aria-hidden="true">✦</span></div><?php } ?></div></div><div class="hero-bottom container"><?php foreach ( explode( '|', gbk_option( 'hero_strip' ) ) as $item ) { echo '<span>' . esc_html( trim( $item ) ) . '</span>'; } ?></div></section>
    <?php return; }
    echo '<section id="' . esc_attr( $key ) . '" class="home-section section-' . esc_attr( $key ) . '"><div class="container">';
    echo '<div class="section-heading"><div><p class="eyebrow">' . esc_html( in_array( $key, array( 'about', 'contact' ), true ) ? $description : gbk_brand() . ' / ' . str_replace( '_', ' ', strtoupper( $key ) ) ) . '</p><h2>' . esc_html( $title ) . '</h2>';
    if ( ! in_array( $key, array( 'about', 'contact' ), true ) ) { echo '<p>' . esc_html( $description ) . '</p>'; }
    echo '</div>';
    if ( 'schedule' === $key ) { echo '<span class="timezone-pill">◷ ' . esc_html( wp_timezone_string() ) . '</span>'; }
    if ( 'latest' === $key ) { echo '<a class="text-link" href="' . esc_url( gbk_archive_url() ) . '">All records ↗</a>'; }
    echo '</div>';
    if ( $image ) { echo wp_get_attachment_image( $image, 'large', false, array( 'class' => 'section-image', 'loading' => 'lazy' ) ); }
    $shortcodes = array( 'schedule' => '[srm_active_games]', 'latest' => '[gbk_latest_records]', 'daily' => '[srm_today_yesterday_results]', 'current_month' => '[srm_monthly_results]', 'previous_month' => '[srm_previous_month_results]', 'archive' => '[gbk_archive]' );
    if ( isset( $shortcodes[ $key ] ) ) { gbk_component( $shortcodes[ $key ] ); }
    elseif ( 'about' === $key ) { echo '<div class="about-grid"><div class="prose">' . wp_kses_post( wpautop( esc_html( gbk_option( 'about_text' ) ) ) ) . '</div><div class="principles"><div><span>01</span><strong>' . esc_html( gbk_option( 'principle_one' ) ) . '</strong></div><div><span>02</span><strong>' . esc_html( gbk_option( 'principle_two' ) ) . '</strong></div><div><span>03</span><strong>' . esc_html( gbk_option( 'principle_three' ) ) . '</strong></div></div></div>'; }
    elseif ( 'contact' === $key ) { echo '<div class="contact-grid"><p class="contact-intro">' . esc_html( gbk_option( 'contact_text' ) ) . '</p><div class="contact-links">'; gbk_contact_links(); echo '</div></div>'; }
    elseif ( 'faq' === $key ) { gbk_faqs(); }
    elseif ( 'blog' === $key ) { gbk_post_cards(); }
    echo '</div></section>';
}
add_action( 'after_switch_theme', function () {
    if ( get_option( 'gbk_faq_initialized' ) ) { return; }
    if ( ! get_posts( array( 'post_type' => 'gbk_faq', 'posts_per_page' => 1, 'post_status' => 'any' ) ) ) {
        $faqs = array(
            'What is this website for?' => 'Guru Bhai Khaiwal is an informational schedule and historical-record archive. It does not offer betting, predictions, payment collection or financial advice.',
            'Why are some records missing?' => 'A record appears only after a source has been checked and an administrator has approved publication. Unavailable or disputed information is not estimated. A blank cell does not mean zero.',
            'Which timezone do schedules use?' => 'Schedules use the timezone configured in WordPress, shown beside the schedule. An entry after midnight belongs to its actual calendar date. The historical date follows the source’s date label.',
            'How can I check or correct a record?' => 'Use the source link beside a published record to inspect the original page. For a correction, send the game name, date, exact value and source URL through the published contact channel. Records are reviewed before changes are published.',
        );
        $order = 0;
        foreach ( $faqs as $question => $answer ) { wp_insert_post( array( 'post_type' => 'gbk_faq', 'post_title' => $question, 'post_content' => $answer, 'post_status' => 'publish', 'menu_order' => ++$order ) ); }
    }
    update_option( 'gbk_faq_initialized', 1, false );
} );
