<?php if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();

/**
 * Function that houses the code that cleans up the plugin on un-installation.
 *
 * @since 1.0.0
 */
function jitso_plugin_cleanup() {

    include_once ( 'models/class-jitso-constants.php' );

    $plugin_constants = JITSO_Constants::instance();

    if ( get_option( $plugin_constants->OPTION_CLEANUP_PLUGIN_OPTIONS() ) == 'yes' ) {

        // Delete the option that specifies the option whether to delete the options on plugin uninstall
        delete_option( $plugin_constants->OPTION_CLEANUP_PLUGIN_OPTIONS() );

        // Delete custom tables
        global $wpdb;

        $offer_views_table_name       = $wpdb->prefix . $plugin_constants->CUSTOM_TABLE_OFFER_VIEWS();
        $offer_accepts_table_name     = $wpdb->prefix . $plugin_constants->CUSTOM_TABLE_OFFER_ACCEPTS();
        $offer_conversions_table_name = $wpdb->prefix . $plugin_constants->CUSTOM_TABLE_OFFER_CONVERSIONS();
        $offer_declines_table_name    = $wpdb->prefix . $plugin_constants->CUSTOM_TABLE_OFFER_DECLINES();

        $wpdb->query( "DROP TABLE IF EXISTS $offer_views_table_name" );
        $wpdb->query( "DROP TABLE IF EXISTS $offer_accepts_table_name" );
        $wpdb->query( "DROP TABLE IF EXISTS $offer_conversions_table_name" );
        $wpdb->query( "DROP TABLE IF EXISTS $offer_declines_table_name" );

        // Clear scheduled crons
        wp_clear_scheduled_hook( $plugin_constants->CRON_HOOK_PAGE_META_CLEANER() );

        // Get all offers created
        $jitso_offers = get_posts( array(
    		'post_type'   => 'jit_sales_offer',
    		'numberposts' => -1,
    		'fields'      => 'ids' // only get the id of the offers
    	) );

        // Force delete all offers so it won't go to trash
    	foreach ( $jitso_offers as $offer_id )
    		$check = wp_delete_post( $offer_id , true );

        flush_rewrite_rules();

    }

}

if ( function_exists( 'is_multisite' ) && is_multisite() ) {

    global $wpdb;

    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

    foreach ( $blog_ids as $blog_id ) {

        switch_to_blog( $blog_id );
        jitso_plugin_cleanup();

    }

    restore_current_blog();

    return;

} else
    jitso_plugin_cleanup();
