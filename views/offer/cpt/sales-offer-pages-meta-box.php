<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $post; ?>

<div id="sales-offer-pages-meta-box" class="jitso-meta-box">

    <div class="edit-meta" style="display: none !important">
        <span class="key"></span>
    </div>

    <div class="meta" style="display: none !important;">
        <span class="offer-id"><?php echo $post->ID; ?></span>
    </div>

    <h3><?php _e( 'Show this sales offer on' , 'just-in-time-sales-offers' ); ?></h3>

    <div id="sales-offer-pages-controls">

        <select id="sales-offer-page-type">

            <option value=""><?php _e( '--Select Type--' , 'just-in-time-sales-offers' ); ?></option>

            <?php foreach ( $sales_offer_pages_types as $key => $text ) {

                $disabled = '';
                if ( $sales_offer_pages_types_simple_mode && $key != 'page' )
                    $disabled = 'disabled="disabled"'; ?>

                <option value="<?php echo $key; ?>" <?php echo $disabled; ?>><?php echo $text; ?></option>

            <?php } ?>

        </select>

        <div id="sales-offer-page-container">
            <select id="sales-offer-page"></select>
            <span class="spinner"></span>
        </div>

        <?php do_action( 'jitso_sales_offer_pages_additional_controls' ); ?>

        <div class="command-button-controls">
            <input type="button" id="cancel-edit-sales-offer-page-btn" class="button button-secondary" value="<?php _e( 'Cancel' , 'just-in-time-sales-offers' ); ?>">
            <input type="button" id="edit-sales-offer-page-btn" class="button button-primary" value="<?php _e( 'Update' , 'just-in-time-sales-offers' ); ?>">
            <input type="button" id="add-sales-offer-page-btn" class="button button-primary" value="<?php _e( 'Add' , 'just-in-time-sales-offers' ); ?>">
            <span class="spinner"></span>
        </div>

    </div>

    <table id="sales-offer-pages-table" class="wp-list-table widefat fixed striped posts">
        <thead>
            <tr>
                <th class="page_type"><?php _e( 'Type' , 'just-in-time-sales-offers' ); ?></th>
                <th class="page"><?php _e( 'Page' , 'just-in-time-sales-offers' ); ?></th>
                <?php do_action( "jitso_offer_page_table_additional_heading" ); ?>
                <th class="page_controls"></th>
            </tr>
        </thead>
        <tbody class="the-list">

            <?php if ( !empty( $sales_offer_pages ) ) {

                foreach ( $sales_offer_pages as $key => $offer_page ) {

                    // Check if indexed page ( Page that will have type_id index on the sales offer cpt post meta )
                    if ( array_key_exists( 'type' , $offer_page ) && array_key_exists( 'id' , $offer_page ) ) {
                        
                        if ( $sales_offer_pages_types_simple_mode && $offer_page[ 'type' ] != 'page' )
                            continue;
                        
                        $id_text = JITSO_Helper::get_id_title( $offer_page[ 'type' ] , $offer_page[ 'id' ] ); ?>

                        <tr>
                            <td class="meta hidden">
                                <span class="key"><?php echo $key; ?></span>
                                <span class="type"><?php echo $offer_page[ 'type' ]; ?></span>
                                <span class="id"><?php echo $offer_page[ 'id' ]; ?></span>
                                <?php do_action( "jitso_offer_page_table_additional_column_meta" ); ?>
                            </td>
                            <td class="type_text"><?php echo $sales_offer_pages_types[ $offer_page[ 'type' ] ]; ?></td>
                            <td class="id_text"><?php echo $id_text; ?></td>

                            <?php do_action( "jitso_offer_page_table_additional_column" ); ?>

                            <td class="controls">
                                <span class="dashicons dashicons-edit edit_offer_page"></span>
                                <span class="dashicons dashicons-no delete_offer_page"></span>
                            </td>
                        </tr>

                    <?php } else
                        do_action( 'jitso_render_sales_offer_page' , $key , $offer_page );

                }

            } else { ?>

                <tr class="no-items">
                    <td class="colspanchange" colspan="<?php echo $offer_pages_table_total_columns; ?>"><?php _e( 'This offer is currently not showing on any pages. Add one above to start showing your offer!' , 'just-in-time-sales-offers' ); ?></td>
                </tr>

            <?php } ?>

        </tbody>
    </table>

</div><!--#sales-offer-pages-meta-box-->
