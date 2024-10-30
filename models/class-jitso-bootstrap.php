<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'JITSO_Bootstrap' ) ) {

    /**
     * Class JITSO_Bootstrap
     *
     * Model that houses the logic of booting up (activating) and shutting down (deactivating) Just In Time Sales Offers plugin.
     *
     * @since 1.0.0
     */
    final class JITSO_Bootstrap {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of JITSO_Bootstrap.
         *
         * @since 1.0.0
         * @access private
         * @var JITSO_Bootstrap
         */
        private static $_instance;

        /**
         * Property that holds various constants utilized throughout the plugin.
         *
         * @since 1.0.0
         * @access private
         * @var JITSO_Constants
         */
        private $_plugin_constants;

        /**
         * Property that holds the plugin initial guided tour help pointers.
         *
         * @since 1.1.2
         * @access private
         * @var JITSO_Initial_Guided_Tour
         */
        private $_initial_guided_tour;

        /**
         * Property that holds the plugin offer entry guided tour help pointers.
         *
         * @since 1.1.2
         * @access private
         * @var JITSO_Offer_Entry_Guided_Tour
         */
        private $_offer_entry_guided_tour;

        /**
         * Property that wraps the logic of jit sales offer custom post type.
         *
         * @since 1.0.0
         * @access private
         * @var JITSO_Offer_CPT
         */
        private $_offer_cpt;




        /*
        |--------------------------------------------------------------------------
        | Class Methods
        |--------------------------------------------------------------------------
        */

        /**
         * Cloning is forbidden.
         *
         * @since 1.0.0
         * @access public
         */
        public function __clone () {

            _doing_it_wrong( __FUNCTION__ , __( 'Cheatin&#8217; huh?' , 'just-in-time-sales-offers' ) , '1.0.0' );

        }

        /**
         * Unserializing instances of this class is forbidden.
         *
         * @since 1.0.0
         * @access public
         */
        public function __wakeup () {

            _doing_it_wrong( __FUNCTION__ , __( 'Cheatin&#8217; huh?' , 'just-in-time-sales-offers' ) , '1.0.0' );

        }

        /**
         * JITSO_Bootstrap constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of JITSO_Bootstrap model.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants        = $dependencies[ 'JITSO_Constants' ];
            $this->_initial_guided_tour     = $dependencies[ 'JITSO_Initial_Guided_Tour' ];
            $this->_offer_entry_guided_tour = $dependencies[ 'JITSO_Offer_Entry_Guided_Tour' ];
            $this->_offer_cpt               = $dependencies[ 'JITSO_Offer_CPT' ];

        }

        /**
         * Ensure that only one instance of JITSO_Bootstrap is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of JITSO_Bootstrap model.
         * @return JITSO_Bootstrap
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Method that houses the logic relating to activating Just In Time Sales Offers plugin.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $network_wide
         */
        public function activate_plugin( $network_wide ) {

            global $wpdb;

            if ( is_multisite() ) {

                if ( $network_wide ) {

                    // get ids of all sites
                    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

                    foreach ( $blog_ids as $blog_id ) {

                        switch_to_blog( $blog_id );
                        $this->_activate_plugin( $blog_id );

                    }

                    restore_current_blog();

                } else {

                    // activated on a single site, in a multi-site
                    $this->_activate_plugin( $wpdb->blogid );

                }

            } else {

                // activated on a single site
                $this->_activate_plugin( $wpdb->blogid );

            }

        }

        /**
         * Method to initialize a newly created site in a multi site set up.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $blog_id
         * @param $user_id
         * @param $domain
         * @param $path
         * @param $site_id
         * @param $meta
         */
        public function new_mu_site_init( $blog_id , $user_id , $domain , $path , $site_id , $meta ) {

            if ( is_plugin_active_for_network( 'just-in-time-sales-offers/just-in-time-sales-offers.php' ) ) {

                switch_to_blog( $blog_id );
                $this->_activate_plugin( $blog_id );
                restore_current_blog();

            }

        }

        /**
         * Actual function that houses the code to execute on plugin activation.
         *
         * @since 1.0.0
         * @access private
         *
         * @param $blogid
         */
        private function _activate_plugin( $blogid ) {

            $this->_offer_cpt->register_jit_sales_offer_cpt();

            // Set initial value of 'no' for the option that sets the option that specify whether to delete the options on plugin uninstall
            if ( !get_option( $this->_plugin_constants->OPTION_CLEANUP_PLUGIN_OPTIONS() , false ) )
                update_option( $this->_plugin_constants->OPTION_CLEANUP_PLUGIN_OPTIONS() , 'no' );

            // Set cron to run 'daily' by default to clean offer id attached to a page meta
            $recurrence = apply_filters( 'jitso_page_meta_cleaner_recurrence' , 'daily' );
            wp_schedule_event( time() , $recurrence , $this->_plugin_constants->CRON_HOOK_PAGE_META_CLEANER() );

            $this->_initial_guided_tour->initialize_guided_tour_options();
            $this->_offer_entry_guided_tour->initialize_guided_tour_options();

            // Add custom tables
            $this->create_plugin_custom_tables();
            $this->migrate_offer_stats_from_post_meta_to_custom_table();

            flush_rewrite_rules();

            update_option( 'jitso_activation_code_triggered' , 'yes' );

        }

        /**
         * Method that houses the logic relating to deactivating Just In Time Sales Offers plugin.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $network_wide
         */
        public function deactivate_plugin( $network_wide ) {

            global $wpdb;

            // check if it is a multisite network
            if ( is_multisite() ) {

                // check if the plugin has been activated on the network or on a single site
                if ( $network_wide ) {

                    // get ids of all sites
                    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

                    foreach ( $blog_ids as $blog_id ) {

                        switch_to_blog( $blog_id );
                        $this->_deactivate_plugin( $wpdb->blogid );

                    }

                    restore_current_blog();

                } else {

                    // activated on a single site, in a multi-site
                    $this->_deactivate_plugin( $wpdb->blogid );

                }

            } else {

                // activated on a single site
                $this->_deactivate_plugin( $wpdb->blogid );

            }

        }

        /**
         * Actual method that houses the code to execute on plugin deactivation.
         *
         * @since 1.0.0
         * @access private
         *
         * @param $blogid
         */
        private function _deactivate_plugin( $blogid ) {

            // Unset cron to run 'daily' by default to clean offer id attached to a page meta
            $timestamp = wp_next_scheduled( $this->_plugin_constants->CRON_HOOK_PAGE_META_CLEANER() );
            wp_unschedule_event( $timestamp , $this->_plugin_constants->CRON_HOOK_PAGE_META_CLEANER() );

            $this->_initial_guided_tour->terminate_guided_tour_options();
            $this->_offer_entry_guided_tour->terminate_guided_tour_options();

            flush_rewrite_rules();

        }

        /**
         * Method that houses codes to be executed on init hook.
         *
         * @since 1.0.0
         * @access public
         */
        public function initialize() {

            if ( get_option( 'jitso_activation_code_triggered' , false ) !== 'yes' ) {

                if ( ! function_exists( 'is_plugin_active_for_network' ) )
                    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

                $network_wide = is_plugin_active_for_network( 'just-in-time-sales-offers/just-in-time-sales-offers.php' );
                $this->activate_plugin( $network_wide );

            }

            $this->_offer_cpt->register_jit_sales_offer_cpt();

        }

        /**
         * Initialize the plugin's settings page. Integrate to WooCommerce settings.
         *
         * @since 1.0.0
         * @access public
         *
		 * @param $settings
		 * @param $models_root_path
         * @return array
         */
        public function initialize_plugin_settings_page( $settings , $models_root_path = null ) {

			if ( is_null( $models_root_path ) )
				$models_root_path = $this->_plugin_constants->MODELS_ROOT_PATH();

            $settings[] = include( $models_root_path . "class-jitso-settings.php" );
            return $settings;

        }

        /**
         * Create plugin custom tables.
         *
         * @since 1.2.0
         * @access public
         */
        public function create_plugin_custom_tables() {

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            global $wpdb;

            $charset_collate = $wpdb->get_charset_collate();

            $latest_offer_views_table_version       = '1.0.0';
            $latest_offer_accepts_table_version     = '1.0.0';
            $latest_offer_conversions_table_version = '1.0.0';
            $latest_offer_declines_table_version    = '1.0.0';

            $installed_offer_views_table_version       = get_option( $this->_plugin_constants->CUSTOM_TABLE_OFFER_VIEWS_VERSION() );
            $installed_offer_accepts_table_version     = get_option( $this->_plugin_constants->CUSTOM_TABLE_OFFER_ACCEPTS_VERSION() );
            $installed_offer_conversions_table_version = get_option( $this->_plugin_constants->CUSTOM_TABLE_OFFER_CONVERSIONS_VERSION() );
            $installed_offer_declines_table_version    = get_option( $this->_plugin_constants->CUSTOM_TABLE_OFFER_DECLINES_VERSION() );

            if ( $latest_offer_views_table_version != $installed_offer_views_table_version ) {

                // Create offer views table
                $offer_views_table_name = $wpdb->prefix . $this->_plugin_constants->CUSTOM_TABLE_OFFER_VIEWS();

                $offer_views_table_sql = "CREATE TABLE $offer_views_table_name (
                                            id int NOT NULL AUTO_INCREMENT,
                                            offer_id int NOT NULL,
                                            user_email varchar(100) NOT NULL,
                                            url text DEFAULT '' NOT NULL,
                                            ip varchar(50) NOT NULL,
                                            user_agent text DEFAULT '' NOT NULL,
                                            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                                            PRIMARY KEY  (id)
                                            ) $charset_collate;";

                dbDelta( $offer_views_table_sql );

                update_option( $this->_plugin_constants->CUSTOM_TABLE_OFFER_VIEWS_VERSION() , $latest_offer_views_table_version );

            }

            if ( $latest_offer_accepts_table_version != $installed_offer_accepts_table_version ) {

                // Create offer accepts table
                $offer_accepts_table_name = $wpdb->prefix . $this->_plugin_constants->CUSTOM_TABLE_OFFER_ACCEPTS();

                $offer_accepts_table_sql = "CREATE TABLE $offer_accepts_table_name (
                                            id int NOT NULL AUTO_INCREMENT,
                                            offer_id int NOT NULL,
                                            user_email varchar(100) NOT NULL,
                                            url text DEFAULT '' NOT NULL,
                                            ip varchar(50) NOT NULL,
                                            user_agent text DEFAULT '' NOT NULL,
                                            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                                            PRIMARY KEY  (id)
                                            ) $charset_collate;";

                dbDelta( $offer_accepts_table_sql );

                update_option( $this->_plugin_constants->CUSTOM_TABLE_OFFER_ACCEPTS_VERSION() , $latest_offer_accepts_table_version );

            }

            if ( $latest_offer_conversions_table_version != $installed_offer_conversions_table_version ) {

                // Create offer conversions table
                $offer_conversions_table_name = $wpdb->prefix . $this->_plugin_constants->CUSTOM_TABLE_OFFER_CONVERSIONS();

                $offer_conversions_table_sql = "CREATE TABLE $offer_conversions_table_name (
                                                id int NOT NULL AUTO_INCREMENT,
                                                accept_id int NOT NULL,
                                                offer_id int NOT NULL,
                                                order_id int NOT NULL,
                                                order_status varchar(50) NOT NULL,
                                                order_modified_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                                                PRIMARY KEY  (id)
                                                ) $charset_collate;";

                dbDelta( $offer_conversions_table_sql );

                update_option( $this->_plugin_constants->CUSTOM_TABLE_OFFER_CONVERSIONS_VERSION() , $latest_offer_conversions_table_version );

            }

            if ( $latest_offer_declines_table_version != $installed_offer_declines_table_version ) {

                // Create offer declines table
                $offer_declines_table_name = $wpdb->prefix . $this->_plugin_constants->CUSTOM_TABLE_OFFER_DECLINES();

                $offer_declines_table_sql = "CREATE TABLE $offer_declines_table_name (
                                            id int NOT NULL AUTO_INCREMENT,
                                            offer_id int NOT NULL,
                                            user_email varchar(100) NOT NULL,
                                            url text DEFAULT '' NOT NULL,
                                            ip varchar(50) NOT NULL,
                                            user_agent text DEFAULT '' NOT NULL,
                                            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                                            PRIMARY KEY  (id)
                                            ) $charset_collate;";

                dbDelta( $offer_declines_table_sql );

                update_option( $this->_plugin_constants->CUSTOM_TABLE_OFFER_DECLINES_VERSION() , $latest_offer_declines_table_version );

            }

        }

        /**
         * JITSO 1.1.x series saves offer stats on offer cpt meta.
         * We get those existing data and move it to the new custom table introduced on JITSO 1.2.x series.
         * TODO: deprecate this function in the future.
         *
         * @since 1.2.0
         * @access public
         */
        public function migrate_offer_stats_from_post_meta_to_custom_table() {

            global $wpdb;

            $jit_offers = JITSO_Helper::get_all_jit_sales_offers();

            // Migrate offer views stats
            $offer_views_table_name = $wpdb->prefix . $this->_plugin_constants->CUSTOM_TABLE_OFFER_VIEWS();

            if ( $wpdb->get_var( "SHOW TABLES LIKE '$offer_views_table_name'" ) ) {

                foreach ( $jit_offers as $jit_offer ) {

                    $offer_views = get_post_meta( $jit_offer->ID , $this->_plugin_constants->POST_META_OFFER_VIEWS() , true );
                    if ( !is_array( $offer_views ) )
                        $offer_views = array();

                    foreach ( $offer_views as $offer_view ) {

                        $wpdb->insert(
                            $offer_views_table_name,
                            array(
                                'offer_id'   => $jit_offer->ID,
                                'user_email' => $offer_view[ 'current_user' ]->ID ? $offer_view[ 'current_user' ]->user_email : '',
                                'url'        => $offer_view[ 'current_url' ],
                                'ip'         => $offer_view[ 'client_ip' ],
                                'user_agent' => $offer_view[ 'user_agent' ],
                                'time'       => date( 'Y-m-d H:i:s' , $offer_view[ 'timestamp' ] )
                            )
                        );

                    }

                    delete_post_meta( $jit_offer->ID , $this->_plugin_constants->POST_META_OFFER_VIEWS() );

                }

            }

            // Migrate offer accepts and conversions stats
            $offer_accepts_table_name     = $wpdb->prefix . $this->_plugin_constants->CUSTOM_TABLE_OFFER_ACCEPTS();
            $offer_conversions_table_name = $wpdb->prefix . $this->_plugin_constants->CUSTOM_TABLE_OFFER_CONVERSIONS();

            if ( $wpdb->get_var( "SHOW TABLES LIKE '$offer_accepts_table_name'" ) && $wpdb->get_var( "SHOW TABLES LIKE '$offer_conversions_table_name'" ) ) {

                foreach ( $jit_offers as $jit_offer ) {

                    $offer_accepts = get_post_meta( $jit_offer->ID , $this->_plugin_constants->POST_META_OFFER_ACCEPTS() , true );
                    if ( !is_array( $offer_accepts ) )
                        $offer_accepts = array();

                    foreach ( $offer_accepts as $offer_accept ) {

                        $record_stats_status = $wpdb->insert(
                                                    $offer_accepts_table_name,
                                                    array(
                                                        'offer_id'   => $jit_offer->ID,
                                                        'user_email' => $offer_accept[ 'current_user' ]->ID ? $offer_accept[ 'current_user' ]->user_email : '',
                                                        'url'        => $offer_accept[ 'current_url' ],
                                                        'ip'         => $offer_accept[ 'client_ip' ],
                                                        'user_agent' => $offer_accept[ 'user_agent' ],
                                                        'time'       => date( 'Y-m-d H:i:s' , $offer_accept[ 'timestamp' ] )
                                                    )
                                                );

                        if ( $record_stats_status && isset( $offer_accept[ 'order-id' ] ) ) {

                            $wpdb->insert(
                                $offer_conversions_table_name,
                                array(
                                    'accept_id'           => $wpdb->insert_id, // Last inserted id of the offer accepts table
                                    'offer_id'            => $jit_offer->ID,
                                    'order_id'            => $offer_accept[ 'order-id' ],
                                    'order_status'        => $offer_accept[ 'order-status' ],
                                    'order_modified_date' => date( 'Y-m-d H:i:s' , $offer_accept[ 'order-timestamp' ] )
                                )
                            );

                        }

                    }

                    delete_post_meta( $jit_offer->ID , $this->_plugin_constants->POST_META_OFFER_ACCEPTS() );

                }

            }

            // Migrate offer declines stats
            $offer_declines_table_name = $wpdb->prefix . $this->_plugin_constants->CUSTOM_TABLE_OFFER_DECLINES();

            if ( $wpdb->get_var( "SHOW TABLES LIKE '$offer_declines_table_name'" ) ) {

                foreach ( $jit_offers as $jit_offer ) {

                    $offer_declines = get_post_meta( $jit_offer->ID , $this->_plugin_constants->POST_META_OFFER_DECLINES() , true );
                    if ( !is_array( $offer_declines ) )
                        $offer_declines = array();

                    foreach ( $offer_declines as $offer_decline ) {

                        $wpdb->insert(
                            $offer_declines_table_name,
                            array(
                                'offer_id'   => $jit_offer->ID,
                                'user_email' => $offer_decline[ 'current_user' ]->ID ? $offer_decline[ 'current_user' ]->user_email : '',
                                'url'        => $offer_decline[ 'current_url' ],
                                'ip'         => $offer_decline[ 'client_ip' ],
                                'user_agent' => $offer_decline[ 'user_agent' ],
                                'time'       => date( 'Y-m-d H:i:s' , $offer_decline[ 'timestamp' ] )
                            )
                        );

                    }

                    delete_post_meta( $jit_offer->ID , $this->_plugin_constants->POST_META_OFFER_DECLINES() );

                }

            }

        }




       /*
        |--------------------------------------------------------------------------
        | WP Integration
        |--------------------------------------------------------------------------
        */

        /**
         * Add plugin settings link custom action for the plugin in the plugin listings.
         *
         * @since 1.1.0
         * @access public
         *
         * @param $links array Plugin action links.
         * @param $file string Current plugin base path.
         * @return array Modified plugin action links.
         */
        public function plugin_listing_custom_action_links( $links , $file ) {

            if ( $file == $this->_plugin_constants->PLUGIN_BASENAME() . '/just-in-time-sales-offers.php' ) {

                $settings_link = '<a href="admin.php?page=wc-settings&tab=jitso_settings">' . __( 'Settings' , 'just-in-time-sales-offers' ) . '</a>';
                $help_link     = '<a href="https://marketingsuiteplugin.com/knowledge-base/just-in-time-sales-offers/?utm_source=JITSO&utm_medium=Settings%20Help&utm_campaign=JITSO">' . __( 'Help' , 'just-in-time-sales-offers' ) . '</a>';
                
                array_unshift( $links , $help_link , $settings_link );

            }

            return $links;

        }

    }

}
