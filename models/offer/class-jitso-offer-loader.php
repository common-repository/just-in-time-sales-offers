<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'JITSO_Offer_Loader' ) ) {

    /**
     * Class JITSO_Offer_Loader
     *
     * Model that houses the logic of loading offers on the right page and on the right conditions.
     *
     * @since 1.0.0
     */
    final class JITSO_Offer_Loader {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of JITSO_Offer_Loader.
         *
         * @since 1.0.0
         * @access private
         * @var JITSO_Offer_Loader
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
         * JITSO_Offer_Loader constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of JITSO_Offer_Loader model.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants = $dependencies[ 'JITSO_Constants' ];

        }

        /**
         * Ensure that only one instance of JITSO_Offer_Loader is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of JITSO_Offer_Loader model.
         * @return JITSO_Offer_Loader
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Get all the sales offers attached to the current page.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function get_page_attached_sales_offers() {

            $attached_offers = array();

            if ( is_page() ) {

                global $post;
                $page_id = $post->ID;

                $attached_offers = get_post_meta( $page_id , $this->_plugin_constants->POST_META_PAGE_SALES_OFFERS() );
                if ( !is_array( $attached_offers ) )
                    $attached_offers = array();

            } else
				$attached_offers = apply_filters( 'jitso_page_attached_sales_offers_before_preview_mods' , $attached_offers );

			// Filter through each un-published jit offers for preview mods
			$temp_attached_offers = $attached_offers;

			$roles_allowed_for_offer_preview = $this->_plugin_constants->ROLES_ALLOWED_FOR_OFFER_PREVIEW();
			$user = wp_get_current_user();

			foreach ( $attached_offers as $index => $offer_id ) {

				$post_status = get_post_status( $offer_id );

                if ( $post_status == 'draft' && count( array_intersect( ( array ) $user->roles , $roles_allowed_for_offer_preview ) ) )
                    continue;

				if ( ( in_array( $post_status , array( false , 'draft' , 'auto-draft', 'future', 'trash' ) ) ) )
					unset( $temp_attached_offers[ $index ] );

			}

			$attached_offers = $temp_attached_offers;

            return apply_filters( 'jitso_page_attached_sales_offers_after_preview_mods' , $attached_offers );

        }

        /**
         * Check sales offer if all of its conditions are satisfied.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $sales_offer
         * @return bool
         */
        public function check_sales_offer_conditions( $sales_offer ) {

            $offer_conditions = get_post_meta( $sales_offer , $this->_plugin_constants->POST_META_OFFER_CONDITIONS() , true );
            if ( !is_array( $offer_conditions ) )
                $offer_conditions = array();

            if ( !empty( $offer_conditions ) ) {

                // Retrieve cart contents
                $cart_items = array();
                foreach( WC()->cart->get_cart() as $cart_item_no => $cart_item )
                    $cart_items[] = $cart_item;

                $condition_groups_attained = true;

                foreach ( $offer_conditions as $condition_group ) {

                    $condition_group_logic = isset( $condition_group[ 'condition-group-logic' ] ) ? $condition_group[ 'condition-group-logic' ] : null;
                    $conditions_attained   = true;

                    foreach( $condition_group[ 'conditions' ] as $condition ) {

                        $condition_logic     = isset( $condition[ 'condition-logic' ] ) ? $condition[ 'condition-logic' ] : null;
                        $conditions_attained = apply_filters( 'jitso_check_' . $condition[ 'condition-type' ] . '_sales_offer_condition' , $conditions_attained , $condition , $condition_logic , $cart_items );

                    }

                    if ( $condition_group_logic ) {

                        if ( $condition_group_logic == 'and' )
                            $condition_groups_attained = $condition_groups_attained && $conditions_attained;
                        elseif ( $condition_group_logic == 'or' )
                            $condition_groups_attained = $condition_groups_attained || $conditions_attained;

                    } else
                        $condition_groups_attained = $conditions_attained;

                }

                $sales_offer_conditions_attained = $condition_groups_attained;

            } else
                $sales_offer_conditions_attained = true;

            return apply_filters( 'jitso_sales_offer_conditions_attained' , $sales_offer_conditions_attained , $sales_offer );

        }

        /**
         * Get all sales offers that is to be loaded in the current page.
         *
         * @since 1.0.0
         * @access public
         *
         * @return array
         */
        public function get_sales_offers_to_load() {

            $sales_offers_to_load  = array();
            $attached_sales_offers = $this->get_page_attached_sales_offers();
            $current_user          = wp_get_current_user();
            $cookie_start_key      = $current_user->ID ? 'jitso_logged_in_user_' . $current_user->ID : 'jitso_guest_user';

            // prevent duplicate offers listed 
            $attached_sales_offers = array_unique( $attached_sales_offers );

            if ( apply_filters( 'jitso_skip_offers_with_responses' , true ) ) {

                foreach ( $attached_sales_offers as $sales_offer_id ) {

                    $cookie_key = $cookie_start_key . '_offer_' . $sales_offer_id . '_response';
                    if ( !array_key_exists( $cookie_key , $_COOKIE ) && $this->check_sales_offer_conditions( $sales_offer_id ) )
                        $sales_offers_to_load[] = $sales_offer_id;

                }

                return $sales_offers_to_load;

            } else {

                foreach ( $attached_sales_offers as $sales_offer_id ) {

                    if ( $this->check_sales_offer_conditions( $sales_offer_id ) )
                        $sales_offers_to_load[] = $sales_offer_id;

                }

                return $sales_offers_to_load;

            }

        }

        /**
         * Load jit sales offer.
         *
         * @since 1.0.0
         * @since 1.1.1 Refactor code
         * @access public
         */
        public function load_jit_sales_offer() {

            $sales_offers = apply_filters( 'jitso_before_render_jit_sales_offers' , $this->get_sales_offers_to_load() );
            foreach ( $sales_offers as $sales_offer_id )
                $this->render_jit_sales_offer( $sales_offer_id );

        }

        /**
         * Render jit sales offer.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $offer_id
         */
        public function render_jit_sales_offer( $offer_id ) {

            $offer = get_post( $offer_id ); ?>

            <div <?php do_action( 'jitso_offer_popup_data_attributes' , $offer_id ); ?> id="jit-sales-offer-<?php echo $offer_id; ?>" class="jit-sales-offer white-popup mfp-hide" style="<?php do_action( 'jitso_per_offer_popup_inline_styling' , $offer_id ); ?>" data-offer-id="<?php echo $offer_id; ?>">

                <?php do_action( 'jitso_before_jit_sales_offer_content' ); ?>

                <div class="offer-content">
                    <?php echo apply_filters( "the_content" , $offer->post_content ); ?>
                </div>

                <?php do_action( 'jitso_after_jit_sales_offer_content' ); ?>

            </div>

        <?php }

        /**
         * Clear out the cookie that stores the current user's response to offers ( accepted or rejected )
         *
         * @since 1.0.0
         * @since 1.1.1 Refactor code
         * @access public
         */
        public function clear_offer_user_response( $response_type = array( 'accept' , 'decline' ) ) {

            $current_user     = wp_get_current_user();
            $cookie_start_key = $current_user->ID ? 'jitso_logged_in_user_' . $current_user->ID : 'jitso_guest_user';

            foreach ( $_COOKIE as $cookie_key => $cookie_data ) {

                if ( strpos( $cookie_key , $cookie_start_key ) !== false ) {

                    $cookie_data = @unserialize( base64_decode( $cookie_data ) );

                    if ( isset( $cookie_data[ 'response' ] ) && in_array( $cookie_data[ 'response' ] , $response_type ) )
                        setcookie( $cookie_key , null , 1 , '/' );

                }

            }

        }

        /**
         * Record offer view.
         *
         * @since 1.1.3
         * @since 1.2.0 Records the data to the custom offer view table. ( 1.1.x ) series used to record data as post meta per offer post.
         * @access public
         *
         * @param $offer_id
         * @param $offer_data
         * @return int
         */
        public function record_offer_view( $offer_id , $offer_data ) {

            global $wpdb;

            $current_user           = wp_get_current_user();
            $offer_views_table_name = $wpdb->prefix . $this->_plugin_constants->CUSTOM_TABLE_OFFER_VIEWS();

            if ( ! apply_filters( 'jitso_execute_record_offer_view' , true , $current_user ) )
                return true;

            if ( $wpdb->get_var( "SHOW TABLES LIKE '$offer_views_table_name'" ) ) {

                // Return The number of rows inserted, or false on error.
                $result = $wpdb->insert(
                    $offer_views_table_name,
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
                    return $wpdb->insert_id; // Return latest inserted id (auto generated) by offer views table
                else
                    return new WP_Error( 'jitso-record_offer_view-failed-to-record-offer-view' , __( 'Failed to record offer view' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'offer_data' => $offer_data ) );

            } else
                return new WP_Error( 'jitso-record_offer_view-failed-to-record-offer-view' , __( 'Failed to record offer view' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'offer_data' => $offer_data ) );

        }

    }

}
