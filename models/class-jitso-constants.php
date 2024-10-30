<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'JITSO_Constants' ) ) {

    /**
     * Class JITSO_Constants
     *
     * Model that houses the various constants After Sale Surveys plugin utilizes.
     *
     * @since 1.0.0
     */
    final class JITSO_Constants {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of JITSO_Constants.
         *
         * @since 1.0.0
         * @access private
         * @var JITSO_Constants
         */
        private static $_instance;

        /**
         * Property that holds the plugin's main file directory path.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_MAIN_PLUGIN_FILE_PATH;

        /**
         * Property that holds the plugin's root directory path.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_PLUGIN_DIR_PATH;

        /**
         * Property that holds the plugin's root directory url.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_PLUGIN_DIR_URL;

        /**
         * Property that holds the plugin's basename.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_PLUGIN_BASENAME;

        /**
         * Property that holds the plugin's unique token.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_TOKEN;

        /**
         * Property that holds the plugin's 'current' version.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_VERSION;

        /**
         * Property that holds the plugin's text domain. Used for internationalization.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_TEXT_DOMAIN;

        /*
         * Property that holds the array of user roles that are allowed to manage "Just In Time Sales Offers" plugin.
         *
         * @since 1.1.0
         * @access private
         * @var array
         */
        private $_ROLES_ALLOWED_TO_MANAGE_JITSO;

        /**
         * Property that holds the 'css' root directory url.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_CSS_ROOT_URL;

        /**
         * Property that holds the 'images' root directory url.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_IMAGES_ROOT_URL;

        /**
         * Property that holds the 'js' root directory url.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_JS_ROOT_URL;

        /**
         * Property that holds the 'models' root directory path.
         *
         * @since 1.0.0
         * @access public
         * @var string
         */
        private $_MODELS_ROOT_PATH;

        /**
         * property that holds 'templates' root directory path.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_TEMPLATES_ROOT_PATH;

        /**
         * Property that holds the path of the current theme overridden plugin template files.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_THEME_TEMPLATE_PATH;

        /**
         * Property that holds the 'views' root directory path.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_VIEWS_ROOT_PATH;

        /**
         * Property that holds the Offer custom post type name.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_OFFER_CPT_NAME;

        /**
         * Property that holds the offer custom post type meta boxes.
         *
         * @since 1.1.2
         * @access private
         * @var array
         */
        private $_OFFER_CPT_META_BOXES;

        /**
         * Property that holds the types of sales offer pages.
         *
         * @since 1.0.0
         * @access private
         * @var array
         */
        private $_SALES_OFFER_PAGE_TYPES;

        /**
         * Property that holds the types of sales offer page that will have custom index.
         *
         * @since 1.0.0
         * @access private
         * @var array
         */
        private $_SALES_OFFER_INDEXED_PAGE_TYPES;

        /**
         * Property that holds the logic of either to show only basic configuration for the sales offer pages option.
         *
         * @since 1.0.0
         * @access private
         * @var bool
         */
        private $_SALES_OFFER_PAGE_TYPES_SIMPLE_MODE;

        /**
         * Property that holds the types of sales offer conditions.
         *
         * @since 1.0.0
         * @access private
         * @var array
         */
        private $_SALES_OFFER_CONDITION_TYPES;

        /**
         * Property that holds the logic of either to show only basic configuration for the sales offer condition options.
         *
         * @since 1.0.0
         * @access private
         * @var bool
         */
        private $_SALES_OFFER_CONDITION_TYPES_SIMPLE_MODE;

        /**
         * Property that holds the product quantity in cart logic conditions ( = , != , > , < ).
         *
         * @since 1.0.0
         * @access private
         * @var array
         */
        private $_PRODUCT_QUANTITY_IN_CART_CONDITIONS;

        /**
         * Property that holds the total number of columns for the offer pages table.
         *
         * @since 1.0.0
         * @access private
         * @var int
         */
        private $_OFFER_PAGES_TABLE_TOTAL_COLUMNS;

        /**
         * Property that holds the total number columns for the product quantity in cart table.
         * Basically used for extensibility in the no product added entry on table. The colspan.
         *
         * @since 1.0.0
         * @access private
         * @var int
         */
        private $_PRODUCT_QUANTITY_IN_CART_TABLE_TOTAL_COLUMNS;

        /**
         * Property that holds the total number of columns for the accept offer action "Add products
         * to cart" table.
         *
         * @since 1.0.0
         * @access private
         * @var int
         */
        private $_ADD_PRODUCTS_TO_CART_TABLE_TOTAL_COLUMNS;

        /**
         * Property that holds the total number of columns for the accept offer action "Apply coupons
         * to cart" table.
         *
         * @since 1.0.0
         * @access private
         * @var int
         */
        private $_APPLY_COUPONS_TO_CART_TABLE_TOTAL_COLUMNS;

        /**
         * Property that holds the accept offer action types.
         *
         * @since 1.0.0
         * @access private
         * @var array
         */
        private $_ACCEPT_OFFER_ACTION_TYPES;

        /**
         * Property that holds the decline offer action types.
         *
         * @since 1.0.0
         * @access private
         * @var array
         */
        private $_DECLINE_OFFER_ACTION_TYPES;

        /**
         * Property that holds the option to either show basic configuration for the decline offer action types.
         *
         * @since 1.0.0
         * @access private
         * @var bool
         */
        private $_DECLINE_OFFER_ACTION_TYPES_SIMPLE_MODE;

        /**
         * Property that holds array of wp capabilities that are allowed to preview jit sales offer.
         * Preview means, to see unpublished jit offers on the front end and other related actions.
         *
         * @since 1.0.0
         * @access private
         * @var array
         */
        private $_ROLES_ALLOWED_FOR_OFFER_PREVIEW;


        /*
        |--------------------------------------------------------------------------
        | Custom Tables
        |--------------------------------------------------------------------------
        */

        /**
         * Offer views custom table name ( Without prefix ).
         *
         * @since 1.2.0
         * @access private
         * @var string
         */
        private $_CUSTOM_TABLE_OFFER_VIEWS;

        /**
         * Option that holds the current offer views custom table version.
         *
         * @since 1.2.0
         * @access private
         * @var string
         */
        private $_CUSTOM_TABLE_OFFER_VIEWS_VERSION;

        /**
         * Offer accepts custom table name ( Withot prefix ).
         *
         * @since 1.2.0
         * @access private
         * @var string
         */
        private $_CUSTOM_TABLE_OFFER_ACCEPTS;

        /**
         * Option that holds the current offer accepts custom table version.
         *
         * @since 1.2.0
         * @access private
         * @var string
         */
        private $_CUSTOM_TABLE_OFFER_ACCEPTS_VERSION;

        /**
         * Offer convesions custom table name ( Without prefix ).
         *
         * @since 1.2.0
         * @access private
         * @var string
         */
        private $_CUSTOM_TABLE_OFFER_CONVERSIONS;

        /**
         * Option that holds the current offer conversions custom table version.
         *
         * @since 1.2.0
         * @access private
         * @var string
         */
        private $_CUSTOM_TABLE_OFFER_CONVERSIONS_VERSION;

        /**
         * Offer declines custom table name ( Withot prefix ).
         *
         * @since 1.2.0
         * @access private
         * @var string
         */
        private $_CUSTOM_TABLE_OFFER_DECLINES;

        /**
         * Option that holds the current offer declines custom table version.
         *
         * @since 1.2.0
         * @access private
         * @var string
         */
        private $_CUSTOM_TABLE_OFFER_DECLINES_VERSION;


        /*
        |--------------------------------------------------------------------------
        | Session
        |--------------------------------------------------------------------------
        */

        /**
         * Session that contains data regarding if a cart/order is spawned by a jit sales offer.
         *
         * @since 1.1.3
         * @access public
         * @var string
         */
        private $_SESSION_JIT_SALES_OFFER_ORDER;


        /*
        |--------------------------------------------------------------------------
        | Post Meta
        |--------------------------------------------------------------------------
        */

        // 'jit_sales_offer' cpt post meta

        /**
         * Property that holds the post meta that determines a sales offer if it is a saved offer or a fresh new offer.
         *
         * @since 1.0.0
         * @access public
         * @var string
         */
        private $_POST_META_SAVED_OFFER;

        /**
         * Property that holds offer's pages post meta.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_POST_META_OFFER_PAGES;

        /**
         * Property that holds offer's conditions post meta.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_POST_META_OFFER_CONDITIONS;

        /**
         * Property that holds page's attached sales offers.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_POST_META_PAGE_SALES_OFFERS;

        /**
         * Property that holds the post meta for the actions to take if an offer is accepted.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_POST_META_OFFER_ACCEPT_ACTIONS;

        /**
         * Property that holds the post meta for the actions to take if an offer is declined.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_POST_META_OFFER_DECLINE_ACTIONS;

        /**
         * Property that holds the data of the offer views.
         *
         * @since 1.1.3
         * @access private
         * @var array
         */
        private $_POST_META_OFFER_VIEWS;

        /**
         * Property that holds the data of the offer accepts.
         *
         * @since 1.1.3
         * @access private
         * @var array
         */
        private $_POST_META_OFFER_ACCEPTS;

        /**
         * Property that holds the data of the offer declines.
         *
         * @since 1.1.3
         * @access private
         * @var array
         */
        private $_POST_META_OFFER_DECLINES;

        // WC Order

        /**
         * Order post meta that holds the jit sales offer id that spawned it.
         *
         * @since 1.1.3
         * @access private
         * @var int
         */
        private $_POST_META_ORDER_JIT_SALES_OFFER_ID;

        /**
         * Order post meta that holds the jit sales offer accept index that spawned it.
         *
         * @since 1.1.3
         * @access private
         * @var int
         */
        private $_POST_META_ORDER_JIT_SALES_OFFER_ACCEPT_STAT_ID;


        /*
        |--------------------------------------------------------------------------
        | Options
        |--------------------------------------------------------------------------
        */

        // Decline

        /**
         * Option that holds the behaviour of the offer popup close button.
         *
         * @since 1.2.0
         * @access private
         * @var string
         */
        private $_OPTION_OFFER_POPUP_CLOSE_BUTTON_BEHAVIOUR;

        // Help Options

        /**
         * Property that holds the option of either cleaning up all plugin options upon plugin un-installation.
         *
         * @since 1.0.0
         * @access private
         * @var string
         */
        private $_OPTION_CLEANUP_PLUGIN_OPTIONS;


        /*
        |--------------------------------------------------------------------------
        | CRON Hooks
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the cron hook name for cleaning page meta from dead offer ids.
         *
         * @since 1.1.0
         * @access private
         * @var string
         */
        private $_CRON_HOOK_PAGE_META_CLEANER;




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
         * JITSO_Constants constructor. Initialize various property values.
         *
         * @since 1.0.0
         * @access public
         */
        public function __construct() {

            // Paths
            $this->_MAIN_PLUGIN_FILE_PATH = WP_PLUGIN_DIR . '/just-in-time-sales-offers/just-in-time-sales-offers.php';

            $this->_PLUGIN_DIR_PATH = plugin_dir_path( $this->_MAIN_PLUGIN_FILE_PATH );
            $this->_PLUGIN_DIR_URL  = plugin_dir_url( $this->_MAIN_PLUGIN_FILE_PATH );
            $this->_PLUGIN_BASENAME = plugin_basename( dirname( $this->_MAIN_PLUGIN_FILE_PATH ) );

            $this->_CSS_ROOT_URL    = $this->_PLUGIN_DIR_URL . 'css/';
            $this->_IMAGES_ROOT_URL = $this->_PLUGIN_DIR_URL . 'images/';
            $this->_JS_ROOT_URL     = $this->_PLUGIN_DIR_URL . 'js/';

            $this->_MODELS_ROOT_PATH    = $this->_PLUGIN_DIR_PATH . 'models/';
            $this->_TEMPLATES_ROOT_PATH = $this->_PLUGIN_DIR_PATH . 'templates/';
            $this->_THEME_TEMPLATE_PATH = apply_filters( 'jitso_theme_template_path' , 'just-in-time-sales-offers' );
            $this->_VIEWS_ROOT_PATH     = $this->_PLUGIN_DIR_PATH . 'views/';

            $this->_TOKEN       = 'jitso';
            $this->_VERSION     = '1.2.7';
            $this->_TEXT_DOMAIN = 'just-in-time-sales-offers';

            $this->_ROLES_ALLOWED_TO_MANAGE_JITSO = apply_filters( 'jitso_roles_allowed_to_manage_jitso' , array( 'administrator' , 'shop_manager' ) );

            $this->_OFFER_CPT_NAME = 'jit_sales_offer';

            $this->_OFFER_CPT_META_BOXES = apply_filters( 'jitso_offer_cpt_meta_boxes' , array(
                'sales-offer-pages' => array(
                    'title'    => __( 'Sales Offer Pages' , 'just-in-time-sales-offers' ),
                    'callback' => 'view_sales_offer_pages_meta_box',
                    'cpt'      => $this->_OFFER_CPT_NAME,
                    'context'  => 'normal',
                    'priority' => 'high'
                ),
                'sales-offer-conditions' => array(
                    'title'    => __( 'Sales Offer Conditions' , 'just-in-time-sales-offers' ),
                    'callback' => 'view_sales_offer_conditions_meta_box',
                    'cpt'      => $this->_OFFER_CPT_NAME,
                    'context'  => 'normal',
                    'priority' => 'high'
                ),
                'accept-offer-actions' => array(
                    'title'    => __( 'Accept Offer Actions' , 'just-in-time-sales-offers' ),
                    'callback' => 'view_accept_offer_actions_meta_box',
                    'cpt'      => $this->_OFFER_CPT_NAME,
                    'context'  => 'normal',
                    'priority' => 'high'
                ),
                'decline-offer-actions' => array(
                    'title'    => __( 'Decline Offer Actions' , 'just-in-time-sales-offers' ),
                    'callback' => 'view_decline_offer_actions_meta_box',
                    'cpt'      => $this->_OFFER_CPT_NAME,
                    'context'  => 'normal',
                    'priority' => 'high'
                ),
                'just-in-time-sales-offers-upgrade' => array(
                    'title'    => __( 'Premium Add-on' , 'just-in-time-sales-offers' ),
                    'callback' => 'view_just_in_time_sales_offers_upgrade_meta_box',
                    'cpt'      => $this->_OFFER_CPT_NAME,
                    'context'  => 'side',
                    'priority' => 'low'
                )
            ) );

            $this->_SALES_OFFER_PAGE_TYPES = apply_filters( 'jitso_sales_offer_page_types' , array(
                'page'             => __( 'Page' , 'just-in-time-sales-offers' ),
                'post'             => __( 'Post' , 'just-in-time-sales-offers' ),
                'product'          => __( 'Product' , 'just-in-time-sales-offers' ),
                'product-category' => __( 'Product Category' , 'just-in-time-sales-offers' )
            ) );

            $this->_SALES_OFFER_INDEXED_PAGE_TYPES = apply_filters( 'jitso_sales_indexed_offer_page_types' , array( 'page' , 'post' , 'product' , 'product-category' ) );

            $this->_SALES_OFFER_PAGE_TYPES_SIMPLE_MODE = apply_filters( 'jitso_sales_offer_page_types_simple_mode' , true );

            $this->_SALES_OFFER_CONDITION_TYPES = apply_filters( 'jitso_sales_offer_condition_types' , array(
                'product-quantity'          => __( 'Product Quantity In The Cart' , 'just-in-time-sales-offers' ),
                'product-category'          => __( 'Product Category Exist In The Cart' , 'just-in-time-sales-offers' ),
                'customer-logged-in-status' => __( 'Customer Logged In Status' , 'just-in-time-sales-offers' ),
                'customer-user-role'        => __( 'Customer User Role' , 'just-in-time-sales-offers' ),
                'cart-subtotal'             => __( 'Cart Subtotal' , 'just-in-time-sales-offers' ),
                'cart-quantity'             => __( 'Cart Quantity' , 'just-in-time-sales-offers' ),
                'has-ordered-before'        => __( 'Has Ordered Before' , 'just-in-time-sales-offers' )
            ) );

            $this->_SALES_OFFER_CONDITION_TYPES_SIMPLE_MODE = apply_filters( 'jitso_sales_offer_condition_types_simple_mode' , true );

            $this->_PRODUCT_QUANTITY_IN_CART_CONDITIONS = apply_filters( 'jitso_product_quantity_in_cart_conditions' , array(
                "="  => __( 'EXACTLY' , 'just-in-time-sales-offers' ),
                "!=" => __( 'ANYTHING EXCEPT' , 'just-in-time-sales-offers' ),
                ">"  => __( 'MORE THAN' , 'just-in-time-sales-offers' ),
                "<"  => __( 'LESS THAN' , 'just-in-time-sales-offers' ),
            ) );

            $this->_OFFER_PAGES_TABLE_TOTAL_COLUMNS              = apply_filters( 'jitso_offer_pages_table_total_columns' , 3 );
            $this->_PRODUCT_QUANTITY_IN_CART_TABLE_TOTAL_COLUMNS = apply_filters( 'jitso_product_quantity_in_cart_table_total_columns' , 4 );
            $this->_ADD_PRODUCTS_TO_CART_TABLE_TOTAL_COLUMNS     = apply_filters( 'jitso_add_products_to_cart_table_total_columns' , 3 );
            $this->_APPLY_COUPONS_TO_CART_TABLE_TOTAL_COLUMNS    = apply_filters( 'jitso_apply_coupons_to_cart_table_total_columns' , 4 );

            $this->_ACCEPT_OFFER_ACTION_TYPES = apply_filters( 'jitso_accept_offer_action_types' , array(
                'add-products-to-cart'  => __( 'Add Products To Cart' , 'just-in-time-sales-offers' ),
                'apply-coupons-to-cart' => __( 'Apply Coupons To Cart' , 'just-in-time-sales-offers' ),
            ) );

            $this->_DECLINE_OFFER_ACTION_TYPES = apply_filters( 'jitso_decline_offer_action_types' , array(
                'do-nothing'         => __( 'Do Nothing' , 'just-in-time-sales-offers' ),
                'page-redirect'      => __( 'Redirect To a Page' , 'just-in-time-sales-offers' ),
                'product-redirect'   => __( 'Redirect To a Product' , 'just-in-time-sales-offers' ),
                'show-another-offer' => __( 'Show Another Offer Straight Away' , 'just-in-time-sales-offers' )
            ) );

            $this->_DECLINE_OFFER_ACTION_TYPES_SIMPLE_MODE = apply_filters( 'jitso_decline_offer_action_types_simple_mode' , true );

            // manage_options : admin
            $this->_ROLES_ALLOWED_FOR_OFFER_PREVIEW = apply_filters( 'jitso_roles_allowed_for_offer_preview' , array( 'administrator' ) );

            // Custom tables names (without prefix) and versions.
            $this->_CUSTOM_TABLE_OFFER_VIEWS               = 'jitso_offer_views';
            $this->_CUSTOM_TABLE_OFFER_VIEWS_VERSION       = 'jitso_offer_views_table_version';
            $this->_CUSTOM_TABLE_OFFER_ACCEPTS             = 'jitso_offer_accepts';
            $this->_CUSTOM_TABLE_OFFER_ACCEPTS_VERSION     = 'jitso_offer_accepts_table_version';
            $this->_CUSTOM_TABLE_OFFER_CONVERSIONS         = 'jitso_offer_conversions';
            $this->_CUSTOM_TABLE_OFFER_CONVERSIONS_VERSION = 'jitso_offer_conversions_version';
            $this->_CUSTOM_TABLE_OFFER_DECLINES            = 'jitso_offer_declines';
            $this->_CUSTOM_TABLE_OFFER_DECLINES_VERSION    = 'jitso_offer_declines_version';

            // Session

            $this->_SESSION_JIT_SALES_OFFER_ORDER = 'jit_sales_offer_order';


            // Post Meta

            // 'jit_sales_offer' cpt post meta
            $this->_POST_META_SAVED_OFFER           = 'jitso_saved_offer';
            $this->_POST_META_OFFER_PAGES           = 'jitso_pages';
            $this->_POST_META_OFFER_CONDITIONS      = 'jitso_conditions';
            $this->_POST_META_PAGE_SALES_OFFERS     = 'jitso_page_sales_offers';
            $this->_POST_META_OFFER_ACCEPT_ACTIONS  = 'jitso_accept_actions';
            $this->_POST_META_OFFER_DECLINE_ACTIONS = 'jitso_decline_actions';
            $this->_POST_META_OFFER_VIEWS           = 'jitso_offer_views';
            $this->_POST_META_OFFER_ACCEPTS         = 'jitso_offer_accept';
            $this->_POST_META_OFFER_DECLINES        = 'jitso_offer_declines';

            // WC Order
            $this->_POST_META_ORDER_JIT_SALES_OFFER_ID             = 'jitso_order_jit_sales_offer_id';
            $this->_POST_META_ORDER_JIT_SALES_OFFER_ACCEPT_STAT_ID = 'jitso_order_jit_sales_offer_accept_index'; // For the sake of simplicity will just use this 'accept_index' to describe the offer accept stat id


            // Options

            // Decline
            $this->_OPTION_OFFER_POPUP_CLOSE_BUTTON_BEHAVIOUR = 'jitso_offer_popup_close_button_behaviour';

            // Help Options
            $this->_OPTION_CLEANUP_PLUGIN_OPTIONS = 'jitso_cleanup_plugin_options';


            // CRON
            $this->_CRON_HOOK_PAGE_META_CLEANER = 'jitso_page_meta_cleaner';

        }

        /**
         * Ensure that there is only one instance of JITSO_Constants is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @return JITSO_Constants
         */
        public static function instance() {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self();

            return self::$_instance;

        }


        /*
        |--------------------------------------------------------------------------
        | Property Getters
        |--------------------------------------------------------------------------
        |
        | Getter functions to read properties of the class.
        | These properties serves as the constants consumed by the plugin.
        |
        */

        /**
         * Return _MAIN_PLUGIN_FILE_PATH. Property that holds the plugin's main file directory path.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function MAIN_PLUGIN_FILE_PATH() {

            return $this->_MAIN_PLUGIN_FILE_PATH;

        }

        /**
         * Return _PLUGIN_DIR_PATH property. Property that holds the plugin's root directory path.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function PLUGIN_DIR_PATH() {

            return $this->_PLUGIN_DIR_PATH;

        }

        /**
         * Return _PLUGIN_DIR_URL property. Property that holds the plugin's root directory url.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function PLUGIN_DIR_URL() {

            return $this->_PLUGIN_DIR_URL;

        }

        /**
         * Return _PLUGIN_BASENAME property. Property that holds the plugin's basename.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function PLUGIN_BASENAME() {

            return $this->_PLUGIN_BASENAME;

        }

        /**
         * Return _TOKEN property. Property that holds the plugin's unique token.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function TOKEN() {

            return $this->_TOKEN;

        }

        /**
         * Return _VERSION property. Property that holds the plugin's 'current' version.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function VERSION() {

            return $this->_VERSION;

        }

        /**
         * Return _TEXT_DOMAIN property. Property that holds the 'views' root directory path.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function TEXT_DOMAIN() {

            return $this->_TEXT_DOMAIN;

        }

        /**
         * Return _ROLES_ALLOWED_TO_MANAGE_JITSO. Property that holds the array of user roles that are allowed to manage "Just In Time Sales Offers" plugin.
         *
         * @since 1.1.0
         * @access public
         * @return array
         */
        public function ROLES_ALLOWED_TO_MANAGE_JITSO() {

            return $this->_ROLES_ALLOWED_TO_MANAGE_JITSO;

        }

        /**
         * Return _CSS_ROOT_URL property. Property that holds the 'css' root directory url.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function CSS_ROOT_URL() {

            return $this->_CSS_ROOT_URL;

        }

        /**
         * Return _IMAGES_ROOT_URL property. Property that holds the 'images' root directory url.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function IMAGES_ROOT_URL() {

            return $this->_IMAGES_ROOT_URL;

        }

        /**
         * Return _JS_ROOT_URL property. Property that holds the 'js' root directory url.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function JS_ROOT_URL() {

            return $this->_JS_ROOT_URL;

        }

        /**
         * Return _MODELS_ROOT_PATH. Property that holds the 'models' root directory path.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function MODELS_ROOT_PATH() {

            return $this->_MODELS_ROOT_PATH;

        }

        /**
         * Return _TEMPLATES_ROOT_PATH. Property that holds 'templates' root directory path.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function TEMPLATES_ROOT_PATH() {

            return $this->_TEMPLATES_ROOT_PATH;

        }

        /**
         * Return _THEME_TEMPLATE_PATH. Property that holds the path of the current theme overridden plugin template files.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function THEME_TEMPLATE_PATH() {

            return $this->_THEME_TEMPLATE_PATH;

        }

        /**
         * Return _VIEWS_ROOT_PATH property. Property that holds the 'views' root directory path.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function VIEWS_ROOT_PATH() {

            return $this->_VIEWS_ROOT_PATH;

        }

        /**
         * Return _OFFER_CPT_NAME. Property that holds the Offer custom post type name.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function OFFER_CPT_NAME() {

            return $this->_OFFER_CPT_NAME;

        }

        /**
         * Return _OFFER_CPT_META_BOXES. Property that holds the offer custom post type meta boxes.
         *
         * @since 1.1.2
         * @access public
         *
         * @return array
         */
        public function OFFER_CPT_META_BOXES() {

            return $this->_OFFER_CPT_META_BOXES;

        }

        /**
         * Return _SALES_OFFER_PAGE_TYPES. Property that holds the types of sales offer pages.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function SALES_OFFER_PAGE_TYPES() {

            return $this->_SALES_OFFER_PAGE_TYPES;

        }

        /**
         * Return _SALES_OFFER_INDEXED_PAGE_TYPES. Property that holds the types of sales offer page that will have custom index.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function SALES_OFFER_INDEXED_PAGE_TYPES() {

            return $this->_SALES_OFFER_INDEXED_PAGE_TYPES;

        }

        /**
         * Return _SALES_OFFER_PAGE_TYPES_SIMPLE_MODE.
         * Property that holds the logic of either to show only basic configuration for the sales offer pages option.
         *
         * @since 1.0.0
         * @access public
         *
         * @return bool
         */
        public function SALES_OFFER_PAGE_TYPES_SIMPLE_MODE() {

            return $this->_SALES_OFFER_PAGE_TYPES_SIMPLE_MODE;

        }

        /**
         * Return _SALES_OFFER_CONDITION_TYPES. Property that holds the types of sales offer conditions.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function SALES_OFFER_CONDITION_TYPES() {

            return $this->_SALES_OFFER_CONDITION_TYPES;

        }

        /**
         * Return _SALES_OFFER_CONDITION_TYPES_SIMPLE_MODE. Property that holds the logic of either to show only basic configuration for the sales offer condition options.
         *
         * @since 1.0.0
         * @access public
         *
         * @return bool
         */
        public function SALES_OFFER_CONDITION_TYPES_SIMPLE_MODE() {

            return $this->_SALES_OFFER_CONDITION_TYPES_SIMPLE_MODE;

        }

        /**
         * Return _PRODUCT_QUANTITY_IN_CART_CONDITIONS. Property that holds the product quantity logic conditions ( = , != , > , < ).
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function PRODUCT_QUANTITY_IN_CART_CONDITIONS() {

            return $this->_PRODUCT_QUANTITY_IN_CART_CONDITIONS;

        }

        /**
         * Return _OFFER_PAGES_TABLE_TOTAL_COLUMNS. Property that holds the total number of columns for the offer pages table.
         *
         * @since 1.0.0
         * @access public
         *
         * @return int
         */
        public function OFFER_PAGES_TABLE_TOTAL_COLUMNS() {

            return $this->_OFFER_PAGES_TABLE_TOTAL_COLUMNS;

        }

        /**
         * Return _PRODUCT_QUANTITY_IN_CART_TABLE_TOTAL_COLUMNS.
         * Property that holds the total number columns for the product quantity in cart table.
         * Basically used for extensibility in the no product added entry on table. The colspan.
         *
         * @since 1.0.0
         * @access public
         *
         * @return int
         */
        public function PRODUCT_QUANTITY_IN_CART_TABLE_TOTAL_COLUMNS() {

            return $this->_PRODUCT_QUANTITY_IN_CART_TABLE_TOTAL_COLUMNS;

        }

        /**
         * Return _ADD_PRODUCTS_IN_CART_TABLE_TOTAL_COLUMNS.
         * Property that holds the total number of columns for the accept offer action "Add products
         * to cart" table.
         *
         * @return int
         */
        public function ADD_PRODUCTS_TO_CART_TABLE_TOTAL_COLUMNS() {

            return $this->_ADD_PRODUCTS_TO_CART_TABLE_TOTAL_COLUMNS;

        }

        /**
         * Return _APPLY_COUPONS_TO_CART_TABLE_TOTAL_COLUMNS.
         * Property that holds the total number of columns for the accept offer action "Apply coupons
         * to cart" table.
         *
         * @return int
         */
        public function APPLY_COUPONS_TO_CART_TABLE_TOTAL_COLUMNS() {

            return $this->_APPLY_COUPONS_TO_CART_TABLE_TOTAL_COLUMNS;

        }

        /**
         * Return _ACCEPT_OFFER_ACTIONS. Property that holds the accept offer action types
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function ACCEPT_OFFER_ACTION_TYPES() {

            return $this->_ACCEPT_OFFER_ACTION_TYPES;

        }

        /**
         * Return _DECLINE_OFFER_ACTION_TYPES. Property that holds the decline offer action types.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function DECLINE_OFFER_ACTION_TYPES() {

            return $this->_DECLINE_OFFER_ACTION_TYPES;

        }

        /**
         * Return _DECLINE_OFFER_ACTION_TYPES_SIMPLE_MODE. Property that holds the option to either show basic configuration for the decline offer action types.
         *
         * @since 1.0.0
         * @access public
         *
         * @return bool
         */
        public function DECLINE_OFFER_ACTION_TYPES_SIMPLE_MODE() {

            return $this->_DECLINE_OFFER_ACTION_TYPES_SIMPLE_MODE;

        }

        /**
         * Return _ROLES_ALLOWED_FOR_OFFER_PREVIEW.
         * Property that holds array of wp capabilities that are allowed to preview jit sales offer.
         * Preview means, to see unpublished jit offers on the front end and other related actions.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function ROLES_ALLOWED_FOR_OFFER_PREVIEW() {

            return $this->_ROLES_ALLOWED_FOR_OFFER_PREVIEW;

        }


        /*
        |--------------------------------------------------------------------------
        | Custom Tables
        |--------------------------------------------------------------------------
        */

        /**
         * Return _CUSTOM_TABLE_OFFER_VIEWS. Offer views custom table name ( Without prefix ).
         *
         * @since 1.2.0
         * @access public
         *
         * @return string
         */
        public function CUSTOM_TABLE_OFFER_VIEWS() {

            return $this->_CUSTOM_TABLE_OFFER_VIEWS;

        }

        /**
         * Return _CUSTOM_TABLE_OFFER_VIEWS_VERSION. Option that holds the current offer views custom table version.
         *
         * @since 1.2.0
         * @access public
         *
         * @return string
         */
        public function CUSTOM_TABLE_OFFER_VIEWS_VERSION() {

            return $this->_CUSTOM_TABLE_OFFER_VIEWS_VERSION;

        }

        /**
         * Return _CUSTOM_TABLE_OFFER_ACCEPTS. Offer accepts custom table name ( Withot prefix ).
         *
         * @since 1.2.0
         * @access public
         *
         * @return string
         */
        public function CUSTOM_TABLE_OFFER_ACCEPTS() {

            return $this->_CUSTOM_TABLE_OFFER_ACCEPTS;

        }

        /**
         * Return _CUSTOM_TABLE_OFFER_ACCEPTS_VERSION. Option that holds the current offer accepts custom table version.
         *
         * @since 1.2.0
         * @access public
         *
         * @return string
         */
        public function CUSTOM_TABLE_OFFER_ACCEPTS_VERSION() {

            return $this->_CUSTOM_TABLE_OFFER_ACCEPTS_VERSION;

        }

        /**
         * Return _CUSTOM_TABLE_OFFER_CONVERSIONS. Offer convesions custom table name ( Without prefix ).
         *
         * @since 1.2.0
         * @access public
         *
         * @return string
         */
        public function CUSTOM_TABLE_OFFER_CONVERSIONS() {

            return $this->_CUSTOM_TABLE_OFFER_CONVERSIONS;

        }

        /**
         * Return _CUSTOM_TABLE_OFFER_CONVERSIONS_VERSION. Option that holds the current offer conversions custom table version.
         *
         * @since 1.2.0
         * @access public
         *
         * @return string
         */
        public function CUSTOM_TABLE_OFFER_CONVERSIONS_VERSION() {

            return $this->_CUSTOM_TABLE_OFFER_CONVERSIONS_VERSION;

        }

        /**
         * Return _CUSTOM_TABLE_OFFER_DECLINES. Offer declines custom table name ( Withot prefix ).
         *
         * @since 1.2.0
         * @access public
         *
         * @return string
         */
        public function CUSTOM_TABLE_OFFER_DECLINES() {

            return $this->_CUSTOM_TABLE_OFFER_DECLINES;

        }

        /**
         * Return _CUSTOM_TABLE_OFFER_DECLINES_VERSION. Option that holds the current offer declines custom table version.
         *
         * @since 1.2.0
         * @access public
         *
         * @return string
         */
        public function CUSTOM_TABLE_OFFER_DECLINES_VERSION() {

            return $this->_CUSTOM_TABLE_OFFER_DECLINES_VERSION;

        }




        /*
        |--------------------------------------------------------------------------
        | Session
        |--------------------------------------------------------------------------
        */

        /**
         * Return _SESSION_JIT_SALES_OFFER_ORDER. Session that contains data regarding if a cart/order is spawned by a jit sales offer.
         *
         * @since 1.1.3
         * @access public
         *
         * @return string
         */
        public function SESSION_JIT_SALES_OFFER_ORDER() {

            return $this->_SESSION_JIT_SALES_OFFER_ORDER;

        }


        /*
        |--------------------------------------------------------------------------
        | Post Meta Property Getters
        |--------------------------------------------------------------------------
        */

        // 'jit_sales_offer' cpt post meta

        /**
         * Return _POST_META_SAVED_OFFER. Property that holds the post meta that determines a sales offer if it is a saved offer or a fresh new offer.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function POST_META_SAVED_OFFER() {

            return $this->_POST_META_SAVED_OFFER;

        }

        /**
         * Return _POST_META_OFFER_PAGE. Property that holds offer's pages post meta.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function POST_META_OFFER_PAGES() {

            return $this->_POST_META_OFFER_PAGES;

        }

        /**
         * Return _POST_META_OFFER_CONDITIONS. Property that holds offer's conditions post meta.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function POST_META_OFFER_CONDITIONS() {

            return $this->_POST_META_OFFER_CONDITIONS;

        }

        /**
         * Return _POST_META_PAGE_SALES_OFFERS. Property that holds page's attached sales offers.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function POST_META_PAGE_SALES_OFFERS() {

            return $this->_POST_META_PAGE_SALES_OFFERS;

        }

        /**
         * Return _POST_META_OFFER_ACCEPT_ACTIONS. Property that holds the post meta for the actions to take if an offer is accepted.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function POST_META_OFFER_ACCEPT_ACTIONS() {

            return $this->_POST_META_OFFER_ACCEPT_ACTIONS;

        }

        /**
         * Return _POST_META_OFFER_DECLINE_ACTIONS. Property that holds the post meta for the actions to take if an offer is declined.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function POST_META_OFFER_DECLINE_ACTIONS() {

            return $this->_POST_META_OFFER_DECLINE_ACTIONS;

        }

        /**
         * Return _POST_META_OFFER_VIEWS. Property that holds the data of the offer views.
         *
         * @since 1.1.3
         * @access public
         *
         * @return array
         */
        public function POST_META_OFFER_VIEWS() {

            return $this->_POST_META_OFFER_VIEWS;

        }

        /**
         * Return _POST_META_OFFER_ACCEPTS. Property that holds the data of the offer accepts.
         *
         * @since 1.1.3
         * @access public
         *
         * @return array
         */
        public function POST_META_OFFER_ACCEPTS() {

            return $this->_POST_META_OFFER_ACCEPTS;

        }

        /**
         * Return _POST_META_OFFER_DECLINES. Property that holds the data of the offer declines.
         *
         * @since 1.1.3
         * @access public
         *
         * @return array
         */
        public function POST_META_OFFER_DECLINES() {

            return $this->_POST_META_OFFER_DECLINES;

        }

        // WC Order

        /**
         * Return _POST_META_ORDER_JIT_SALES_OFFER_ID. Order post meta that holds the jit sales offer id that spawned it.
         *
         * @since 1.1.3
         * @access public
         *
         * @return int
         */
        public function POST_META_ORDER_JIT_SALES_OFFER_ID() {

            return $this->_POST_META_ORDER_JIT_SALES_OFFER_ID;

        }

        /**
         * Return _POST_META_ORDER_JIT_SALES_OFFER_ACCEPT_STAT_ID. Order post meta that holds the jit sales offer accept index that spawned it.
         *
         * @since 1.1.3
         * @access public
         *
         * @return int
         */
        public function POST_META_ORDER_JIT_SALES_OFFER_ACCEPT_STAT_ID() {

            return $this->_POST_META_ORDER_JIT_SALES_OFFER_ACCEPT_STAT_ID;

        }


        /*
        |--------------------------------------------------------------------------
        | Options
        |--------------------------------------------------------------------------
        */

        // Decline

        /**
         * Return _OPTION_OFFER_POPUP_CLOSE_BUTTON_BEHAVIOUR. Option that holds the behaviour of the offer popup close button.
         *
         * @since 1.2.0
         * @access private
         *
         * @return string
         */
        public function OPTION_OFFER_POPUP_CLOSE_BUTTON_BEHAVIOUR() {

            return $this->_OPTION_OFFER_POPUP_CLOSE_BUTTON_BEHAVIOUR;

        }

        // Help

        /**
         * Return _OPTION_CLEANUP_PLUGIN_OPTIONS. Property that holds the option of either cleaning up all plugin options upon plugin un-installation.
         *
         * @since 1.0.0
         * @access public
         *
         * @return string
         */
        public function OPTION_CLEANUP_PLUGIN_OPTIONS() {

            return $this->_OPTION_CLEANUP_PLUGIN_OPTIONS;

        }


        /*
        |--------------------------------------------------------------------------
        | CRON Hooks
        |--------------------------------------------------------------------------
        */

        /**
         * Return _CRON_HOOK_PAGE_META_CLEANER. Property that holds the cron hook name for cleaning page meta from dead offer ids.
         *
         * @since 1.1.0
         * @access public
         *
         * @return string
         */
        public function CRON_HOOK_PAGE_META_CLEANER() {

            return $this->_CRON_HOOK_PAGE_META_CLEANER;

        }

    }

}
