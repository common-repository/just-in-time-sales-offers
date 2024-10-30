<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'JITSO_Script_Loader' ) ) {

    /**
     * Class JITSO_Script_Loader
     *
     * Model that houses the logic of loading various js and css scripts Just In Time Sales Offers plugin utilizes.
     *
     * @since 1.0.0
     */
    final class JITSO_Script_Loader {

        /**
         * Property that holds the single main instance of JITSO_Script_Loader.
         *
         * @since 1.0.0
         * @access private
         * @var JITSO_Script_Loader
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
         * JITSO_Script_Loader constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants        = $dependencies[ 'JITSO_Constants' ];
            $this->_initial_guided_tour     = $dependencies[ 'JITSO_Initial_Guided_Tour' ];
            $this->_offer_entry_guided_tour = $dependencies[ 'JITSO_Offer_Entry_Guided_Tour' ];
            $this->_offer_loader            = $dependencies[ 'JITSO_Offer_Loader' ];

        }

        /**
         * Ensure that there is only one instance of JITSO_Script_Loader is loaded or can be loaded.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         * @return JITSO_Script_Loader
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Load backend js and css scripts.
         *
         * @since 1.0.0
         * @access public
         *
         * @param string $handle Unique identifier of the current backend page.
         */
        public function load_backend_scripts( $handle ) {

            $screen = get_current_screen();

            $post_type = get_post_type();
            if ( !$post_type && isset( $_GET[ 'post_type' ] ) )
                $post_type = $_GET[ 'post_type' ];

            if ( ( $handle == 'post-new.php' || $handle == 'post.php' ) && $post_type == $this->_plugin_constants->OFFER_CPT_NAME() ) {
                // 'jit_sales_offer' cpt new post and edit single post page

                wp_enqueue_script( 'jitso_vue_js' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/vuejs/vue.js' , array() , $this->_plugin_constants->VERSION() , true );                
                wp_enqueue_script( 'jitso_sortable_js' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/sortablejs/Sortable.min.js' , array() , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'jitso_vue_draggable_js' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/vue_draggable/vuedraggable.min.js' , array() , $this->_plugin_constants->VERSION() , true );
                
                wp_enqueue_style( 'jitso_vex_css' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/vex/css/vex.css' , array() , $this->_plugin_constants->VERSION() , 'all' );
                wp_enqueue_style( 'jitso_vex-theme-plain_css' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/vex/css/vex-theme-plain.css' , array() , $this->_plugin_constants->VERSION() , 'all' );
                wp_enqueue_style( 'jitso_chosen_css' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/chosen_js/chosen.min.css' , array() , $this->_plugin_constants->VERSION() , 'all' );
                wp_enqueue_style( 'jitso_sales-offer-cpt_css' , $this->_plugin_constants->CSS_ROOT_URL() . 'offer/cpt/sales-offer-cpt.css' , array() , $this->_plugin_constants->VERSION() , 'all' );

                wp_enqueue_script( 'jitso_vex_js' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/vex/js/vex.combined.min.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'jitso_chosen_js' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/chosen_js/chosen.jquery.min.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'jitso_sales-offer-pages_js' , $this->_plugin_constants->JS_ROOT_URL() . 'offer/cpt/sales-offer-pages.js' , array( 'jquery' , 'jquery-ui-core' , 'jquery-tiptip' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'jitso_sales-offer-conditions_js' , $this->_plugin_constants->JS_ROOT_URL() . 'offer/cpt/sales-offer-conditions.js' , array( 'jquery' , 'jquery-ui-core' , 'jquery-tiptip' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'jitso_sales-offer-accept-actions_js' , $this->_plugin_constants->JS_ROOT_URL() . 'offer/cpt/sales-offer-accept-actions.js' , array( 'jquery' , 'jquery-ui-core' , 'jquery-tiptip' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'jitso_sales-offer-decline-actions_js' , $this->_plugin_constants->JS_ROOT_URL() . 'offer/cpt/sales-offer-decline-actions.js' , array( 'jquery' , 'jquery-ui-core' , 'jquery-tiptip' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'jitso_sales-offer-cpt_js' , $this->_plugin_constants->JS_ROOT_URL() . 'offer/cpt/sales-offer-cpt.js' , array( 'jquery' , 'jitso_sales-offer-conditions_js' , 'jitso_sales-offer-accept-actions_js' , 'jitso_sales-offer-decline-actions_js' ) , $this->_plugin_constants->VERSION() , true );

                // Create nonce for ajax interfaces that is called on mutliple files
                $nonce_get_product_additional_info = wp_create_nonce( 'jitso-get-product-additional-info' );
                

                wp_localize_script( 'jitso_sales-offer-pages_js' , 'sales_offer_pages_params' , array(
                    'offer_pages_table_total_columns' => $this->_plugin_constants->OFFER_PAGES_TABLE_TOTAL_COLUMNS(),
                    'nonce_get_site_pages'            => wp_create_nonce( 'jitso-get-site-pages' ),
                    'nonce_add_sales_offer_page'      => wp_create_nonce( 'jitso-add-sales-offer-page' ),
                    'nonce_edit_sales_offer_page'     => wp_create_nonce( 'jitso-edit-sales-offer-page' ),
                    'nonce_delete_sales_offer_page'   => wp_create_nonce( 'jitso-delete-sales-offer-page' ),
                    'i18n_get_site_pages_failed'  	  => __( 'Failed to retrieve site pages' , 'just-in-time-sales-offers' ),
                    'i18n_page_type_field_empty'      => __( 'Page type field empty' , 'just-in-time-sales-offers' ),
                    'i18n_page_field_empty'           => __( 'Page field empty' , 'just-in-time-sales-offers' ),
                    'i18n_add_offer_page_failed'      => __( 'Failed to add new offer page' , 'just-in-time-sales-offers' ),
                    'i18n_form_have_errors'           => __( 'There are errors on the form' , 'just-in-time-sales-offers' ),
                    'i18n_edit_offer_page_failed'     => __( 'Failed to edit existing offer page' , 'just-in-time-sales-offers' ),
                    'i18n_confirm_delete_offer_page'  => __( 'Are you sure to delete this sales offer page?' , 'just-in-time-sales-offers' ),
                    'i18n_no_offers_page'             => __( 'This offer is currently not showing on any pages. Add one above to start showing your offer!' , 'just-in-time-sales-offers' ),
                    'i18n_delete_offer_page_failed'   => __( 'Failed to delete existing offer page' , 'just-in-time-sales-offers' )
                ) );

                wp_localize_script( 'jitso_sales-offer-conditions_js' , 'sales_offer_conditions_params' , array(
                    'product_quantity_conditions'                                => $this->_plugin_constants->PRODUCT_QUANTITY_IN_CART_CONDITIONS(),
                    'product_quantity_table_total_columns'                       => $this->_plugin_constants->PRODUCT_QUANTITY_IN_CART_TABLE_TOTAL_COLUMNS(),
                    'nonce_generate_offer_condition_group_markup'                => wp_create_nonce( 'jitso-generate-offer-condition-group-markup' ),
                    'nonce_generate_offer_condition_markup'                      => wp_create_nonce( 'jitso-generate-offer-condition-markup' ),
                    'nonce_get_product_additional_info'                          => $nonce_get_product_additional_info,
                    'nonce_generate_product_quantity_in_cart_entry_markup'       => wp_create_nonce( 'jitso-generate-product-quantity-in-cart-entry-markup' ),
                    'nonce_save_sales_offer_conditions'                          => wp_create_nonce( 'jitso-save-sales-offer-conditions' ),
                    'i18n_failed_generate_condition_markup'                      => __( 'Failed to generate offer condition group markup' , 'just-in-time-sales-offers' ),
                    'i18n_confirm_remove_condition_group'                        => __( 'Are you sure you want to remove this condition group?' , 'just-in-time-sales-offers' ),
                    'i18n_no_condition_set'                                      => __( 'No Conditions Set. Click <b>"Add Condition Group"</b> and <b>"Add Condition"</b> buttons to add some conditions.' , 'just-in-time-sales-offers' ),
                    'i18n_please_fill_form_properly'                             => __( 'Please fill the form properly' , 'just-in-time-sales-offers' ),
                    'i18n_confirm_remove_condition'                              => __( 'Are you sure you want to remove this condition?' , 'just-in-time-sales-offers' ),
                    'i18n_empty_condition_group'                                 => __( 'Empty Condition Group. Click <b>"Add Condition"</b> button to add condition.' , 'just-in-time-sales-offers' ),
                    'i18n_please_select_variation'                               => __( 'Please select a variation...' , 'just-in-time-sales-offers' ),
                    'i18n_any_variation'                                         => __( 'Any Variation' , 'just-in-time-sales-offers' ),
                    'i18n_product_variations'                                    => __( 'Product variations' , 'just-in-time-sales-offers' ),
                    'i18n_failed_retrieve_product_data'                          => __( 'Failed to retrieve product additional data' , 'just-in-time-sales-offers' ),
                    'i18n_please_select_product'                                 => __( 'Please select product' , 'just-in-time-sales-offers' ),
                    'i18n_please_select_product_variation'                       => __( 'Please select product variation' , 'just-in-time-sales-offers' ),
                    'i18n_please_select_product_quantity_condition'              => __( 'Please select product quantity condition' , 'just-in-time-sales-offers' ),
                    'i18n_please_select_product_quantity'                        => __( 'Please select product quantity' , 'just-in-time-sales-offers' ),
                    'i18n_failed_generate_product_quantity_in_cart_entry_markup' => __( 'Failed to generate product quantity in cart entry markup' , 'just-in-time-sales-offers' ),
                    'i18n_confirm_remove_product'                                => __( 'Are you sure you want to remove this product?' , 'just-in-time-sales-offers' ),
                    'i18n_no_products_added'                                     => __( 'No products added' , 'just-in-time-sales-offers' ),
                    'i18n_success_save_conditions'                               => __( 'Successfully saved sales offer conditions' , 'just-in-time-sales-offers' ),
                    'i18n_failed_save_conditions'                                => __( 'Failed to save sales offer conditions' , 'just-in-time-sales-offers' ),
                    'i18n_failed_generate_offer_condition_markup'                => __( 'Failed to generate new sales offer condition markup' , 'just-in-time-sales-offers' )
                ) );

                wp_localize_script( 'jitso_sales-offer-accept-actions_js' , 'accept_sales_offer_actions_params' , array(
                    'add_product_table_total_columns'             => $this->_plugin_constants->ADD_PRODUCTS_TO_CART_TABLE_TOTAL_COLUMNS(),
                    'apply_coupon_table_total_columns'            => $this->_plugin_constants->APPLY_COUPONS_TO_CART_TABLE_TOTAL_COLUMNS(),
                    'nonce_get_product_additional_info'           => $nonce_get_product_additional_info,
                    'nonce_get_new_accept_offer_action_markup'    => wp_create_nonce( 'jitso-get-new-accept-offer-action-markup' ),
                    'nonce_generate_product_to_add_entry_markup'  => wp_create_nonce( 'jitso-generate-product-to-add-entry-markup' ),
                    'nonce_generate_coupon_to_apply_entry_markup' => wp_create_nonce( 'jitso-generate-coupon-to-apply-entry-markup' ),
                    'nonce_save_accept_offer_actions'             => wp_create_nonce( 'jitso-save-accept-offer-actions' ),
                    'i18n_failed_add_accept_offer_action'         => __( 'Failed to add new accept offer action' , 'just-in-time-sales-offers' ),
                    'i18n_select_variation'                       => __( 'Please select a variation...' , 'just-in-time-sales-offers' ),
                    'i18n_product_variations'                     => __( 'Product variations' , 'just-in-time-sales-offers' ),
                    'i18n_failed_retrieve_product_data'           => __( 'Failed to retrieve product additional data' , 'just-in-time-sales-offers' ),
                    'i18n_select_product_to_add'                  => __( 'Please select a product to add' , 'just-in-time-sales-offers' ),
                    'i18n_specify_product_quantity'               => __( 'Please specify a product quantity to add' , 'just-in-time-sales-offers' ),
                    'i18n_failed_generate_product_entry_markup'   => __( 'Failed to generate product to add entry mark up' , 'just-in-time-sales-offers' ),
                    'i18n_fill_form_properly'                     => __( 'Please fill the form properly' , 'just-in-time-sales-offers' ),
                    'i18n_confirm_delete_product'                 => __( 'Are you sure to delete this product?' , 'just-in-time-sales-offers' ),
                    'i18n_no_products_found'                      => __( 'No products added' , 'just-in-time-sales-offers' ),
                    'i18n_failed_generate_coupon_entry_markup'    => __( 'Failed to generate coupon to apply entry markup' , 'just-in-time-sales-offers' ),
                    'i18n_confirm_delete_coupon'                  => __( 'Are you sure to remove this coupon?' , 'just-in-time-sales-offers' ),
                    'i18n_no_coupons_added'                       => __( 'No coupons added' , 'just-in-time-sales-offers' ),
                    'i18n_confirm_delete_action'                  => __( 'Are you sure to delete this accept offer action?' , 'just-in-time-sales-offers' ),
                    'i18n_no_action_to_take'                      => __( '<p class="no-actions">No action to take. Click <b>"Add Action"</b> button to add one.</p>' , 'just-in-time-sales-offers' ),
                    'i18n_successfully_saved_action'              => __( 'Successfully saved accept offer actions' , 'just-in-time-sales-offers' ),
                    'i18n_failed_save_action'                     => __( 'Failed to save accept offer actions' , 'just-in-time-sales-offers' ),
                    'i18n_select_product_variation_to_add'        => __( 'Please select a product variation to add' , 'just-in-time-sales-offers' ),
                    'i18n_select_coupon_to_apply'                 => __( 'Please select a coupon to apply' , 'just-in-time-sales-offers' ),
                    'i18n_select_action_to_execute'               => __( 'Please select an action to execute' , 'just-in-time-sales-offers' )
                ) );

                wp_localize_script( 'jitso_sales-offer-decline-actions_js' , 'decline_sales_offer_actions_params' , array(
                    'nonce_save_decline_offer_actions'        => wp_create_nonce( 'jitso-save-decline-offer-actions' ),
                    'i18n_please_fill_form_properly'          => __( 'Please fill the form properly' , 'just-in-time-sales-offers' ),
                    'i18n_success_save_decline_offer_actions' => __( 'Successfully saved decline offer actions' , 'just-in-time-sales-offers' ),
                    'i18n_failed_save_decline_offer_actions'  => __( 'Failed to save decline offer actions' , 'just-in-time-sales-offers' )
                ) );

            } elseif ( in_array( $screen->id , array( 'woocommerce_page_wc-settings' ) ) && isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] == 'jitso_settings' ) {

                // Settings

                if ( !isset( $_GET[ 'section' ] ) || $_GET[ 'section' ] == '' ) {

                    // General

                    wp_enqueue_style( 'jitso_general-options_css' , $this->_plugin_constants->CSS_ROOT_URL() . 'offer/settings/general/general-options.css' , array() , $this->_plugin_constants->VERSION() , 'all' );

                }

            }
            
            // Help Pointers
            if ( get_option( JITSO_Initial_Guided_Tour::OPTION_INITIAL_GUIDED_TOUR_STATUS , false ) == JITSO_Initial_Guided_Tour::STATUS_OPEN && array_key_exists( $screen->id , $this->_initial_guided_tour->get_screens() ) ) {

                wp_enqueue_style( 'jitso_plugin-guided-tour_css' , $this->_plugin_constants->CSS_ROOT_URL() . 'admin/plugin-guided-tour.css' , array( 'wp-pointer' ) , $this->_plugin_constants->VERSION() , 'all' );                

                wp_enqueue_script( 'jitso_plugin-initial-guided-tour_js' , $this->_plugin_constants->JS_ROOT_URL() . 'admin/plugin-initial-guided-tour.js' , array( 'wp-pointer' , 'thickbox' ) , $this->_plugin_constants->VERSION() , true );

                wp_localize_script( 'jitso_plugin-initial-guided-tour_js' , 'jitso_initial_guided_tour_params', array(
                    'actions' => array( 'close_tour' => 'jitso_close_initial_guided_tour' ),
                    'nonces'  => array( 'close_tour' => wp_create_nonce( 'jitso-close-initial-guided-tour' ) ),
                    'screen'  => $this->_initial_guided_tour->get_current_screen(),
                    'height'  => 640,
                    'width'   => 640,
                    'texts'   => array(
                                    'btn_prev_tour'  => __( 'Previous' , 'just-in-time-sales-offers' ),
                                    'btn_next_tour'  => __( 'Next' , 'just-in-time-sales-offers' ),
                                    'btn_close_tour' => __( 'Close' , 'just-in-time-sales-offers' ),
                                    'btn_start_tour' => __( 'Start Tour' , 'just-in-time-sales-offers' )
                                ),
                    'urls'    => array( 'ajax' => admin_url( 'admin-ajax.php' ) ),
                    'post'    => isset( $post ) && isset( $post->ID ) ? $post->ID : 0
                ) );

            }

            if ( get_option( JITSO_Offer_Entry_Guided_Tour::OPTION_OFFER_ENTRY_GUIDED_TOUR_STATUS , false ) == JITSO_Offer_Entry_Guided_Tour::STATUS_OPEN && array_key_exists( $screen->id , $this->_offer_entry_guided_tour->get_screens() ) ) {
                
                wp_enqueue_style( 'jitso_plugin-guided-tour_css' , $this->_plugin_constants->CSS_ROOT_URL() . 'admin/plugin-guided-tour.css' , array( 'wp-pointer' ) , $this->_plugin_constants->VERSION() , 'all' );                

                wp_enqueue_script( 'jitso_plugin-offer-entry-guided-tour_js' , $this->_plugin_constants->JS_ROOT_URL() . 'admin/plugin-offer-entry-guided-tour.js' , array( 'wp-pointer' , 'thickbox' ) , $this->_plugin_constants->VERSION() , true );

                wp_localize_script( 'jitso_plugin-offer-entry-guided-tour_js' , 'jitso_offer_entry_guided_tour_params', array(
                    'actions' => array( 'close_tour' => 'jitso_close_offer_entry_guided_tour' ),
                    'nonces'  => array( 'close_tour' => wp_create_nonce( 'jitso-close-offer-entry-guided-tour' ) ),
                    'screen'  => $this->_offer_entry_guided_tour->get_current_screen(),
                    'height'  => 640,
                    'width'   => 640,
                    'texts'   => array(
                                    'btn_prev_tour'  => __( 'Previous' , 'just-in-time-sales-offers' ),
                                    'btn_next_tour'  => __( 'Next' , 'just-in-time-sales-offers' ),
                                    'btn_close_tour' => __( 'Close' , 'just-in-time-sales-offers' ),
                                    'btn_start_tour' => __( 'Start Tour' , 'just-in-time-sales-offers' )
                                ),
                    'urls'    => array( 'ajax' => admin_url( 'admin-ajax.php' ) ),
                    'post'    => isset( $post ) && isset( $post->ID ) ? $post->ID : 0
                ) );


            }            

        }

        /**
         * Load frontend js and css scripts.
         *
         * @since 1.0.0
         * @access public
         */
        public function load_frontend_scripts() {

            global $post, $wp;

            // Load styling for shortcodes
            wp_enqueue_style( 'jitso_jitso-shortcodes_css' , $this->_plugin_constants->CSS_ROOT_URL() . 'offer/front-end/jitso-shortcodes.css' , array() , $this->_plugin_constants->VERSION() , 'all' );

            $jit_sales_offers_to_load = $this->_offer_loader->get_sales_offers_to_load();
            
            if ( is_cart() )
                wp_enqueue_script( 'jitso_cart_js' , $this->_plugin_constants->JS_ROOT_URL() . 'offer/front-end/jitso-cart.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );                
            
            if ( !empty( $jit_sales_offers_to_load ) ) {
                // Load jit sales offer scripts
                
                wp_enqueue_style( 'jitso_vex_css' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/vex/css/vex.css' , array() , $this->_plugin_constants->VERSION() , 'all' );
                wp_enqueue_style( 'jitso_vex-theme-plain_css' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/vex/css/vex-theme-plain.css' , array() , $this->_plugin_constants->VERSION() , 'all' );
                wp_enqueue_style( 'jitso_magnific-popup_css' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/magnific-popup/magnific-popup.css' , array() , $this->_plugin_constants->VERSION() , 'all' );
                wp_enqueue_style( 'jitso_just-in-time-sales-offer_css' , $this->_plugin_constants->CSS_ROOT_URL() . 'offer/front-end/just-in-time-sales-offer.css' , array() , $this->_plugin_constants->VERSION() , 'all' );

                wp_enqueue_script( 'jitso_vex_js' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/vex/js/vex.combined.min.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'jitso_magnific-popup_js' , $this->_plugin_constants->JS_ROOT_URL() . 'lib/magnific-popup/magnific-popup.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );
                wp_enqueue_script( 'jitso_just-in-time-sales-offer_js' , $this->_plugin_constants->JS_ROOT_URL() . 'offer/front-end/just-in-time-sales-offer.js' , array( 'jquery' ) , $this->_plugin_constants->VERSION() , true );

                wp_localize_script( 'jitso_just-in-time-sales-offer_js' , 'jitso_params' , array(
                    'ajaxurl'                                  => admin_url( 'admin-ajax.php' ),
                    'nonce_record_offer_view'                  => wp_create_nonce( 'jitso-record-offer-view' ),
                    'nonce_execute_accept_offer_actions'       => wp_create_nonce( 'jitso-execute-accept-offer-actions' ),
                    'nonce_execute_decline_offer_actions'      => wp_create_nonce( 'jitso-execute-decline-offer-actions' ),
                    'i18n_failed_avail_offer'                  => __( 'There was a problem accepting the offer. Please contact the store.' , 'just-in-time-sales-offers' ),
                    'i18n_failed_execute_decline_offer_action' => __( 'There was a problem declining the offer. Please contact the store.' , 'just-in-time-sales-offers' ),
                    'options'                                  => apply_filters( 'jitso_sales_offer_on_frontend_options' , array(
                                                                        'time_delay'             => 3000,
                                                                        'close_button_behaviour' => get_option( $this->_plugin_constants->OPTION_OFFER_POPUP_CLOSE_BUTTON_BEHAVIOUR() )
                                                                    ) )
                ) );

                do_action( 'jitso_load_jit_sales_offer_scripts' , $jit_sales_offers_to_load );

            }

        }

    }

}
