<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'JITSO_Settings' ) ) {

    /**
     * Class JITSO_Settings
     *
     * Integrate into WooCommerce settings page and initialize Just In Time Sales Offers settings page.
     * We do it in traditional way ( none singleton pattern ) for full compatibility with woocommerce
     * settings page integration requirements.
     *
     * @since 1.0.0
     */
    class JITSO_Settings extends WC_Settings_Page {

        /**
         * JITSO_Constants instance. Holds various constants this class uses.
         *
         * @since 1.0.0
         * @access private
         * @var JITSO_Constants
         */
        private $_plugin_constants;

        /**
         * JITSO_Settings constructor.
         *
         * @since 1.0.0
         * @access public
         */
        public function __construct() {

            $this->_plugin_constants = JITSO_Constants::instance(); // Not dependency injection but this is safe as JITSO_Constants class is already loaded.

            $this->id    = 'jitso_settings';
            $this->label = __( 'Just In Time Sales Offers' , 'just-in-time-sales-offers' );

            add_filter( 'woocommerce_settings_tabs_array' , array( $this, 'add_settings_page' ), 30 ); // 30 so it is after the API tab
            add_action( 'woocommerce_settings_' . $this->id , array( $this, 'output' ) );
            add_action( 'woocommerce_settings_save_' . $this->id , array( $this, 'save' ) );
            add_action( 'woocommerce_sections_' . $this->id , array( $this, 'output_sections' ) );

            // Custom settings fields
            add_action( 'woocommerce_admin_field_jitso_upgrade_banner_controls' , array( $this , 'render_jitso_upgrade_banner_controls' ) );
            add_action( 'woocommerce_admin_field_jitso_help_resources_controls' , array( $this , 'render_jitso_help_resources_controls' ) );

            do_action( 'jitso_settings_construct' );

        }

        /**
         * Get sections.
         *
         * @return array
         * @since 1.0.0
         */
        public function get_sections() {

            $sections = array(
                ''                              => __( 'General' , 'just-in-time-sales-offers' ),
                'jitso_setting_decline_section' => __( 'Decline' , 'just-in-time-sales-offers' ),
                'jitso_setting_help_section'    => __( 'Help' , 'just-in-time-sales-offers' )
            );

            return apply_filters( 'woocommerce_get_sections_' . $this->id , $sections );

        }

        /**
         * Output the settings.
         *
         * @since 1.0.0
         */
        public function output() {

            global $current_section;

            $settings = $this->get_settings( $current_section );
            WC_Admin_Settings::output_fields( $settings );

        }

        /**
         * Save settings.
         *
         * @since 1.0.0
         */
        public function save() {

            global $current_section;

            $settings = $this->get_settings( $current_section );

            do_action( 'jitso_before_save_settings' , $settings );

            WC_Admin_Settings::save_fields( $settings );

            do_action( 'jitso_after_save_settings' , $settings );

        }

        /**
         * Get settings array.
         *
         * @param string $current_section
         *
         * @return mixed
         * @since 1.0.0
         */
        public function get_settings( $current_section = '' ) {

            if ( $current_section == 'jitso_setting_help_section' ) {

                // Help Section Options
                $settings = apply_filters( 'jitso_setting_help_section_options' , $this->_get_help_section_options() );

            } elseif ( $current_section == 'jitso_setting_decline_section' ) {

                // Decline Section Options
                $settings = apply_filters( 'jitso_setting_decline_section_options' , $this->_get_decline_section_options() );

            } else {

                // General Section Options
                $settings = apply_filters( 'jitso_setting_general_section_options' , $this->_get_general_section_options() );

            }

            return apply_filters( 'woocommerce_get_settings_' . $this->id , $settings , $current_section );

        }




        /*
         |--------------------------------------------------------------------------------------------------------------
         | Section Settings
         |--------------------------------------------------------------------------------------------------------------
         */

        /**
         * Get general section options.
         *
         * @since 1.0.0
         * @access private
         *
         * @return array
         */
        private function _get_general_section_options() {

            return array(

                array(
                    'title' => __( 'General Options', 'just-in-time-sales-offers' ),
                    'type'  => 'title',
                    'desc'  => '',
                    'id'    => 'jitso_general_main_title'
                ),

                array(
                    'name'  =>  '',
                    'type'  =>  'jitso_upgrade_banner_controls',
                    'desc'  =>  '',
                    'id'    =>  'jitso_upgrade_banner',
                ),

                array(
                    'type' => 'sectionend',
                    'id'   => 'jitso_general_sectionend'
                )

            );

        }

        /**
         * Decline section options.
         *
         * @since 1.2.0
         * @access private
         *
         * @return array
         */
        private function _get_decline_section_options() {

            return array(

                array(
                    'title' => __( 'Decline Options' , 'just-in-time-sales-offers' ),
                    'type'  => 'title',
                    'desc'  => __( 'Options relating to declining offers.' , 'just-in-time-sales-offers' ),
                    'id'    => 'jitso_decline_main_title'
                ),

                array(
                    'title'             => __( 'Close button behaviour' , 'just-in-time-sales-offers' ),
                    'type'              => 'select',
                    'desc'              => __( 'Specify the behaviour of the close button of the offer popup on the frontend.' , 'just-in-time-sales-offers' ),
                    'id'                => $this->_plugin_constants->OPTION_OFFER_POPUP_CLOSE_BUTTON_BEHAVIOUR(),
					'class'             => 'chosen_select',
					'css'               => 'min-width:300px;',
					'custom_attributes'	=> array( 'data-placeholder' => __( 'Select behaviour...' , 'woocommerce-wholesale-lead-capture' ) ),
					'options'           =>  apply_filters( 'jitso_offer_popup_close_button_behaviours' , array(
                                                'show_but_decline_not_count' => __( "Show close but don't count as decline" , 'just-in-time-sales-offers' ),
                                                'show_and_decline_count'     => __( 'Show close and count as decline' , 'just-in-time-sales-offers' ),
                                                'hide_button'                => __( 'Hide the close button' , 'just-in-time-sales-offers' )
                                            ) ),
                    'default'           => 'show_but_decline_not_count',
                    'desc_tip'          =>  __( 'This setting defines what happens when the customer clicks the X close button in the top right hand corner of the popup. By default, clicking this button will close the popup but not record the offer as declined.', 'just-in-time-sales-offers' )
                ),

                array(
                    'type' => 'sectionend',
                    'id'   => 'jitso_declined_sectionend'
                )

            );

        }

        /**
         * Get help section options
         *
         * @since 1.0.0
         * @access private
         *
         * @return array
         */
        private function _get_help_section_options() {

            return array(

                array(
                    'title' => __( 'Help Options' , 'just-in-time-sales-offers' ),
                    'type'  => 'title',
                    'desc'  => '',
                    'id'    => 'jitso_help_main_title'
                ),

                array(
                    'name'  =>  '',
                    'type'  =>  'jitso_help_resources_controls',
                    'desc'  =>  '',
                    'id'    =>  'jitso_help_resources',
                ),

                array(
                    'title' => __( 'Clean up plugin options on un-installation' , 'just-in-time-sales-offers' ),
                    'type'  => 'checkbox',
                    'desc'  => __( 'If checked, removes all plugin options when this plugin is uninstalled.' , 'just-in-time-sales-offers' ),
                    'id'    => $this->_plugin_constants->OPTION_CLEANUP_PLUGIN_OPTIONS()
                ),

                array(
                    'type' => 'sectionend',
                    'id'   => 'jitso_help_sectionend'
                )

            );

        }




        /*
         |--------------------------------------------------------------------------------------------------------------
         | Custom Settings Fields
         |--------------------------------------------------------------------------------------------------------------
         */

        /**
         * Render help resources controls.
         *
         * @param $value
         *
         * @since 1.0.0
         */
        public function render_jitso_help_resources_controls( $value ) {
            ?>

            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for=""><?php _e( 'Knowledge Base' , 'just-in-time-sales-offers' ); ?></label>
                </th>
                <td class="forminp forminp-<?php echo sanitize_title( $value[ 'type' ] ); ?>">
                    <?php echo sprintf( __( 'Looking for documentation? Please see our growing <a href="%1$s" target="_blank">Knowledge Base</a>' , 'just-in-time-sales-offers' ) , "https://marketingsuiteplugin.com/knowledge-base/just-in-time-sales-offers/?utm_source=JITSO&utm_medium=Settings%20Help&utm_campaign=JITSO" ); ?>
                </td>
            </tr>

            <?php
        }

        /**
         * Render upgrade banner for JITSO.
         *
         * @param $value
         *
         * @since 1.0.0
         */
        public function render_jitso_upgrade_banner_controls( $value ) {
            ?>

            <tr valign="top">
                <th scope="row" class="titledesc">
                    <a style="outline: none; display: inline-block;" target="_blank" href="https://marketingsuiteplugin.com/product/just-in-time-sales-offers/?utm_source=JITSO&utm_medium=Settings%20Banner&utm_campaign=JITSO"><img style="outline: none;" src="<?php echo $this->_plugin_constants->IMAGES_ROOT_URL() . 'jitso-premium-upsell-settings.png'; ?>" alt="<?php _e( 'Just-In-Time Sales Offers Premium' , 'just-in-time-sales-offers' ); ?>"/></a>
                </th>
            </tr>

            <?php
        }

    }

}

return new JITSO_Settings();
