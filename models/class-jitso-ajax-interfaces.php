<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'JITSO_AJAX_Interfaces' ) ) {

    /**
     * Property that wraps the ajax interfaces of the plugin.
     *
     * Class JITSO_AJAX_Interfaces
     */
    final class JITSO_AJAX_Interfaces {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of JITSO_AJAX_Interfaces.
         *
         * @since 1.0.0
         * @access private
         * @var JITSO_AJAX_Interfaces
         */
        private static $_instance;

        /**
         * JITSO_Constants instance. Holds various constants this class uses.
         *
         * @since 1.0.0
         * @access private
         * @var JITSO_Constants
         */
        private $_plugin_constants;

        /**
         * Property that wraps the logic relating to the current site the plugin is installed.
         * 
         * @since 1.1.0
         * @access private
         * @var JITSO_Site
         */
        private $_site;

        /**
         * Property that houses the logic of the various helper functions related to the shop's product.
         * 
         * @since 1.1.0
         * @access private
         * @var JITSO_Product
         */
        private $_product;

        /**
         * Property that houses the logic of the various helper functions related to the shop's coupons.
         *
         * @since 1.1.0
         * @access private
         * @var JITSO_Coupon
         */
        private $_coupon;

        /**
         * Property that houses the logic of the various helper functions related to the sales offer pages.
         * 
         * @since 1.1.0
         * @access private
         * @var JITSO_Offer_Page
         */
        private $_offer_page;

        /**
         * Property that houses the logic of the various helper functions related to the sales offer conditions.
         * 
         * @since 1.1.0
         * @access private
         * @var JITSO_Offer_Condition
         */
        private $_offer_condition;

        /**
         * Property that houses the logic of the various helper functions related to the sales offer accept actions.
         * 
         * @since 1.1.0
         * @access private
         * @var JITSO_Offer_Accept_Action
         */
        private $_offer_accept_action;

        /**
         * Property that houses the logic of the various helper functions related to the sales offer decline actions.
         *
         * @since 1.1.0
         * @access private
         * @var JITSO_Offer_Decline_Action
         */
        private $_offer_decline_action;

        /**
         * Property that wraps the logic of loading sales offer on the right page and on the right conditions.
         *
         * @since 1.0.0
         * @access private
         * @var JITSO_Offer_Loader
         */
        private $_offer_loader;




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
         * JITSO_AJAX_Interfaces constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants     = $dependencies[ 'JITSO_Constants' ];
            $this->_site                 = $dependencies[ 'JITSO_Site' ];
            $this->_product              = $dependencies[ 'JITSO_Product' ];
            $this->_coupon               = $dependencies[ 'JITSO_Coupon' ];
            $this->_offer_page           = $dependencies[ 'JITSO_Offer_Page' ];
            $this->_offer_condition      = $dependencies[ 'JITSO_Offer_Condition' ];
            $this->_offer_accept_action  = $dependencies[ 'JITSO_Offer_Accept_Action' ];
            $this->_offer_decline_action = $dependencies[ 'JITSO_Offer_Decline_Action' ];
            $this->_offer_loader         = $dependencies[ 'JITSO_Offer_Loader' ];

        }

        /**
         * Ensure that there is only one instance of JITSO_AJAX_Interfaces is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         * @return JITSO_AJAX_Interfaces
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }




        /*
        |--------------------------------------------------------------------------
        | AJAX Callbacks
        |--------------------------------------------------------------------------
        */

        /**
         * Get all the site pages.
         *
         * @since 1.1.0
         * @access public
         */
        public function jitso_get_site_pages() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'jitso-get-site-pages' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'just-in-time-sales-offers' ) );

                $args = $_POST[ 'args' ];

                $site_pages = $this->_site->get_site_pages( $args );

                if ( is_wp_error( $site_pages ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => __( 'Failed to get site pages.' , 'just-in-time-sales-offers' )
                    );

                } else {

                    $response = array(
                        'status'     => 'success',
                        'site_pages' => $site_pages
                    );

                }

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid Ajax Call' , 'just-in-time-sales-offers' ) );
            
        }

        /**
         * Get shop coupons.
         *
         * @since 1.1.0
         * @access public
         */
        public function jitso_get_coupons() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'jitso-get-coupons' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'just-in-time-sales-offers' ) );

                $args = $_POST[ 'args' ];
                
                $coupons = $this->_coupon->get_coupons( $args );

                if ( is_wp_error( $coupons ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => __( 'Failed to get coupons.' , 'just-in-time-sales-offers' )
                    );

                } else {

                    $response = array(
                        'status'  => 'success',
                        'coupons' => $return_coupons
                    );

                }

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'just-in-time-sales-offers' ) );

        }

        /**
         * Get site products.
         *
         * @since 1.1.0
         * @access public
         */
        public function jitso_get_products() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'jitso-get-products' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'just-in-time-sales-offers' ) );
                
                $args = $_POST[ 'args' ];

                $products = $this->_product->get_products( $args );

                if ( is_wp_error( $products ) ) {

                    $response = array(
                        'status'        => 'success',
                        'error_message' => __( 'Failed to get products.' , 'just-in-time-sales-offers' )
                    );

                } else {

                    $response = array(
                        'status'   => 'success',
                        'products' => $return_products
                    );
                    
                }

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid Ajax Call' , 'just-in-time-sales-offers' ) );

        }

        /**
         * Get additional info about a product.
         *
         * @since 1.1.0
         * @access public
         */
        public function jitso_get_product_additional_info() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'jitso-get-product-additional-info' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'just-in-time-sales-offers' ) );
                
                $product_id = $_POST[ 'product_id' ];   

                $product_additional_info = $this->_product->get_product_additional_info( $product_id );

                if ( is_wp_error( $product_additional_info ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => __( 'Failed to get product additional info.' , 'just-in-time-sales-offers' )
                    );

                } else {

                    $response = array(
                        'status'       => 'success',
                        'product_data' => $product_additional_info
                    );

                }          

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'just-in-time-sales-offers' ) );
            
        }


        // Sales Offer Pages

        /**
         * Add sales offer page.
         *
         * @since 1.1.0
         * @access public
         */
        public function jitso_add_sales_offer_page() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'jitso-add-sales-offer-page' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'just-in-time-sales-offers' ) );
                
                $offer_id  = $_POST[ 'offer_id' ];
                $page_data = apply_filters( 'jitso_new_sales_offer_page_data' , $_POST[ 'page_data' ] , $offer_id );

                $result = $this->_offer_page->add_sales_offer_page( $offer_id , $page_data );

                if ( is_wp_error( $result ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $result->get_error_message()
                    );

                } else
                    $response = $result;

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'just-in-time-sales-offers' ) );

        }

        /**
         * Edit sales offer page.
         *
         * @since 1.1.0
         * @access public
         */
        public function jitso_edit_sales_offer_page() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
                
                if ( !check_ajax_referer( 'jitso-edit-sales-offer-page' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'just-in-time-sales-offers' ) );
                
                $offer_id  = $_POST[ 'offer_id' ];
                $key       = $_POST[ 'key' ];
                $page_data = apply_filters( 'jitso_edited_sales_offer_page_data' , $_POST[ 'page_data' ] , $offer_id );

                $result = $this->_offer_page->edit_sales_offer_page( $offer_id , $key , $page_data );

                if ( is_wp_error( $result ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $result->get_error_message()
                    );

                } else
                    $response = $result;
                
                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();
                
            } else
                wp_die( __( 'Invalid AJAX Call' , 'just-in-time-sales-offers' ) );
            
        }

        /**
         * Delete sales offer page.
         *
         * @since 1.1.0
         * @access public
         */
        public function jitso_delete_sales_offer_page() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'jitso-delete-sales-offer-page' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'just-in-time-sales-offers' ) );

                $offer_id = $_POST[ 'offer_id' ];
                $key      = $_POST[ 'key' ];

                $result = $this->_offer_page->delete_sales_offer_page( $offer_id , $key );

                if ( is_wp_error( $result ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $result->get_error_message()
                    );

                } else
                    $response = array( 'status' => 'success' );

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'just-in-time-sales-offers' ) );
            
        }


        // Sales Offer Condition

        /**
         * Generate offer condition group markup.
         *
         * @since 1.1.0
         * @access public
         */
        public function jitso_generate_offer_condition_group_markup() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'jitso-generate-offer-condition-group-markup' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'just-in-time-sales-offers' ) );

                $offer_id = $_POST[ 'offer_id' ];
                $args     = apply_filters( 'jitso_offer_condition_group_args' , $_POST[ 'args' ] , $offer_id );

                $mark_up = $this->_offer_condition->generate_offer_condition_group_markup( $offer_id , $args );

                if ( is_wp_error( $mark_up ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $mark_up->get_error_message()
                    );

                } else {

                    $response = array(
                        'status'  => 'success',
                        'mark_up' => $mark_up
                    );

                }

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'just-in-time-sales-offers' ) );
            
        }

        /**
         * Get new offer condition markup.
         *
         * @since 1.1.0
         * @access public
         */
        public function jitso_generate_offer_condition_markup() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'jitso-generate-offer-condition-markup' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'just-in-time-sales-offers' ) );
                
                $offer_id = $_POST[ 'offer_id' ];
                $args     = $_POST[ 'args' ];

                $mark_up = $this->_offer_condition->generate_offer_condition_markup( $offer_id , $args );

                if ( is_wp_error( $mark_up ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $mark_up->get_error_message()
                    );

                } else {

                    $response = array(
                        'status'  => 'success',
                        'mark_up' => $mark_up
                    );

                }

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();
                
            } else
                wp_die( __( 'Invalid AJAX Call' , 'just-in-time-sales-offers' ) );
            
        }

        /**
         * Generate markup for product quantity in cart entry.
         *
         * @since 1.1.0
         * @access public
         */
        public function jitso_generate_product_quantity_in_cart_entry_markup() {

            if ( !is_admin() )
                wp_die( __( 'Invalid Operation' , 'just-in-time-sales-offers' ) );

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'jitso-generate-product-quantity-in-cart-entry-markup' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'just-in-time-sales-offers' ) );
                
                $offer_id = $_POST[ 'offer_id' ];
                $data     = apply_filters( 'jitso_product_quantity_in_cart_entry_data' , $_POST[ 'data' ] );

                $mark_up = $this->_offer_condition->generate_product_quantity_in_cart_entry_markup( $offer_id , $data );

                if ( is_wp_error( $mark_up ) ) {

                    $response = array(
                        'status'         => 'fail',
                        'error_messsage' => $mark_up->get_error_message()
                    );

                } else {

                    $response = array(
                        'status'  => 'success',
                        'mark_up' => $mark_up
                    );

                }

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'just-in-time-sales-offers' ) );
            
        }

        /**
         * Save sales offer conditions.
         *
         * @since 1.1.0
         * @access public
         */
        public function jitso_save_sales_offer_conditions() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'jitso-save-sales-offer-conditions' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'just-in-time-sales-offers' ) );

                $offer_id = $_POST[ 'offer_id' ];
                $data     = apply_filters( 'jitso_save_sales_offer_conditions_data' , $_POST[ 'data' ] , $offer_id );

                $result = $this->_offer_condition->save_sales_offer_conditions( $offer_id , $data );

                if ( is_wp_error( $result ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $result->get_error_message()
                    );
                
                } else
                    $response = array( 'status' => 'success' );

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'just-in-time-sales-offers' ) );
            
        }

        
        // Offer Loader

        /**
         * Record offer view.
         *
         * @since 1.1.3
         * @access public
         */
        public function jitso_record_offer_view() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'jitso-record-offer-view' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'just-in-time-sales-offers' ) );

                $offer_id   = filter_var( $_POST[ 'offer_id' ] , FILTER_SANITIZE_NUMBER_INT );
                $offer_data = apply_filters( 'jitso_record_offer_view_data' , $_POST[ 'offer_data' ] );

                $response = $this->_offer_loader->record_offer_view( $offer_id , $offer_data );

                if ( is_wp_error( $response ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $response->get_error_message()
                    );
                
                } else
                    $response = array( 'status' => 'success' );

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'just-in-time-sales-offers' ) );
            
        }


        // Sales Offer Accept Action

        /**
         * Get new accept offer action markup.
         *
         * @since 1.0.0
         * @access public
         */
        public function jitso_get_new_accept_offer_action_markup() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'jitso-get-new-accept-offer-action-markup' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'just-in-time-sales-offers' ) );
                
                $offer_id = $_POST[ 'offer_id' ];
                $args     = $_POST[ 'args' ];

                $mark_up = $this->_offer_accept_action->get_new_accept_offer_action_markup( $offer_id , $args );

                if ( is_wp_error( $mark_up ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $mark_up->get_error_message()
                    );

                } else {

                    $response = array(
                        'status'  => 'success',
                        'mark_up' => $mark_up
                    );

                }

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'just-in-time-sales-offers' ) );

        }

        /**
         * Generate product to add to cart entry markup.
         *
         * @since 1.1.0
         * @access public
         */
        public function jitso_generate_product_to_add_entry_markup() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'jitso-generate-product-to-add-entry-markup' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'just-in-time-sales-offers' ) );

                $offer_id = $_POST[ 'offer_id' ];
                $data     = apply_filters( 'jitso_product_to_add_entry_data' , $_POST[ 'data' ] );

                $mark_up = $this->_offer_accept_action->generate_product_to_add_entry_markup( $offer_id , $data );

                if ( is_wp_error( $mark_up ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $mark_up->get_error_message()
                    );

                } else {

                    $response = array(
                        'status'  => 'success',
                        'mark_up' => $mark_up
                    );

                }

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'just-in-time-sales-offers' ) );
            
        }

        /**
         * Generate coupon to apply entry markup.
         *
         * @since 1.1.0
         * @access public
         */
        public function jitso_generate_coupon_to_apply_entry_markup() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'jitso-generate-coupon-to-apply-entry-markup' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'just-in-time-sales-offers' ) );
                
                $offer_id = $_POST[ 'offer_id' ];
                $data     = apply_filters( 'jitso_coupon_to_apply_entry_data' , $_POST[ 'data' ] );

                $mark_up = $this->_offer_accept_action->generate_coupon_to_apply_entry_markup( $offer_id , $data );

                if ( is_wp_error( $mark_up ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $mark_up->get_error_message()
                    );

                } else {

                    $response = array(
                        'status'  => 'success',
                        'mark_up' => $mark_up
                    );

                }

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Callback' , 'just-in-time-sales-offers' ) );

        }

        /**
         * Save accept offer actions.
         *
         * @since 1.1.0
         * @access public
         */
        public function jitso_save_accept_offer_actions() {
            
            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'jitso-save-accept-offer-actions' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'just-in-time-sales-offers' ) );

                $offer_id = $_POST[ 'offer_id' ];
                $data     = apply_filters( 'jitso_save_accept_offer_actions_data' , $_POST[ 'data' ] , $offer_id );

                $result = $this->_offer_accept_action->save_accept_offer_actions( $offer_id , $data );

                if ( is_wp_error( $result ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $result->get_error_message()
                    );

                } else
                    $response = array( 'status' => 'success' );

                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Callback' , 'just-in-time-sales-offers' ) );
            
        }
        
        /**
         * Executes accept actions for this offer.
         *
         * @since 1.1.0
         * @access public
         */
        public function jitso_execute_accept_offer_actions() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'jitso-execute-accept-offer-actions' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'just-in-time-sales-offers' ) );

                $offer_id   = $_POST[ 'offer_id' ];
                $offer_data = apply_filters( 'jitso_record_offer_accept_data' , $_POST[ 'offer_data' ] );

                $result = $this->_offer_accept_action->execute_accept_offer_actions( $offer_id , $offer_data );

                if ( is_wp_error( $result ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $result->get_error_message()
                    );

                } else
                    $response = $result;
                
                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'just-in-time-sales-offers' ) );
            
        }
        
        
        // Sales Offer Decline Action

        /**
         * Save decline offer actions.
         *
         * @since 1.1.0
         * @access public
         */
        public function jitso_save_decline_offer_actions() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'jitso-save-decline-offer-actions' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'just-in-time-sales-offers' ) );
                
                $offer_id = $_POST[ 'offer_id' ];
                $data     = apply_filters( 'jitso_save_decline_offer_actions_data' , $_POST[ 'data' ] , $offer_id );

                $result = $this->_offer_decline_action->save_decline_offer_actions( $offer_id , $data );

                if ( is_wp_error( $result ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $result->get_error_message()
                    );

                } else
                    $response = array( 'status' => 'success' );
                
                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();
                
            } else
                wp_die( __( 'Invalid AJAX Call' , 'just-in-time-sales-offers' ) );
            
        }

        /**
         * Executes decline actions for this offer.
         *
         * @since 1.1.0
         * @access public
         */
        public function jitso_execute_decline_offer_actions() {

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

                if ( !check_ajax_referer( 'jitso-execute-decline-offer-actions' , 'ajax-nonce' , false ) )
                    wp_die( __( 'Security Check Failed' , 'just-in-time-sales-offers' ) );

                $offer_id   = $_POST[ 'offer_id' ];
                $offer_data = apply_filters( 'jitso_record_offer_decline_data' , $_POST[ 'offer_data' ] );

                $result = $this->_offer_decline_action->execute_decline_offer_actions( $offer_id , $offer_data );
                
                if ( is_wp_error( $result ) ) {

                    $response = array(
                        'status'        => 'fail',
                        'error_message' => $result->get_error_message()
                    );

                } else
                    $response = $result;
                
                @header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
                echo wp_json_encode( $response );
                wp_die();

            } else
                wp_die( __( 'Invalid AJAX Call' , 'just-in-time-sales-offers' ) );

        }

    }

}