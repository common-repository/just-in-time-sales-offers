/* global jQuery */
jQuery( document ).ready( function( $ ) {

    /*
     |--------------------------------------------------------------------------
     | Initialize Offer Queue
     |--------------------------------------------------------------------------
     */

    // Offer Queue must be an object or else we will not be able to pass it by refferrence. Making it an array won't do either.
    var offer_queue            = { 'offer_id_arr' : [] },
        first_offer_time_delay = jitso_params.options[ 'time_delay' ], // Time delay before loading first offer ( in seconds ).
        close_button_behaviour = jitso_params.options[ 'close_button_behaviour' ];

    $( ".jit-sales-offer" ).each( function() {

        offer_queue.offer_id_arr.push( $( this ).attr( "id" ) ); // Offer element id, not the offer id in the db

    } );




    /*
     |--------------------------------------------------------------------------
     | Utility Functions
     |--------------------------------------------------------------------------
     */

    function load_jit_sales_offer( offer_id ) {

        var $popup_options = {
            items               : { src: '#' + offer_id },
            type                : 'inline',
            closeOnContentClick : false,
            closeOnBgClick      : false,
            enableEscapeKey     : false,
            showCloseBtn        : close_button_behaviour != 'hide_button' ? true : false,
            callbacks           : {
                                    open : function() {
                                        // Open callback function if different, we just can't override it coz if we do,
                                        // magnific popup instance might not be initialized yet as opposed to the close callback
                                        // where we are assured that an instance is already initiated.

                                        $( "body" ).trigger( "jit_sales_offer_open" , [ offer_id , $.magnificPopup.instance ] );

                                    }
                                }
        };

        $( 'body' ).trigger( 'initialize_jit_sales_offer_popup_options' , [ $popup_options , offer_id ] );

        $.magnificPopup.open( $popup_options );

    }




    /*
     |--------------------------------------------------------------------------
     | Maybe Load First Sales Offer
     |--------------------------------------------------------------------------
     */

    var current_offer_id = offer_queue.offer_id_arr.shift();

    if ( current_offer_id )
        setTimeout( function() { load_jit_sales_offer( current_offer_id ); } , first_offer_time_delay );




    /*
     |--------------------------------------------------------------------------
     | Open Offer
     |--------------------------------------------------------------------------
     */

    $( "body" ).on( "jit_sales_offer_open" , function( event , offer_id , $popup_instance ) {

        event.stopPropagation();

        offer_id = offer_id.replace( 'jit-sales-offer-' , '' );
        var offer_data = { 'current_url' : window.location.href };

        $( "body" ).trigger( 'jitso_record_offer_view_data' , [ offer_id , offer_data ] );

        $.ajax( {
            url      : jitso_params.ajaxurl,
            type     : "POST",
            data     : { action : "jitso_record_offer_view" , offer_id : offer_id , offer_data : offer_data , 'ajax-nonce' : jitso_params.nonce_record_offer_view },
            dataType : "json"
        } )
        .done( function( data , text_status , jqxhr ) { $( "body" ).trigger( 'jitso_record_offer_view_done' , [ offer_id , data , text_status , jqxhr ] ); } )
        .fail( function( jqxhr , text_status , error_thrown ) { $( "body" ).trigger( 'jitso_record_offer_view_fail' , [ offer_id , jqxhr , text_status , error_thrown ] ); } )
        .always( function() { $( "body" ).trigger( 'jitso_record_offer_view_always' , [ offer_id ] ); } );

        return $( this );

    } );




    /*
     |--------------------------------------------------------------------------
     | Close Offer
     |--------------------------------------------------------------------------
     */

    $( "body" ).on( "load_next_jit_sales_offer" , function( event , next_offer_id , offer_queue , $popup_instance ) {

        event.stopPropagation();

        // If next_offer_id is 'reload' or 'redirect' it means that don't load next offer coz we are going to reload/redirect this page.
        if ( next_offer_id !== 'reload' &&  next_offer_id !== 'redirect' ) {

            if ( next_offer_id ) {

                // If the next offer id that is to be shown is in the offer queue, remove it from the queue
                // to prevent from re-showing the offer later when we proceed on popping offer id from the queue
                if ( $.inArray( next_offer_id , offer_queue.offer_id_arr ) ) {

                    offer_queue.offer_id_arr = jQuery.grep( offer_queue.offer_id_arr , function( value ) {
                        return value != next_offer_id;
                    } );

                }

            } else
                next_offer_id = offer_queue.offer_id_arr.shift();

            if ( next_offer_id ) {

                /*
                 * Intentional 1.5 seconds delay of showing up the next offer.
                 * The reason is if we show it immediately, if user clicks the close button, it may also click the
                 * newly shown ( next popup ) offer's close button, so the next offer seems to be not loaded.
                 */
                setTimeout( function() { load_jit_sales_offer( next_offer_id ); } , 1500 );

            }

        }

        return $( this );

    } );

    // Override the close function of magnific popup
    $.magnificPopup.instance.close = function( next_offer_id ) {

        $( "body" ).trigger( "jit_sales_offer_close" , [ next_offer_id , $.magnificPopup.instance ] );
        $( "body" ).trigger( "load_next_jit_sales_offer" , [ next_offer_id , offer_queue , $.magnificPopup.instance ] );

        // You may call parent ("original") method like so:
        $.magnificPopup.proto.close.call( this /*, optional arguments */);

    };

    $( 'body' ).on( 'mousedown' , '.jit-sales-offer .mfp-close' , function( event ) {

        event.stopPropagation();

        if ( close_button_behaviour == 'show_and_decline_count' )
            jitso_decline_offer( $( ".jit-sales-offer #jitso-decline-offer" ) );

        return $( this );

    } );




    /*
     |--------------------------------------------------------------------------
     | Offer Actions
     |--------------------------------------------------------------------------
     */

    $( "body" ).on( "execute_offer_accept_action" , function( event , data ) {

        event.stopPropagation();

        if ( !data.has_actions_on_front_end ) {

            $.magnificPopup.instance.close( 'reload' );
            location.reload();

        }

        return $( this );

    } );

    $( "body" ).on( "execute_offer_decline_action" , function( event , data ) {

        event.stopPropagation();

        for ( var decline_action in data.decline_actions_executed ) {

            if ( data.decline_actions_executed.hasOwnProperty( decline_action ) ) {

                if ( decline_action == "do-nothing" )
                    $.magnificPopup.instance.close( false );

            }

        }

        return $( this );

    } );

    $( "body" ).on( "click" , ".jit-sales-offer #jitso-accept-offer" , function( event ) {

        var $this      = $( this ),
            offer_id   = $this.closest( ".jit-sales-offer" ).attr( "data-offer-id" ),
            offer_data = { 'current_url' : window.location.href };

        $( "body" ).trigger( 'jitso_record_offer_accept_data' , [ offer_id , offer_data ] );

        $this.attr( "disabled" , "disabled" );
        $this.siblings( "#jitso-decline-offer" ).attr( "disabled" , "disabled" );

        $.ajax( {
            url      : jitso_params.ajaxurl,
            type     : "POST",
            data     : { action : "jitso_execute_accept_offer_actions" , offer_id : offer_id , offer_data : offer_data , 'ajax-nonce' : jitso_params.nonce_execute_accept_offer_actions },
            dataType : "json"
        } )
        .done( function( data , text_status , jqxhr ) {

            if ( data.status == 'success' ) {

                $( "body" ).trigger( "execute_offer_accept_action" , [ data ] );

            } else {

                console.log( data );
                vex.dialog.alert( data.error_message );

            }

        } )
        .fail( function( jqxhr , text_status , error_thrown ) {

            console.log( jqxhr );
            vex.dialog.alert( jitso_params.i18n_failed_avail_offer );

        } )
        .always( function() {

            $this.removeAttr( "disabled" );
            $this.siblings( "#jitso-decline-offer" ).removeAttr( "disabled" );

        } );

    } );

    function jitso_decline_offer( $this ) {

        var offer_id   = $this.closest( ".jit-sales-offer" ).attr( "data-offer-id" ),
            offer_data = { 'current_url' : window.location.href };

        $( "body" ).trigger( 'jitso_record_offer_decline_data' , [ offer_id , offer_data ] );

        $this.attr( "disabled" , "disabled" );
        $this.siblings( "#jitso-accept-offer" ).attr( "disabled" , "disabled" );

        $.ajax( {
            url      : jitso_params.ajaxurl,
            type     : "POST",
            data     : { action : "jitso_execute_decline_offer_actions" , offer_id : offer_id , offer_data : offer_data , 'ajax-nonce' : jitso_params.nonce_execute_decline_offer_actions },
            dataType : "json"
        } )
        .done( function( data , text_status , jqxhr ) {

            if ( data.status == 'success' ) {

                $( "body" ).trigger( "execute_offer_decline_action" , [ data ] );

            } else {

                console.log( data );
                vex.dialog.alert( data.error_message );

            }

        } )
        .fail( function( jqxhr , text_status , error_thrown ) {

            console.log( jqxhr );
            vex.dialog.alert( jitso_params.i18n_failed_execute_decline_offer_action );

        } )
        .always( function() {

            $this.removeAttr( "disabled" );
            $this.siblings( "#jitso-accept-offer" ).removeAttr( "disabled" );

        } );

    }

    $( "body" ).on( "click" , ".jit-sales-offer #jitso-decline-offer" , function( event ) {

        var $this = $( this );

        jitso_decline_offer( $this );

    } );




    /*
     |--------------------------------------------------------------------------
     | Initialization
     |--------------------------------------------------------------------------
     */

    // Initialize Vex Library
    vex.defaultOptions.className = 'vex-theme-plain';

} );
