<?php

final class JITSO_Initial_Guided_Tour {

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
    */

    private static $_instance = null;

    const OPTION_INITIAL_GUIDED_TOUR_STATUS = 'jitso_initial_guided_tour_status';
    const STATUS_OPEN                       = 'open';
    const STATUS_CLOSE                      = 'close';

    private $urls;
    private $screens;




    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Cloning is forbidden.
     *
     * @since 1.1.2
     * @access public
     */
    public function __clone () {

        _doing_it_wrong( __FUNCTION__ , __( 'Cheatin&#8217; huh?' , 'just-in-time-sales-offers' ) , '1.1.2' );

    }

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.1.2
     * @access public
     */
    public function __wakeup () {

        _doing_it_wrong( __FUNCTION__ , __( 'Cheatin&#8217; huh?' , 'just-in-time-sales-offers' ) , '1.1.2' );

    }

    /**
     * JITSO_Bootstrap constructor.
     *
     * @since 1.1.2
     * @access public
     */
    private function __construct() {

        $this->urls = apply_filters( 'jitso_initial_guided_tour_pages' , array(
            'plugin-listing'  => admin_url( 'plugins.php' ),
            'product-listing' => admin_url( 'edit.php?post_type=product' ),
            'jitso-settings'  => admin_url( 'admin.php?page=wc-settings&tab=jitso_settings' ),
            'jitso-listing'   => admin_url( 'edit.php?post_type=jit_sales_offer' )
        ) );

        $this->screens = apply_filters( 'jitso_initial_guided_tours' , array(
            'plugins' => array(
                'elem'  => '#toplevel_page_woocommerce',
                'html'  => __( '<h3>Welcome to Just-In-Time Sales Offers!</h3>
                                <p>Would you like to go on a guided tour of the plugin? Takes less than 30 seconds.</p>' , 'just-in-time-sales-offers' ),
                'prev'  => null,
                'next'  => $this->urls[ 'product-listing' ],
                'edge'  => 'left',
                'align' => 'left'
            ),
            'edit-product' => array(
                'elem'  => '#menu-posts-product .wp-has-current-submenu',
                'html'  => __( '<h3>Just-In-Time Sales Offers is made for promoting products in your store based on what your customers have in their cart currently.</h3>
                                <p>Up-sells are offers that get your customers to buy a higher priced item. Eg. If they have a Big Mac in their cart, you might offer them a meal for a slight discount than if they were to purchase the Big Mac, Fries and Coke separately.</p>
                                <p>Cross-sells are for promoting related items. Eg. If they have a laptop in their cart, you might offer a Bluetooth travel mouse for 30% off the retail price if they add it to their cart now.</p>
                                <p>Both strategies are great for increasing the average size of your store\'s orders but work best under specific scenarios.</p>' , 'just-in-time-sales-offers' ),
                'prev'  => $this->urls[ 'plugin-listing' ],
                'next'  => $this->urls[ 'jitso-settings' ],
                'edge'  => 'left',
                'align' => 'left'
            ),
            'woocommerce_page_wc-settings' => array(
                'elem'  => '.nav-tab-active',
                'html'  => __( '<h3>This is the settings area where you can configure important options related to the performance of your up-sell and cross-sell popups.</h3>
                                <p>You can come back here anytime after the tour to configure all the settings related to your sales offer popups.</p>' , 'just-in-time-sales-offers' ),
                'prev'  => $this->urls[ 'product-listing' ],
                'next'  => $this->urls[ 'jitso-listing' ],
                'edge'  => 'top',
                'align' => 'left'
            ),
            'edit-jit_sales_offer' => array(
                'elem'  => '#toplevel_page_woocommerce ul li a.current',
                'html'  => sprintf( __( '<h3>This is the Offers list which shows you what Offers you currently have running on your store.</h3>
                                         <p>Offers in Draft mode are only visible to admins and store managers. Use the draft mode to edit your Offer like you would a product that isn\'t quite ready put on your store.</p>
                                         <p>This concludes the tour. Click on the button below to add your first offer:</p>
                                         <p><a id="jitso-add-first-offer" href="%1$s" class="button button-primary">Add My First Offer</a></p>' , 'just-in-time-sales-offers' ) , admin_url( 'post-new.php?post_type=jit_sales_offer' ) ),
                'prev'  => $this->urls[ 'jitso-settings' ],
                'next'  => null,
                'edge'  => 'left',
                'align' => 'left'
            )
        ) );

    }

    /**
     * Get the only instance of the class.
     *
     * @since 1.1.2
     * @access public
     *
     * @return JITSO_Initial_Guided_Tour
     */
    public static function instance() {

        if ( !self::$_instance )
            self::$_instance = new self();

        return self::$_instance;

    }

    /**
     * Get current screen.
     * 
     * @since 1.1.2
     * @access public
     */
    public function get_current_screen() {

        $screen = get_current_screen();

        if ( !empty( $this->screens[ $screen->id ] ) )
            return $this->screens[ $screen->id ];

        return false;

    }

    /**
     * Initialize guided tour options.
     *
     * @since 1.1.2
     * @access public
     */
    public function initialize_guided_tour_options() {

        if ( get_option( self::OPTION_INITIAL_GUIDED_TOUR_STATUS ) === false )
            update_option( self::OPTION_INITIAL_GUIDED_TOUR_STATUS , self::STATUS_OPEN );
        
    }

    /**
     * Terminate guided tour options.
     *
     * @since 1.1.2
     * @access public
     */
    public function terminate_guided_tour_options() {
        
        delete_option( self::OPTION_INITIAL_GUIDED_TOUR_STATUS );

    }

    /**
     * Get screens with registered guide.
     * 
     * @since 1.1.2
     * @access public
     */
    public function get_screens() {

        return $this->screens;

    }

    /**
     * Close initial guided tour.
     * 
     * @since 1.1.2
     * @access public
     */
    public function jitso_close_initial_guided_tour() {

        if ( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX ) {

            if ( !check_ajax_referer( 'jitso-close-initial-guided-tour' , 'nonce' , false ) )
                wp_die( __( 'Security Check Failed' , 'just-in-time-sales-offers' ) );

            update_option( self::OPTION_INITIAL_GUIDED_TOUR_STATUS , self::STATUS_CLOSE );

            wp_send_json_success();

        } else
            wp_die( __( 'Invalid AJAX Call' , 'just-in-time-sales-offers' ) );
        
    }

} // end class
