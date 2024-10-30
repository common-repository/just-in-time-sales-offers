<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $post;
$disabled_condition_types = array(); ?>

<div id="sales-offer-conditions-meta-box" class="jitso-meta-box">

    <div class="meta" style="display: none !important;">
        <span class="offer-id"><?php echo $post->ID; ?></span>
    </div>

    <h3><?php _e( 'Show this sales offer if conditions below are met' , 'just-in-time-sales-offers' ); ?></h3>

    <div id="offer-conditions">

        <?php if ( !empty( $sales_offer_conditions ) ) {

            foreach ( $sales_offer_conditions as $condition_group ) {

                if ( isset( $condition_group[ 'condition-group-logic' ] ) ) { ?>

                    <div class="offer-condition-group-logic">

                        <div class="controls">

                            <select class="condition-group-logic" autocomplete="off">
                                <option value="and" <?php echo ( $condition_group[ 'condition-group-logic' ] == 'and' ) ? 'selected="selected"' : ''; ?>><?php _e( 'AND' , 'just-in-time-sales-offers' ); ?></option>
                                <option value="or" <?php echo ( $condition_group[ 'condition-group-logic' ] == 'or' ) ? 'selected="selected"' : ''; ?>><?php _e( 'OR' , 'just-in-time-sales-offers' ); ?></option>
                            </select>

                        </div>

                    </div>

                <?php } ?>

                <div class="offer-condition-group">

                    <div class="offer-condition-group-actions">
                        <?php do_action( 'jitso_offer_condition_group_additional_actions' ); ?>
                        <a class="remove-condition-group"><?php _e( 'Remove Condition Group' , 'just-in-time-sales-offers' ); ?></a>
                    </div>

                    <?php foreach ( $condition_group[ 'conditions' ] as $condition ) {

                        if ( $condition[ 'condition-type' ] == 'product-quantity' ) {

                            if ( isset( $condition[ 'condition-logic' ] ) ) { ?>

                                <div class="offer-condition-logic">

                                    <div class="controls">

                                        <select class="condition-logic" autocomplete="off">
                                            <option value="and" <?php echo ( $condition[ 'condition-logic' ] == 'and' ) ? 'selected="selected"' : ''; ?>><?php _e( 'AND' , 'just-in-time-sales-offers' ); ?></option>
                                            <option value="or" <?php echo ( $condition[ 'condition-logic' ] == 'or' ) ? 'selected="selected"' : ''; ?>><?php _e( 'OR' , 'just-in-time-sales-offers' ); ?></option>
                                        </select>

                                    </div>

                                </div>

                            <?php } ?>

                            <div class="offer-condition" data-condition-type="product-quantity">

                                <div class="offer-condition-actions">
                                    <?php do_action( 'jitso_offer_condition_additional_actions' ); ?>
                                    <a class="remove-condition"><?php _e( 'Remove Condition' , 'just-in-time-sales-offers' ); ?></a>
                                </div>

                                <div class="fields">

                                    <div class="field-set product-in-cart-field-set">
                                        <span class="meta" style="display: none !important;">
                                            <span class="product-type"></span>
                                        </span>

                                        <label><?php _e( 'Product In Cart' , 'just-in-time-sales-offers' ); ?></label>
                                        <select class="product-in-cart" style="min-width: 340px;" data-placeholder="<?php _e( 'Please select a product...' , 'just-in-time-sales-offers' ); ?>">
                                            <?php echo $all_products_select_options; ?>
                                        </select>
                                    </div>

                                    <div class="field-set product-in-cart-quantity-condition-field-set">
                                        <label><?php _e( 'Condition' , 'just-in-time-sales-offers' ); ?></label>
                                        <select class="product-in-cart-quantity-condition">
                                            <?php foreach ( $product_conditions as $condition_val => $condition_text ) { ?>
                                                <option value="<?php echo $condition_val; ?>"><?php echo $condition_text; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="field-set product-in-cart-quantity-field-set">
                                        <label><?php _e( 'Quantity' , 'just-in-time-sales-offers' ); ?></label>
                                        <input type="number" class="product-in-cart-quantity" value="1" min="0">
                                    </div>

                                    <?php do_action( 'jitso_product-quantity_in_cart_additional_controls' ); ?>

                                    <div class="field-set button-field-set">
                                        <input type="button" class="add-product-in-cart-btn button button-primary" value="<?php _e( 'Add' , 'just-in-time-sales-offers' ); ?>">
                                        <input type="button" class="edit-product-in-cart-btn button button-primary" value="<?php _e( 'Edit' , 'just-in-time-sales-offers' ); ?>">
                                        <input type="button" class="cancel-edit-product-in-cart-btn button button-secondary" value="<?php _e( 'Cancel' , 'just-in-time-sales-offers' ); ?>">
                                        <span class="spinner"></span>
                                    </div>

                                    <div style="clear: both; float: none; display: block;"></div>

                                </div>

                                <table class="product-quantity-table wp-list-table widefat fixed striped" cellspacing="0" width="100%">

                                    <thead>
                                        <tr>
                                            <th class="product-heading"><?php _e( 'Product' , 'just-in-time-sales-offers' ); ?></th>
                                            <th class="product-quantity-condition-heading"><?php _e( 'Condition' , 'just-in-time-sales-offers' ); ?></th>
                                            <th class="product-quantity-heading"><?php _e( 'Quantity' , 'just-in-time-sales-offers' ); ?></th>
                                            <?php do_action( 'jitso_product-quantity_in_cart_additional_column_heading_markup' ); ?>
                                            <th class="controls-heading"></th>
                                        </tr>
                                    </thead>

                                    <?php foreach ( $condition[ 'product-conditions' ] as $data ) { ?>

                                        <tr>
                                            <td class="row-meta hidden">

                                                <span class="product-type"><?php echo $data[ 'product-type' ]; ?></span>
                                                <span class="product-id"><?php echo $data[ 'product-id' ]; ?></span>

                                                <?php if ( isset( $data[ 'product-variation-id' ] ) ) { ?>
                                                    <span class="product-variation-id">

                                                        <?php foreach ( $data['product-variation-id' ] as $variation_id ) { ?>
                                                            <span class="variation-id"><?php echo $variation_id; ?></span>
                                                        <?php } ?>

                                                    </span>
                                                <?php } ?>

                                                <span class="product-quantity-condition"><?php echo $data[ 'product-quantity-condition' ]; ?></span>
                                                <span class="product-quantity"><?php echo $data[ 'product-quantity' ]; ?></span>
                                                <?php do_action( 'jitso_product-quantity_in_cart_additional_meta_markup' , $data ); ?>

                                            </td>

                                            <td class="product-text">

                                                <?php $product_text = "";

                                                if ( $data[ "product-type" ] == 'variable' ) {
                                                    // Variable Product

                                                    $product_text  = '<div class="product"><a href="' . home_url( "/wp-admin/post.php?post=" . $data[ 'product-id' ] . "&action=edit" ) . '" target="_blank">[ID : ' . $data[ 'product-id' ] . '] ' . get_the_title( $data[ 'product-id' ] ) . '</a></div>';

                                                    foreach ( $data[ "product-variation-id" ] as $variation_id ) {

                                                        if ( $variation_id == 'any' )
                                                            $product_text .= '<div class="product-variation">' . __( 'Any Variation' , 'just-in-time-sales-offers' ) . '</div>';
                                                        else {

                                                            $variation_info_arr = JITSO_Helper::get_product_variations( array(
                                                                'variable_id'  => $data[ 'product-id' ],
                                                                'variation_id' => $variation_id
                                                            ) );

                                                            $product_text .= '<div class="product-variation">' . $variation_info_arr[ 0 ][ 'text' ] . '</div>';

                                                        }

                                                    }


                                                } else if ( $data[ "product-type" ] == 'simple' )
                                                    $product_text =  '<div class="product"><a href="' . home_url( "/wp-admin/post.php?post=" . $data[ 'product-id' ] . "&action=edit" ) . '" target="_blank">[ID : ' . $data[ 'product-id' ] . '] ' . get_the_title( $data[ 'product-id' ] ) . '</a></div>';
                                                else
                                                    $product_text = apply_filters( 'jitso_' . $data[ "product-type" ] . '_product_quantity_in_cart_entry_text' , $product_text , $data );
                                                
                                                echo  $product_text; ?>

                                            </td>

                                            <td class="product-quantity-condition"><?php echo $product_conditions[ $data[ 'product-quantity-condition' ] ]; ?></td>
                                            <td class="product-quantity"><?php echo $data[ 'product-quantity' ]; ?></td>

                                            <?php do_action( 'jitso_product-quantity_in_cart_additional_column_data_markup' , $data ); ?>

                                            <td class="row-controls">
                                                <span class="dashicons dashicons-edit edit-product"></span>
                                                <span class="dashicons dashicons-no delete-product"></span>
                                            </td>
                                        </tr>

                                    <?php } ?>

                                </table>

                            </div>

                        <?php } else
                            do_action( 'jitso_render_' . $condition[ 'condition-type' ] . '_sales_offer_condition_markup' , $condition );
                            
                    } ?>

                    <div class="offer-condition-controls">

                        <div class="controls">

                            <?php do_action( 'jitso_offer_condition_group_additional_controls' ); ?>

                            <select class="condition-types">
                                <?php foreach ( $offer_condition_types as $key => $text ) {

                                    $disabled = '';
                                    if ( $offer_condition_types_simple_mode && $key != 'product-quantity' ) {

                                        $disabled = 'disabled="disabled"';
                                        $text .= ' (PREMIUM)';

                                    } ?>

                                    <option value="<?php echo $key; ?>" <?php echo $disabled; ?>><?php echo $text; ?></option>

                                <?php } ?>
                            </select>

                            <input type="button" class="show-add-condition-controls button button-secondary" value="<?php _e( 'Add Condition' , 'just-in-time-sales-offers' ); ?>">
                            <input type="button" class="add-condition button button-primary" value="<?php _e( 'Add' , 'just-in-time-sales-offers' ); ?>">
                            <input type="button" class="hide-add-condition-controls button button-secondary" value="<?php _e( 'Cancel' , 'just-in-time-sales-offers' ); ?>">

                            <span class="spinner"></span>

                        </div>

                    </div>

                </div>

            <?php }

        } else { ?>

            <div id="no-offer-condition-container">
                <p id="no-condition-message"><?php _e( 'No Conditions Set. Click <b>"Add Condition Group"</b> and <b>"Add Condition"</b> buttons to add some conditions.' , 'just-in-time-sales-offers' ); ?></p>
            </div>

        <?php } ?>

        <div class="offer-condition-group-controls">

            <div class="controls">

                <?php do_action( 'jitso_offer_condition_group_additional_controls' ); ?>
                <input type="button" class="add-condition-group button button-secondary" value="<?php _e( 'Add Condition Group' , 'just-in-time-sales-offers' ); ?>">
                <span class="spinner"></span>

            </div>

        </div>

    </div><!--#offer-conditions-->

    <div id="offer-condition-general-controls">

        <?php do_action( 'jitso_offer_condition_additional_general_controls' ); ?>
        <input type="button" id="save-offer-conditions" class="button button-primary" value="<?php _e( 'Save Conditions' , 'just-in-time-sales-offers' ); ?>">
        <span class="spinner"></span>

    </div><!--#offer-condition-general-controls-->

</div>