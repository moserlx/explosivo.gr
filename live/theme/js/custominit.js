jQuery(document).ready(function($) {

    $( ".custom-table-products input[type='checkbox']" ).change(function() {
       
        var this_var = $(this).closest('.custom-table-products-wrap');
        var data = {
            action: 'update_custom_cart',
            post_data: this.value + '-' + this_var.next().next().next().find('div').attr('data-variation_id') + '-' + $(this).is(':checked')
        };

        this_var.next().next().next().find('div').addClass('disabled');
        this_var.find('input').attr("disabled", true);

        jQuery.post( custominit.ajax_url, data, function( response ) {
            
            this_var.next().next().next().find('div').attr('data-variation_id', response);

            var length_checkboxes = this_var.find('input:checked').length;
            if (length_checkboxes == 0) {
                this_var.next().next().next().find('div').addClass('disabled');
                this_var.find('input').attr("disabled", false);
            }
            else if (length_checkboxes == 3) {
                this_var.next().next().next().find('div').removeClass('disabled');
                this_var.find('input').attr("disabled", false);
                this_var.find('input:not(:checked)').attr("disabled", true);
            }
            else {
                this_var.next().next().next().find('div').removeClass('disabled');
                this_var.find('input').attr("disabled", false);
            }
        });
    });

    $( ".addToCartButton div" ).click(function() {
        var this_var = $(this);
        var data = {
            action: 'update_custom_cart_add',
            post_data: $(this).attr('data-product_id') + '-' + $(this).attr('data-variation_id')
        };

        this_var.addClass('disabled');
        $('.custom-table-products input:not(:checked)').attr("disabled", true);

        $.post( custominit.ajax_url, data, function( response ) {
            window.location.replace(response);
        });
        
    });

    //
    $(document).on('mouseenter', '.login-button a', function () {
        $(this).parent().addClass('login-wrapper-hover');

        $(document).on('mouseleave', '.login-wrapper-hover', function () {
            $(this).removeClass('login-wrapper-hover');
        });
    });

    //
    $(document).on('click', '.login-button-mob a', function () {
        $(this).next('.login-button_dr').show();
    });

    $( ".custom-generic-error .close-button" ).click(function() {
        $(this).parent().parent().parent().parent().hide();
    });

    //
    $(document).on('click', '.check-availability', function () {
        if ($('.check-wrapper').hasClass('active')) {
            $('.stVarAttributes').removeClass('active');
            $('.check-wrapper').removeClass('active');
            $(this).removeClass('active');
            $(this).text('Έλεγχος διαθεσιμότητας');
        }
        else {
            $('.stVarAttributes').addClass('active');
            $(this).text('Καθαρισμός');
            $('.check-wrapper').addClass('active');
            $(this).addClass('active');
        }
    });

    //
    $('.inline').modaal();
});