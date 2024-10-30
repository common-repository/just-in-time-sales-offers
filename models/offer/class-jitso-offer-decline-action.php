<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'JITSO_Offer_Decline_Action' ) ) {

    /**
     * Class JITSO_Offer_Decline_Action
     *
     * Model that houses the logic of the various helper functions related to the sales offer decline actions.
     *
     * @since 1.1.0
     */
    final class JITSO_Offer_Decline_Action {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of JITSO_Offer_Decline_Action.
         *
         * @since 1.1.0
         * @access private
         * @var JITSO_Offer_Decline_Action
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
         * JITSO_Offer_Decline_Action constructor.
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
         * Ensure that there is only one instance of JITSO_Offer_Decline_Action is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.1.0
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         * @return JITSO_Offer_Decline_Action
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Save decline offer actions.
         * This function is only valid on admin page.
         *
         * @since 1.1.0
         * @access public
         *
         * @param $offer_id
         * @param $data
         * @return mixed
         */
        public function save_decline_offer_actions( $offer_id , $data ) {

            if ( !is_admin() )
                return new WP_Error( 'jitso-save_decline_offer_actions-invalid-operation' , __( 'Invalid Operation' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );

            if ( !JITSO_Helper::current_user_authorized() )
                return new WP_Error( 'jitso-save_decline_offer_actions-authorization-failed' , __( 'Authorization Failed' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'data' => $data ) );

            // TODO: Validation

            $data = apply_filters( 'jitso_before_save_decline_offer_actions' , $data , $offer_id );

            update_post_meta( $offer_id , $this->_plugin_constants->POST_META_OFFER_DECLINE_ACTIONS() , $data );

            return true;

        }

        /**
         * Executes decline actions for this offer.
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
        public function execute_decline_offer_actions( $offer_id , $offer_data ) {

            $current_user = wp_get_current_user();

            if ( apply_filters( 'jitso_execute_record_offer_decline' , true , $current_user ) ) {

                // Record offer decline
                $offer_decline_stat_id = $this->record_offer_decline( $offer_id , $offer_data );

                if ( is_wp_error( $offer_decline_stat_id ) ) {
                    // TODO: Log error here, no need to interfere with the offer decline action
                }
                
            }

            // Store the customers response to the current offer ( 'decline' ) to a cookie
            $cookie_key      = $current_user->ID ? 'jitso_logged_in_user_' . $current_user->ID . '_offer_' . $offer_id . '_response' : 'jitso_guest_user_offer_' . $offer_id . '_response';
            $cookie_data     = base64_encode( @serialize( apply_filters( 'jitso_decline_offer_response_cookie_data' , array( 'response' => 'decline' ) ) ) );
            $cookie_lifespan = apply_filters( 'jitso_decline_offer_response_cookie_lifespan' , 24 );
            $cookie_lifespan = '+' . $cookie_lifespan . ' hours';

            setcookie( $cookie_key , $cookie_data , strtotime( $cookie_lifespan ) , '/' );

            $decline_offer_action = get_post_meta( $offer_id , $this->_plugin_constants->POST_META_OFFER_DECLINE_ACTIONS() , true );
            if ( !is_array( $decline_offer_action ) )
                $decline_offer_action = array();

            $decline_actions_executed = array();

            foreach ( $decline_offer_action as $action_type => $action_data ) {

                if ( $action_type == 'do-nothing' )
                    $decline_actions_executed[ 'do-nothing' ] = array( 'status' => 'success' );
                else
                    $decline_actions_executed = apply_filters( 'jitso_execute_' . $action_type . '_decline_offer_action' , $decline_actions_executed , $action_data );

            }

            $result = array(
                'status'                   => 'success',
                'success_message'          => __( 'Thanks, the offer was declined.' , 'just-in-time-sales-offers' ),
                'decline_actions_executed' => $decline_actions_executed
            );

            do_action( 'jitso_after_decline_offer_actions_executed' , $result , $decline_offer_action );

            return $result;

        }

        /**
         * Record offer decline.
         *
         * @since 1.1.3
         * @since 1.2.0 Records the data to the custom offer decline table. ( 1.1.x ) series used to record data as post meta per offer post.
         * @access public
         *
         * @param $offer_id
         * @param $offer_data
         * @return int
         */
        public function record_offer_decline( $offer_id , $offer_data ) {

            global $wpdb;

            $current_user              = wp_get_current_user();
            $offer_declines_table_name = $wpdb->prefix . $this->_plugin_constants->CUSTOM_TABLE_OFFER_DECLINES();

            if ( $wpdb->get_var( "SHOW TABLES LIKE '$offer_declines_table_name'" ) ) {

                // Return The number of rows inserted, or false on error.
                $result = $wpdb->insert(
                    $offer_declines_table_name,
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
                    return $wpdb->insert_id; // Return latest inserted id (auto generated) by offer declines table
                else
                    return new WP_Error( 'jitso-record_offer_decline-failed-to-record-offer-decline' , __( 'Failed to record offer decline' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'offer_data' => $offer_data ) );

            } else
                return new WP_Error( 'jitso-record_offer_decline-failed-to-record-offer-decline' , __( 'Failed to record offer decline' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'offer_data' => $offer_data ) );

        }

    }

}
