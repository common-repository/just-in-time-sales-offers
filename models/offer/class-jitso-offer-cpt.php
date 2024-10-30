<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'JITSO_Offer_CPT' ) ) {

    // TODO: Tidy up code and Extensibility

    /**
     * Class JITSO_Offer_CPT
     *
     * Model that houses the logic relating to Offer CPT.
     *
     * @since 1.0.0
     */
    final class JITSO_Offer_CPT {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of JITSO_Offer_CPT.
         *
         * @since 1.0.0
         * @access private
         * @var JITSO_Offer_CPT
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
         * Property that houses the logic of the various helper functions related to the shop's products.
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

            _doing_it_wrong( __FUNCTION__ , __( 'Cheatin&#8217; huh?' , 'just-in-time-sales-offers' ), '1.0.0' );

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
         * JITSO_Offer_CPT constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of JITSO_Offer_CPT model.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants = $dependencies[ 'JITSO_Constants' ];
            $this->_product          = $dependencies[ 'JITSO_Product' ];
            $this->_coupon           = $dependencies[ 'JITSO_Coupon' ];

        }

        /**
         * Ensure that only one instance of JITSO_Offer_CPT is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of JITSO_Offer_CPT model.
         * @return JITSO_Offer_CPT
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Register 'jit_sales_offer' custom post type.
         *
         * @since 1.0.0
         * @access public
         */
        public function register_jit_sales_offer_cpt() {

            $labels = array(
                'name'                => __( 'Just In Time Sales Offers' , 'just-in-time-sales-offers' ),
                'singular_name'       => __( 'Just In Time Sales Offer' , 'just-in-time-sales-offers' ),
                'menu_name'           => __( 'Just In Time Sales Offer' , 'just-in-time-sales-offers' ),
                'parent_item_colon'   => __( 'Parent Just In Time Sales Offer' , 'just-in-time-sales-offers' ),
                'all_items'           => __( 'Just In Time Sales Offers' , 'just-in-time-sales-offers' ),
                'view_item'           => __( 'View Just In Time Sales Offer' , 'just-in-time-sales-offers' ),
                'add_new_item'        => __( 'Add Just In Time Sales Offer' , 'just-in-time-sales-offers' ),
                'add_new'             => __( 'New Just In Time Sales Offer' , 'just-in-time-sales-offers' ),
                'edit_item'           => __( 'Edit Just In Time Sales Offer' , 'just-in-time-sales-offers' ),
                'update_item'         => __( 'Update Just In Time Sales Offer' , 'just-in-time-sales-offers' ),
                'search_items'        => __( 'Search Just In Time Sales Offers' , 'just-in-time-sales-offers' ),
                'not_found'           => __( 'No Just In Time Sales Offer found' , 'just-in-time-sales-offers' ),
                'not_found_in_trash'  => __( 'No Just In Time Sales Offers found in Trash' , 'just-in-time-sales-offers' ),
            );

            $args = array(
                'label'               => __( 'Just In Time Sales Offers' , 'just-in-time-sales-offers' ),
                'description'         => __( 'Just In Time Sales Offer Information Pages' , 'just-in-time-sales-offers' ),
                'labels'              => $labels,
                'supports'            => array( 'title' , 'editor' ),
                'taxonomies'          => array(),
                'hierarchical'        => false,
                'public'              => false,
                'show_ui'             => true,
                //'show_in_menu'        => true,
                //'show_in_menu'        => 'edit.php?post_type=shop_order',
                'show_in_menu'        => 'woocommerce',
                'show_in_json'        => false,
                'query_var'           => true,
                'rewrite'             => array(),
                'show_in_nav_menus'   => false,
                'show_in_admin_bar'   => true,
                'menu_position'       => 26,
                'menu_icon'           => 'dashicons-forms',
                'can_export'          => true,
                'has_archive'         => true,
                'exclude_from_search' => true,
                'publicly_queryable'  => false,
                'capability_type'     => 'post'
            );

            $args = apply_filters( 'jitso_offer_cpt_args' , $args );

            register_post_type( $this->_plugin_constants->OFFER_CPT_NAME() , $args );

        }

        /**
         * Add shortcodes legend.
         *
         * @since 1.1.2
         * @access public 
         */
        public function content_after_editor() {

            global $post;

            if ( $post->post_type == $this->_plugin_constants->OFFER_CPT_NAME() ) { ?>
                
                <a id="jitso-show-shortcodes-legend"><?php _e( '+ Show available template shortcodes' , 'just-in-time-sales-offers' ); ?></a>

                <div id="jitso-content-after-editor">

                    <a id="jitso-hide-shortcodes-legend"><?php _e( '- Hide available template shortcodes' , 'just-in-time-sales-offers' ); ?></a>
                    
                    <?php do_action( 'jitso_before_editor_shortcodes_legend' ); ?>

                    <ul class="shortcodes">
                        <li>
                            <strong>[jitso_accept_offer text="Accept Offer"]</strong>
                            <span class="desc"><?php _e( 'Shortcode for adding "Accept Offer" button' , 'just-in-time-sales-offers' ); ?></span>
                        </li>
                        <li>
                            <strong>[jitso_decline_offer text="Decline Offer"]</strong>
                            <span class="desc"><?php _e( 'Shortcode for adding "Decline Offer" button' , 'just-in-time-sales-offers' ); ?></span>
                        </li>
                        <?php do_action( 'jitso_editor_shortcodes_item' ); ?>
                    </ul>

                    <?php do_action( 'jitso_after_editor_shortcodes_legend' ); ?>

                </div>

            <?php }

        }

        /**
         * Register 'jit_sales_offer' cpt meta boxes.
         *
         * @since 1.0.0
         * @access public
         */
        public function register_jit_sales_offer_cpt_custom_meta_boxes() {
            
            foreach ( $this->_plugin_constants->OFFER_CPT_META_BOXES() as $id => $data ) {
                
                $callback = is_array( $data[ 'callback' ] ) ? $data[ 'callback' ] : array( $this , $data[ 'callback' ] );

                add_meta_box(
                    $id,
                    $data[ 'title' ],
                    $callback,
                    $data[ 'cpt' ],
                    $data[ 'context' ],
                    $data[ 'priority' ]
                );

            }
            
        }

        /**
         * Add default content to new jit offer cpt entries.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $content
         * @param $post
         * @return string
         */
        public function default_content( $content , $post ) {

            if ( $post->post_type == $this->_plugin_constants->OFFER_CPT_NAME() ) {

                $content = '<h2>' . __( 'Catchy Sales Offer Heading' , 'just-in-time-sales-offers' ) . '</h2>
                            <p>' . __( 'This is some small text specifically about the sales offer' , 'just-in-time-sales-offers' ) . '</p>
                            <img src="' . $this->_plugin_constants->IMAGES_ROOT_URL() . 'default-offer-image.jpg" class="alignnone" />
                            [jitso_accept_offer text="Accept Offer"] [jitso_decline_offer text="Decline Offer"]';

            }

            return $content;

        }

        /**
         * Save 'jit_sales_offer' cpt entry.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $post_id
         */
        public function save_post( $post_id ) {

            // Note: $post_id here is the offer id (Coz this is a sales offer cpt entry)

            // On manual click of 'update' , 'publish' or 'save draft' button, execute code inside the if statement
            if ( $this->_valid_save_post_action( $post_id ) ) {

                // On manual click of "update" and "save" action we mark this offer as saved offer
                update_post_meta( $post_id , $this->_plugin_constants->POST_META_SAVED_OFFER() , true );

            }

            // On every jit sales offer post creation, execute code below
            if ( get_post_type( $post_id ) == $this->_plugin_constants->OFFER_CPT_NAME() ) {

                if ( !get_post_meta( $post_id , $this->_plugin_constants->POST_META_SAVED_OFFER() , true ) ) {

                    // This is a fresh offer, set default page as cart page
                    $cart_page_id = wc_get_page_id( 'cart' );
                    $offer_pages  = get_option( $post_id , $this->_plugin_constants->POST_META_OFFER_PAGES() , array() );

                    if ( $cart_page_id && !empty( $offer_pages ) ) {

                        $cart_page_args = array(
                            'page_' . $cart_page_id => array(
                                'type' => 'page',
                                'id'   => $cart_page_id
                            )
                        );

                        update_post_meta( $post_id , $this->_plugin_constants->POST_META_OFFER_PAGES() , $cart_page_args );

                        // Attach offer id ( $post_id ) to page only if necessary
                        $page_sales_offers = get_post_meta( $cart_page_id , $this->_plugin_constants->POST_META_PAGE_SALES_OFFERS() );
                        if ( !is_array( $page_sales_offers ) )
                            $page_sales_offers = array();

                        if ( !in_array( $post_id , $page_sales_offers ) )
                            add_post_meta( $cart_page_id , $this->_plugin_constants->POST_META_PAGE_SALES_OFFERS() , $post_id );

                    }

                }

            }

        }

        /**
         * Remove offer stats data when offer is deleted permanently.
         *
         * @since 1.2.0
         * @access public
         *
         * @param $post_id int/string Id of te post ( offer ) deleted permanently.
         */
        public function delete_offer_stats_data( $post_id ) {

            if ( get_post_type( $post_id ) == $this->_plugin_constants->OFFER_CPT_NAME() ) {

                global $wpdb;

                $offer_views_table_name       = $wpdb->prefix . $this->_plugin_constants->CUSTOM_TABLE_OFFER_VIEWS();
                $offer_accepts_table_name     = $wpdb->prefix . $this->_plugin_constants->CUSTOM_TABLE_OFFER_ACCEPTS();
                $offer_conversions_table_name = $wpdb->prefix . $this->_plugin_constants->CUSTOM_TABLE_OFFER_CONVERSIONS();
                $offer_declines_table_name    = $wpdb->prefix . $this->_plugin_constants->CUSTOM_TABLE_OFFER_DECLINES();

                if ( $wpdb->get_var( "SHOW TABLES LIKE '$offer_views_table_name'" ) &&  $wpdb->get_var( "SHOW TABLES LIKE '$offer_accepts_table_name'" ) && 
                     $wpdb->get_var( "SHOW TABLES LIKE '$offer_conversions_table_name'" ) &&  $wpdb->get_var( "SHOW TABLES LIKE '$offer_declines_table_name'" ) ) {
                    
                    $wpdb->query( $wpdb->prepare( "DELETE FROM $offer_views_table_name WHERE offer_id = %d" , $post_id ) );
                    $wpdb->query( $wpdb->prepare( "DELETE FROM $offer_accepts_table_name WHERE offer_id = %d" , $post_id ) );
                    $wpdb->query( $wpdb->prepare( "DELETE FROM $offer_conversions_table_name WHERE offer_id = %d" , $post_id ) );
                    $wpdb->query( $wpdb->prepare( "DELETE FROM $offer_declines_table_name WHERE offer_id = %d" , $post_id ) );

                }

            }

        }
        
        
        
        
        /*
        |--------------------------------------------------------------------------
        | Views
        |--------------------------------------------------------------------------
        */

        /**
         * Sales offer pages meta box.
         *
         * @since 1.0.0
         * @access public
         */
        public function view_sales_offer_pages_meta_box() {

            global $post;

            $offer_pages_table_total_columns = $this->_plugin_constants->OFFER_PAGES_TABLE_TOTAL_COLUMNS();

            $sales_offer_pages_types             = $this->_plugin_constants->SALES_OFFER_PAGE_TYPES();
            $sales_offer_pages_types_simple_mode = $this->_plugin_constants->SALES_OFFER_PAGE_TYPES_SIMPLE_MODE();

            $sales_offer_pages = get_post_meta( $post->ID , $this->_plugin_constants->POST_META_OFFER_PAGES() , true );
            if ( !is_array( $sales_offer_pages ) )
                $sales_offer_pages = array();

            include_once ( $this->_plugin_constants->VIEWS_ROOT_PATH() . 'offer/cpt/sales-offer-pages-meta-box.php' );

        }

        /**
         * Sales offer conditions meta box.
         *
         * @since 1.0.0
         * @access public
         */
        public function view_sales_offer_conditions_meta_box() {

            global $post;

            $sales_offer_conditions      = get_post_meta( $post->ID , $this->_plugin_constants->POST_META_OFFER_CONDITIONS() , true );
            if ( !is_array( $sales_offer_conditions ) )
                $sales_offer_conditions = array();

            $products                                     = $this->_product->get_products( array( 'return_format' => 'raw' ) );
            $offer_condition_types                        = $this->_plugin_constants->SALES_OFFER_CONDITION_TYPES();
            $offer_condition_types_simple_mode            = $this->_plugin_constants->SALES_OFFER_CONDITION_TYPES_SIMPLE_MODE();
            $all_products_select_options                  = $this->_product->get_products( array( 'return_format' => 'select_option' , 'add_empty_option' => true , 'empty_option_text' => '' , 'product_url' => true ) );
            $product_conditions                           = $this->_plugin_constants->PRODUCT_QUANTITY_IN_CART_CONDITIONS();
            $product_quantity_in_cart_table_total_columns = $this->_plugin_constants->PRODUCT_QUANTITY_IN_CART_TABLE_TOTAL_COLUMNS();

            include_once ( $this->_plugin_constants->VIEWS_ROOT_PATH() . 'offer/cpt/sales-offer-conditions-meta-box.php' );

        }

        /**
         * Accept offer actions meta box.
         *
         * @since 1.0.0
         * @access public
         */
        public function view_accept_offer_actions_meta_box() {

            global $post;

            $all_products_select_options = $this->_product->get_products( array( 'return_format' => 'select_option' , 'add_empty_option' => true , 'empty_option_text' => '' , 'product_url' => true ) );
            $all_coupons_select_options  = $this->_coupon->get_coupons( array( 'return_format' => 'select_option' , 'add_empty_option' => true , 'empty_option_text' => '' , 'coupon_url' => true , 'coupon_type' => true , 'coupon_amount' => true ) );
            $accept_offer_action_types   = $this->_plugin_constants->ACCEPT_OFFER_ACTION_TYPES();

            $accept_offer_actions = get_post_meta( $post->ID , $this->_plugin_constants->POST_META_OFFER_ACCEPT_ACTIONS() , true );
            if ( !is_array( $accept_offer_actions ) )
                $accept_offer_actions = array();

            $add_products_to_cart_table_total_columns  = $this->_plugin_constants->ADD_PRODUCTS_TO_CART_TABLE_TOTAL_COLUMNS();
            $apply_coupons_to_cart_table_total_columns = $this->_plugin_constants->APPLY_COUPONS_TO_CART_TABLE_TOTAL_COLUMNS();

            include_once ( $this->_plugin_constants->VIEWS_ROOT_PATH() . 'offer/cpt/accept-offer-actions-meta-box.php' );

        }

        /**
         * Decline offer actions meta box.
         *
         * @since 1.0.0
         * @access public
         */
        public function view_decline_offer_actions_meta_box() {

            global $post;

            $decline_offer_action_types             = $this->_plugin_constants->DECLINE_OFFER_ACTION_TYPES();
            $decline_offer_action_types_simple_mode = $this->_plugin_constants->DECLINE_OFFER_ACTION_TYPES_SIMPLE_MODE();

            $decline_offer_action = get_post_meta( $post->ID , $this->_plugin_constants->POST_META_OFFER_DECLINE_ACTIONS() , true );
            if ( !is_array( $decline_offer_action ) )
                $decline_offer_action = array();

            include_once ( $this->_plugin_constants->VIEWS_ROOT_PATH() . 'offer/cpt/decline-offer-actions-meta-box.php' );

        }

        /**
         * Just in time sales offer upgrade meta box.
         * 
         * @since 1.1.2
         * @access public
         */
        public function view_just_in_time_sales_offers_upgrade_meta_box() {

            $banner_img_url = $this->_plugin_constants->IMAGES_ROOT_URL() . 'jitso-premium-upsell-edit-screen.png';

            include_once ( $this->_plugin_constants->VIEWS_ROOT_PATH() . 'offer/cpt/view-just-in-time-sales-offers-upgrade-meta-box.php' );

        }




        /*
        |--------------------------------------------------------------------------
        | CPT entry custom columns
        |--------------------------------------------------------------------------
        */

        /**
         * Add 'offer pages' cpt listing custom field.
         *
         * @since 1.2.0
         * @access public
         *
         * @param $columns array CPT listing columns array.
         * @return array Modified CPT listing columns array.
         */
        public function add_offer_page_listing_column( $columns ) {

            $all_keys = array_keys( $columns );
            $price_index = array_search( 'title' , $all_keys);

            $new_columns_array = array_slice( $columns , 0 , $price_index + 1 , true ) +
                array( 'offer_pages' => __( 'Appears On' , 'just-in-time-sales-offers' ) ) +
                array_slice( $columns , $price_index + 1 , NULL , true );

            return $new_columns_array;

        }

        /**
         * Add value to 'offer pages' cpt listing custom field.
         *
         * @since 1.4.0
         * @access public
         *
         * @param $columns array CPT listing columns array.
         * @param $post_id int/string Post Id.
         */
        public function add_offer_page_listing_column_data( $column , $post_id ) {

            switch ( $column ) {
                case 'offer_pages': ?>
                    
                    <?php 
                    $sales_offer_pages = get_post_meta( $post_id , $this->_plugin_constants->POST_META_OFFER_PAGES() , true );
                    if ( !is_array( $sales_offer_pages ) )
                        $sales_offer_pages = array();
                    
                    $page_titles = array();
                    foreach ( $sales_offer_pages as $offer_page ) {

                        switch ( $offer_page[ 'type' ] ) {

                            case 'page':
                            case 'post':
                            case 'product':

                                $page_titles[] = '<b>[' . ucfirst( $offer_page[ 'type' ] ) . ']</b> ' . get_the_title( $offer_page[ 'id' ] );
                                break;

                            case 'product-category':

                                $term  = get_term( $offer_page[ 'id' ] , 'product_cat' );

                                $title = 'Product Category ' . $offer_page[ 'id' ];
                                if ( !is_wp_error( $term ) )
                                    $title = "<b>[Product Category]</b> " . $term->name;
                                
                                $page_titles[] = $title;

                                break;

                            default:
                                $page_titles = apply_filters( 'jitso_get_' . $offer_page[ 'type' ] . '_offer_page_title' , $page_titles , $post_id , $offer_page );
                            
                        }

                    } ?>

                    <div class="offer_pages" id="offer_pages_<?php echo $post_id; ?>">

                        <ul style="margin-top: 0;">
                            <?php foreach ( $page_titles as $title ) { ?>
                                <li><?php echo $title; ?></li>
                            <?php } ?>
                        </ul>

                    </div>

                    <?php

                    break;

                default :
                    break;
            }

        }




        /*
        |--------------------------------------------------------------------------
        | Utilities
        |--------------------------------------------------------------------------
        */

        /**
         * Check validity of a save post action.
         *
         * @since 1.0.0
         * @access private
         *
         * @param $post_id
         * @return bool
         */
        private function _valid_save_post_action( $post_id ) {

            if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) || !current_user_can( 'edit_page' , $post_id ) || get_post_type() != $this->_plugin_constants->OFFER_CPT_NAME() || empty( $_POST ) )
                return false;
            else
                return true;

        }

    }

}
