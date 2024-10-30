<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'JITSO_Offer_Shortcode' ) ) {

    /**
     * Class JITSO_Offer_Shortcode
     *
     * Model that houses the logic of the plugin's various shortcodes.
     *
     * @since 1.0.0
     */
    final class JITSO_Offer_Shortcode {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of JITSO_Offer_Shortcode.
         *
         * @since 1.0.0
         * @access private
         * @var JITSO_Offer_Shortcode
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
         * JITSO_Offer_Shortcode constructor.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of JITSO_Offer_Shortcode model.
         */
        public function __construct( $dependencies ) {

            $this->_plugin_constants = $dependencies[ 'JITSO_Constants' ];

        }

        /**
         * Ensure that only one instance of JITSO_Offer_Shortcode is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $dependencies Array of instance objects of all dependencies of JITSO_Offer_Shortcode model.
         * @return JITSO_Offer_Shortcode
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }




        /*
        |--------------------------------------------------------------------------
        | Shortcodes
        |--------------------------------------------------------------------------
        */

        /**
         * 'jitso_accept_offer' shortcode offer callback.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $atts
         * @return mixed
         */
        public function jitso_accept_offer( $atts ) {

            $atts = shortcode_atts( array(
                'class' => 'jitso-button jitso-button-primary',
                'text'  => __( 'Accept Offer' , 'just-in-time-sales-offers' )
            ) , $atts , 'jitso_accept_offer' );

            ob_start(); ?>

            <a id="jitso-accept-offer" class="<?php echo $atts[ 'class' ]; ?>"><?php echo $atts[ 'text' ]; ?></a>

            <?php $accept_offer_markup = trim( ob_get_clean() );

            return apply_filters( 'jitso_accept_offer_markup' , $accept_offer_markup );

        }

        /**
         * 'jitso_decline_offer' shortcode callback.
         *
         * @since 1.0.0
         * @access public
         *
         * @param $atts
         * @return mixed
         */
        public function jitso_decline_offer( $atts ) {

            $atts = shortcode_atts( array(
                'class' => 'jitso-button jitso-button-secondary',
                'text'  => __( 'Decline Offer' , 'just-in-time-sales-offers' )
            ) , $atts , 'jitso_decline_offer' );

            ob_start(); ?>

            <a id="jitso-decline-offer" class="<?php echo $atts[ 'class' ]; ?>"><?php echo $atts[ 'text' ]; ?></a>

            <?php $decline_offer_markup = trim( ob_get_clean() );

            return apply_filters( 'jitso_decline_offer_markup' , $decline_offer_markup );

        }

    }

}