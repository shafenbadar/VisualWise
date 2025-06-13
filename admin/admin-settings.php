<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Register plugin settings
 */
function visualwise_jules_register_settings() {
    // Option group, option name
    register_setting( 'visualwise_jules_general_options', 'visualwise_jules_general_settings' );

    // General Settings Section
    add_settings_section(
        'visualwise_jules_general_section', // ID
        'General Settings', // Title
        'visualwise_jules_general_section_callback', // Callback
        'visualwise-jules-general' // Page slug (for this section)
    );

    // Currency Symbol Field
    add_settings_field(
        'currency_symbol', // ID
        'Currency Symbol', // Title
        'visualwise_jules_currency_symbol_callback', // Callback
        'visualwise-jules-general', // Page slug
        'visualwise_jules_general_section' // Section
    );

    // Currency Position Field
    add_settings_field(
        'currency_position', // ID
        'Currency Position', // Title
        'visualwise_jules_currency_position_callback', // Callback
        'visualwise-jules-general', // Page slug
        'visualwise_jules_general_section' // Section
    );

    // Currency Spacing Field
    add_settings_field(
        'currency_spacing', // ID
        'Currency Spacing', // Title
        'visualwise_jules_currency_spacing_callback', // Callback
        'visualwise-jules-general', // Page slug
        'visualwise_jules_general_section' // Section
    );
}
add_action( 'admin_init', 'visualwise_jules_register_settings' );

/**
 * Callback for the General Settings section
 */
function visualwise_jules_general_section_callback() {
    echo '<p>Configure the general settings for the Visualwise-Jules plugin.</p>';
}

/**
 * Callback for Currency Symbol field
 */
function visualwise_jules_currency_symbol_callback() {
    $options = get_option( 'visualwise_jules_general_settings' );
    $currency_symbol = isset( $options['currency_symbol'] ) ? $options['currency_symbol'] : '$';
    echo '<input type="text" id="currency_symbol" name="visualwise_jules_general_settings[currency_symbol]" value="' . esc_attr( $currency_symbol ) . '" />';
}

/**
 * Callback for Currency Position field
 */
function visualwise_jules_currency_position_callback() {
    $options = get_option( 'visualwise_jules_general_settings' );
    $currency_position = isset( $options['currency_position'] ) ? $options['currency_position'] : 'prefix';
    ?>
    <label>
        <input type="radio" name="visualwise_jules_general_settings[currency_position]" value="prefix" <?php checked( $currency_position, 'prefix' ); ?>>
        Prefix
    </label>
    <br>
    <label>
        <input type="radio" name="visualwise_jules_general_settings[currency_position]" value="postfix" <?php checked( $currency_position, 'postfix' ); ?>>
        Postfix
    </label>
    <?php
}

/**
 * Callback for Currency Spacing field
 */
function visualwise_jules_currency_spacing_callback() {
    $options = get_option( 'visualwise_jules_general_settings' );
    $currency_spacing = isset( $options['currency_spacing'] ) ? $options['currency_spacing'] : 0;
    ?>
    <label>
        <input type="checkbox" name="visualwise_jules_general_settings[currency_spacing]" value="1" <?php checked( $currency_spacing, 1 ); ?>>
        Add a space between symbol and amount?
    </label>
    <?php
}

// --- Google Ads Settings ---

function visualwise_jules_register_google_ads_settings() {
    register_setting( 'visualwise_jules_google_ads_options', 'visualwise_jules_google_ads_settings' );

    add_settings_section(
        'visualwise_jules_google_ads_section',
        'Google Ads Settings',
        'visualwise_jules_google_ads_section_callback',
        'visualwise-jules-google-ads'
    );

    $fields = [
        'sales_factor' => 'Sales Factor',
        'base_sales_amount' => 'Base Sales Amount',
        'traffic_factor' => 'Traffic Factor',
        'leads_factor' => 'Leads Factor',
        'month_2_plus_increase_factor' => 'Month 2+ Increase Factor (%)'
    ];

    foreach ( $fields as $id => $title ) {
        add_settings_field(
            $id,
            $title,
            "visualwise_jules_google_ads_{$id}_callback",
            'visualwise-jules-google-ads',
            'visualwise_jules_google_ads_section'
        );
    }
}
add_action( 'admin_init', 'visualwise_jules_register_google_ads_settings' );

function visualwise_jules_google_ads_section_callback() {
    echo '<p>Configure settings related to Google Ads performance and projection.</p>';
}

function visualwise_jules_google_ads_sales_factor_callback() {
    $options = get_option( 'visualwise_jules_google_ads_settings' );
    $value = isset( $options['sales_factor'] ) ? $options['sales_factor'] : '';
    echo '<input type="number" step="any" id="sales_factor" name="visualwise_jules_google_ads_settings[sales_factor]" value="' . esc_attr( $value ) . '" />';
}

function visualwise_jules_google_ads_base_sales_amount_callback() {
    $options = get_option( 'visualwise_jules_google_ads_settings' );
    $value = isset( $options['base_sales_amount'] ) ? $options['base_sales_amount'] : '';
    echo '<input type="number" step="any" id="base_sales_amount" name="visualwise_jules_google_ads_settings[base_sales_amount]" value="' . esc_attr( $value ) . '" />';
}

function visualwise_jules_google_ads_traffic_factor_callback() {
    $options = get_option( 'visualwise_jules_google_ads_settings' );
    $value = isset( $options['traffic_factor'] ) ? $options['traffic_factor'] : '';
    echo '<input type="number" step="any" id="traffic_factor" name="visualwise_jules_google_ads_settings[traffic_factor]" value="' . esc_attr( $value ) . '" />';
}

function visualwise_jules_google_ads_leads_factor_callback() {
    $options = get_option( 'visualwise_jules_google_ads_settings' );
    $value = isset( $options['leads_factor'] ) ? $options['leads_factor'] : '';
    echo '<input type="number" step="any" id="leads_factor" name="visualwise_jules_google_ads_settings[leads_factor]" value="' . esc_attr( $value ) . '" />';
}

function visualwise_jules_google_ads_month_2_plus_increase_factor_callback() {
    $options = get_option( 'visualwise_jules_google_ads_settings' );
    $value = isset( $options['month_2_plus_increase_factor'] ) ? $options['month_2_plus_increase_factor'] : '';
    echo '<input type="number" step="any" id="month_2_plus_increase_factor" name="visualwise_jules_google_ads_settings[month_2_plus_increase_factor]" value="' . esc_attr( $value ) . '" placeholder="e.g., 5 for 5%" />';
}

// --- SEO Settings ---

function visualwise_jules_register_seo_settings() {
    register_setting( 'visualwise_jules_seo_options', 'visualwise_jules_seo_settings' );

    add_settings_section(
        'visualwise_jules_seo_section',
        'SEO Settings',
        'visualwise_jules_seo_section_callback',
        'visualwise-jules-seo'
    );

    $fields = [
        'base_sales_progression' => 'Base Sales Progression (comma-separated)',
        'base_traffic_progression' => 'Base Traffic Progression (comma-separated)',
        'base_leads_progression' => 'Base Leads Progression (comma-separated)'
    ];

    foreach ( $fields as $id => $title ) {
        add_settings_field(
            $id,
            $title,
            "visualwise_jules_seo_{$id}_callback",
            'visualwise-jules-seo',
            'visualwise_jules_seo_section'
        );
    }
}
add_action( 'admin_init', 'visualwise_jules_register_seo_settings' );

function visualwise_jules_seo_section_callback() {
    echo '<p>Configure settings related to SEO performance and projection. Enter comma-separated values for monthly progressions (e.g., for 12 or 24 months).</p>';
}

function visualwise_jules_seo_base_sales_progression_callback() {
    $options = get_option( 'visualwise_jules_seo_settings' );
    $value = isset( $options['base_sales_progression'] ) ? $options['base_sales_progression'] : '';
    echo '<textarea id="base_sales_progression" name="visualwise_jules_seo_settings[base_sales_progression]" rows="3" cols="50" class="large-text code">' . esc_textarea( $value ) . '</textarea>';
}

function visualwise_jules_seo_base_traffic_progression_callback() {
    $options = get_option( 'visualwise_jules_seo_settings' );
    $value = isset( $options['base_traffic_progression'] ) ? $options['base_traffic_progression'] : '';
    echo '<textarea id="base_traffic_progression" name="visualwise_jules_seo_settings[base_traffic_progression]" rows="3" cols="50" class="large-text code">' . esc_textarea( $value ) . '</textarea>';
}

function visualwise_jules_seo_base_leads_progression_callback() {
    $options = get_option( 'visualwise_jules_seo_settings' );
    $value = isset( $options['base_leads_progression'] ) ? $options['base_leads_progression'] : '';
    echo '<textarea id="base_leads_progression" name="visualwise_jules_seo_settings[base_leads_progression]" rows="3" cols="50" class="large-text code">' . esc_textarea( $value ) . '</textarea>';
}

// --- Meta Ads Settings ---

function visualwise_jules_register_meta_ads_settings() {
    register_setting( 'visualwise_jules_meta_ads_options', 'visualwise_jules_meta_ads_settings' );

    add_settings_section(
        'visualwise_jules_meta_ads_section',
        'Meta Ads Settings',
        'visualwise_jules_meta_ads_section_callback',
        'visualwise-jules-meta-ads'
    );

    $fields = [
        'sales_factor' => 'Sales Factor',
        'base_sales_amount' => 'Base Sales Amount',
        'traffic_factor' => 'Traffic Factor',
        'leads_factor' => 'Leads Factor',
        'month_2_plus_increase_factor' => 'Month 2+ Increase Factor (%)'
    ];

    foreach ( $fields as $id => $title ) {
        add_settings_field(
            $id,
            $title,
            "visualwise_jules_meta_ads_{$id}_callback",
            'visualwise-jules-meta-ads',
            'visualwise_jules_meta_ads_section'
        );
    }
}
add_action( 'admin_init', 'visualwise_jules_register_meta_ads_settings' );

function visualwise_jules_meta_ads_section_callback() {
    echo '<p>Configure settings related to Meta Ads (Facebook/Instagram) performance and projection.</p>';
}

function visualwise_jules_meta_ads_sales_factor_callback() {
    $options = get_option( 'visualwise_jules_meta_ads_settings' );
    $value = isset( $options['sales_factor'] ) ? $options['sales_factor'] : '';
    echo '<input type="number" step="any" id="meta_sales_factor" name="visualwise_jules_meta_ads_settings[sales_factor]" value="' . esc_attr( $value ) . '" />';
}

function visualwise_jules_meta_ads_base_sales_amount_callback() {
    $options = get_option( 'visualwise_jules_meta_ads_settings' );
    $value = isset( $options['base_sales_amount'] ) ? $options['base_sales_amount'] : '';
    echo '<input type="number" step="any" id="meta_base_sales_amount" name="visualwise_jules_meta_ads_settings[base_sales_amount]" value="' . esc_attr( $value ) . '" />';
}

function visualwise_jules_meta_ads_traffic_factor_callback() {
    $options = get_option( 'visualwise_jules_meta_ads_settings' );
    $value = isset( $options['traffic_factor'] ) ? $options['traffic_factor'] : '';
    echo '<input type="number" step="any" id="meta_traffic_factor" name="visualwise_jules_meta_ads_settings[traffic_factor]" value="' . esc_attr( $value ) . '" />';
}

function visualwise_jules_meta_ads_leads_factor_callback() {
    $options = get_option( 'visualwise_jules_meta_ads_settings' );
    $value = isset( $options['leads_factor'] ) ? $options['leads_factor'] : '';
    echo '<input type="number" step="any" id="meta_leads_factor" name="visualwise_jules_meta_ads_settings[leads_factor]" value="' . esc_attr( $value ) . '" />';
}

function visualwise_jules_meta_ads_month_2_plus_increase_factor_callback() {
    $options = get_option( 'visualwise_jules_meta_ads_settings' );
    $value = isset( $options['month_2_plus_increase_factor'] ) ? $options['month_2_plus_increase_factor'] : '';
    echo '<input type="number" step="any" id="meta_month_2_plus_increase_factor" name="visualwise_jules_meta_ads_settings[month_2_plus_increase_factor]" value="' . esc_attr( $value ) . '" placeholder="e.g., 5 for 5%" />';
}

// --- Help/Documentation Tab ---

function visualwise_jules_register_help_settings() {
    // No options to save for help tab, so no register_setting() needed yet.
    add_settings_section(
        'visualwise_jules_help_section',
        'Help & Documentation',
        'visualwise_jules_help_section_callback',
        'visualwise-jules-help' // Page slug for this section
    );

    add_settings_field(
        'help_content',
        'Plugin Guide', // Field title (optional, can be empty if section callback has all info)
        'visualwise_jules_help_content_callback',
        'visualwise-jules-help',
        'visualwise_jules_help_section'
    );
}
add_action( 'admin_init', 'visualwise_jules_register_help_settings' );

function visualwise_jules_help_section_callback() {
    echo '<p>Find useful information and documentation for the Visualwise-Jules plugin below.</p>';
}

function visualwise_jules_help_content_callback() {
    ?>
    <div class="visualwise-jules-help-content">
        <h4>Overview</h4>
        <p>Visualwise-Jules is a marketing strategy visualizer. It allows you to project potential traffic, leads, and sales based on configurable parameters for different marketing channels like Google Ads, Meta Ads, and SEO efforts. The plugin provides a dynamic frontend interface with a line graph to visualize these projections over time.</p>

        <h4>Shortcode Usage</h4>
        <p>To display the visualizer on your website, use the following shortcode on any page or post:</p>
        <p><code>[visualwise_jules_visualizer]</code></p>
        <p>Simply add this shortcode to the content editor of the page/post where you want the visualizer to appear.</p>

        <h4>Admin Settings Explanation</h4>
        <p>Configure the plugin's calculation parameters from the "Visualwise-Jules" menu in your WordPress admin dashboard.</p>
        <ul>
            <li><strong>General Tab:</strong>
                <ul>
                    <li><strong>Currency Symbol:</strong> Set the currency symbol (e.g., $, £, €) to be displayed for monetary values.</li>
                    <li><strong>Currency Position:</strong> Choose whether the symbol appears before (Prefix) or after (Postfix) the amount.</li>
                    <li><strong>Currency Spacing:</strong> Check this box to add a space between the currency symbol and the amount.</li>
                </ul>
            </li>
            <li><strong>Google Ads Tab:</strong>
                <ul>
                    <li><strong>Sales Factor:</strong> A multiplier applied to the monthly budget to estimate sales.</li>
                    <li><strong>Base Sales Amount:</strong> A fixed amount added to the monthly sales calculation. This could represent a baseline of sales independent of the ad spend factor.</li>
                    <li><strong>Traffic Factor:</strong> A multiplier applied to the monthly budget to estimate website traffic.</li>
                    <li><strong>Leads Factor:</strong> A multiplier applied to the monthly budget to estimate leads generated.</li>
                    <li><strong>Month 2+ Increase Factor (%):</strong> A percentage increase applied cumulatively each month (starting from month 2) to traffic, leads, and sales. This models growth or improved efficiency over time. For example, a 5% increase means month 2 is 5% better than month 1, month 3 is 5% better than month 2's increased value, and so on.</li>
                </ul>
            </li>
            <li><strong>SEO Tab:</strong>
                <ul>
                    <li><strong>Base Sales Progression:</strong> Enter comma-separated numbers representing projected monthly sales for a <em>base budget of 1000 currency units</em> (e.g., "100,120,150,..."). The visualizer will scale these values based on the actual monthly budget you set on the frontend.</li>
                    <li><strong>Base Traffic Progression:</strong> Similar to sales, but for projected monthly traffic (e.g., "1000,1200,1500,...").</li>
                    <li><strong>Base Leads Progression:</strong> Similar to sales, but for projected monthly leads (e.g., "50,60,70,...").</li>
                    <li><em>Handling Shorter Progressions:</em> If your selected timespan on the frontend is longer than the number of values in these progression arrays, the projection will use the <em>last value</em> from the array for all subsequent months.</li>
                </ul>
            </li>
            <li><strong>Meta Ads Tab:</strong>
                <ul>
                    <li>Parameters are similar to Google Ads (Sales Factor, Base Sales Amount, Traffic Factor, Leads Factor, Month 2+ Increase Factor) but are specific to your Meta (Facebook/Instagram) advertising efforts.</li>
                </ul>
            </li>
        </ul>

        <h4>Frontend Interface Usage</h4>
        <ul>
            <li><strong>Adding Modules:</strong> Click buttons like "Add Google Ads", "Add SEO", or "Add Meta Ads" to create a new projection module in the controls panel.</li>
            <li><strong>Adjusting Sliders:</strong>
                <ul>
                    <li><strong>Budget:</strong> For Ads modules, this is a <em>daily</em> budget. For SEO, it's a <em>monthly</em> budget. The current value is displayed next to the slider.</li>
                    <li><strong>Timespan:</strong> Select the number of months (1-24) for the projection.</li>
                </ul>
            </li>
            <li><strong>Removing Modules:</strong> Click the "X" button on a module card to remove it.</li>
            <li><strong>Interpreting Projections:</strong> Each module card will display its calculated Traffic, Leads, and Sales for the selected budget and timespan.</li>
            <li><strong>Line Graph:</strong> The graph visualizes the combined total projections for Sales, Traffic, and Leads over the selected timespan. You can toggle the visibility of each metric line using the checkboxes above the graph.</li>
            <li><strong>Summary Section:</strong> Below the graph, you'll find the combined total Traffic, Leads, and Sales across all active modules for their entire projected timespans.</li>
        </ul>

        <h4>Troubleshooting/FAQ</h4>
        <ul>
            <li><strong>Graph not updating?</strong> Ensure you have JavaScript enabled in your browser. Check your browser's developer console for any error messages.</li>
            <li><strong>Projections seem off?</strong> Double-check your parameters in the admin settings for each module. Ensure factors and progression arrays are entered correctly. Remember the SEO progression is based on a 1000 unit budget.</li>
            <li><strong>Currency symbol incorrect?</strong> Verify settings under the "General" tab in the admin panel.</li>
        </ul>

        <h4>Support</h4>
        <p>For further assistance, please refer to our <a href="https[COLON]//example.com/visualwise-jules-support" target="_blank">support forum</a> (replace with actual link if available).</p>
    </div>
    <?php
    // No input field is needed here as we are not saving any options for help yet.
}
?>
