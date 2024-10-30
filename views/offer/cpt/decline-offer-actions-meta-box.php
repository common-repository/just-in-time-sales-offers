<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $post; ?>

<div id="decline-offer-actions-meta-box" class="jitso-meta-box">

    <div class="meta" style="display: none !important;">
        <span class="offer-id"><?php echo $post->ID; ?></span>
    </div>

    <h3><?php _e( 'If offer is declined close the popup & do the following: ' , 'just-in-time-sales-offers' ); ?></h3>

    <select id="decline-offer-action-types" autocomplete="off">
        <?php foreach ( $decline_offer_action_types as $key => $text ) {

            $disabled = "";
            $selected = "";

            if ( $decline_offer_action_types_simple_mode && $key != 'do-nothing' ) {

                $disabled  = 'disabled="disabled"';
                $text     .= ' (PREMIUM)';

            } elseif ( array_key_exists( $key , $decline_offer_action ) )
                $selected = 'selected="selected"'; ?>

            <option value="<?php echo $key; ?>" <?php echo $disabled; ?> <?php echo $selected; ?>><?php echo $text; ?></option>

        <?php } ?>
    </select>

    <div id="additional-decline-offer-action-type-options">
        <?php do_action( 'jitso_additional_decline_offer_action_type_options' , $decline_offer_action ); ?>
    </div>

    <div id="decline-offer-action-controls">

        <?php do_action( 'jitso_decline_offer_action_additional_controls' ); ?>

        <input type="button" id="save-decline-offer-actions-btn" class="button button-primary" value="<?php _e( 'Save Actions' , 'just-in-time-sales-offers' ); ?>">
        <span class="spinner"></span>

    </div><!--#decline-offer-action-controls-->

</div>