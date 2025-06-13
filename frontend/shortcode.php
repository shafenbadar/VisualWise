<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Shortcode to display the visualizer.
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output for the visualizer.
 */
function visualwise_jules_visualizer_shortcode( $atts ) {
    // Enqueue assets specifically when the shortcode is used.
    // This is a more reliable place if the previous enqueue in the main file is too broad.
    // However, wp_enqueue_scripts is the standard hook. If issues arise, consider moving
    // visualwise_jules_frontend_assets() call here or using a flag.

    ob_start();
    if ( file_exists( plugin_dir_path( __FILE__ ) . 'visualizer-template.php' ) ) {
        include plugin_dir_path( __FILE__ ) . 'visualizer-template.php';
    } else {
        echo 'Error: Visualizer template not found.';
    }
    return ob_get_clean();
}
add_shortcode( 'visualwise_jules_visualizer', 'visualwise_jules_visualizer_shortcode' );

/**
 * AJAX handler for visualizer data.
 */
function visualwise_jules_ajax_calculate_projections() {
    check_ajax_referer( 'visualwise_jules_visualizer_nonce', 'nonce' );

    // Get data from AJAX request
    $modules_data = isset( $_POST['modules'] ) ? json_decode( stripslashes( $_POST['modules'] ), true ) : array();

    // Initialize response structure
    $response_data = array(
        'modules' => array(), // Per-module total projections
        'totals'  => array(   // Combined total projections
            'traffic' => 0,
            'leads'   => 0,
            'sales'   => 0,
        ),
        'graph_data' => array( // Data for Chart.js
            'labels'   => array(), // Month labels: Month 1, Month 2...
            'datasets' => array()  // Datasets for Sales, Traffic, Leads
        )
    );

    $max_timespan = 0;
    $all_modules_monthly_data = array( // To store [metric][month_idx] across all modules
        'sales' => array(),
        'traffic' => array(),
        'leads' => array(),
    );

    // Calculate projections for each module
    foreach ( $modules_data as $module_params ) {
        $module_id = isset( $module_params['id'] ) ? sanitize_text_field( $module_params['id'] ) : null;
        $module_type = isset( $module_params['type'] ) ? sanitize_text_field( $module_params['type'] ) : null;
        $budget = isset( $module_params['budget'] ) ? floatval( $module_params['budget'] ) : 0;
        $timespan = isset( $module_params['timespan'] ) ? intval( $module_params['timespan'] ) : 1;

        if ( !$module_id || !$module_type ) {
            continue;
        }

        if ( $timespan > $max_timespan ) {
            $max_timespan = $timespan;
        }

        $module_projection_data = null;

        switch ( $module_type ) {
            case 'google_ads':
                $module_projection_data = visualwise_jules_calculate_google_ads_projections( $budget, $timespan );
                break;
            case 'seo':
                // SEO budget is monthly, others are daily. This needs to be handled in JS or by having different field names.
                // For now, assuming 'budget' is passed correctly as monthly for SEO.
                $module_projection_data = visualwise_jules_calculate_seo_projections( $budget, $timespan );
                break;
            case 'meta_ads':
                $module_projection_data = visualwise_jules_calculate_meta_ads_projections( $budget, $timespan );
                break;
        }

        if ( $module_projection_data ) {
            // Store totals for this module (to be displayed on the module card)
            $response_data['modules'][ $module_id ] = $module_projection_data['totals'];

            // Aggregate overall totals
            $response_data['totals']['sales'] += $module_projection_data['totals']['sales'];
            $response_data['totals']['traffic'] += $module_projection_data['totals']['traffic'];
            $response_data['totals']['leads'] += $module_projection_data['totals']['leads'];

            // Aggregate monthly data for combined graph lines
            foreach (['sales', 'traffic', 'leads'] as $metric) {
                foreach ($module_projection_data[$metric] as $month_idx => $value) {
                    if (!isset($all_modules_monthly_data[$metric][$month_idx])) {
                        $all_modules_monthly_data[$metric][$month_idx] = 0;
                    }
                    $all_modules_monthly_data[$metric][$month_idx] += $value;
                }
            }
        }
    }

    $response_data['totals']['sales'] = round($response_data['totals']['sales'], 2);
    $response_data['totals']['traffic'] = round($response_data['totals']['traffic'], 0);
    $response_data['totals']['leads'] = round($response_data['totals']['leads'], 0);

    // Prepare graph labels (months)
    for ( $i = 1; $i <= $max_timespan; $i++ ) {
        $response_data['graph_data']['labels'][] = "Month " . $i;
    }

    // Prepare graph datasets
    $metric_colors = array(
        'sales'   => 'rgba(75, 192, 192, 1)', // Teal
        'traffic' => 'rgba(54, 162, 235, 1)', // Blue
        'leads'   => 'rgba(255, 159, 64, 1)'  // Orange
    );

    foreach (['sales', 'traffic', 'leads'] as $metric) {
        $monthly_values = array();
        for ($i = 0; $i < $max_timespan; $i++) {
            $monthly_values[] = isset($all_modules_monthly_data[$metric][$i]) ? $all_modules_monthly_data[$metric][$i] : 0;
        }

        // Round final aggregated values for display
        if ($metric === 'sales') {
            $monthly_values = array_map(function($val) { return round($val, 2); }, $monthly_values);
        } else {
            $monthly_values = array_map(function($val) { return round($val, 0); }, $monthly_values);
        }

        $response_data['graph_data']['datasets'][] = array(
            'label'       => 'Total Projected ' . ucfirst( $metric ),
            'data'        => $monthly_values,
            'borderColor' => $metric_colors[ $metric ],
            'tension'     => 0.1,
            'fill'        => false
        );
    }

    wp_send_json_success( $response_data );
}
add_action( 'wp_ajax_visualwise_jules_calculate_projections', 'visualwise_jules_ajax_calculate_projections' );
add_action( 'wp_ajax_nopriv_visualwise_jules_calculate_projections', 'visualwise_jules_ajax_calculate_projections' ); // If accessible by non-logged-in users

?>
