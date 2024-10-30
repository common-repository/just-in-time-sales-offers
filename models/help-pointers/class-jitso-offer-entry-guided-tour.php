<?php

final class JITSO_Offer_Entry_Guided_Tour {

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
    */

    private static $_instance = null;

    const OPTION_OFFER_ENTRY_GUIDED_TOUR_STATUS = 'JITSO_Offer_Entry_Guided_Tour_status';
    const STATUS_OPEN                           = 'open';
    const STATUS_CLOSE                          = 'close';

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

        $this->urls = apply_filters( 'jitso_offer_entry_guided_tour_pages' , array() );

        $tours_array = array(
            array(
                'id'    => 'offer_entry_guide_intro',
                'elem'  => '#toplevel_page_woocommerce ul li a.current',
                'html'  => __( '<h3>Congratulations, you just added your first offer!</h3>
                                <p>Would you like to learn how to configure it? It takes less than a minute and you\'ll then know exactly how to setup your first offer!</p>' , 'just-in-time-sales-offers' ),
                'prev'  => null,
                'next'  => '@offer_entry_guide_title',
                'edge'  => 'left',
                'align' => 'left'
            ),
            array(
                'id'    => 'offer_entry_guide_title',
                'elem'  => '#titlediv',
                'html'  => __( '<h3>First, give your Offer a name.</h3>
                                <p>This is used internally for you to identify the Offer in the system, so make it something that describes what the Offer is all about.</p>' , 'just-in-time-sales-offers' ),
                'prev'  => '@offer_entry_guide_intro',
                'next'  => '@offer_entry_guide_content',
                'edge'  => 'top',
                'align' => 'center'
            ),
            array(
                'id'    => 'offer_entry_guide_content',
                'elem'  => '#wp-content-editor-container',
                'html'  => __( '<h3>Content editor</h3>
                                <p>Use the content editor to edit what shows in your Offer popup.</p>
                                <p>Treat this like you would a product or blog post. You can use headings, insert images, shortcodes, whatever you like!</p>
                                <p>We recommend you keep it short and snappy as this all about grabbing their attention and getting them to accept the offer.</p>' , 'just-in-time-sales-offers' ),
                'prev'  => '@offer_entry_guide_title',
                'next'  => '@offer_entry_guide_pages',
                'edge'  => 'left',
                'align' => 'center'
            ),
            array(
                'id'    => 'offer_entry_guide_pages',
                'elem'  => '#sales-offer-pages',
                'html'  => __( '<h3>Sales Offer Pages</h3>
                                <p>Next you need to tell the plugin what page you want your offer to show on.</p>
                                <p>We\'ve already loaded the Cart page, but feel free to remove it or add extra pages where you think this sales offer belongs.</p>
                                <p>Even if you have it configured for a certain page, it will only show if the Offer conditions are met.</p>
                                <p>Hit next to hear more about Offer Conditions!</p>' , 'just-in-time-sales-offers' ),
                'prev'  => '@offer_entry_guide_content',
                'next'  => '@offer_entry_guide_conditions',
                'edge'  => 'left',
                'align' => 'center'
            ),
            array(
                'id'    => 'offer_entry_guide_conditions',
                'elem'  => '#sales-offer-conditions',
                'html'  => __( '<h3>Sales Offer Conditions</h3>
                                <p>Here\'s the exciting part! You get to decide under what conditions your Offer will show to customers.</p>
                                <p>There are no conditions set up yet, so it will show to everybody. To get started, click on Add Condition Group and then click on Add Condition to add your first condition.</p>
                                <p>Conditions can be grouped together to use the one Offer for multiple scenarios. It gets really powerful in the Premium add-on where you can add loads of different condition types.</p>' , 'just-in-time-sales-offers' ),
                'prev'  => '@offer_entry_guide_pages',
                'next'  => '@offer_entry_guide_accept_action',
                'edge'  => 'left',
                'align' => 'center'
            ),
            array(
                'id'    => 'offer_entry_guide_accept_action',
                'elem'  => '#accept-offer-actions',
                'html'  => __( '<h3>Accept Offer Actions</h3>
                                <p>If the customer accepts your Offer, you can have the Offer automatically apply Products and Coupons to the cart for them.</p>
                                <p>Likewise, you can remove certain Products and Coupons should they not be included in the Offer.</p>
                                <p>This will make the whole Offer process seem completely smooth and painless from the customer\'s perspective. They don\'t have to lift a finger!</p>' , 'just-in-time-sales-offers' ),
                'prev'  => '@offer_entry_guide_conditions',
                'next'  => '@offer_entry_guide_decline_action',
                'edge'  => 'left',
                'align' => 'center'
            )
        );

        if ( in_array( 'just-in-time-sales-offers-premium/just-in-time-sales-offers-premium.php' , apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

            $tours_array[] = array(
                'id'    => 'offer_entry_guide_decline_action',
                'elem'  => '#decline-offer-actions',
                'html'  => __( '<h3>Decline Offer Actions</h3>
                                <p>If the customer declines your Offer, you can also have the plugin perform certain actions.</p>
                                <p>In the Premium version, you can redirect them to a specific page or product or even show them another Offer instead.</p>' , 'just-in-time-sales-offers' ),
                'prev'  => '@offer_entry_guide_accept_action',
                'next'  => null,
                'edge'  => 'left',
                'align' => 'center'
            );

        } else {

            $tours_array[] = array(
                'id'    => 'offer_entry_guide_decline_action',
                'elem'  => '#decline-offer-actions',
                'html'  => __( '<h3>Decline Offer Actions</h3>
                                <p>If the customer declines your Offer, you can also have the plugin perform certain actions.</p>
                                <p>In the Premium version, you can redirect them to a specific page or product or even show them another Offer instead.</p>' , 'just-in-time-sales-offers' ),
                'prev'  => '@offer_entry_guide_accept_action',
                'next'  => '@offer_entry_guide_plugin_upgrade',
                'edge'  => 'left',
                'align' => 'center'
            );

            $tours_array[] = array(
                'id'    => 'offer_entry_guide_plugin_upgrade',
                'elem'  => '#just-in-time-sales-offers-upgrade',
                'html'  => sprintf( __( '<h3>Premium Upsell</h3>
                                <p>This concludes the guide. You are now ready to setup your first offer!</p>
                                <p>Want to unlock all of the features you see here? The Premium add-on will unlock all this and more and we\'re adding new features all the time!</p>
                                <p><a href="%1$s" target="_blank">Check out the Premium version now &rarr;</a></p>' , 'just-in-time-sales-offers' ) , 'https://marketingsuiteplugin.com/product/just-in-time-sales-offers/?utm_source=JITSO&utm_medium=Settings%20Banner&utm_campaign=JITSO' ),
                'prev'  => '@offer_entry_guide_decline_action',
                'next'  => null,
                'edge'  => 'right',
                'align' => 'center'
            );

        }
        
        $this->screens = apply_filters( 'jitso_offer_entry_guided_tours' , array( 'jit_sales_offer' => $tours_array ) );
        
    }

    /**
     * Get the only instance of the class.
     *
     * @since 1.1.2
     * @access public
     *
     * @return JITSO_Offer_Entry_Guided_Tour
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

        if ( get_option( self::OPTION_OFFER_ENTRY_GUIDED_TOUR_STATUS ) === false )
            update_option( self::OPTION_OFFER_ENTRY_GUIDED_TOUR_STATUS , self::STATUS_OPEN );

    }

    /**
     * Terminate guided tour options.
     *
     * @since 1.1.2
     * @access public
     */
    public function terminate_guided_tour_options() {

        delete_option( self::OPTION_OFFER_ENTRY_GUIDED_TOUR_STATUS );

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
     * Close offer entry guided tour.
     *
     * @since 1.1.2
     * @access public
     */
    public function jitso_close_offer_entry_guided_tour() {

        if ( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX ) {

            if ( !check_ajax_referer( 'jitso-close-offer-entry-guided-tour' , 'nonce' , false ) )
                wp_die( __( 'Security Check Failed' , 'just-in-time-sales-offers' ) );

            update_option( self::OPTION_OFFER_ENTRY_GUIDED_TOUR_STATUS , self::STATUS_CLOSE );

            wp_send_json_success();

        } else
            wp_die( __( 'Invalid AJAX Call' , 'just-in-time-sales-offers' ) );

    }

} // end class
