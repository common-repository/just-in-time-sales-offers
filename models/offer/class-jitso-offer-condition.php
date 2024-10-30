<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'JITSO_Offer_Condition' ) ) {

    /**
     * Class JITSO_Offer_Condition
     *
     * Model that houses the logic of the various helper functions related to the sales offer conditions.
     *
     * @since 1.1.0
     */
    final class JITSO_Offer_Condition {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of JITSO_Offer_Condition.
         *
         * @since 1.1.0
         * @access private
         * @var JITSO_Offer_Condition
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
         * JITSO_Offer_Condition constructor.
         *
         * @since 1.1.0
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants = $dependencies[ 'JITSO_Constants' ];
            $this->_product          = $dependencies[ 'JITSO_Product' ];
            
        }

        /**
         * Ensure that there is only one instance of JITSO_Offer_Condition is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.1.0
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         * @return JITSO_Offer_Condition
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Generate offer condition group markup.
         * This function is only valid on admin page.
         *
         * @since 1.1.0
         * @access public
         *
         * @param $offer_id
         * @param $args
         * @return mixed
         */
        public function generate_offer_condition_group_markup( $offer_id , $args ) {

            if ( !is_admin() )
                return new WP_Error( 'jitso-generate_offer_condition_group_markup-invalid-operation' , __( 'Invalid Operation.' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'args' => $args ) );

            if ( !JITSO_Helper::current_user_authorized() )
                return new WP_Error( 'jitso-generate_offer_condition_group_markup-authorization-failed' , __( 'Authorization Failed.' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'args' => $args ) );
            
            $offer_condition_types             = $this->_plugin_constants->SALES_OFFER_CONDITION_TYPES();
            $offer_condition_types_simple_mode = $this->_plugin_constants->SALES_OFFER_CONDITION_TYPES_SIMPLE_MODE();

            ob_start();

            if ( isset( $args[ 'show-condition-group-logic' ] ) ) { ?>

                <div class="offer-condition-group-logic">

                    <div class="controls">

                        <select class="condition-group-logic">
                            <option value="and"><?php _e( 'AND' , 'just-in-time-sales-offers' ); ?></option>
                            <option value="or"><?php _e( 'OR' , 'just-in-time-sales-offers' ); ?></option>
                        </select>

                    </div>

                </div>

            <?php } ?>

            <div class="offer-condition-group">

                <div class="offer-condition-group-actions">
                    <?php do_action( 'jitso_offer_condition_group_additional_actions' ); ?>
                    <a class="remove-condition-group"><?php _e( 'Remove Condition Group' , 'just-in-time-sales-offers' ); ?></a>
                </div>

                <div class="empty-condition-group-container">
                    <p class="empty-condition-group-message"><?php _e( 'Empty Condition Group. Click <b>"Add Condition"</b> button to add condition.' , 'just-in-time-sales-offers' ); ?></p>
                </div>

                <div class="offer-condition-controls">

                    <div class="controls">

                        <?php do_action( 'jitso_offer_condition_group_additional_controls' ); ?>

                        <select class="condition-types">
                            <?php foreach ( $offer_condition_types as $key => $text ) {

                                $disabled = '';
                                if ( $offer_condition_types_simple_mode && $key != 'product-quantity' ) {

                                    $disabled = 'disabled="disabled"';
                                    $text .= ' (PREMIUM)';

                                } ?>

                                <option value="<?php echo $key; ?>" <?php echo $disabled; ?>><?php echo $text; ?></option>

                            <?php } ?>
                        </select>

                        <input type="button" class="show-add-condition-controls button button-secondary" value="<?php _e( 'Add Condition' , 'just-in-time-sales-offers' ); ?>">
                        <input type="button" class="add-condition button button-primary" value="<?php _e( 'Add' , 'just-in-time-sales-offers' ); ?>">
                        <input type="button" class="hide-add-condition-controls button button-secondary" value="<?php _e( 'Cancel' , 'just-in-time-sales-offers' ); ?>">

                        <span class="spinner"></span>

                    </div>

                </div>

            </div>

            <?php $mark_up = ob_get_clean();

            return $mark_up;

        }

        /**
         * Get new offer condition markup.
         * This function is only valid on admin page.
         *
         * @since 1.1.0
         * @access public
         *
         * @param $offer_id
         * @param $args
         * @return mixed
         */
        public function generate_offer_condition_markup( $offer_id , $args ) {

            if ( !is_admin() )
                return new WP_Error( 'jitso-generate_offer_condition_markup-invalid-operation' , __( 'Invalid Operation.' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'args' => $args ) );

            if ( !JITSO_Helper::current_user_authorized() )
                return new WP_Error( 'jitso-generate_offer_condition_markup-authorization-failed' , __( 'Authorization Failed.' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'args' => $args ) );

            if ( $args[ 'condition-type' ] == 'product-quantity' ) {

                $all_products_select_options = $this->_product->get_products( array( 'return_format' => 'select_option' , 'add_empty_option' => true , 'empty_option_text' => '' , 'product_url' => true ) );
                $product_conditions          = $this->_plugin_constants->PRODUCT_QUANTITY_IN_CART_CONDITIONS();

                ob_start();

                if ( isset( $args[ 'show-condition-logic' ] ) ) { ?>

                    <div class="offer-condition-logic">

                        <div class="controls">

                            <select class="condition-logic">
                                <option value="and"><?php _e( 'AND' , 'just-in-time-sales-offers' ); ?></option>
                                <option value="or"><?php _e( 'OR' , 'just-in-time-sales-offers' ); ?></option>
                            </select>

                        </div>

                    </div>

                <?php } ?>

                <div class="offer-condition" data-condition-type="product-quantity">

                    <div class="offer-condition-actions">
                        <?php do_action( 'jitso_offer_condition_additional_actions' ); ?>
                        <a class="remove-condition"><?php _e( 'Remove Condition' , 'just-in-time-sales-offers' ); ?></a>
                    </div>

                    <div class="fields">

                        <div class="field-set product-in-cart-field-set">
                            <span class="meta" style="display: none !important;">
                                <span class="product-type"></span>
                            </span>

                            <label><?php _e( 'Product In Cart' , 'just-in-time-sales-offers' ); ?></label>
                            <select class="product-in-cart" style="min-width: 340px;" data-placeholder="<?php _e( 'Please select a product...' , 'just-in-time-sales-offers' ); ?>">
                                <?php echo $all_products_select_options; ?>
                            </select>
                        </div>

                        <div class="field-set product-in-cart-quantity-condition-field-set">
                            <label><?php _e( 'Condition' , 'just-in-time-sales-offers' ); ?></label>
                            <select class="product-in-cart-quantity-condition">
                                <?php foreach ( $product_conditions as $condition_val => $condition_text ) { ?>
                                    <option value="<?php echo $condition_val; ?>"><?php echo $condition_text; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="field-set product-in-cart-quantity-field-set">
                            <label><?php _e( 'Quantity' , 'just-in-time-sales-offers' ); ?></label>
                            <input type="number" class="product-in-cart-quantity" value="1" min="0">
                        </div>

                        <?php do_action( 'jitso_product-quantity_in_cart_additional_controls' ); ?>

                        <div class="field-set button-field-set">
                            <input type="button" class="add-product-in-cart-btn button button-primary" value="<?php _e( 'Add' , 'just-in-time-sales-offers' ); ?>">
                            <input type="button" class="edit-product-in-cart-btn button button-primary" value="<?php _e( 'Edit' , 'just-in-time-sales-offers' ); ?>">
                            <input type="button" class="cancel-edit-product-in-cart-btn button button-secondary" value="<?php _e( 'Cancel' , 'just-in-time-sales-offers' ); ?>">
                            <span class="spinner"></span>
                        </div>

                        <div style="clear: both; float: none; display: block;"></div>

                    </div>

                    <table class="product-quantity-table wp-list-table widefat fixed striped" cellspacing="0" width="100%">

                        <thead>
                            <tr>
                                <th class="product-heading"><?php _e( 'Product' , 'just-in-time-sales-offers' ); ?></th>
                                <th class="product-quantity-condition-heading"><?php _e( 'Condition' , 'just-in-time-sales-offers' ); ?></th>
                                <th class="product-quantity-heading"><?php _e( 'Quantity' , 'just-in-time-sales-offers' ); ?></th>
                                <?php do_action( 'jitso_product-quantity_in_cart_additional_column_heading_markup' ); ?>
                                <th class="controls-heading"></th>
                            </tr>
                        </thead>

                        <tbody class="the-list">
                            <tr class="no-items">
                                <td class="colspanchange" colspan="<?php echo $this->_plugin_constants->PRODUCT_QUANTITY_IN_CART_TABLE_TOTAL_COLUMNS(); ?>"><?php _e( 'No products added' , 'just-in-time-sales-offers' ); ?></td>
                            </tr>
                        </tbody>

                    </table>

                </div>

                <?php $mark_up = ob_get_clean();

                return $mark_up;

            } else {

                // For future proofing, for other types of offer condition mark up

                // Give mark_up a default of WP_Error
                // Just like the old saying goes, "All codes are guilty until proven innocent".

                $mark_up = new WP_Error( 'jitso-generate_offer_condition_markup-unknown-error' , __( 'An unknown error occur when generating offer condition markup.' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'args' => $args ) );
                return apply_filters( 'jitso_' . $args[ 'condition-type' ] . '_sales_offer_condition_markup' , $mark_up , $offer_id , $args );

            }

        }

        /**
         * Generate markup for product quantity in cart entry.
         * This function is only valid on admin page.
         *
         * @since 1.1.0
         * @access public
         *
         * @param $offer_id
         * @param $data
         * @return mixed
         */
        public function generate_product_quantity_in_cart_entry_markup( $offer_id , $data ) {

            if ( !is_admin() )
                return new WP_Error( 'jitso-generate_product_quantity_in_cart_entry_markup-invalid-operation' , __( 'Invalid Operation.' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );

            if ( !JITSO_Helper::current_user_authorized() )
                return new WP_Error( 'jitso-generate_product_quantity_in_cart_entry_markup-authorization-failed' , __( 'Authorization Failed.' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );

            $product_conditions = $this->_plugin_constants->PRODUCT_QUANTITY_IN_CART_CONDITIONS();

            ob_start(); ?>

            <tr>
                <td class="row-meta hidden">

                    <span class="product-type"><?php echo $data[ 'product-type' ]; ?></span>
                    <span class="product-id"><?php echo $data[ 'product-id' ]; ?></span>

                    <?php if ( isset( $data[ 'product-variation-id' ] ) ) { ?>
                        <span class="product-variation-id">

                            <?php foreach ( $data['product-variation-id' ] as $variation_id ) { ?>
                                <span class="variation-id"><?php echo $variation_id; ?></span>
                            <?php } ?>

                        </span>
                    <?php } ?>

                    <span class="product-quantity-condition"><?php echo $data[ 'product-quantity-condition' ]; ?></span>
                    <span class="product-quantity"><?php echo $data[ 'product-quantity' ]; ?></span>
                    <?php do_action( 'jitso_product-quantity_in_cart_additional_meta_markup' , $data ); ?>

                </td>

                <td class="product-text">

                    <?php $product_text = "";

                    if ( $data[ "product-type" ] == 'variable' ) {
                        // Variable Product

                        $product_text  = '<div class="product"><a href="' . home_url( "/wp-admin/post.php?post=" . $data[ 'product-id' ] . "&action=edit" ) . '" target="_blank">[ID : ' . $data[ 'product-id' ] . '] ' . get_the_title( $data[ 'product-id' ] ) . '</a></div>';

                        foreach ( $data[ "product-variation-id" ] as $variation_id ) {

                            if ( $variation_id == 'any' )
                                $product_text .= '<div class="product-variation">' . __( 'Any Variation' , 'just-in-time-sales-offers' ) . '</div>';
                            else {

                                $variation_info_arr = JITSO_Helper::get_product_variations( array(
                                    'variable_id'  => $data[ 'product-id' ],
                                    'variation_id' => $variation_id
                                ) );

                                $product_text .= '<div class="product-variation">' . $variation_info_arr[ 0 ][ 'text' ] . '</div>';

                            }

                        }


                    } else if ( $data[ "product-type" ] == 'simple' )
                        $product_text = '<div class="product"><a href="' . home_url( "/wp-admin/post.php?post=" . $data[ 'product-id' ] . "&action=edit" ) . '" target="_blank">[ID : ' . $data[ 'product-id' ] . '] ' . get_the_title( $data[ 'product-id' ] ) . '</a></div>';
                    else
                        $product_text = apply_filters( 'jitso_' . $data[ "product-type" ] . '_product_quantity_in_cart_entry_text' , $product_text , $data );

                    echo  $product_text; ?>

                </td>

                <td class="product-quantity-condition"><?php echo $product_conditions[ $data[ 'product-quantity-condition' ] ]; ?></td>
                <td class="product-quantity"><?php echo $data[ 'product-quantity' ]; ?></td>

                <?php do_action( 'jitso_product-quantity_in_cart_additional_column_data_markup' , $data ); ?>

                <td class="row-controls">
                    <span class="dashicons dashicons-edit edit-product"></span>
                    <span class="dashicons dashicons-no delete-product"></span>
                </td>
            </tr>

            <?php $mark_up = ob_get_clean();

            return $mark_up;

        }

        /**
         * Save sales offer conditions.
         * This function is only valid on admin page.
         *
         * @since 1.1.0
         * @access public
         *
         * @param $offer_id
         * @param $data
         * @return mixed
         */
        public function save_sales_offer_conditions( $offer_id , $data ) {

            if ( !is_admin() )
                return new WP_Error( 'jitso-save_sales_offer_conditions-invalid-operation' , __( 'Invalid Operation.' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );

            if ( !JITSO_Helper::current_user_authorized() )
                return new WP_Error( 'jitso-save_sales_offer_conditions-authorization-failed' , __( 'Authorization Failed.' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );

            // TODO: Validation

            $data = apply_filters( 'jitso_before_save_sales_offer_conditions' , $data , $offer_id );

            update_post_meta( $offer_id , $this->_plugin_constants->POST_META_OFFER_CONDITIONS() , $data );

            return true;

        }



        
        /*
        |--------------------------------------------------------------------------
        | Offer Condition Check
        |--------------------------------------------------------------------------
        */
        
        /**
         * Check [product-quantity] offer condition.
         * 
         * @since 1.0.0
         * @access public
         * 
         * @param $conditions_attained
         * @param $condition
         * @param $condition_logic
         * @param $cart_items
         * @return boolean
         */
        public function check_product_quantity_sales_offer_condition( $conditions_attained , $condition , $condition_logic , $cart_items ) {

            $product_condition_attained = true;

            if ( empty( $cart_items ) )
                $product_condition_attained = false; // Empty cart meaning condition automatically fails

            foreach ( $condition[ 'product-conditions' ] as $product_condition ) {

                if ( $product_condition_attained === false )
                    break; // No point in continuing. We are using && here and if this variable is false then we will always gets false

                $product_id         = $product_condition[ 'product-id' ];
                $quantity_condition = $product_condition[ 'product-quantity-condition' ];
                $quantity           = $product_condition[ 'product-quantity' ];

                if ( ( $quantity_condition == '<' && $quantity <= 1 ) || ( $quantity_condition == '=' && $quantity == 0 ) ) {
                
                    // Product not in cart condition.
                    // Meaning this product must not be in the cart

                    $product_in_cart = false;

                    if ( $product_condition[ 'product-type' ] == 'simple' ) {

                        foreach ( $cart_items as $cart_item ) {

                            if ( $cart_item[ 'product_id' ] == $product_id ) {
                                $product_in_cart = true;
                                break;
                            }

                        }

                    } elseif ( $product_condition[ 'product-type' ] == 'variable' ) {

                        $variations = $product_condition[ 'product-variation-id' ];

                        if ( in_array( 'any' , $variations ) ) {

                            foreach ( $cart_items as $cart_item ) {

                                if ( $cart_item[ 'product_id' ] == $product_id ) {
                                    $product_in_cart = true;
                                    break;
                                }

                            }

                        } else {

                            foreach ( $cart_items as $cart_item ) {

                                if ( in_array( $cart_item[ 'variation_id' ] , $variations ) ) {
                                    $product_in_cart = true;
                                    break;
                                }

                            }

                        }

                    } else
                        $product_in_cart = apply_filters( 'jitso_check_if_product_is_in_cart_' . $product_condition[ 'product-type' ] , $product_in_cart , $cart_items );

                    if ( !$product_in_cart )
                        $product_condition_attained = $product_condition_attained && true;
                    else
                        $product_condition_attained = $product_condition_attained && false;

                    continue;

                } else {
                    
                    if ( $product_condition[ 'product-type' ] == 'simple' ) {

                        $product_in_cart = false;

                        foreach ( $cart_items as $cart_item ) {
                            
                            $process_simple_product_cart_item = true;
                            $process_simple_product_cart_item = apply_filters( 'jitso_condition_check_simple_product_cart_item' , $process_simple_product_cart_item , $cart_item , $cart_items , $product_condition );

                            if ( !$process_simple_product_cart_item )
                                continue;

                            if ( $cart_item[ 'product_id' ] == $product_id ) {

                                $cart_item_qty = isset( $cart_item[ 'quantity' ] ) ? $cart_item[ 'quantity' ] : $cart_item[ 'qty' ];

                                if ( !$product_in_cart )
                                    $product_in_cart = true;

                                switch ( $quantity_condition ) {
                                    case '=':

                                        if ( $cart_item_qty == $quantity )
                                            $product_condition_attained = $product_condition_attained && true;
                                        else
                                            $product_condition_attained = $product_condition_attained && false;
                                        break;

                                    case '!=':

                                        if ( $cart_item_qty != $quantity )
                                            $product_condition_attained = $product_condition_attained && true;
                                        else
                                            $product_condition_attained = $product_condition_attained && false;
                                        break;

                                    case '>':

                                        if ( $cart_item_qty > $quantity )
                                            $product_condition_attained = $product_condition_attained && true;
                                        else
                                            $product_condition_attained = $product_condition_attained && false;
                                        break;

                                    case '<':

                                        if ( $cart_item_qty < $quantity )
                                            $product_condition_attained = $product_condition_attained && true;
                                        else
                                            $product_condition_attained = $product_condition_attained && false;
                                        break;

                                } // end switch

                                break; // No point in continuing with the loop

                            }

                        }

                        if ( !$product_in_cart )
                            $product_condition_attained = false;

                    } elseif ( $product_condition[ 'product-type' ] == 'variable' ) {

                        $variations = $product_condition[ 'product-variation-id' ];

                        if ( in_array( 'any' , $variations ) ) { // Any variation

                            // Get total variable product quantity in cart
                            // Sum up the variations of that variable product in cart to get the total
                            $variable_product_total_quantity_in_cart = array();
                            foreach ( $cart_items as $cart_item ) {
                                
                                $process_variable_product_cart_item = true;
                                $process_variable_product_cart_item = apply_filters( 'jitso_condition_check_variable_product_cart_item' , $process_variable_product_cart_item , $cart_item , $cart_items , $product_condition );

                                if ( !$process_variable_product_cart_item )
                                    continue;

                                if ( isset( $cart_item[ 'variation_id' ] ) ) {

                                    $cart_item_qty = isset( $cart_item[ 'quantity' ] ) ? $cart_item[ 'quantity' ] : $cart_item[ 'qty' ];

                                    if ( isset( $variable_product_total_quantity_in_cart[ $cart_item[ 'product_id' ] ] ) )
                                        $variable_product_total_quantity_in_cart[ $cart_item[ 'product_id' ] ] += ( int ) $cart_item_qty;
                                    else
                                        $variable_product_total_quantity_in_cart[ $cart_item[ 'product_id' ] ] = ( int ) $cart_item_qty;

                                }

                            }

                        } else { // Specified specific variations

                            // Get the total quantity of a variable product in cart dependent on what variations are specified.
                            $variable_product_total_quantity_in_cart = array();
                            foreach ( $cart_items as $cart_item ) {
                                
                                $process_variable_product_cart_item = true;
                                $process_variable_product_cart_item = apply_filters( 'jitso_condition_check_variable_product_cart_item' , $process_variable_product_cart_item , $cart_item , $cart_items , $product_condition );

                                if ( !$process_variable_product_cart_item )
                                    continue;

                                if ( isset( $cart_item[ 'variation_id' ] ) && in_array( $cart_item[ 'variation_id' ] , $variations ) ) {

                                    $cart_item_qty = isset( $cart_item[ 'quantity' ] ) ? $cart_item[ 'quantity' ] : $cart_item[ 'qty' ];

                                    if ( isset( $variable_product_total_quantity_in_cart[ $cart_item[ 'product_id' ] ] ) )
                                        $variable_product_total_quantity_in_cart[ $cart_item[ 'product_id' ] ] += ( int ) $cart_item_qty;
                                    else
                                        $variable_product_total_quantity_in_cart[ $cart_item[ 'product_id' ] ] = ( int ) $cart_item_qty;

                                }

                            }

                        }

                        switch ( $quantity_condition ) {

                            case '=':

                                if ( isset( $variable_product_total_quantity_in_cart[ $product_id ] ) && $variable_product_total_quantity_in_cart[ $product_id ] == $quantity )
                                    $product_condition_attained = $product_condition_attained && true;
                                else
                                    $product_condition_attained = $product_condition_attained && false;
                                break;

                            case '!=':

                                if ( isset( $variable_product_total_quantity_in_cart[ $product_id ] ) && $variable_product_total_quantity_in_cart[ $product_id ] != $quantity )
                                    $product_condition_attained = $product_condition_attained && true;
                                else
                                    $product_condition_attained = $product_condition_attained && false;
                                break;

                            case '>':

                                if ( isset( $variable_product_total_quantity_in_cart[ $product_id ] ) && $variable_product_total_quantity_in_cart[ $product_id ] > $quantity )
                                    $product_condition_attained = $product_condition_attained && true;
                                else
                                    $product_condition_attained = $product_condition_attained && false;
                                break;

                            case '<':

                                if ( isset( $variable_product_total_quantity_in_cart[ $product_id ] ) && $variable_product_total_quantity_in_cart[ $product_id ] < $quantity )
                                    $product_condition_attained = $product_condition_attained && true;
                                else
                                    $product_condition_attained = $product_condition_attained && false;
                                break;

                        } // end switch

                    } else
                        $product_condition_attained = apply_filters( 'jitso_' . $product_condition[ 'product-type' ] . '_product_condition_check' , $product_condition_attained , $product_condition , $cart_items ); // For extensibility of non-woocommerce native product types

                }

            }

            if ( $condition_logic ) {

                if ( $condition_logic == 'and' )
                    $conditions_attained = $conditions_attained && $product_condition_attained;
                elseif ( $condition_logic == 'or' )
                    $conditions_attained = $conditions_attained || $product_condition_attained;

            } else
                $conditions_attained = $product_condition_attained;
            
            return $conditions_attained;

        }

    }

}
