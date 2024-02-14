jQuery(function( $ ) {
    
    $( "form.checkout" ).on( "change", ".persons select", function( e ) {
        var npersons = $( '.persons select' ).val();

        var data = {
            action: 'update_order_review',
            security: wc_checkout_params.update_order_review_nonce,
            post_data: $( '.persons select' ).val()
        };

        jQuery.post( add_quantity.ajax_url, data, function( response ) {
            /*$( '.custom_checkout_fields_wrapper' ).html( response['fragments']['div.custom_checkout_field']);*/
            $( '.custom_checkout_fields_wrapper' ).html( response);

            $( 'body' ).trigger( 'update_checkout' );
            //$('.custom_checkout_fields_wrapper .standard-var select').addClass('disabled');
            $('.custom_checkout_fields_wrapper').find('.custom-d').not('.standard-var').find('select').select2({
                minimumResultsForSearch: 7,
                dropdownAutoWidth: true
            });

            //Add persons
            //$('.custom_checkout_fields_wrapper').find('.custom-d').not('.standard-var').find('select').val( $('.custom_checkout_fields_wrapper').find('.custom-d').not('.standard-var').find('select option:first').val()).trigger('change');
            
            //Add-Remove persons
            /*var arr = $('.custom-var-person select').map(function(){
                return this.value
            }).get();
            var data = {
                action: 'update_order_review_cart',
                security: wc_checkout_params.update_order_review_nonce,
                post_data: arr
            };
    
            jQuery.post( add_quantity.ajax_url, data, function( response ) {
                $( 'body' ).trigger( 'update_checkout' );
                //Extra Button
                if ($('.custom_checkout_continue').css('display') == 'none') {
                    $('.custom_checkout_field').addClass('show');
                }
            });*/

             var data = {
                action: 'update_order_test',
                security: wc_checkout_params.update_order_test_nonce,
                post_data: npersons
            };

            jQuery.post( add_quantity.ajax_url, data, function( response ) {
                $( 'body' ).trigger( 'update_checkout' );
                //Extra Button
                if ($('.custom_checkout_continue').css('display') == 'none') {
                    $('.custom_checkout_field').addClass('show');
                }
            });
        });
    });

    $(".persons select").val($(".persons select option:first").val()).trigger('change');

    //
    /*$( "form.checkout" ).on( "change", ".custom-var-person select", function( e ) {
        var arr = $('.custom-var-person select').map(function(){
            return this.value
        }).get();

        var data = {
            action: 'update_order_review_cart',
            security: wc_checkout_params.update_order_review_nonce,
            post_data: arr
        };

        jQuery.post( add_quantity.ajax_url, data, function( response ) {
            $( 'body' ).trigger( 'update_checkout' );
        });
    });*/

    //
    $( "form.checkout" ).on( "change", ".custom-datebirth", function( e ) {
        let datebirth = $(this).find('input').val();
        let datebirthAll = datebirth.split("-");
        let year = 0;

        if (datebirthAll.length == 1) {
            datebirthAll[0] = "1980";
            datebirthAll[1] = "01";
            datebirthAll[2] = "01";
        }

        if (datebirthAll.length > 0) {
            year = datebirthAll[0]
        }
        let age = new Date().getFullYear() - parseInt(year);

        if (year.length > 4) {
            $(this).find('input').val("1980-" + datebirthAll[1] + '-' + datebirthAll[2]);
        }


        if (age >= 3 && age < 7) {
            $('.form-row-last.extra-checkbox.3-7').show();
        }
        else if (age >= 7 && age < 12) {
            $('.form-row-last.extra-checkbox.7-12').show();
        }
        else if (age >= 12 && age < 18) {
            $('.form-row-last.extra-checkbox.12-18').show();
        }
        else if (age >= 18) {
            $('.form-row-last.extra-checkbox.18-150').show();
        }
    });

    //
    $( "form.checkout" ).on( "change", ".extra-checkbox", function( e ) {

         let total = 0;

        $("form.checkout .extra-checkbox input").each(function() {
            if ($(this).is(":checked")) {
                total = total + parseInt($(this).attr('id'))
            }
            else {
                console.log("test1")
            }
        });
        
         var data = {
            action: 'update_order_review_extra',
            security: wc_checkout_params.update_order_review_nonce,
            post_data: total
        };

        jQuery.post( add_quantity.ajax_url, data, function( response ) {
            $( 'body' ).trigger( 'update_checkout' );
            console.log(response)
        });

    });

});