<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/** @var array<object> $games */
/** @var array<string,string> $atts */

if ( empty( $games ) ) {
    echo '<p class="srm-no-data">' . esc_html__( 'No active games found.', 'scheduled-results-manager' ) . '</p>';
    return;
}

$show_time      = 'yes' === ( $atts['show_time'] ?? 'yes' );
$show_secondary = 'yes' === ( $atts['show_secondary'] ?? 'no' );
$title          = ! empty( $atts['title'] ) ? esc_html( $atts['title'] ) : esc_html__( 'Active Games', 'scheduled-results-manager' );
$table_class    = '' !== ( $atts['ul_class'] ?? '' ) ? $atts['ul_class'] : '';
?>
<div class="srm-wrap srm-active-games">
    <div class="srm-table-header">
        <h3 class="srm-table-title"><?php echo $title; ?></h3>
    </div>

    <div class="srm-table-responsive <?php echo esc_attr( $table_class ); ?>" role="region" tabindex="0" aria-label="गेम की समय-सारणी">
        <table class="srm-table"><caption class="screen-reader-text">गेम की समय-सारणी</caption>
            <thead>
                <tr>
                    <th scope="col"><?php esc_html_e( 'गेम / Game', 'scheduled-results-manager' ); ?></th>
                    <?php if ( $show_time ) : ?>
                    <th scope="col"><?php esc_html_e( 'समय / Time', 'scheduled-results-manager' ); ?></th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $games as $game ) : ?>
                <tr>
                    <td class="srm-event-col">
                        <span class="srm-event-primary"><?php $url=SRM_Reference_Tables::game_url((int)$game->id); if($url): ?><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($game->event_name); ?></a><?php else:echo esc_html($game->event_name);endif; ?></span>
                        <?php if ( $show_secondary && ! empty( $game->event_name_secondary ) && false === strpos( (string) $game->event_name, (string) $game->event_name_secondary ) ) : ?>
                            <span class="srm-event-secondary-fe"><?php echo esc_html( $game->event_name_secondary ); ?></span>
                        <?php endif; ?>
                    </td>
                    <?php if ( $show_time ) : ?>
                    <td class="srm-time-col"><?php echo esc_html( SRM_Games::format_time( $game->scheduled_time ) ); ?></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
