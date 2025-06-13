<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <?php $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'general'; ?>
    <nav class="nav-tab-wrapper">
        <a href="?page=visualwise-jules&tab=general" class="nav-tab <?php echo $active_tab === 'general' ? 'nav-tab-active' : ''; ?>" data-tab="general">General</a>
        <a href="?page=visualwise-jules&tab=google_ads" class="nav-tab <?php echo $active_tab === 'google_ads' ? 'nav-tab-active' : ''; ?>" data-tab="google_ads">Google Ads</a>
        <a href="?page=visualwise-jules&tab=seo" class="nav-tab <?php echo $active_tab === 'seo' ? 'nav-tab-active' : ''; ?>" data-tab="seo">SEO</a>
        <a href="?page=visualwise-jules&tab=meta_ads" class="nav-tab <?php echo $active_tab === 'meta_ads' ? 'nav-tab-active' : ''; ?>" data-tab="meta_ads">Meta Ads</a>
        <a href="?page=visualwise-jules&tab=help" class="nav-tab <?php echo $active_tab === 'help' ? 'nav-tab-active' : ''; ?>" data-tab="help">Help/Documentation</a>
    </nav>

    <div id="tab-content-general" class="tab-pane <?php echo $active_tab === 'general' ? 'active' : ''; ?>">
        <form method="post" action="options.php">
            <?php
            settings_fields( 'visualwise_jules_general_options' );
            do_settings_sections( 'visualwise-jules-general' );
            submit_button();
            ?>
        </form>
    </div>

    <div id="tab-content-google_ads" class="tab-pane <?php echo $active_tab === 'google_ads' ? 'active' : ''; ?>">
        <form method="post" action="options.php">
            <?php
            settings_fields( 'visualwise_jules_google_ads_options' );
            do_settings_sections( 'visualwise-jules-google-ads' );
            submit_button();
            ?>
        </form>
    </div>

    <div id="tab-content-seo" class="tab-pane <?php echo $active_tab === 'seo' ? 'active' : ''; ?>">
        <form method="post" action="options.php">
            <?php
            settings_fields( 'visualwise_jules_seo_options' );
            do_settings_sections( 'visualwise-jules-seo' );
            submit_button();
            ?>
        </form>
    </div>

    <div id="tab-content-meta_ads" class="tab-pane <?php echo $active_tab === 'meta_ads' ? 'active' : ''; ?>">
        <form method="post" action="options.php">
            <?php
            settings_fields( 'visualwise_jules_meta_ads_options' );
            do_settings_sections( 'visualwise-jules-meta-ads' );
            submit_button();
            ?>
        </form>
    </div>

    <div id="tab-content-help" class="tab-pane <?php echo $active_tab === 'help' ? 'active' : ''; ?>">
        <?php
        // settings_fields( 'visualwise_jules_help_options' ); // Not strictly needed if no options group is registered
        do_settings_sections( 'visualwise-jules-help' );
        ?>
    </div>

</div>

<style>
    /* Keep styles here or move to a dedicated admin CSS file if it grows */
    .tab-pane { display: none; }
    .tab-pane.active { display: block; }
</style>
