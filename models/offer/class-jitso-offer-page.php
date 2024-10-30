<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'JITSO_Offer_Page' ) ) {

    /**
     * Class JITSO_Offer_Page
     *
     * Model that houses the logic of the various helper functions related to the sales offer pages.
     *
     * @since 1.1.0
     */
    final class JITSO_Offer_Page {

        /*
        |--------------------------------------------------------------------------
        | Class Properties
        |--------------------------------------------------------------------------
        */

        /**
         * Property that holds the single main instance of JITSO_Offer_Page.
         *
         * @since 1.1.0
         * @access private
         * @var JITSO_Offer_Page
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
         * JITSO_Offer_Page constructor.
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
         * Ensure that there is only one instance of JITSO_Offer_Page is loaded or can be loaded (Singleton Pattern).
         *
         * @since 1.1.0
         * @access public
         *
         * @param array $dependencies Array of instances of dependencies for this class.
         * @return JITSO_Offer_Page
         */
        public static function instance( $dependencies ) {

            if ( !self::$_instance instanceof self )
                self::$_instance = new self( $dependencies );

            return self::$_instance;

        }

        /**
         * Add sales offer page.
         * This function is only valid on admin page.
         *
         * @since 1.1.0
         * @access public
         *
         * @param $offer_id
         * @param $page_data
         * @return mixed
         */
        public function add_sales_offer_page( $offer_id , $page_data ) {
            
            if ( !is_admin() )
                return new WP_Error( 'jitso-add_sales_offer_page-invalid-operation' , __( 'Invalid Operation.' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'page_data' => $page_data ) );

            if ( !JITSO_Helper::current_user_authorized() )
                return new WP_Error( 'jitso-add_sales_offer_page-authorization-failed' , __( 'Authorization Failed.' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'page_data' => $page_data ) );

            $offer_pages = get_post_meta( $offer_id , $this->_plugin_constants->POST_META_OFFER_PAGES() , true );
            if ( !is_array( $offer_pages ) )
                $offer_pages = array();

            if ( in_array( $page_data[ 'type' ] , $this->_plugin_constants->SALES_OFFER_INDEXED_PAGE_TYPES() ) ) {

                // Attach sales offer to the page ( adding it to that page's post meta ).
                if ( $page_data[ 'type' ] == 'page' )
                    add_post_meta( $page_data[ 'id' ] , $this->_plugin_constants->POST_META_PAGE_SALES_OFFERS() , $offer_id );
                else
                    do_action( 'jitso_attach_sales_offer_to_page' , $page_data , $offer_id );

                $key = $page_data[ 'type' ] . "_" . $page_data[ 'id' ];

                if ( array_key_exists( $key , $offer_pages ) ) {

                    return new WP_Error( 
                        'jitso-add_sales_offer_page-new-page-duplicate' ,
                        __( 'Page already added on the list.' , 'just-in-time-sales-offers' ) ,
                        array(
                            'offer_id'  => $offer_id,
                            'key'       => $key,
                            'page_data' => $page_data
                        )
                    );

                } else {

                    $offer_pages[ $key ] = $page_data;

                    update_post_meta( $offer_id , $this->_plugin_constants->POST_META_OFFER_PAGES() , $offer_pages );

                    // Construct new row markup
                    $sales_offer_pages_types = $this->_plugin_constants->SALES_OFFER_PAGE_TYPES();
                    $id_text = JITSO_Helper::get_id_title( $page_data[ 'type' ] , $page_data[ 'id' ] );
                    ob_start(); ?>

                    <tr>
                        <td class="meta hidden">
                            <span class="key"><?php echo $key; ?></span>
                            <span class="type"><?php echo $page_data[ 'type' ]; ?></span>
                            <span class="id"><?php echo $page_data[ 'id' ]; ?></span>
                            <?php do_action( "jitso_offer_page_table_additional_column_meta" ); ?>
                        </td>
                        <td class="type_text"><?php echo $sales_offer_pages_types[ $page_data[ 'type' ] ]; ?></td>
                        <td class="id_text"><?php echo $id_text; ?></td>

                        <?php do_action( "jitso_offer_page_table_additional_column" ); ?>

                        <td class="controls">
                            <span class="dashicons dashicons-edit edit_offer_page"></span>
                            <span class="dashicons dashicons-no delete_offer_page"></span>
                        </td>
                    </tr>

                    <?php $new_row_markup = ob_get_clean();

                    $result = array(
                        'status'         => 'success',
                        'key'            => $key,
                        'new_row_markup' => $new_row_markup
                    );
                    
                    return $result;

                }

            } else {

                // For future proofing, for other or unknown types of offer page ( ex. custom url or dynamically generated page that has no representation inside of wordpress )

                // Give result a default of WP_Error
                // Just like the old saying goes, "All codes are guilty until proven innocent".

                $result = new WP_Error( 'jitso-add_sales_offer_page-unknown-error' , __( 'An unknown error occur when adding new sales offer page.' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'page_data' => $page_data ) );

                return apply_filters( 'jitso_add_new_other_type_sales_offer_page' , $result , $offer_id , $page_data , $offer_pages );

            }

        }

        /**
         * Edit sales offer page.
         * This function is only valid on admin page.
         *
         * @since 1.1.0
         * @access public
         *
         * @param $offer_id
         * @param $key
         * @param $page_data
         * @return mixed
         */
        public function edit_sales_offer_page( $offer_id , $key , $page_data ) {

            if ( !is_admin() )
                return new WP_Error( 'jitso-edit_sales_offer_page-invalid-operation' , __( 'Invalid Operation.' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'key' => $key , 'page_data' => $page_data ) );

            if ( !JITSO_Helper::current_user_authorized() )
                return new WP_Error( 'jitso-edit_sales_offer_page-authorization-failed' , __( 'Authorization Failed.' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'key' => $key , 'page_data' => $page_data ) );
            
            $offer_pages = get_post_meta( $offer_id , $this->_plugin_constants->POST_META_OFFER_PAGES() , true );
            if ( !is_array( $offer_pages ) )
                $offer_pages = array();

            if ( !array_key_exists( $key , $offer_pages ) ) {

                return new WP_Error( 'jitso-edit_sales_offer_page-edit-page-not-exist' , __( 'Sales offer page you are trying to edit does not exist on the list of pages that this offer is to be shown.' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'key' => $key , 'page_data' => $page_data ) );

            } else {

                if ( in_array( $page_data[ 'type' ] , $this->_plugin_constants->SALES_OFFER_INDEXED_PAGE_TYPES() ) ) {

                    // Because of our logic behind the key, we need to generate a new one so that the key
                    // synchronizes with the edited data. If we don't do it we could end up having the issue below:
                    // page_1 as the key and the data is a product archive.
                    $new_key = $page_data[ 'type' ] . "_" . $page_data[ 'id' ];

                    if ( array_key_exists( $new_key , $offer_pages ) && $new_key != $key ) {
                        
                        return new WP_Error( 'jitso-edit_sales_offer_page-edit-operation-cause-duplicate' , __( 'Edited page duplicates with an existing page on the list' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'key' => $key , 'page_data' => $page_data ) );

                    } else {

                        // Edit sales offer that was attached to a page

						// Detach sales offer id from old page
						$old_page_data = $offer_pages[ $key ];

						if ( $old_page_data[ 'type' ] == 'page' )
							delete_post_meta( $old_page_data[ 'id' ] , $this->_plugin_constants->POST_META_PAGE_SALES_OFFERS() , $offer_id );
						else
							do_action( 'jitso_detach_sales_offer_to_page' , $old_page_data , $offer_id );

						// Attach sales offer id to new page
						if ( $page_data[ 'type' ] == "page" )
                            add_post_meta( $page_data[ 'id' ] , $this->_plugin_constants->POST_META_PAGE_SALES_OFFERS() , $offer_id );
                        else
							do_action( 'jitso_attach_sales_offer_to_page' , $page_data , $offer_id );

                        unset( $offer_pages[ $key ] ); // Remove the old data
                        $offer_pages[ $new_key ] = $page_data; // Add the edited data

                        update_post_meta( $offer_id , $this->_plugin_constants->POST_META_OFFER_PAGES() , $offer_pages );

                        $result = array(
                            'status'  => 'success',
                            'key'     => $key,
                            'new_key' => $new_key
                        );

                        return $result;

                    }

                } else {

                    // For future proofing, for other or unknown types of offer page ( ex. custom url or dynamically generated page that has no representation inside of wordpress )

                    // Give result a default of WP_Error
                    // Just like the old saying goes, "All codes are guilty until proven innocent".

                    $result = new WP_Error( 'jitso-edit_sales_offer_page-unknown-error' , __( 'An unknown error occur when editing sales offer page.' , 'just-in-time-sales-offers' ) , array( 'offer_id'  => $offer_id , 'key' => $key , 'page_data' => $page_data ) );

                    return apply_filters( 'jitso_edit_other_type_sales_offer_page' , $result , $offer_id , $key , $page_data , $offer_pages );

                }

            }

        }

        /**
         * Delete sales offer page.
         * This function is only valid on admin page.
         *
         * @since 1.1.0
         * @access public
         *
         * @param $offer_id
         * @param $key
         * @return mixed
         */
        public function delete_sales_offer_page( $offer_id , $key ) {

            if ( !is_admin() )
                return new WP_Error( 'jitso-delete_sales_offer_page-invalid-operation' , __( 'Invalid Operation.' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'key' => $key ) );

            if ( !JITSO_Helper::current_user_authorized() )
                return new WP_Error( 'jitso-delete_sales_offer_page-authorization-failed' , __( 'Authorization Failed.' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'key' => $key ) );

            $offer_pages = get_post_meta( $offer_id , $this->_plugin_constants->POST_META_OFFER_PAGES() , true );
            if ( !is_array( $offer_pages ) )
                $offer_pages = array();

            if ( !array_key_exists( $key , $offer_pages ) ) {

                return new WP_Error( 'jitso-delete_sales_offer_page-delete-page-not-exist' , __( 'Sales offer page you are trying to delete does not exist on the list of pages that this offer is to be shown.' , 'just-in-time-sales-offers' ) , array( 'offer_id' => $offer_id , 'key' => $key ) );

            } else {

                // Delete sales offer that was attached to a page
                $page_data = $offer_pages[ $key ];

                if ( $page_data[ 'type' ] == 'page' )
                    delete_post_meta( $page_data[ 'id' ] , $this->_plugin_constants->POST_META_PAGE_SALES_OFFERS() , $offer_id );
                else
                    do_action( 'jitso_detach_sales_offer_to_page' , $page_data , $offer_id );

                unset( $offer_pages[ $key ] );

                update_post_meta( $offer_id , $this->_plugin_constants->POST_META_OFFER_PAGES() , $offer_pages );

                return true;

            }

        }

    }

}
