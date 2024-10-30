<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'JITSO_WooCommerce_Composite_Products' ) ) {

    /**
     * Class JITSO_WooCommerce_Composite_Products
     *
     * Model that houses the logic of integrating with WooCommerce Composite Products plugin.
     *
     * @since 1.1.0
     */
    final class JITSO_WooCommerce_Composite_Products {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of JITSO_WooCommerce_Composite_Products.
         *
         * @since 1.1.0
         * @access private
         * @var JITSO_WooCommerce_Composite_Products
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
         * JITSO_WooCommerce_Composite_Products constructor.
         *
         * @since 1.1.0
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants = $dependencies[ 'JITSO_Constants' ];

        }

        /**
         * Ensure that there is only one instance of JITSO_WooCommerce_Composite_Products is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.1.0
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         * @return JITSO_WooCommerce_Composite_Products
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Get bundle product additional info.
         * 
         * @since 1.1.0
         * @access public
         *
         * @param $product_additional_data
         * @param $product
         * @return array
         */
        public function get_product_additional_info( $product_additional_data , $product ) {

            $product_additional_data = array( 'product_type' => 'composite' );

            return $product_additional_data;

        }


        // Offer Condition

        /**
         * Check whether to condition check product cart item.
         * Ex. a simple product is a component of a composite product, then that simple product should be excluded on offer condition check.
         *
         * @param boolean $process_product_cart_item
         * @param array $cart_item
         * @param array $cart_items
         * @param array $product_condition
         * @return boolean
         */
        public function condition_check_product_cart_item( $process_product_cart_item , $cart_item , $cart_items , $product_condition ) {

            if ( isset( $cart_item[ 'composite_item' ] ) && $cart_item[ 'composite_item' ] )
                return false; // This product is a component of a composite product
            else
                return $process_product_cart_item && true;
            
        }

        /**
         * Get quantity in cart entry text for 'bundle' type product.
         * 
         * @since 1.1.0
         * @access public
         * 
         * @param $product_text
         * @param $data
         * @return text
         */
        public function product_quantity_in_cart_entry_text( $product_text , $data ) {

            $product_text = '<div class="product"><a href="' . home_url( "/wp-admin/post.php?post=" . $data[ 'product-id' ] . "&action=edit" ) . '" target="_blank">[ID : ' . $data[ 'product-id' ] . '] ' . get_the_title( $data[ 'product-id' ] ) . '</a></div>';

            return $product_text;

        }

        /**
         * Check bundle product condition is meet.
         * 
         * @since 1.1.0
         * @access public
         * 
         * @param $product_condition_attained
         * @param $product_condition
         * @param $cart_items
         * @return boolean
         */
        public function product_condition_check( $product_condition_attained , $product_condition , $cart_items ) {

            $product_id         = $product_condition[ 'product-id' ];
            $quantity_condition = $product_condition[ 'product-quantity-condition' ];
            $quantity           = $product_condition[ 'product-quantity' ];

            $product_in_cart = false;

            foreach ( $cart_items as $cart_item ) {
                
                $cart_quantity = isset( $cart_item[ 'qty' ] ) ? $cart_item[ 'qty' ] : isset( $cart_item[ 'quantity' ] ) ? $cart_item[ 'quantity' ] : 0;

                // Compoception, composite product component of another composite product ( mind blown )
                $process_composite_product_cart_item = true;
                $process_composite_product_cart_item = apply_filters( 'jitso_condition_check_composite_product_cart_item' , $process_composite_product_cart_item , $cart_item , $cart_items , $product_condition );

                if ( !$process_composite_product_cart_item )
                    continue;
                
                if ( $cart_item[ 'product_id' ] == $product_id ) {

                    if ( !$product_in_cart )
                        $product_in_cart = true;

                    switch ( $quantity_condition ) {
                        case '=':

                            if ( $cart_quantity == $quantity )
                                $product_condition_attained = $product_condition_attained && true;
                            else
                                $product_condition_attained = $product_condition_attained && false;
                            break;

                        case '!=':

                            if ( $cart_quantity != $quantity )
                                $product_condition_attained = $product_condition_attained && true;
                            else
                                $product_condition_attained = $product_condition_attained && false;
                            break;

                        case '>':

                            if ( $cart_quantity > $quantity )
                                $product_condition_attained = $product_condition_attained && true;
                            else
                                $product_condition_attained = $product_condition_attained && false;
                            break;

                        case '<':

                            if ( $cart_quantity < $quantity )
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

            $product_condition_attained = apply_filters( 'jitso_composite_product_additional_condition_check' , $product_condition_attained , $product_condition , $cart_items );

            return $product_condition_attained;
            
        }


        // Offer Accept Action

        /**
         * Get bundle product to add in cart entry text.
         */
        public function product_to_add_entry_text( $product_text , $data ) {

            $product_id   = isset( $data[ 'product_id' ] ) ? $data[ 'product_id' ] : $data[ 'product-id' ];
            $product_text = '<div class="product"><a href="' . home_url( "/wp-admin/post.php?post=" . $product_id . "&action=edit" ) . '" target="_blank">[ID : ' . $product_id . '] ' . get_the_title( $product_id ) . '</a></div>';
            
            return $product_text;

        }

        /**
         * Add composite product to cart on executing offer accept action if necessary.
         * 
         * @since 1.1.0
         * @access public
         *
         * @param $cart_item_key
         * @param $action_id
         * @param $product_data
         * @return mixed Cart item key on success, Boolean false on failure
         */
        public function accept_offer_action_add_composite_product_to_cart( $cart_item_key , $action_id , $product_data , $additional_product_data ) {

           // Initialize components of a composite product
            $composite_product     = wc_get_product( $product_data[ 'product-id' ] );
            $composite_data        = $composite_product->get_composite_data();
            $components_to_add     = array();
            $components_to_add_qty = array();

            foreach ( $composite_data as $composite ) {

                // Only add required components
                if ( $composite[ 'optional' ] == 'no' ) {
                    
                    $components_to_add[ $composite[ 'component_id' ] ]     = $composite[ 'default_id' ]; // The actual product id
                    $components_to_add_qty[ $composite[ 'component_id' ] ] = $composite[ 'quantity_min' ]; // Minimum qty times the composite product qty

                    $product = wc_get_product( $composite[ 'default_id' ] );

                    if ( $product->product_type == 'variable' ) {

                        $default_attributes = $product->get_variation_default_attributes();

                        // There are instances where the admin only supplly default values for partial set of attributes.
                        // We need them to supply defaults to all attributes, that's why we check if the 2 arrays below is the same length.
                        // 'get_variation_default_attributes' just add on the array the attributes with value, disregarding the other ones with no default value.
                        // So we need to check againts the variable products attribute length the result array of 'get_variation_default_attributes'.
                        if ( count( $default_attributes ) == count( $product->get_attributes() ) ) {

                            $variation_data = JITSO_Helper::get_variation_data_by_attributes( $composite[ 'default_id' ] , $default_attributes );

                            foreach ( $default_attributes as $attribute => $value ) {
                                
                                $_POST[ 'wccp_attribute_' . $attribute ][ $composite[ 'component_id' ] ] = $value;
                                $_POST[ 'wccp_variation_id' ][ $composite[ 'component_id' ] ]            = $variation_data[ 'variation_id' ];

                            }

                        } else
                            wc_add_notice( sprintf( __( 'Please provide default selected attributes for the composite component variable product %1$s' , 'just-in-time-sales-offers' ) , $product->get_title() ) , 'error' );
                        
                    }

                }
                
            }

            $components_to_add     = apply_filters( 'jitso_components_to_add_for_composite_product' , $components_to_add , $composite_data , $composite_product , $product_data , $additional_product_data );
            $components_to_add_qty = apply_filters( 'jitso_components_to_add_quantity_for_composite_product' , $components_to_add_qty , $composite_data , $composite_product, $product_data , $additional_product_data );

            if ( !empty( $components_to_add ) && !empty( $components_to_add_qty ) ) {

                $_POST[ 'wccp_component_selection' ] = $components_to_add;
                $_POST[ 'wccp_component_quantity' ]  = $components_to_add_qty;

            }

            $_POST[ 'quantity' ]    = $product_data[ 'product-quantity' ];
            $_POST[ 'add-to-cart' ] = $product_data[ 'product-id' ];

            // Add composite product
            $cart_item_key = WC()->cart->add_to_cart( $product_data[ 'product-id' ] , $product_data[ 'product-quantity' ] , 0 , array() , $additional_product_data );

            return $cart_item_key;

        }
        
        
        // Executable

        /**
         * Execute the model that integrates JITSO with WooCommerce Product Composites plugin.
         *
         * @since 1.2.4
         * @access public
         */
        public function run() {

            // Get composite product additional info
            add_filter( 'jitso_get_composite_product_additional_info' , array( $this , 'get_product_additional_info' ) , 10 , 2 );


            // [Offer Condition]

            // Check whether to condition check product cart item
            add_filter( 'jitso_condition_check_simple_product_cart_item' , array( $this , 'condition_check_product_cart_item' ) , 10 , 4 );
            add_filter( 'jitso_condition_check_variable_product_cart_item' , array( $this , 'condition_check_product_cart_item' ) , 10 , 4 );
            add_filter( 'jitso_condition_check_bundle_product_cart_item' , array( $this , 'condition_check_product_cart_item' ) , 10 , 4 );
            add_filter( 'jitso_condition_check_composite_product_cart_item' , array( $this , 'condition_check_product_cart_item' ) , 10 , 4 );

            // Get quantity in cart entry text for 'composite' type product
            add_filter( 'jitso_composite_product_quantity_in_cart_entry_text' , array( $this , 'product_quantity_in_cart_entry_text' ) , 10 , 2 );

            // Check composite product condition is meet
            add_filter( 'jitso_composite_product_condition_check' , array( $this , 'product_condition_check' ) , 10 , 3 );


            // [Offer Accpet Action]

            // Get composite product to add in cart entry text
            add_filter( 'jitso_composite_product_to_add_entry_text' , array( $this , 'product_to_add_entry_text' ) , 10 , 2 );

            // Add composite product to cart on executing offer accept action if necessary
            add_filter( 'jitso_accept_offer_action_add_composite_product_to_cart' , array( $this , 'accept_offer_action_add_composite_product_to_cart' ) , 10 , 4 );  

        }
        
    }

}
