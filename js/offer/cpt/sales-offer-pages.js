/* global jQuery */
jQuery( document ).ready( function( $ ) {

    /*
     |--------------------------------------------------------------------------
     | Variables
     |--------------------------------------------------------------------------
     */

    var $sales_offer_pages_meta_box = $( "#sales-offer-pages-meta-box" ),
        $sales_offer_pages_controls = $sales_offer_pages_meta_box.find( "#sales-offer-pages-controls" ),
        $page_type_field            = $sales_offer_pages_controls.find( "#sales-offer-page-type" ),
        $offer_page_field           = $sales_offer_pages_controls.find( "#sales-offer-page" ),
        $command_button_controls    = $sales_offer_pages_controls.find( ".command-button-controls" ),
        $add_offer_page_btn         = $command_button_controls.find( "#add-sales-offer-page-btn" ),
        $edit_offer_page_btn        = $command_button_controls.find( "#edit-sales-offer-page-btn" ),
        $cancel_edit_offer_page_btn = $command_button_controls.find( "#cancel-edit-sales-offer-page-btn" ),
        $button_controls_spinner    = $command_button_controls.find( ".spinner" ),
        $sales_offer_pages_table    = $sales_offer_pages_meta_box.find( "#sales-offer-pages-table" ),
        offer_id                    = $sales_offer_pages_meta_box.find( ".meta .offer-id" ).text();




    /*
     |--------------------------------------------------------------------------
     | Load Sales Offer Page Depending On The Page Type Selected
     |--------------------------------------------------------------------------
     */

    $page_type_field.on( "construct_offer_pages_ajax_options" , function( event , ajax_options ) {

        event.stopPropagation();

        $page_type_field.removeClass( "err" );
        $offer_page_field.removeClass( "err" );

        if ( $page_type_field.val() == 'page' ) {

            var args = {
                return_format    : "select_option",
                add_empty_option : true
            };

            ajax_options[ "url" ]      = ajaxurl;
            ajax_options[ "type" ]     = "POST";
            ajax_options[ "data" ]     = { action : "jitso_get_site_pages" , args : args , 'ajax-nonce' : sales_offer_pages_params.nonce_get_site_pages };
            ajax_options[ "dataType" ] = "json";

        } else if ( $page_type_field.val() == '' ) {

            $offer_page_field.html( "" );
            $offer_page_field.css( "display" , "none" );

        }

        return $( this );

    } );

    $page_type_field.on( "execute_offer_pages_ajax_call" , function( event , ajax_promise , id_value ) {

        event.stopPropagation();

		$command_button_controls.trigger( "disabled_mode" );
		$page_type_field.attr( "disabled" , "disabled" );
		$offer_page_field.css( "display" , "none" );
		$sales_offer_pages_controls.find( "#sales-offer-page-container .spinner" ).css( "visibility" , "visible" );

		ajax_promise
			.done( function( data , text_status , jqxhr ) {

				if ( data.status == 'success' ) {

					$offer_page_field.html( data.site_pages );
					$offer_page_field.css( "display" , "inline-block" );

					// Check if there is a default value the page field should use
					if ( id_value )
						$offer_page_field.val( id_value );

				} else {

					$offer_page_field.html( "" );
					vex.dialog.alert( data.error_message );

				}

			} )
			.fail( function( jqxhr , text_status , error_thrown ) {

				console.log( jqxhr );
				vex.dialog.alert( sales_offer_pages_params.i18n_get_site_pages_failed );
				$offer_page_field.html( "" );

			} ).always( function() {

				$command_button_controls.trigger( "enabled_mode" );
				$page_type_field.removeAttr( 'disabled' );
				$sales_offer_pages_controls.find( ".spinner" ).css( "visibility" , "hidden" );

			} );

        return $( this );

    } );

    $page_type_field.on( "change" , function( event , id_value ) {

        event.stopPropagation();

        var ajax_options     = {};

        $page_type_field.trigger( "construct_offer_pages_ajax_options" , [ ajax_options ] );

        if ( !$.isEmptyObject( ajax_options ) )
            $page_type_field.trigger( "execute_offer_pages_ajax_call" , [ $.ajax( ajax_options ) , id_value ] );

        return $( this );

    } )

    $offer_page_field.change( function() {

        var $this = $( this );

        $this.removeClass( "err" );

    } );




    /*
     |--------------------------------------------------------------------------
     | Add Sales Offer Page
     |--------------------------------------------------------------------------
     */

    $add_offer_page_btn.on( "construct_new_offer_page_data" , function( event , page_data , errors ) {

        event.stopPropagation();

        // For custom indexed type pages ( Page that will have "type_id" format of index on the sales offer cpt post meta )
        if ( $page_type_field.is( ":visible" ) && $offer_page_field.is( ":visible" ) ) {

            $page_type_field.removeClass( "err" );
            $offer_page_field.removeClass( "err" );

            var type = $.trim( $page_type_field.val() ),
                id   = $.trim( $offer_page_field.val() );

            if ( type == "" ) {

                errors.push( sales_offer_pages_params.i18n_page_type_field_empty );
                $page_type_field.addClass( "err" );

            } else
                page_data[ 'type' ] = type;

            if ( id == "" ) {

                errors.push( sales_offer_pages_params.i18n_page_field_empty );
                $offer_page_field.addClass( "err" );

            } else
                page_data[ 'id' ] = id;

            // Construct additional data for this new offer page if there are any
            $add_offer_page_btn.trigger( "construct_new_offer_page_additional_data" , [ page_data , errors ] );

        }

        return $( this );

    } );

    $add_offer_page_btn.on( "add_new_offer_page" , function( event , ajax_options , page_data ) {

        event.stopPropagation();

        // For custom indexed type pages ( Page that will have "type_id" format of index on the sales offer cpt post meta )
        if ( page_data.hasOwnProperty( "type" ) && page_data.hasOwnProperty( "id" ) ) {

            $add_offer_page_btn.trigger( "adding_new_offer_page" , [ page_data ] );

            $page_type_field.attr( "disabled" , "disabled" );
            $offer_page_field.attr( "disabled" , "disabled" );

            $add_offer_page_btn.attr( "disabled" , "disabled" );
            $button_controls_spinner.css( "visibility" , "visible" );

            $.ajax( ajax_options )
            .done( function( data , text_status , jqxhr ) {

                if ( data.status == 'success' ) {

                    if ( $sales_offer_pages_table.find( "tr.no-items" ).length > 0 )
                        $sales_offer_pages_table.find( "tr.no-items").remove();

                    $sales_offer_pages_table.find( "tbody" ).append( data.new_row_markup );

                } else
                    vex.dialog.alert( data.error_message );

            } )
            .fail( function( jqxhr , text_status , error_thrown ) {

                console.log( jqxhr );

                vex.dialog.alert( sales_offer_pages_params.i18n_add_offer_page_failed );

            } )
            .always( function() {

                $add_offer_page_btn.trigger( "done_adding_new_offer_page" , [ page_data ] );

                $page_type_field.removeAttr( "disabled" );
                $offer_page_field.removeAttr( "disabled" );

                $add_offer_page_btn.removeAttr( "disabled" );
                $button_controls_spinner.css( "visibility" , "hidden" );

            } );

        }

        return $( this );

    } );

    $add_offer_page_btn.click( function() {

        var page_data = {},
            errors    = [];

        // TODO: Note there could be more fields to get data from here depending on the type of the page selected and the page selected

        $add_offer_page_btn.trigger( "construct_new_offer_page_data" , [ page_data , errors ] );

        if ( errors.length <= 0 ) {

            var ajax_options = {
                url      : ajaxurl,
                type     : "POST",
                data     : { action : "jitso_add_sales_offer_page" , offer_id : offer_id , page_data : page_data , 'ajax-nonce' : sales_offer_pages_params.nonce_add_sales_offer_page },
                dataType : "json"
            };

            $add_offer_page_btn.trigger( "add_new_offer_page" , [ ajax_options , page_data ] );

        } else {

            var err_msg = sales_offer_pages_params.i18n_form_have_errors + "<br/>";

            for ( var i = 0 ; i < errors.length ; i++ )
                err_msg += errors[ i ] + "<br/>";

            vex.dialog.alert( err_msg );

        }

    } );




    /*
     |--------------------------------------------------------------------------
     | Edit Sales Offer Page
     |--------------------------------------------------------------------------
     */

    $command_button_controls.on( "edit_mode" , function( event ) {

        event.stopPropagation();

        $( this ).addClass( "editing_mode" );

        return $( this );

    } );

    $command_button_controls.on( "add_mode" , function( event ) {

        event.stopPropagation();

        $( this )
            .removeClass( "editing_mode" )
            .find( ".button" ).removeAttr( "disabled" );

        return $( this );

    } );

    $command_button_controls.on( "disabled_mode" , function( event ) {

        event.stopPropagation();

        $( this ).find( ".button" ).attr( "disabled" , "disabled" );

        return $( this );

    } );

    $command_button_controls.on( "enabled_mode" , function( event ) {

        event.stopPropagation();

        $( this ).find( ".button" ).removeAttr( "disabled" );

        return $( this );

    } );

    $sales_offer_pages_table.on( "edit_mode" , function( event , $tr ) {

        event.stopPropagation();

        $( this ).find( "tbody tr td.controls .dashicons" ).css( "visibility" , "hidden" );
        $tr.addClass( "editing_mode" );

        return $( this );

    } );

    $sales_offer_pages_table.on( "normal_mode" , function( event ) {

        event.stopPropagation();

        $( this ).find( "tbody tr td.controls .dashicons" ).css( "visibility" , "visible" );
        $( this ).find( "tbody tr").removeClass( "editing_mode" );

        return $( this );

    } );

    $sales_offer_pages_table.on( "construct_to_be_edited_offer_page_data" , function( event , $tr , page_data_to_edit ) {

        event.stopPropagation();

        // For custom indexed type pages ( Page that will have "type_id" format of index on the sales offer cpt post meta )
        if ( $tr.find( ".meta .type" ).length > 0 && $tr.find( ".meta .id" ).length > 0 ) {

            page_data_to_edit[ 'key' ]  = $.trim( $tr.find( ".meta .key" ).text() );
            page_data_to_edit[ 'type' ] = $.trim( $tr.find( ".meta .type" ).text() );
            page_data_to_edit[ 'id' ]   = $.trim( $tr.find( ".meta .id" ).text() );

        }

        return $( this );

    } );

    $sales_offer_pages_controls.on( "prepopulate_offer_page_controls" , function( event , page_data_to_edit ) {

        event.stopPropagation();

        $sales_offer_pages_meta_box.find( ".edit-meta .key" ).text( page_data_to_edit.key );

        // For custom indexed type pages ( Page that will have "type_id" format of index on the sales offer cpt post meta )
        if ( page_data_to_edit.hasOwnProperty( 'type' ) && page_data_to_edit.hasOwnProperty( 'id' ) ) {

            $page_type_field.val( page_data_to_edit.type );
            $page_type_field.trigger( "change" , [ page_data_to_edit.id ] );

        }

        return $( this );

    } );

    $sales_offer_pages_table.delegate( ".edit_offer_page" , "click" , function() {

        var $this             = $( this ),
            $tr               = $this.closest( 'tr' ),
            page_data_to_edit = {};

        // Construct data of offer page to edit
        $sales_offer_pages_table.trigger( "construct_to_be_edited_offer_page_data" , [ $tr , page_data_to_edit ] );

        // Prepopulate forms
        $sales_offer_pages_controls.trigger( "prepopulate_offer_page_controls" , [ page_data_to_edit ] );

        // Set table in edit mode
        $sales_offer_pages_table.trigger( "edit_mode" , [ $tr ] );

        // Set controls in edit mode
        $command_button_controls.trigger( "edit_mode" );

    } );

    $sales_offer_pages_controls.on( "reset_fields" , function( event ) {

        event.stopPropagation();

        $sales_offer_pages_meta_box.find( "edit-meta .key" ).text( "" );
        $page_type_field.val( "" ).trigger( "change" );

        return $( this );

    } );

    $cancel_edit_offer_page_btn.click( function( event ) {

        event.stopPropagation();

        // Reset form fields
        $sales_offer_pages_controls.trigger( "reset_fields" );

        // Set button controls to normal mode
        $command_button_controls.trigger( "add_mode" );

        // Set sales offer pages table to normal mode
        $sales_offer_pages_table.trigger( "normal_mode" );

        return $( this );

    } );

    $edit_offer_page_btn.on( "construct_edited_offer_page_data" , function( event , page_data , errors ) {

        event.stopPropagation();

        // For custom indexed type pages ( Page that will have "type_id" format of index on the sales offer cpt post meta )
        if ( $page_type_field.is( ":visible" ) && $offer_page_field.is( ":visible" ) ) {

            $page_type_field.removeClass( "err" );
            $offer_page_field.removeClass( "err" );

            var type = $.trim( $page_type_field.val() ),
                id   = $.trim( $offer_page_field.val() );

            if ( type == "" ) {

                errors.push( sales_offer_pages_params.i18n_page_type_field_empty );
                $page_type_field.addClass( "err" );

            } else
                page_data[ 'type' ] = type;

            if ( id == "" ) {

                errors.push( sales_offer_pages_params.i18n_page_field_empty );
                $offer_page_field.addClass( "err" );

            } else
                page_data[ 'id' ] = id;

            // Construct additional data for this new offer page if there are any
            $add_offer_page_btn.trigger( "construct_edited_offer_page_additional_data" , [ page_data , errors ] );

        }

        return $( this );

    } );

    $edit_offer_page_btn.on( "edit_existing_offer_page" , function( event , ajax_options , key , page_data ) {

        event.stopPropagation();

        // For custom indexed type pages ( Page that will have "type_id" format of index on the sales offer cpt post meta )
        if ( page_data.hasOwnProperty( "type" ) && page_data.hasOwnProperty( "id" ) ) {

            $add_offer_page_btn.trigger( "editing_existing_offer_page" , [ key , page_data ] );

            $page_type_field.attr( "disabled" , "disabled" );
            $offer_page_field.attr( "disabled" , "disabled" );

            $command_button_controls.trigger( "disabled_mode" );
            $button_controls_spinner.css( "visibility" , "visible" );

            $.ajax( ajax_options )
            .done( function( data , text_status , jqxhr ) {

                if ( data.status == 'success' ) {

                    var $tr = $sales_offer_pages_table.find( "tr td.meta .key:contains('" + key + "')" ).closest( "tr" );

                    $tr
                        .find( ".meta" )
                            .find( ".key" ).text( data.new_key ).end()
                            .find( ".type" ).text( page_data.type ).end()
                            .find( ".id" ).text( page_data.id ).end().end()
                        .find( ".type_text" ).text( $page_type_field.find( "option[value='" + page_data.type + "']" ).text() ).end()
                        .find( ".id_text" ).text( $offer_page_field.find( "option[value='" + page_data[ 'id' ] + "']" ).text() );

                    // For extensibility.
                    $sales_offer_pages_table.trigger( "edit_row_data" , [ $tr , page_data , data ] );

                } else
                    vex.dialog.alert( data.error_message );

            } )
            .fail( function( jqxhr , text_status , error_thrown ) {

                console.log( jqxhr );

                vex.dialog.alert( sales_offer_pages_params.i18n_edit_offer_page_failed );

            } )
            .always( function() {

                $add_offer_page_btn.trigger( "done_editing_existing_offer_page" , [ page_data ] );

                $page_type_field.removeAttr( "disabled" );
                $offer_page_field.removeAttr( "disabled" );

                $command_button_controls.trigger( "enabled_mode" );
                $button_controls_spinner.css( "visibility" , "hidden" );

                $command_button_controls.trigger( "add_mode" );
                $sales_offer_pages_table.trigger( "normal_mode" );

                $sales_offer_pages_controls.trigger( "reset_fields" );

            } );

        }

        return $( this );

    } )

    $edit_offer_page_btn.click( function() {

        var key       = $.trim( $sales_offer_pages_meta_box.find( ".edit-meta .key" ).text() ),
            page_data = {},
            errors    = [];

        $edit_offer_page_btn.trigger( "construct_edited_offer_page_data" , [ page_data , errors ] );

        if ( errors.length <= 0 ) {

            var ajax_options = {
                url      : ajaxurl,
                type     : "POST",
                data     : { action : "jitso_edit_sales_offer_page" , offer_id : offer_id , key : key , page_data : page_data , 'ajax-nonce' : sales_offer_pages_params.nonce_edit_sales_offer_page },
                dataType : "json"
            };

            $edit_offer_page_btn.trigger( "edit_existing_offer_page" , [ ajax_options , key , page_data ] );

        } else {

            var err_msg = sales_offer_pages_params.i18n_form_have_errors + "<br/>";

            for ( var i = 0 ; i < errors.length ; i++ )
                err_msg += errors[ i ] + "<br/>";

            vex.dialog.alert( err_msg );

        }

    } );




    /*
     |--------------------------------------------------------------------------
     | Delete Sales Offer Page
     |--------------------------------------------------------------------------
     */

    $sales_offer_pages_table.delegate( ".delete_offer_page" , "click" , function() {

        var $this = $( this ),
            $tr   = $this.closest( 'tr' ),
            key   = $.trim( $tr.find( ".meta .key" ).text() );

        vex.dialog.confirm({
            message  : sales_offer_pages_params.i18n_confirm_delete_offer_page ,
            callback : function( value ) {

                if ( value ) {

                    $sales_offer_pages_table.trigger( "edit_mode" , [ $tr ] );
                    $command_button_controls.trigger( "disabled_mode" );

                    $.ajax( {
                        url : ajaxurl,
                        type : "POST",
                        data : { action : "jitso_delete_sales_offer_page" , offer_id : offer_id , key : key , 'ajax-nonce' : sales_offer_pages_params.nonce_delete_sales_offer_page },
                        dataType : "json"
                    } )
                    .done( function( data , text_status , jqxhr ) {

                        if ( data.status == 'success' ) {

                            $tr.fadeOut( "fast" , function() {

                                $tr.remove();

                                if ( $sales_offer_pages_table.find( "tbody tr" ).length <= 0 ) {

                                    $sales_offer_pages_table.append( '<tr class="no-items">' +
                                                                        '<td class="colspanchange" colspan="' + sales_offer_pages_params.offer_pages_table_total_columns + '">' + sales_offer_pages_params.i18n_no_offers_page + '</td>' +
                                                                    '</tr>' );

                                }

                            } );

                        } else
                            vex.dialog.alert( data.error_message );

                    } )
                    .fail( function( jqxhr , text_status , error_thrown ) {

                        console.log( jqxhr );

                        vex.dialog.alert( sales_offer_pages_params.i18n_delete_offer_page_failed );

                    } )
                    .always( function() {

                        $sales_offer_pages_table.trigger( "normal_mode" );
                        $command_button_controls.trigger( "enabled_mode" );

                    } );

                }

            }

        });

    } );




    /*
     |--------------------------------------------------------------------------
     | Initialize
     |--------------------------------------------------------------------------
     */

    $page_type_field.val( "" ).trigger( "change" );
    $command_button_controls.trigger( "enabled_mode" );

    // Initialize Vex Library
    vex.defaultOptions.className = 'vex-theme-plain';

} );
