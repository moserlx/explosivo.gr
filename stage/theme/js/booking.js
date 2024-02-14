jQuery(function( $ ) {
    var prices;
    var types;
    var quantities;

    $('.booking').find('select').select2({
        minimumResultsForSearch: 7,
        dropdownAutoWidth: true
    });

    $( ".booking" ).on( "click", ".submitorder", function( e ) {
        e.preventDefault();

        var arr = [$('.submitorder').attr('data-product_id'), $('.submitorder').attr('data-variation_id'),$('.submitorder').attr('data-quan')];
        
        var data = {
            action: 'get_booking_cart',
            post_data: arr
        };

        jQuery.post( booking.ajax_url, data, function( response ) {
            window.location.replace(response);
        });
    });

    $( ".booking" ).on( "change", "#hotel", function( e ) {
        var value = $(this).val();
        var arr = [$( '#productid' ).val(), value];
        
        var data = {
            action: 'get_children_limits',
            post_data: arr
        };

        jQuery.post( booking.ajax_url, data, function( response ) {
            var attrs = JSON.parse(response);
            var children = attrs['children'];
            $('#adults').val('');
            $('.children, .adults').hide();

            $('.secondstep, .secondstepempty, .submitorder').hide();

            for (i = 0; i < children.length; ++i) {
                if (children[i] != '0')
                    $(`.children${children[i]}`).show();
            }

            //
            if (value == '0') {
                $('.continue, .adults').hide();
                return;
            }
            else {
                $('.continue, .adults').show();
            }
        });

    });

    $( ".booking" ).on( "change", "input[type='number']", function( e ) {
        $('.secondstep, .secondstepempty, .submitorder').hide();
    });

    /*
    children313
    children512
    children213
    children212
    children12150
    children13150
    */
    
    $( ".booking" ).on( "click", ".continue", function( e ) {

        var arr = [$( '#productid' ).val(), $( '#hotel' ).val(), $( '#adults' ).val()];

        $('.children').each(function(i, obj) {
            arr.push($(this).find('input').val())
        });

        var data = {
            action: 'get_price',
            //security: wc_checkout_params.update_order_review_nonce,
            post_data: arr
        };

        jQuery.post( booking.ajax_url, data, function( response ) {
            var attrs = JSON.parse(response);
            types = attrs['types'];
            quantities = attrs['quantities'];
            var typeNames = attrs['typeNames'];
            prices = attrs['prices'];
            //console.log(response)

            if (types.length > 0) {
                $('.secondstep').show();
                $('.secondstepempty').hide();
                $('.submitorder').css('display', 'inline-block');
                
                //$('.continue').hide();
                $('.price').text(prices[0] + '€');
                //$('.submitorder').attr('href', '?add-to-cart=' + types[0]);
                $('.submitorder').attr('data-variation_id', types[0]);
                $('.submitorder').attr('data-quan', quantities[0]);

                $("#roomtype option").remove();
            }
            else {
                $('.secondstep').hide();
                $('.secondstepempty').show();
                $('.submitorder').hide();
            }

            for (i = 0; i < types.length; ++i) {
                var selected = false
                if (i == 0) {
                    selected = true
                }
                
                $('#roomtype').append($('<option>', {
                    value: types[i],
                    text: typeNames[i],
                    selected,
                    selectedIndex: i
                }));
            }
        });
    });

    $( ".booking" ).on( "change", "#roomtype", function( e ) {
        //console.log($( this ).find(":selected").attr('selectedIndex'));
        var selectedIndex = $( this ).find(":selected").attr('selectedIndex');
        if (prices.length > 0) {
            $('.price').text(prices[$( this ).find(":selected").attr('selectedIndex')] + '€');

            $('.submitorder').attr('data-variation_id', types[selectedIndex]);
            $('.submitorder').attr('data-quan', quantities[selectedIndex]);
        }
        
        /*var arr = [$( this ).val()];
        
        var data = {
            action: 'get_price_from_var',
            //security: wc_checkout_params.update_order_review_nonce,
            post_data: arr
        };

        jQuery.post( booking.ajax_url, data, function( response ) {

        });*/
    });
});