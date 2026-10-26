<?php
/**
 * Template: Monthly Results Matrix
 *
 * @var array<object> $events
 * @var array<string,array<int,object>> $data  Indexed by date then event_id
 * @var int           $year
 * @var int           $month
 * @var int           $days
 * @var string        $date_format
 * @var string        $month_label
 * @var array<string,string> $atts
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$title = ! empty( $atts['title'] ) ? sanitize_text_field( $atts['title'] ) : $month_label;
$show_filter = ( $atts['show_filter'] ?? 'yes' ) === 'yes';
?>
<div class="srm-wrap srm-monthly-results">
    <div class="srm-table-header">
        <h3 class="srm-table-title"><?php echo esc_html( $title ); ?></h3>
        <?php if ( $show_filter ) : ?>
            <form class="srm-monthly-filter" method="get">
<?php if(!get_option("permalink_structure") && is_page()): ?><input type="hidden" name="page_id" value="<?php echo absint(get_queried_object_id()); ?>"><?php endif; ?>
                <label for="srm-game-filter-<?php echo esc_attr( $instance ); ?>">
                    <?php esc_html_e( 'गेम:', 'scheduled-results-manager' ); ?>
                </label>
                <select id="srm-game-filter-<?php echo esc_attr( $instance ); ?>" name="<?php echo esc_attr($prefix); ?>game_id">
                    <?php if(!$fixed): ?><option value="0" <?php selected(0,$selected_game_id); ?>>सभी गेम</option><?php endif; ?>
                    <?php foreach ( $filter_options as $filter_event ) : ?>
                        <option value="<?php echo esc_attr( (string) $filter_event->id ); ?>" <?php selected( (int) $filter_event->id, $selected_game_id ); ?>>
                            <?php echo esc_html( $filter_event->event_name ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="srm-month-filter-<?php echo esc_attr( $instance ); ?>">
                    <?php esc_html_e( 'महीना:', 'scheduled-results-manager' ); ?>
                </label>
                <select id="srm-month-filter-<?php echo esc_attr( $instance ); ?>" name="<?php echo esc_attr($prefix); ?>month">
                    <?php for ( $filter_month = 1; $filter_month <= 12; $filter_month++ ) : ?>
                        <option value="<?php echo esc_attr( (string) $filter_month ); ?>" <?php selected( $filter_month, $month ); ?>>
                            <?php echo esc_html( SRM_Reference_Tables::month_name($filter_month) ); ?>
                        </option>
                    <?php endfor; ?>
                </select>
                <label for="srm-year-filter-<?php echo esc_attr( $instance ); ?>">
                    <?php esc_html_e( 'वर्ष:', 'scheduled-results-manager' ); ?>
                </label>
                <input id="srm-year-filter-<?php echo esc_attr( $instance ); ?>" type="number" name="<?php echo esc_attr($prefix); ?>year" value="<?php echo esc_attr( (string) $year ); ?>" min="1900" max="2100">
                <button type="submit"><?php esc_html_e( 'देखें', 'scheduled-results-manager' ); ?></button>
            </form>
        <?php endif; ?>
    </div>

    <?php if ( empty( $events ) ) : ?>
        <p class="srm-no-data"><?php esc_html_e( 'No games configured yet.', 'scheduled-results-manager' ); ?></p>
    <?php else : ?>
    <div class="srm-table-responsive srm-monthly-responsive" role="region" tabindex="0" aria-label="<?php echo esc_attr($month_label); ?>">
        <table class="srm-table srm-monthly-table"><caption class="screen-reader-text"><?php echo esc_html($month_label); ?></caption>
            <thead>
                <tr>
                    <th scope="col" class="srm-sticky-col"><?php esc_html_e( 'तारीख', 'scheduled-results-manager' ); ?></th>
                    <?php foreach ( $events as $event ) : ?>
                        <th scope="col">
                            <span class="srm-event-name-th"><?php echo esc_html( $event->event_name ); ?></span>
                            <span class="srm-event-time-th"><?php echo esc_html( SRM_Games::format_time( $event->scheduled_time ) ); ?></span>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $today = SRM_Results::get_site_date();
                $visible_days = $days;
                if ( substr( $today, 0, 7 ) === sprintf( '%04d-%02d', $year, $month ) ) {
                    $visible_days = (int) substr( $today, 8, 2 );
                }
                for ( $d = 1; $d <= $visible_days; $d++ ) :
                    $date       = sprintf( '%04d-%02d-%02d', $year, $month, $d );
                    $display    = date( $date_format, strtotime( $date ) );
                    $is_today   = ( $date === $today );
                ?>
                <tr class="<?php echo $is_today ? 'srm-today-row' : ''; ?>">
                    <th scope="row" class="srm-sticky-col srm-date-cell">
                        <?php echo esc_html( $display ); ?>
                        <?php if ( $is_today ) : ?>
                            <span class="srm-today-tag"><?php esc_html_e( 'आज', 'scheduled-results-manager' ); ?></span>
                        <?php endif; ?>
                    </th>
                    <?php foreach ( $events as $event ) :
                        $result = $data[ $date ][ $event->id ] ?? null;
                    ?>
                        <td class="srm-result-cell">
                            <?php
                            if ( $result && $result->result_status === 'published' && $result->result_value !== null ) {
                                echo '<span class="srm-result-published">' . esc_html( $result->result_value ) . '</span>';
                            } else {
                                echo '<span class="srm-result-empty">—</span>';
                            }
                            ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
