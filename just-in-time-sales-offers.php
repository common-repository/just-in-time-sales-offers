<?php
/**
 * Plugin Name: Just In Time Sales Offers
 * Plugin URI: https://marketingsuiteplugin.com
 * Description: WooCommerce extension to automatically show targeted popup sales offers to your customers.
 * Version: 1.2.7
 * Author: Rymera Web Co
 * Author URI: https://rymera.com.au
 * Requires at least: 4.4.2
 * Tested up to: 4.7.0
 *
 * Text Domain: just-in-time-sales-offers
 * Domain Path: /languages/
 *
 * @package Just_In_Time_Sales_Offers
 * @category Core
 * @author Rymera Web Co
 */

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'Just_In_Time_Sales_Offers' ) ) {

    /**
     * Just In Time Sales Offers plugin main class.
     *
     * This serves as the plugin's main Controller.
     *
     * @since 1.0.0.
     */
    final class Just_In_Time_Sales_Offers {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Single main instance of Just In Time Sales Offers plugin.
         *
         * @since 1.0.0
         * @access private
         * @var Just_In_Time_Sales_Offers
         */
        private static $_instance;


        /*
        |--------------------------------------------------------------------------
        | Model Properties.
        |--------------------------------------------------------------------------
        |
        | These properties are instances of various models Events Manager
        | Seat Manager  utilizes. These models handles the logic of the
        | various aspects of the plugin. Ex. Internationalization, loading of
        | various scripts, booting the plugin, and other various business logic.
        |
        */

        /**
         * Property that holds various constants utilized throughout the plugin.
         *
         * @since 1.0.0
         * @access public
         * @var JITSO_Constants
         */
        public $constants;

        /**
         * Property that holds the plugin initial guided tour help pointers.
         * 
         * @since 1.1.2
         * @access public
         * @var JITSO_Initial_Guided_Tour
         */
        public $initial_guided_tour;

        /**
         * Property that holds the plugin offer entry guided tour help pointers.
         * 
         * @since 1.1.2
         * @access public
         * @var JITSO_Offer_Entry_Guided_Tour
         */
        public $offer_entry_guided_tour;

        /**
         * Property that wraps the logic of Internationalization.
         *
         * @since 1.0.0
         * @access public
         * @var JITSO_I18n
         */
        public $i18n;

        /**
         * Property that wraps the logic of loading js and css scripts the plugin utilizes.
         *
         * @since 1.0.0
         * @access public
         * @var JITSO_Script_Loader
         */
        public $script_loader;

        /**
         * Property that wraps the logic of booting up and shutting down the plugin.
         *
         * @since 1.0.0
         * @access public
         * @var JITSO_Bootstrap
         */
        public $bootstrap;


        /*
        |--------------------------------------------------------------------------
        | Site
        |--------------------------------------------------------------------------
        */

        /**
         * Property that houses the logic of the various helper functions related to the current site the plugin is installed.
         * 
         * @since 1.1.0
         * @access public
         * @var JITSO_Site
         */
        public $site;


        /*
        |--------------------------------------------------------------------------
        | Shop
        |--------------------------------------------------------------------------
        */

        /**
         * Property that houses the logic of the various helper functions related to the shop's products.
         * 
         * @since 1.1.0
         * @access public
         * @var JITSO_Product
         */
        public $product;

        /**
         * Property that houses the logic of the various helper functions related to the shop's coupons.
         *
         * @since 1.1.0
         * @access public
         * @var JITSO_Coupon
         */
        public $coupon;


        /*
        |--------------------------------------------------------------------------
        | Offer Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that wraps the logic of 'jit_sales_offer' cpt.
         *
         * @since 1.0.0
         * @access public
         * @var JITSO_Offer_CPT
         */
        public $offer_cpt;

        /**
         * Property that houses the logic of the various helper functions related to the sales offer pages.
         * 
         * @since 1.1.0
         * @access public
         * @var JITSO_Offer_Page
         */
        public $offer_page;

        /**
         * Property that houses the logic of the various helper functions related to the sales offer conditions.
         * 
         * @since 1.1.0
         * @access public
         * @var JITSO_Offer_Condition
         */
        public $offer_condition;

        /**
         * Property that houses the logic of the various helper functions related to the sales offer accept actions.
         * 
         * @since 1.1.0
         * @access public
         * @var JITSO_Offer_Accept_Action
         */
        public $offer_accept_action;

        /**
         * Property that houses the logic of the various helper functions related to the sales offer decline actions.
         *
         * @since 1.1.0
         * @access public
         * @var JITSO_Offer_Decline_Action
         */
        public $offer_decline_action;

        /**
         * Property that wraps the logic of offer.
         *
         * @since 1.0.0
         * @access public
         * @var JITSO_Offer
         */
        public $offer;

        /**
         * Property that wraps the logic of loading sales offer on the right page and on the right conditions.
         *
         * @since 1.0.0
         * @access public
         * @var JITSO_Offer_Loader
         */
        public $offer_loader;

        /**
         * Property that wraps the logic of various shortcode's of the plugin.
         *
         * @since 1.0.0
         * @access public
         * @var JITSO_Offer_Shortcode
         */
        public $offer_shortcode;


        /*
        |--------------------------------------------------------------------------
        | Plugin Integrations
        |--------------------------------------------------------------------------
        */

        // WooCommerce Product Bundles
        
        /**
         * Property that houses the logic of integrating with WooCommerce Product Bundles plugin.
         * 
         * @since 1.1.0
         * @access public
         * @var JITSO_WooCommerce_Product_Bundles
         */
        public $wc_product_bundles;

        // WooCommerce Composite Products

        /**
         * Property that houses the logic of integrating  with WooCommerce Composite Products plugin.
         *
         * @since 1.1.0
         * @access public
         * @var JITSO_WooCommerce_Composite_Products
         */
        public $wc_composite_products;


        /*
        |--------------------------------------------------------------------------
        | AJAX Interfaces
        |--------------------------------------------------------------------------
        */

        /**
         * Property that wraps the logic of ajax interfaces of the plugin.
         * 
         * @since 1.1.0
         * @access public
         * @var JITSO_AJAX_Interfaces
         */
        public $ajax_interface;




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
         * Just_In_Time_Sales_Offers constructor.
         *
         * @since 1.0.0
         * @access public
         */
        public function __construct() {

            register_deactivation_hook( __FILE__ , array( $this , 'general_deactivation_code' ) );            

            if ( $this->_check_plugin_dependencies() ) {

                $this->_load_dependencies();
                $this->_init();
                $this->_exe();

            } else {

                // Display notice that plugin dependency ( WooCommerce ) is not present.
                add_action( 'admin_notices' , array( $this , 'missing_plugin_dependencies_notice' ) );

            }

        }

        /**
         * Ensure that only one instance of Just In Time Sales Offers is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @return Just_In_Time_Sales_Offers
         */
        public static function instance() {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self();

            return self::$_instance;

        }

        /**
         * General code base to be always executed on plugin deactivation.
         *
         * @since 1.2.4
         * @access public
         *
         * @param boolean $network_wide Flag that determines if the plugin is activated network wide.
         */
        public function general_deactivation_code( $network_wide ) {

            global $wpdb;

            // check if it is a multisite network
            if ( is_multisite() ) {

                // check if the plugin has been activated on the network or on a single site
                if ( $network_wide ) {

                    // get ids of all sites
                    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

                    foreach ( $blog_ids as $blog_id ) {

                        switch_to_blog( $blog_id );
                        delete_option( 'jitso_activation_code_triggered' );

                    }

                    restore_current_blog();

                } else {

                    // activated on a single site, in a multi-site
                    delete_option( 'jitso_activation_code_triggered' );

                }

            } else {

                // activated on a single site
                delete_option( 'jitso_activation_code_triggered' );

            }

        }

        /**
         * Check for plugin dependencies of Just In Time Sales Offers plugin.
         *
         * @since 1.0.0
         * @access private
         *
         * @return bool
         */
        private function _check_plugin_dependencies() {

            // Makes sure the plugin is defined before trying to use it
            if ( !function_exists( 'is_plugin_active' ) )
                include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

            return is_plugin_active( 'woocommerce/woocommerce.php' );

        }

        /**
         * Add notice to notify users that a required plugin dependency of Just In Time Sales Offers plugin is missing.
         *
         * @since 1.0.0
         * @access public
         */
        public function missing_plugin_dependencies_notice() {

            $plugin_file = 'woocommerce/woocommerce.php';
            $sptFile = trailingslashit( WP_PLUGIN_DIR ) . plugin_basename( $plugin_file );

            $sptInstallText = '<a href="' . wp_nonce_url( 'update.php?action=install-plugin&plugin=woocommerce', 'install-plugin_woocommerce' ) . '">' . __( 'Click here to install from WordPress.org repo &rarr;' , 'just-in-time-sales-offers' ) . '</a>';
            if ( file_exists( $sptFile ) )
                $sptInstallText = '<a href="' . wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $plugin_file . '&amp;plugin_status=all&amp;s', 'activate-plugin_' . $plugin_file ) . '" title="' . __( 'Activate this plugin' , 'just-in-time-sales-offers' ) . '" class="edit">' . __( 'Click here to activate &rarr;' , 'just-in-time-sales-offers' ) . '</a>'; ?>

            <div class="error">
                <p>
                    <?php _e( '<b>Just In Time Sales Offers</b> plugin missing dependency.<br/><br/>Please ensure you have the <a href="http://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce</a> plugin installed and activated.<br/>' , 'just-in-time-sales-offers' ); ?>
                    <?php echo $sptInstallText; ?>
                </p>
            </div>

            <?php

        }

        /**
         * Load controllers that handles various business logic of the plugin.
         *
         * @since 1.0.0
         * @access private
         */
        private function _load_dependencies() {

            include_once ( 'models/class-jitso-constants.php' );
            include_once ( 'models/class-jitso-helper.php' );
            include_once ( 'models/help-pointers/class-jitso-initial-guided-tour.php' );
            include_once ( 'models/help-pointers/class-jitso-offer-entry-guided-tour.php' );
            include_once ( 'models/class-jitso-i18n.php' );
            include_once ( 'models/class-jitso-script-loader.php' );
            include_once ( 'models/class-jitso-bootstrap.php' );

            // Site
            include_once ( 'models/site/class-jitso-site.php' );

            // Shop
            include_once ( 'models/shop/class-jitso-product.php' );
            include_once ( 'models/shop/class-jitso-coupon.php' );

            // Offer
            include_once ( 'models/offer/class-jitso-offer-cpt.php' );
            include_once ( 'models/offer/class-jitso-offer-page.php' );
            include_once ( 'models/offer/class-jitso-offer-condition.php' );
            include_once ( 'models/offer/class-jitso-offer-accept-action.php' );
            include_once ( 'models/offer/class-jitso-offer-decline-action.php' );
            include_once ( 'models/offer/class-jitso-offer.php' );
            include_once ( 'models/offer/class-jitso-offer-loader.php' );
            include_once ( 'models/offer/class-jitso-offer-shortcode.php' );

            // Plugin Integrations

            // WooCommerce Product Bundles
            include_once ( 'models/plugin-integrations/woocommerce-product-bundles/class-jitso-woocommerce-product-bundles.php' );

            // WooCommerce Composite Products
            include_once ( 'models/plugin-integrations/woocommerce-composite-products/class-jitso-woocommerce-composite-products.php' );

            // Ajax Interface
            include_once ( 'models/class-jitso-ajax-interfaces.php' );

        }

        /**
         * Initialize the plugin.
         *
         * Initialize various property values and instantiate controller properties.
         *
         * @since 1.0.0
         * @access private
         */
        private function _init() {

            /*
             * Note: We are using "Dependency Injection" to inject anything a specific controller requires in order
             * for it to perform its job. This makes models decoupled and is very modular.
             */

            $this->constants               = JITSO_Constants::instance();
            $this->initial_guided_tour     = JITSO_Initial_Guided_Tour::instance();
            $this->offer_entry_guided_tour = JITSO_Offer_Entry_Guided_Tour::instance();
            $common_deps                   = array( 'JITSO_Constants' => $this->constants );

            $this->i18n = JITSO_I18n::instance( $common_deps );

            // Site
            $this->site = JITSO_Site::instance( $common_deps );

            // Shop
            $this->product = JITSO_Product::instance( $common_deps );
            $this->coupon  = JITSO_Coupon::instance( $common_deps );

            // Offer
            $this->offer_cpt            = JITSO_Offer_CPT::instance( array(
                                                                        'JITSO_Constants' => $this->constants,
                                                                        'JITSO_Product'   => $this->product,
                                                                        'JITSO_Coupon'    => $this->coupon
                                                                    ) );
            $this->offer_page           = JITSO_Offer_Page::instance( $common_deps );
            $this->offer_condition      = JITSO_Offer_Condition::instance( array(
                                                                            'JITSO_Constants' => $this->constants,
                                                                            'JITSO_Product'   => $this->product
                                                                        ) );
            $this->offer_accept_action  = JITSO_Offer_Accept_Action::instance( array(
                                                                                'JITSO_Constants' => $this->constants,
                                                                                'JITSO_Product'   => $this->product,
                                                                                'JITSO_Coupon'    => $this->coupon
                                                                            ) );
            $this->offer_decline_action = JITSO_Offer_Decline_Action::instance( $common_deps );
            $this->offer                = JITSO_Offer::instance( $common_deps );
            $this->offer_loader         = JITSO_Offer_Loader::instance( $common_deps );
            $this->offer_shortcode      = JITSO_Offer_Shortcode::instance( $common_deps );

            $this->script_loader = JITSO_Script_Loader::instance( array(
                                                                    'JITSO_Constants'               => $this->constants,
                                                                    'JITSO_Initial_Guided_Tour'     => $this->initial_guided_tour,
                                                                    'JITSO_Offer_Entry_Guided_Tour' => $this->offer_entry_guided_tour,                                                                    
                                                                    'JITSO_Offer_Loader'            => $this->offer_loader
                                                                ) );
            
            // Plugin Integrations

            // WooCommerce Product Bundles
            $this->wc_product_bundles    = JITSO_WooCommerce_Product_Bundles::instance( $common_deps );

            // WooCommerce Composite Products
            $this->wc_composite_products = JITSO_WooCommerce_Composite_Products::instance( $common_deps );

            // Help Pointers
            

            // Ajax Interface
            $this->ajax_interface = JITSO_AJAX_Interfaces::instance( array(
                                                                        'JITSO_Constants'            => $this->constants,
                                                                        'JITSO_Site'                 => $this->site,
                                                                        'JITSO_Product'              => $this->product,
                                                                        'JITSO_Coupon'               => $this->coupon,
                                                                        'JITSO_Offer_Page'           => $this->offer_page,
                                                                        'JITSO_Offer_Condition'      => $this->offer_condition,
                                                                        'JITSO_Offer_Accept_Action'  => $this->offer_accept_action,
                                                                        'JITSO_Offer_Decline_Action' => $this->offer_decline_action,
                                                                        'JITSO_Offer_Loader'         => $this->offer_loader
                                                                    ) );
            
            // Bootstrap
            $bootstrap_deps = array(
                                    'JITSO_Constants'               => $this->constants,
                                    'JITSO_Initial_Guided_Tour'     => $this->initial_guided_tour,
                                    'JITSO_Offer_Entry_Guided_Tour' => $this->offer_entry_guided_tour,
                                    'JITSO_Offer_CPT'               => $this->offer_cpt
                                );
            
            $this->bootstrap = JITSO_Bootstrap::instance( $bootstrap_deps );

        }

        /**
         * Run the plugin. This is the main "method controller", this is where the various processes
         * are being routed to the appropriate models to handle them.
         *
         * @since 1.0.0
         * @access private
         */
        private function _exe() {

            /*
            |--------------------------------------------------------------------------
            | Internationalization
            |--------------------------------------------------------------------------
            */
            add_action( 'plugins_loaded' , array( $this->i18n , 'load_plugin_textdomain' ) );


            /*
            |--------------------------------------------------------------------------
            | Bootstrap
            |--------------------------------------------------------------------------
            */
            register_activation_hook( __FILE__ , array( $this->bootstrap , 'activate_plugin' ) );
            register_deactivation_hook( __FILE__ , array( $this->bootstrap , 'deactivate_plugin' ) );

            // Execute plugin initialization ( plugin activation ) on every newly created site in a multi site set up
            add_action( 'wpmu_new_blog' , array( $this->bootstrap , 'new_mu_site_init' ) , 10 , 6 );

            add_action( 'init' , array( $this->bootstrap , 'initialize' ) );
            add_action( 'init' , array( $this , 'register_ajax_handlers' ) );


            /*
            |--------------------------------------------------------------------------
            | Load JS and CSS Scripts
            |--------------------------------------------------------------------------
            */
            add_action( 'admin_enqueue_scripts' , array( $this->script_loader , 'load_backend_scripts' ) , 10 , 1 );
            add_action( 'wp_enqueue_scripts' , array( $this->script_loader , 'load_frontend_scripts' ) );
            

            /*
            |--------------------------------------------------------------------------
            | WP Integration
            |--------------------------------------------------------------------------
            */

            // Add custom action links for the plugin in the plugin listings
            add_filter( 'plugin_action_links' , array( $this->bootstrap , 'plugin_listing_custom_action_links' ) , 10 , 2 );


            /*
            |--------------------------------------------------------------------------
            | Settings
            |--------------------------------------------------------------------------
            */

            // Register Settings Page
            add_filter( "woocommerce_get_settings_pages" , array( $this->bootstrap , 'initialize_plugin_settings_page' ) , 10 , 1 );
            

            /*
            |--------------------------------------------------------------------------
            | Offers
            |--------------------------------------------------------------------------
            */

            // CPT
            add_action( 'edit_form_after_editor' , array( $this->offer_cpt , 'content_after_editor' ) );
            add_action( 'add_meta_boxes' , array( $this->offer_cpt , 'register_jit_sales_offer_cpt_custom_meta_boxes' ) );
            add_filter( 'default_content', array( $this->offer_cpt , 'default_content' ) , 10, 2 );
            add_action( 'save_post' , array( $this->offer_cpt , 'save_post' ) , 10 , 1 );
            add_action( 'delete_post' , array( $this->offer_cpt , 'delete_offer_stats_data' ) , 10 , 1 );
            
            // CPT Entry Listing Custom Columns
            add_filter( 'manage_jit_sales_offer_posts_columns' , array( $this->offer_cpt , 'add_offer_page_listing_column' ) , 10 , 1 );
            add_action( 'manage_jit_sales_offer_posts_custom_column' , array( $this->offer_cpt , 'add_offer_page_listing_column_data' ) , 10 , 2 );
            
            // Offer Shortcodes
            add_shortcode( 'jitso_accept_offer' , array( $this->offer_shortcode , 'jitso_accept_offer' ) , 10 , 1 );
            add_shortcode( 'jitso_decline_offer' , array( $this->offer_shortcode , 'jitso_decline_offer' ) , 10 , 1 );

            // Offer Conditions
            add_filter( 'jitso_check_product-quantity_sales_offer_condition' , array( $this->offer_condition , 'check_product_quantity_sales_offer_condition' ) , 10 , 4 );

            // Render Just In Time Sales Offer Popup Markup
            add_action( 'wp_footer' , array( $this->offer_loader , 'load_jit_sales_offer' ) );
            
            // Manage orders spawned by jit sales offers
            add_action( 'woocommerce_checkout_order_processed' , array( $this->offer_accept_action , 'link_wc_order_and_jitso_offer_on_order_creation' ) , 10 , 2 );
            add_action( 'woocommerce_order_status_changed' , array( $this->offer_accept_action , 'update_offer_order_metadata' ) , 10 , 3 );
            add_action( 'wp_trash_post' , array( $this->offer_accept_action , 'adjust_spawning_offer_when_spawned_order_is_trashed' ) , 10 , 1 );
            add_action( 'untrashed_post' , array( $this->offer_accept_action , 'adjust_spawning_offer_when_trashed_spawned_order_is_untrashed' ) , 10 , 1 );
            
            
            /*
            |--------------------------------------------------------------------------
            | Third Party Plugin Integrations
            |--------------------------------------------------------------------------
            */

            // WooCommerce Product Bundles
            $this->wc_product_bundles->run();
            
            // WooCommerce Composite Products
            $this->wc_composite_products->run();
            
            
            /*
            |--------------------------------------------------------------------------
            | CRON
            |--------------------------------------------------------------------------
            */

            // Clean offer ids attached to a certain page meta.
            add_action( $this->constants->CRON_HOOK_PAGE_META_CLEANER() , array( $this->site , 'site_pages_offer_meta_cleaner' ) );
            
        }

        /**
         * Register the various ajax interfaces the plugin exposes. This is the main controller for ajax interfaces.
         *
         * @since 1.0.0
         * @access public
         */
        public function register_ajax_handlers() {

            // Plugin Help Pointers
            add_action( 'wp_ajax_jitso_close_initial_guided_tour' , array( $this->initial_guided_tour , 'jitso_close_initial_guided_tour' ) );
            add_action( 'wp_ajax_jitso_close_offer_entry_guided_tour' , array( $this->offer_entry_guided_tour , 'jitso_close_offer_entry_guided_tour' ) );

            // Site
            add_action( 'wp_ajax_jitso_get_site_pages' , array( $this->ajax_interface , 'jitso_get_site_pages' ) );
            

            // Shop
            add_action( 'wp_ajax_jitso_get_coupons' , array( $this->ajax_interface , 'jitso_get_coupons' ) );
            add_action( 'wp_ajax_jitso_get_products' , array( $this->ajax_interface , 'jitso_get_products' ) );
            add_action( 'wp_ajax_jitso_get_product_additional_info' , array( $this->ajax_interface , 'jitso_get_product_additional_info' ) );
            

            // Sales Offer Pages
            add_action( 'wp_ajax_jitso_add_sales_offer_page' , array( $this->ajax_interface , 'jitso_add_sales_offer_page' ) );
            add_action( 'wp_ajax_jitso_edit_sales_offer_page' , array( $this->ajax_interface , 'jitso_edit_sales_offer_page' ) );
            add_action( 'wp_ajax_jitso_delete_sales_offer_page' , array( $this->ajax_interface , 'jitso_delete_sales_offer_page' ) );


            // Sales Offer Conditions
            add_action( 'wp_ajax_jitso_generate_offer_condition_group_markup' , array( $this->ajax_interface , 'jitso_generate_offer_condition_group_markup' ) );
            add_action( 'wp_ajax_jitso_generate_offer_condition_markup' , array( $this->ajax_interface , 'jitso_generate_offer_condition_markup' ) );
            add_action( 'wp_ajax_jitso_generate_product_quantity_in_cart_entry_markup' , array( $this->ajax_interface , 'jitso_generate_product_quantity_in_cart_entry_markup' ) );
            add_action( 'wp_ajax_jitso_save_sales_offer_conditions' , array( $this->ajax_interface , 'jitso_save_sales_offer_conditions' ) );

            // Sales Offer Loader
            add_action( 'wp_ajax_jitso_record_offer_view' , array( $this->ajax_interface , 'jitso_record_offer_view' ) );
            add_action( 'wp_ajax_nopriv_jitso_record_offer_view' , array( $this->ajax_interface , 'jitso_record_offer_view' ) );

            // Accept Offer Actions
            add_action( 'wp_ajax_jitso_get_new_accept_offer_action_markup' , array( $this->ajax_interface , 'jitso_get_new_accept_offer_action_markup' ) );
            add_action( 'wp_ajax_jitso_generate_product_to_add_entry_markup' , array( $this->ajax_interface , 'jitso_generate_product_to_add_entry_markup' ) );
            add_action( 'wp_ajax_jitso_generate_coupon_to_apply_entry_markup' , array( $this->ajax_interface , 'jitso_generate_coupon_to_apply_entry_markup' ) );
            add_action( 'wp_ajax_jitso_save_accept_offer_actions' , array( $this->ajax_interface , 'jitso_save_accept_offer_actions' ) );
            add_action( 'wp_ajax_jitso_execute_accept_offer_actions' , array( $this->ajax_interface , 'jitso_execute_accept_offer_actions' ) );
            add_action( 'wp_ajax_nopriv_jitso_execute_accept_offer_actions' , array( $this->ajax_interface , 'jitso_execute_accept_offer_actions' ) );


            // Decline Offer Actions
            add_action( 'wp_ajax_jitso_save_decline_offer_actions' , array( $this->ajax_interface , 'jitso_save_decline_offer_actions' ) );
            add_action( 'wp_ajax_jitso_execute_decline_offer_actions' , array( $this->ajax_interface , 'jitso_execute_decline_offer_actions' ) );
            add_action( 'wp_ajax_nopriv_jitso_execute_decline_offer_actions' , array( $this->ajax_interface , 'jitso_execute_decline_offer_actions' ) );
            
        }

    }

}

/**
 * Main instance of Just In Time Sales Offers.
 *
 * Returns the main instance of Just In Time Sales Offers to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return Just_In_Time_Sales_Offers
 */
function JITSO() {
    return Just_In_Time_Sales_Offers::instance();
}

// Global for backwards compatibility.
$GLOBALS[ 'jitso' ] = JITSO();