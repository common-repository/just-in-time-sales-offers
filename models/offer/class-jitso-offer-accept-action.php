<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'JITSO_Offer_Accept_Action' ) ) {

    /**
     * Class JITSO_Offer_Accept_Action
     *
     * Model that houses the logic of the various helper functions related to the sales offer accept actions.
     *
     * @since 1.1.0
     */
    final class JITSO_Offer_Accept_Action {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of JITSO_Offer_Accept_Action.
         *
         * @since 1.1.0
         * @access private
         * @var JITSO_Offer_Accept_Action
         */
        private static $_instance;

        /**
         * JITSO_Constants instance. Holds various constants this class uses.
         *
         * @since 1.1.0
         * @access private
         * @var JITSO_Constants
         */
        private $_plugin_constants;

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




        /*
        |--------------------------------------------------------------------------
        | Class Methods
        |--------------------------------------------------------------------------
        */

        /**
         * Cloning is forbidden.
         *
         * @since 1.1.0
         * @access public
         */
        public function __clone () {

            _doing_it_wrong( __FUNCTION__ , __( 'Cheatin&#8217; huh?' , 'just-in-time-sales-offers' ) , '1.1.0' );

        }

        /**
         * Unserializing instances of this class is forbidden.
         *
         * @since 1.1.0
         * @access public
         */
        public function __wakeup () {

            _doing_it_wrong( __FUNCTION__ , __( 'Cheatin&#8217; huh?' , 'just-in-time-sales-offers' ) , '1.1.0' );

        }

        /**
         * JITSO_Offer_Accept_Action constructor.
         *
         * @since 1.1.0
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants = $dependencies[ 'JITSO_Constants' ];
            $this->_product          = $dependencies[ 'JITSO_Product' ];
            $this->_coupon           = $dependencies[ 'JITSO_Coupon' ];

        }

        /**
         * Ensure that there is only one instance of JITSO_Offer_Accept_Action is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.1.0
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         * @return JITSO_Offer_Accept_Action
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Get new accept offer action markup.
         * This function is only valid on admin page.
         *
         * @since 1.1.0
         * @access public
         *
         * @param $offer_id
         * @param $args
         * @return mixed
         */
        public function get_new_accept_offer_action_markup( $offer_id , $args ) {

            if ( !is_admin() )
                return new WP_Error( 'jitso-get_new_accept_offer_action_markup-invalid-operation' , __( 'Invalid Operation.' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'args' => $args ) );

            if ( !JITSO_Helper::current_user_authorized() )
                return new WP_Error( 'jitso-get_new_accept_offer_action_markup-authorization-failed' , __( 'Authorization Failed.' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'args' => $args ) );

            if ( $args[ 'action_type' ] == 'add-products-to-cart' ) {

                $all_products_select_options = $this->_product->get_products( array( 'return_format' => 'select_option' , 'add_empty_option' => true , 'empty_option_text' => '' , 'product_url' => true ) );
                ob_start(); ?>

                <div id="add-products-to-cart-action-container" class="accept-offer-action" data-action-type="add-products-to-cart">

                    <div class="action-controls">
                        <a class="remove-action"><?php _e( 'Remove Action' , 'just-in-time-sales-offers' ); ?></a>
                    </div>

                    <div class="fields">

                        <div class="field-set product-filter-field-set">

                            <span class="meta" style="display: none !important;">
                                <span class="product-type"></span>
                            </span>

                            <label for="add-products-to-cart-filter"><?php _e( 'Add product to cart' , 'just-in-time-sales-offers' ); ?></label>
                            <select id="add-products-to-cart-filter" class="product-filter-control" style="min-width: 340px;" data-placeholder="<?php _e( 'Please select a product...' , 'just-in-time-sales-offers' ); ?>">
                                <?php echo $all_products_select_options; ?>
                            </select>

                        </div>

                        <div class="field-set product-quantity-field-set">
                            <label for="add-products-to-cart-quantity"><?php _e( 'Quantity' , 'just-in-time-sales-offers' ); ?></label>
                            <input type="number" id="add-products-to-cart-quantity" value="1" min="1">
                        </div>

                        <?php do_action( 'jitso_add-products-to-cart_additional_controls' ); ?>

                        <div class="field-set button-field-set">
                            <input type="button" id="add-product-to-cart-btn" class="button button-primary" value="<?php _e( 'Add' , 'just-in-time-sales-offers' ); ?>">
                            <input type="button" id="add-product-to-cart-edit-button" class="button button-primary" value="<?php _e( 'Edit' , 'just-in-time-sales-offers' ); ?>">
                            <input type="button" id="cancel-add-product-cart-edit-button" class="button button-secondary" value="<?php _e( 'Cancel' , 'just-in-time-sales-offers' ); ?>">
                            <span class="spinner"></span>
                        </div>

                        <div style="clear: both; float: none; display: block;"></div>

                    </div>

                    <table id="add-products-to-cart-table" class="wp-list-table widefat fixed striped" cellspacing="0" width="100%">

                        <thead>
                            <tr>
                                <th class="product-heading"><?php _e( 'Product' , 'just-in-time-sales-offers' ); ?></th>
                                <th class="quantity-heading"><?php _e( 'Quantity' , 'just-in-time-sales-offers' ); ?></th>
                                <?php do_action( 'jitso_add-products-to-cart_additional_column_heading_markup' ); ?>
                                <th class="controls-heading"></th>
                            </tr>
                        </thead>

                        <tbody class="the-list">
                            <tr class="no-items">
                                <td class="colspanchange" colspan="<?php echo $this->_plugin_constants->ADD_PRODUCTS_TO_CART_TABLE_TOTAL_COLUMNS(); ?>"><?php _e( 'No products added' , 'just-in-time-sales-offers' ); ?></td>
                            </tr>
                        </tbody>

                    </table>

                </div>

                <?php $mark_up = ob_get_clean();

            } elseif ( $args[ 'action_type' ] == 'apply-coupons-to-cart' ) {

                $all_coupons_select_options = $this->_coupon->get_coupons( array( 'return_format' => 'select_option' , 'add_empty_option' => true , 'empty_option_text' => '' , 'coupon_url' => true , 'coupon_type' => true , 'coupon_amount' => true ) );
                ob_start(); ?>

                <div id="apply-coupons-to-cart-action-container" class="accept-offer-action" data-action-type="apply-coupons-to-cart">

                    <div class="action-controls">
                        <a class="remove-action"><?php _e( 'Remove Action' , 'just-in-time-sales-offers' ); ?></a>
                    </div>

                    <div class="fields">

                        <div class="field-set coupons-filter-field-set">

                            <label for="apply-coupons-to-cart-filter"><?php _e( 'Apply coupon to cart' , 'just-in-time-sales-offers' ); ?></label>
                            <select id="apply-coupons-to-cart-filter" class="coupon-filter-control" style="min-width: 340px;" data-placeholder="<?php _e( 'Please select a coupon...' , 'just-in-time-sales-offers' ); ?>">
                                <?php echo $all_coupons_select_options; ?>
                            </select>

                        </div>

                        <?php do_action( 'jitso_apply-coupons-to-cart_additional_controls' ); ?>

                        <div class="field-set button-field-set">
                            <input type="button" id="add-coupon-to-be-applied-to-cart-btn" class="button button-primary" value="<?php _e( 'Add' , 'just-in-time-sales-offers' ); ?>">
                            <input type="button" id="edit-coupon-to-be-applied-to-cart-btn" class="button button-primary" value="<?php _e( 'Edit' , 'just-in-time-sales-offers' ); ?>">
                            <input type="button" id="cancel-edit-coupon-to-be-applied-to-cart-btn" class="button button-secondary" value="<?php _e( 'Cancel' , 'just-in-time-sales-offers' ); ?>">
                            <span class="spinner"></span>
                        </div>

                        <div style="clear: both; float: none; display: block;"></div>

                    </div>

                    <table id="apply-coupons-to-cart-table" class="wp-list-table widefat fixed striped" cellspacing="0" width="100%">

                        <thead>
                            <tr>
                                <th class="coupon-heading"><?php _e( 'Coupon' , 'just-in-time-sales-offers' ); ?></th>
                                <th class="coupon-type-heading"><?php _e( 'Coupon Type' , 'just-in-time-sales-offers' ); ?></th>
                                <th class="coupon-amount-heading"><?php _e( 'Coupon Amount' , 'just-in-time-sales-offers' ); ?></th>
                                <?php do_action( 'jitso_apply-coupons-to-cart_additional_column_heading_markup' ); ?>
                                <th class="controls-heading"></th>
                            </tr>
                        </thead>

                        <tbody class="the-list">
                            <tr class="no-items">
                                <td class="colspanchange" colspan="<?php echo $this->_plugin_constants->APPLY_COUPONS_TO_CART_TABLE_TOTAL_COLUMNS(); ?>"><?php _e( 'No coupons added' , 'just-in-time-sales-offers' ); ?></td>
                            </tr>
                        </tbody>

                    </table>

                </div>

                <?php $mark_up = ob_get_clean();

            } else {

                // For future proofing, for other or unknown types of offer accept actions

                // Give mark_up a default of WP_Error
                // Just like the old saying goes, "All codes are guilty until proven innocent".

                $mark_up = new WP_Error( 'jitso-get_new_accept_offer_action_markup-unknown-error' , __( 'An unknown error occur when retrieving new accept offer action markup.' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'args' => $args ) );
                $mark_up = apply_filters( 'jitso_' . $args[ 'action_type' ] . '_accept_offer_action_markup' , $mark_up , $offer_id , $args );

            }

            return $mark_up;

        }

        /**
         * Generate product to add to cart entry markup.
         * This function is only valid on admin page.
         *
         * @since 1.1.0
         * @access public
         *
         * @param $offer_id
         * @param $data
         * @return mixed
         */
        public function generate_product_to_add_entry_markup( $offer_id , $data ) {

            if ( !is_admin() )
                return new WP_Error( 'jitso-generate_product_to_add_entry_markup-invalid-operation' , __( 'Invalid Operation.' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );

            if ( !JITSO_Helper::current_user_authorized() )
                return new WP_Error( 'jitso-generate_product_to_add_entry_markup-authorization-failed' , __( 'Authorization Failed.' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );

            ob_start(); ?>

            <tr>

                <td class="row-meta hidden">
                    <span class="product-type"><?php echo $data[ "product_type" ]; ?></span>
                    <span class="product-id"><?php echo $data[ "product_id" ]; ?></span>

                    <?php if ( isset( $data[ "product_variation_id" ] ) ) { ?>

                        <span class="product-variation-id"><?php echo $data[ "product_variation_id" ]; ?></span>

                    <?php } ?>

                    <span class="product-quantity"><?php echo $data[ "product_quantity" ]; ?></span>
                    <?php do_action( 'jitso_add-products-to-cart_additional_meta_markup' , $data ); ?>
                </td>

                <td class="product-text">

                    <?php $product_text = "";

                    if ( $data[ "product_type" ] == 'variable' ) {
                        // Variable Product

                        $variation_info_arr = JITSO_Helper::get_product_variations( array(
                            'variable_id'  => $data[ 'product_id' ],
                            'variation_id' => $data[ "product_variation_id" ]
                        ) );

                        $product_text  = '<div class="product"><a href="' . home_url( "/wp-admin/post.php?post=" . $data[ 'product_id' ] . "&action=edit" ) . '" target="_blank">[ID : ' . $data[ 'product_id' ] . '] ' . get_the_title( $data[ 'product_id' ] ) . '</a></div>';
                        $product_text .= '<div class="product-variation">' . $variation_info_arr[ 0 ][ 'text' ] . '</div>';

                    } else if ( $data[ "product_type" ] == 'simple' )
                        $product_text = '<div class="product"><a href="' . home_url( "/wp-admin/post.php?post=" . $data[ 'product_id' ] . "&action=edit" ) . '" target="_blank">[ID : ' . $data[ 'product_id' ] . '] ' . get_the_title( $data[ 'product_id' ] ) . '</a></div>';
                    else
                        $product_text = apply_filters( 'jitso_' . $data[ "product_type" ] . '_product_to_add_entry_text' , $product_text , $data );

                    echo  $product_text; ?>

                </td>

                <td class="product-quantity">
                    <?php echo $data[ "product_quantity" ]; ?>
                </td>

                <?php do_action( 'jitso_add-products-to-cart_additional_column_data_markup' , $data ); ?>

                <td class="row-controls">
                    <span class="dashicons dashicons-edit edit-product"></span>
                    <span class="dashicons dashicons-no delete-product"></span>
                </td>

            </tr>

            <?php $mark_up = ob_get_clean();

            return $mark_up;

        }

        /**
         * Generate coupon to apply entry markup.
         * This function is only valid on admin page.
         *
         * @since 1.1.0
         * @access public
         *
         * @param $offer_id
         * @param $data
         * @return mixed
         */
        public function generate_coupon_to_apply_entry_markup( $offer_id , $data ) {

            if ( !is_admin() )
                return new WP_Error( 'jitso-generate_coupon_to_apply_entry_markup-invalid-operation' , __( 'Invalid Operation.' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );

            if ( !JITSO_Helper::current_user_authorized() )
                return new WP_Error( 'jitso-generate_coupon_to_apply_entry_markup-authorization-failed' , __( 'Authorization Failed.' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );

            $coupon_details = JITSO_Helper::get_coupon_info( $data[ 'coupon_id' ] );
            $coupon_amount  = JITSO_Helper::get_coupon_data( $coupon_details[ 'coupon_obj' ] , 'coupon_amount' );

            ob_start(); ?>

            <tr>

                <td class="row-meta hidden">
                    <span class="coupon-id"><?php echo $data[ 'coupon_id' ]; ?></span>
                    <?php do_action( 'jitso_apply-coupons-to-cart_additional_meta_markup' ); ?>
                </td>
                <td class="coupon">
                    <?php $coupon_text = '<div class="coupon"><a href="' . $coupon_details[ 'coupon_url' ] . '" target="_blank">[ID : ' .  $data[ 'coupon_id' ] . '] ' . get_the_title( $data[ 'coupon_id' ] ) . '</a></div>';
                    $coupon_text = apply_filters( 'jitso_coupon_to_apply_entry_text' , $coupon_text , $data , $coupon_details );
                    echo $coupon_text; ?>
                </td>
                <td class="coupon-type-text"><?php echo $coupon_details[ 'coupon_type_text' ]; ?></td>
                <td class="coupon-amount"><?php echo $coupon_amount; ?></td>

                <?php do_action( 'jitso_apply-coupons-to-cart_additional_column_data_markup' , $data ); ?>

                <td class="row-controls">
                    <span class="dashicons dashicons-edit edit-coupon"></span>
                    <span class="dashicons dashicons-no delete-coupon"></span>
                </td>

            </tr>

            <?php $mark_up = ob_get_clean();

            return $mark_up;

        }

        /**
         * Save accept offer actions.
         * This function is only valid on admin page.
         *
         * @since 1.1.0
         * @access public
         *
         * @param $offer_id
         * @param $data
         * @return mixed
         */
        public function save_accept_offer_actions( $offer_id , $data ) {

            if ( !is_admin() )
                return new WP_Error( 'jitso-save_accept_offer_actions-invalid-operation' , __( 'Invalid Operation.' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );

            if ( !JITSO_Helper::current_user_authorized() )
                return new WP_Error( 'jitso-save_accept_offer_actions-authorization-failed' , __( 'Authorization Failed.' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );

            // TODO: Validation

            $data = apply_filters( 'jitso_before_save_accept_offer_actions' , $data , $offer_id );

            update_post_meta( $offer_id , $this->_plugin_constants->POST_META_OFFER_ACCEPT_ACTIONS() , $data );

            return true;

        }

        /**
         * Executes accept actions for this offer.
         *
         * @since 1.0.0
         * @since 1.1.0 Remove the ajax callback related codes and move to the proper ajax interface model.
         * @since 1.1.1 Refactor cookie data format
         * @access public
         *
         * @param $offer_id
         * @param $offer_data
         * @return mixed
         */
        public function execute_accept_offer_actions( $offer_id , $offer_data ) {

            // TODO: Set session data for the spawned cart that links the spawned cart to the spawning offer

            // Initialize cart session
            WC()->session->set_customer_session_cookie( true );

            $current_user = wp_get_current_user();

            if ( apply_filters( 'jitso_execute_record_offer_accept' , true , $current_user ) ) {

                // Record offer accept
                $offer_accept_stat_id = $this->record_offer_accept( $offer_id , $offer_data );
                if ( is_wp_error( $offer_accept_stat_id ) ) {

                    // TODO: Log error here.
                    $offer_accept_stat_id = 0;

                }
            }

            // Store the customers response to the current offer ( 'accept' ) to a cookie
            $cookie_key      = $current_user->ID ? 'jitso_logged_in_user_' . $current_user->ID . '_offer_' . $offer_id . '_response' : 'jitso_guest_user_offer_' . $offer_id . '_response';
            $cookie_data     = base64_encode( @serialize( apply_filters( 'jitso_accept_offer_response_cookie_data' , array( 'response' => 'accept' ) ) ) );
            $cookie_lifespan = apply_filters( 'jitso_accept_offer_response_cookie_lifespan' , 24 );
            $cookie_lifespan = '+' . $cookie_lifespan . ' hours';

            setcookie( $cookie_key , $cookie_data , strtotime( $cookie_lifespan ) , '/' );

            $accept_offer_actions = get_post_meta( $offer_id , $this->_plugin_constants->POST_META_OFFER_ACCEPT_ACTIONS() , true );
            if ( !is_array( $accept_offer_actions ) )
                $accept_offer_actions = array();

            $result = apply_filters( 'jitso_accept_offer_response' , array(
                                        'status'                   => 'success',
                                        'success_message'          => __( 'Thanks, the offer was accepted.' , 'just-in-time-sales-offers' ),
                                        'has_actions_on_front_end' => false,
                                        'response_args'            => array()
                                    ) );

            foreach ( $accept_offer_actions as $action_id => $offer_data ) {

                if ( $action_id == 'add-products-to-cart' ) {

                    $products_not_added_to_cart = array();

                    foreach ( $offer_data as $product_data ) {

                        $add_product_to_cart     = apply_filters( 'jitso_maybe_add_product_to_cart_flag' , true , $action_id , $offer_data );
                        $additional_product_data = apply_filters( 'jitso_add_product_to_cart_additional_product_data' , ( isset( $product_data[ 'product-price' ] ) && $product_data[ 'product-price' ] ) ? array( 'jitso_product_data' => $product_data ) : array() );

                        if ( $add_product_to_cart ) {

                            if ( $product_data[ 'product-type' ] == 'simple' )
                                $cart_item_key = WC()->cart->add_to_cart( $product_data[ 'product-id' ] , $product_data[ 'product-quantity' ] , 0 , array() , $additional_product_data );
                            elseif ( $product_data[ 'product-type' ] == 'variable' ) {

                                $variation_info_arr = JITSO_Helper::get_product_variations( array(
                                                        'variable_id'  => $product_data[ 'product-id' ],
                                                        'variation_id' => $product_data[ 'product-variation-id' ]
                                                    ) );

                                $variation_attr = isset( $variation_info_arr[ 0 ][ 'attributes' ][ $product_data[ 'product-variation-id' ] ] ) ? $variation_info_arr[ 0 ][ 'attributes' ][ $product_data[ 'product-variation-id' ] ] : array();

                                $cart_item_key = WC()->cart->add_to_cart( $product_data[ 'product-id' ] , $product_data[ 'product-quantity' ] , $product_data[ 'product-variation-id' ] , $variation_attr , $additional_product_data );

                            } else
                                $cart_item_key = apply_filters( 'jitso_accept_offer_action_add_' . $product_data[ 'product-type' ] . '_product_to_cart' , false , $action_id , $product_data , $additional_product_data ); // For other unknown product types

                            if ( !$cart_item_key )
                                $products_not_added_to_cart[] = $product_data;

                        }

                    }

                    if ( !empty( $products_not_added_to_cart ) )
                        do_action( 'jitso_accept_offer_action_error_products_not_added_to_cart' , $products_not_added_to_cart , $action_id , $offer_data );

                    $result[ 'response_args'][ $action_id ] = array(
                                                                    'products-not-added-to-cart' => $products_not_added_to_cart,
                                                                    'offer-data'                 => $offer_data
                                                                );

                } elseif ( $action_id == 'apply-coupons-to-cart' ) {

                    $coupons_not_applied_to_cart = array();

                    foreach ( $offer_data as $coupon_data ) {

                        $coupon_code = get_the_title( $coupon_data[ 'coupon-id' ] );

                        if ( !WC()->cart->add_discount( $coupon_code ) )
                            $coupons_not_applied_to_cart[] = $coupon_data;

                    }

                    if ( !empty( $coupons_not_applied_to_cart ) )
                        do_action( 'jitso_accept_offer_action_error_coupons_not_applied_to_cart' , $coupons_not_applied_to_cart , $action_id , $offer_data );

                    $result[ 'response_args' ][ $action_id ] = array(
                                                                    'coupons-not-applied-to-cart' => $coupons_not_applied_to_cart,
                                                                    'offer-data'                  => $offer_data
                                                                );

                } else
                    $result = apply_filters( 'jitso_execute_' . $action_id . '_accept_offer_action' , $result , $action_id , $offer_data );

            }

            // Set the proper cart session data
            WC()->cart->set_session();


            // -------------------------------------------------------------------------------------------------
            // Set cookie or session to indicate this cart is spawned because of this jitso offer
            // This session data is used later when creating an order for this cart
            // -------------------------------------------------------------------------------------------------

            $session_data = array( 'jitso-offer-id' => $offer_id , 'jitso-offer-accept-stat-id' => $offer_accept_stat_id );
            WC()->session->set( $this->_plugin_constants->SESSION_JIT_SALES_OFFER_ORDER() , $session_data );

            // -------------------------------------------------------------------------------------------------


            $result = apply_filters( 'jitso_accept_offer_response' , $result );

            do_action( 'jitso_after_accept_offer_actions_executed' , $result , $accept_offer_actions );

            return $result;

        }

        /**
         * Record offer accept.
         *
         * @since 1.1.3
         * @since 1.2.0 Records the data to the custom offer accept table. ( 1.1.x ) series used to record data as post meta per offer post.
         * @access public
         *
         * @param $offer_id
         * @param $offer_data
         * @return int
         */
        public function record_offer_accept( $offer_id , $offer_data ) {

            global $wpdb;

            $current_user             = wp_get_current_user();
            $offer_accepts_table_name = $wpdb->prefix . $this->_plugin_constants->CUSTOM_TABLE_OFFER_ACCEPTS();

            if ( $wpdb->get_var( "SHOW TABLES LIKE '$offer_accepts_table_name'" ) ) {

                // Return The number of rows inserted, or false on error.
                $result = $wpdb->insert(
                    $offer_accepts_table_name,
                    array(
                        'offer_id'   => $offer_id,
                        'user_email' => $current_user->ID ? $current_user->user_email : '',
                        'url'        => $offer_data[ 'current_url' ],
                        'ip'         => JITSO_Helper::get_client_ip(),
                        'user_agent' => $_SERVER[ 'HTTP_USER_AGENT' ],
                        'time'       => current_time( 'mysql' )
                    )
                );

                if ( $result )
                    return $wpdb->insert_id; // Return latest inserted id (auto generated) by offer accepts table
                else
                    return new WP_Error( 'jitso-record_offer_accept-failed-to-record-offer-accept' , __( 'Failed to record offer accept' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'offer_data' => $offer_data ) );

            } else
                return new WP_Error( 'jitso-record_offer_accept-failed-to-record-offer-accept' , __( 'Failed to record offer accept' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'offer_data' => $offer_data ) );

        }

        /**
         * When creating a new order, if the order is a result of accepting a jit sales offer, then link the order and the offer together.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $order_id
         * @param $posted_data
         */
        public function link_wc_order_and_jitso_offer_on_order_creation( $order_id , $posted_data ) {

            $jitso_order_session_data = WC()->session->get( $this->_plugin_constants->SESSION_JIT_SALES_OFFER_ORDER() );

            if ( isset( $jitso_order_session_data ) && isset( $jitso_order_session_data[ 'jitso-offer-id' ] ) ) {

                $jitso_offer_id             = $jitso_order_session_data[ 'jitso-offer-id' ];
                $jitso_offer_accept_stat_id = $jitso_order_session_data[ 'jitso-offer-accept-stat-id' ];

                $order = wc_get_order( $order_id );

                global $wpdb;

                $offer_conversions_table_name = $wpdb->prefix . $this->_plugin_constants->CUSTOM_TABLE_OFFER_CONVERSIONS();

                $wpdb->insert(
                    $offer_conversions_table_name,
                    array(
                        'accept_id'           => $jitso_offer_accept_stat_id,
                        'offer_id'            => $jitso_offer_id,
                        'order_id'            => $order_id,
                        'order_status'        => $order->get_status(),
                        'order_modified_date' => JITSO_Helper::get_order_data( $order , 'modified_date' )
                    )
                );

                // Add jit sales offer id on this order's post meta ( Meaning this order has been spawned due to that jit sales offer )
                update_post_meta( $order_id , $this->_plugin_constants->POST_META_ORDER_JIT_SALES_OFFER_ID() , $jitso_offer_id );
                update_post_meta( $order_id , $this->_plugin_constants->POST_META_ORDER_JIT_SALES_OFFER_ACCEPT_STAT_ID() , $jitso_offer_accept_stat_id );

            }

        }

        /**
         * When order that is linked to an offer is updated, update as well the offer's order metadata.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $order_id
         * @param $old_status
         * @param $new_status
         */
        public function update_offer_order_metadata( $order_id , $old_status , $new_status ) {

            $jitso_offer_id             = get_post_meta( $order_id , $this->_plugin_constants->POST_META_ORDER_JIT_SALES_OFFER_ID() , true );
            $jitso_offer_accept_stat_id = get_post_meta( $order_id , $this->_plugin_constants->POST_META_ORDER_JIT_SALES_OFFER_ACCEPT_STAT_ID() , true );

            if ( isset( $jitso_offer_id ) && isset( $jitso_offer_accept_stat_id ) ) {

                $order = wc_get_order( $order_id );

                global $wpdb;

                $offer_conversions_table_name = $wpdb->prefix . $this->_plugin_constants->CUSTOM_TABLE_OFFER_CONVERSIONS();

                $result = $wpdb->update(
                    $offer_conversions_table_name,
                    array(
                        'order_status'        => $order->get_status(),
                        'order_modified_date' => JITSO_Helper::get_order_data( $order , 'modified_date' )
                    ),
                    array(
                        'accept_id' => $jitso_offer_accept_stat_id,
                        'offer_id'  => $jitso_offer_id,
                        'order_id'  => $order_id
                    ),
                    array( '%s' , '%s' ),
                    array( '%d' , '%d' , '%d' )
                );

            }

        }

        /**
         * Order trashed and is linked to an offer ( meaning this order is spawn by accepting an offer )
         * Therefore adjust the status of the spawning offer when a linked order is trashed.
         *
         * @since 1.1.3
         * @access public
         *
         * @param $order_id
         */
        public function adjust_spawning_offer_when_spawned_order_is_trashed( $order_id ) {

            if ( get_post_type( $order_id ) == 'shop_order' ) {

                $jitso_offer_id             = get_post_meta( $order_id , $this->_plugin_constants->POST_META_ORDER_JIT_SALES_OFFER_ID() , true );
                $jitso_offer_accept_stat_id = get_post_meta( $order_id , $this->_plugin_constants->POST_META_ORDER_JIT_SALES_OFFER_ACCEPT_STAT_ID() , true );

                if ( isset( $jitso_offer_id ) && isset( $jitso_offer_accept_stat_id ) ) {

                    global $wpdb;

                    $offer_conversions_table_name = $wpdb->prefix . $this->_plugin_constants->CUSTOM_TABLE_OFFER_CONVERSIONS();

                    $wpdb->delete(
                        $offer_conversions_table_name,
                        array(
                            'accept_id' => $jitso_offer_accept_stat_id,
                            'offer_id'  => $jitso_offer_id,
                            'order_id'  => $order_id
                        ),
                        array( '%d' , '%d' , '%d' )
                    );

                }

            }

        }

        /**
         * Trashed order is unstrashed and is linked to an offer ( meaning this order is spawn by accepting an offer )
         * Therefore adjust the status of the spawning offer when a linked order is untrashed.
         *
         * @since 1.1.3
         * @access public
         *
         * @param $order_id
         */
        public function adjust_spawning_offer_when_trashed_spawned_order_is_untrashed( $order_id ) {

            if ( get_post_type( $order_id ) == 'shop_order' ) {

                $jitso_offer_id             = get_post_meta( $order_id , $this->_plugin_constants->POST_META_ORDER_JIT_SALES_OFFER_ID() , true );
                $jitso_offer_accept_stat_id = get_post_meta( $order_id , $this->_plugin_constants->POST_META_ORDER_JIT_SALES_OFFER_ACCEPT_STAT_ID() , true );

                if ( isset( $jitso_offer_id ) && isset( $jitso_offer_accept_stat_id ) ) {

                    $order = wc_get_order( $order_id );

                    global $wpdb;

                    $offer_conversions_table_name = $wpdb->prefix . $this->_plugin_constants->CUSTOM_TABLE_OFFER_CONVERSIONS();

                    $wpdb->insert(
                        $offer_conversions_table_name,
                        array(
                            'accept_id'           => $jitso_offer_accept_stat_id,
                            'offer_id'            => $jitso_offer_id,
                            'order_id'            => $order_id,
                            'order_status'        => $order->get_status(),
                            'order_modified_date' => JITSO_Helper::get_order_data( $order , 'modified_date' )
                        )
                    );

                }

            }

        }

    }

}
