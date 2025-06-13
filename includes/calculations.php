<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Retrieve and parse comma-separated string into an array of numbers.
 *
 * @param string $string Comma-separated string of numbers.
 * @return array Array of floats.
 */
function visualwise_jules_parse_progression_string( $string ) {
    if ( empty( $string ) ) {
        return array();
    }
    return array_map( 'floatval', explode( ',', $string ) );
}

/**
 * Get a value from a progression array for a given month.
 * If month index is out of bounds, use the last available value or a default.
 *
 * @param array $progression_array Array of progression values.
 * @param int   $month_index       0-based month index.
 * @param float $default_value     Default value if not found.
 * @return float Progression value.
 */
function visualwise_jules_get_progression_value( $progression_array, $month_index, $default_value = 0.0 ) {
    if ( empty( $progression_array ) ) {
        return $default_value;
    }
    if ( isset( $progression_array[ $month_index ] ) ) {
        return $progression_array[ $month_index ];
    }
    // If month_index is beyond the array, use the last element
    return end( $progression_array );
}


/**
 * Calculate Google Ads Projections.
 *
 * @param float $budget_daily Daily budget.
 * @param int   $timespan_months Timespan in months.
 * @return array Projection data.
 */
function visualwise_jules_calculate_google_ads_projections( $budget_daily, $timespan_months ) {
    $settings = get_option( 'visualwise_jules_google_ads_settings', array() );

    $sales_factor = isset( $settings['sales_factor'] ) ? floatval( $settings['sales_factor'] ) : 0;
    // Assuming base_sales_amount is a one-time addition or per month. Let's assume per month for now.
    $base_sales_amount = isset( $settings['base_sales_amount'] ) ? floatval( $settings['base_sales_amount'] ) : 0;
    $traffic_factor = isset( $settings['traffic_factor'] ) ? floatval( $settings['traffic_factor'] ) : 0;
    $leads_factor = isset( $settings['leads_factor'] ) ? floatval( $settings['leads_factor'] ) : 0;
    $increase_factor = isset( $settings['month_2_plus_increase_factor'] ) ? floatval( $settings['month_2_plus_increase_factor'] ) / 100 : 0; // Convert percentage to decimal

    $projections = array(
        'sales'   => array(),
        'traffic' => array(),
        'leads'   => array(),
        'totals'  => array(
            'sales'   => 0,
            'traffic' => 0,
            'leads'   => 0,
        )
    );

    $monthly_budget = $budget_daily * 30.44; // Average days in a month

    for ( $month = 1; $month <= $timespan_months; $month++ ) {
        $current_month_sales = $monthly_budget * $sales_factor + $base_sales_amount;
        $current_month_traffic = $monthly_budget * $traffic_factor;
        $current_month_leads = $monthly_budget * $leads_factor;

        if ( $month > 1 && $increase_factor > 0 ) {
            // Apply increase factor cumulatively from the previous month's *calculated* value before this month's increase
            // This means the increase is compound.
            // Let's adjust to apply to the base calculation for month > 1
            // Or, more simply, apply to the *previous* month's actual projection.
            // For simplicity, let's take it as an increase over the base figures calculated for *this* month if month > 1
            // A clearer definition might be needed, but let's go with an increase on the previous month's numbers.

            if ($month == 2) { // First month of increase applies to base
                 $current_month_sales *= (1 + $increase_factor);
                 $current_month_traffic *= (1 + $increase_factor);
                 $current_month_leads *= (1 + $increase_factor);
            } else { // Subsequent months increase over the prior month's *increased* value
                $current_month_sales = $projections['sales'][$month-2] * (1 + $increase_factor); // month-2 because array is 0-indexed
                $current_month_traffic = $projections['traffic'][$month-2] * (1 + $increase_factor);
                $current_month_leads = $projections['leads'][$month-2] * (1 + $increase_factor);
            }
        }

        // If increase_factor is 0, or month is 1, the initially calculated values are used.
        // A better way for month > 1 with increase factor:
        if ($month > 1 && $increase_factor > 0) {
            $multiplier = pow((1 + $increase_factor), $month - 1);
            $current_month_sales = ($monthly_budget * $sales_factor + $base_sales_amount) * $multiplier;
            $current_month_traffic = ($monthly_budget * $traffic_factor) * $multiplier;
            $current_month_leads = ($monthly_budget * $leads_factor) * $multiplier;
        }


        $projections['sales'][] = round($current_month_sales, 2);
        $projections['traffic'][] = round($current_month_traffic, 0);
        $projections['leads'][] = round($current_month_leads, 0);

        $projections['totals']['sales'] += $current_month_sales;
        $projections['totals']['traffic'] += $current_month_traffic;
        $projections['totals']['leads'] += $current_month_leads;
    }

    $projections['totals']['sales'] = round($projections['totals']['sales'], 2);
    $projections['totals']['traffic'] = round($projections['totals']['traffic'], 0);
    $projections['totals']['leads'] = round($projections['totals']['leads'], 0);


    return $projections;
}


/**
 * Calculate SEO Projections.
 *
 * @param float $budget_monthly Monthly budget for SEO.
 * @param int   $timespan_months Timespan in months.
 * @return array Projection data.
 */
function visualwise_jules_calculate_seo_projections( $budget_monthly, $timespan_months ) {
    $settings = get_option( 'visualwise_jules_seo_settings', array() );

    // Base budget for which progressions are defined (e.g. Rs1000 or $1000)
    // This needs to be a defined standard or an admin setting itself.
    // For now, let's assume a base budget of 1000 units of currency for the progressions.
    $base_progression_budget = 1000;

    $sales_progression_str = isset( $settings['base_sales_progression'] ) ? $settings['base_sales_progression'] : '';
    $traffic_progression_str = isset( $settings['base_traffic_progression'] ) ? $settings['base_traffic_progression'] : '';
    $leads_progression_str = isset( $settings['base_leads_progression'] ) ? $settings['base_leads_progression'] : '';

    $sales_prog_array = visualwise_jules_parse_progression_string( $sales_progression_str );
    $traffic_prog_array = visualwise_jules_parse_progression_string( $traffic_progression_str );
    $leads_prog_array = visualwise_jules_parse_progression_string( $leads_progression_str );

    $projections = array(
        'sales'   => array(),
        'traffic' => array(),
        'leads'   => array(),
        'totals'  => array(
            'sales'   => 0,
            'traffic' => 0,
            'leads'   => 0,
        )
    );

    // Calculate scaling factor if the actual budget differs from the base progression budget
    $budget_scale_factor = 1;
    if ( $base_progression_budget > 0 && $budget_monthly > 0 ) {
        $budget_scale_factor = $budget_monthly / $base_progression_budget;
    } elseif ($budget_monthly == 0) { // If budget is 0, all projections are 0
        $budget_scale_factor = 0;
    }


    for ( $month_idx = 0; $month_idx < $timespan_months; $month_idx++ ) {
        $current_month_sales = visualwise_jules_get_progression_value( $sales_prog_array, $month_idx, 0 ) * $budget_scale_factor;
        $current_month_traffic = visualwise_jules_get_progression_value( $traffic_prog_array, $month_idx, 0 ) * $budget_scale_factor;
        $current_month_leads = visualwise_jules_get_progression_value( $leads_prog_array, $month_idx, 0 ) * $budget_scale_factor;

        $projections['sales'][] = round($current_month_sales, 2);
        $projections['traffic'][] = round($current_month_traffic, 0);
        $projections['leads'][] = round($current_month_leads, 0);

        $projections['totals']['sales'] += $current_month_sales;
        $projections['totals']['traffic'] += $current_month_traffic;
        $projections['totals']['leads'] += $current_month_leads;
    }

    $projections['totals']['sales'] = round($projections['totals']['sales'], 2);
    $projections['totals']['traffic'] = round($projections['totals']['traffic'], 0);
    $projections['totals']['leads'] = round($projections['totals']['leads'], 0);

    return $projections;
}


/**
 * Calculate Meta Ads Projections.
 *
 * @param float $budget_daily Daily budget.
 * @param int   $timespan_months Timespan in months.
 * @return array Projection data.
 */
function visualwise_jules_calculate_meta_ads_projections( $budget_daily, $timespan_months ) {
    $settings = get_option( 'visualwise_jules_meta_ads_settings', array() );

    $sales_factor = isset( $settings['sales_factor'] ) ? floatval( $settings['sales_factor'] ) : 0;
    $base_sales_amount = isset( $settings['base_sales_amount'] ) ? floatval( $settings['base_sales_amount'] ) : 0; // Assuming per month
    $traffic_factor = isset( $settings['traffic_factor'] ) ? floatval( $settings['traffic_factor'] ) : 0;
    $leads_factor = isset( $settings['leads_factor'] ) ? floatval( $settings['leads_factor'] ) : 0;
    $increase_factor = isset( $settings['month_2_plus_increase_factor'] ) ? floatval( $settings['month_2_plus_increase_factor'] ) / 100 : 0;

    $projections = array(
        'sales'   => array(),
        'traffic' => array(),
        'leads'   => array(),
        'totals'  => array(
            'sales'   => 0,
            'traffic' => 0,
            'leads'   => 0,
        )
    );

    $monthly_budget = $budget_daily * 30.44; // Average days in a month

    for ( $month = 1; $month <= $timespan_months; $month++ ) {
        $current_month_sales = $monthly_budget * $sales_factor + $base_sales_amount;
        $current_month_traffic = $monthly_budget * $traffic_factor;
        $current_month_leads = $monthly_budget * $leads_factor;

        if ( $month > 1 && $increase_factor > 0 ) {
            $multiplier = pow((1 + $increase_factor), $month - 1);
            $current_month_sales = ($monthly_budget * $sales_factor + $base_sales_amount) * $multiplier;
            $current_month_traffic = ($monthly_budget * $traffic_factor) * $multiplier;
            $current_month_leads = ($monthly_budget * $leads_factor) * $multiplier;
        }

        $projections['sales'][] = round($current_month_sales, 2);
        $projections['traffic'][] = round($current_month_traffic, 0);
        $projections['leads'][] = round($current_month_leads, 0);

        $projections['totals']['sales'] += $current_month_sales;
        $projections['totals']['traffic'] += $current_month_traffic;
        $projections['totals']['leads'] += $current_month_leads;
    }

    $projections['totals']['sales'] = round($projections['totals']['sales'], 2);
    $projections['totals']['traffic'] = round($projections['totals']['traffic'], 0);
    $projections['totals']['leads'] = round($projections['totals']['leads'], 0);

    return $projections;
}

?>
