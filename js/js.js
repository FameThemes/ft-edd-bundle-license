jQuery( document ).ready( function( $ ){
   // $( 'body' ).hide();
   // $( '.edd-sl-adjust-limit' ).parent().remove();

    function set_activation_limit( number, license_id, $input ){
        $input.text( number );
        $.get( ajaxurl, { action: 'ft_edd_bundle_set_activation_limit', number: number, license_id: license_id }, function( res ){

        } );
    }

    $( 'td.column-limit' ).each( function () {
        var p = $( this ).closest('tr');
        var id = $( '.check-column input', p ).val( );
        if ( id ) {
            var input = $( '#edd-sl-'+id+'-limit' );
            input.addClass( 'edd-can-change' );
            input.click( function(e  )  {
                e.preventDefault();
                var n = prompt("Enter Your Activation Limit", input.text() );
                n = parseInt( n );
                if ( ! isNaN( n ) ) {
                    set_activation_limit( n, id, input );
                }
            });
        }
    } );


    $( '#edd-item-card-wrapper #edit-item-info' ).each( function(){
        var p = $( this );
        var id = $( 'input[name="license_id"]', p ).val();
        var input = $( '#edd-sl-'+id+'-limit' );
        input.addClass( 'edd-can-change' );
        input.click( function(e  )  {
            e.preventDefault();
            var n = prompt("Enter Your Activation Limit", input.text() );
            n = parseInt( n );
            if ( ! isNaN( n ) ) {
                set_activation_limit( n, id, input );
            }
        });

    } );

} );