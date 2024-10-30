/** global jQuery */
jQuery( document ).ready( function( $ ) {

    $( "body" ).on( 'updated_cart_totals' , function() {

        window.location.reload();

    } );
    
} );