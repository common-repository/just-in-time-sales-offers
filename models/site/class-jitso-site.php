<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'JITSO_Site' ) ) {

    /**
     * Class JITSO_Site
     *
     * Model that houses the logic of the various helper functions related to the current site the plugin is installed.
     *
     * @since 1.1.0
     */
    final class JITSO_Site {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of JITSO_Site.
         *
         * @since 1.1.0
         * @access private
         * @var JITSO_Site
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
         * JITSO_Site constructor.
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
         * Ensure that there is only one instance of JITSO_Site is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.1.0
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         * @return JITSO_Site
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Get all the site pages.
         *
         * @since 1.1.0
         * @access public
         *
         * @param array $args
         * @return array
         */
        public function get_site_pages( $args ) {
            
            if ( !is_array( $args ) )
                return new WP_Error( 'jitso-get_site_pages-function-invalid-args' , __( 'Function "get_site_pages" requires an $args argument in array format.' , 'just-in-time-sales-offers' ) , $args );

            $site_pages        = JITSO_Helper::get_all_site_pages();
            $return_site_pages = null;

            if ( array_key_exists( 'return_format' , $args ) ) {

                switch ( $args[ 'return_format' ] ) {

                    case 'select_option':

                        if ( isset( $args[ 'add_empty_option' ] ) && $args[ 'add_empty_option' ] )
                            $return_site_pages = '<option value="">' . __( '--Select Page--' , 'just-in-time-sales-offers' ) . '</option>';
                        else
                            $return_site_pages = "";

                        if ( isset( $args[ 'selected_values' ] ) && is_array( $args[ 'selected_values' ] ) ) {

                            foreach ( $site_pages as $page ) {

                                $selected = in_array( $page->ID , $args[ 'selected_values' ] ) ? 'selected="selected"' : '';

                                $return_site_pages .= '<option value="' . $page->ID . '" ' . $selected . '>' . $page->post_title . '</option>';

                            }

                        } else {
                            
                            foreach ( $site_pages as $page )
                                $return_site_pages .= '<option value="' . $page->ID . '">' . $page->post_title . '</option>';
                            
                        }

                        break;

                    case 'raw':

                        $return_site_pages = array();

                        foreach ( $site_pages as $page )
                            $return_site_pages[ $page->ID ] = $page->post_title;

                        break;

                    default:
                        return new WP_Error( 'jitso-get_site_pages-function-unsupported-return_format' , __( 'Unsupported "return_format" in the $args argument.' , 'just-in-time-sales-offers' ) , $args );

                }

            } else
                return new WP_Error( 'jitso-get_site_pages-function-missing-return_format-key-in-args' , __( 'Missing "return_format" key in the $args argument.' , 'just-in-time-sales-offers' ) , $args );

            return $return_site_pages;

        }

        /**
         * Clean offer ids attached to a certain page meta.
         * There will be times that this meta gets populated with values that will be invalid in the future.
         * Ex. deleted offers and the id of that deleted offer is still linked to that certain page.
         *
         * @since 1.1.0
         * @since 1.1.1 Add hook to extend functionality
         * @access public
         */
        public function site_pages_offer_meta_cleaner() {

            global $wpdb;

            $wpdb->query(
                "DELETE FROM $wpdb->postmeta
                 WHERE meta_key = '" . $this->_plugin_constants->POST_META_PAGE_SALES_OFFERS() . "'
                 AND meta_value NOT IN (
                    SELECT ID FROM $wpdb->posts
                    WHERE post_type = '" . $this->_plugin_constants->OFFER_CPT_NAME() . "'
                    AND post_status != 'auto-draft'
                 )"
            );

            do_action( 'jitso_offer_meta_cleaner' );

        }

    }

}
