jQuery(document).ready(function($) {
	
    $('.persons').find('select').select2({
        minimumResultsForSearch: 7,
        dropdownAutoWidth: true
    });

    $('.custom_checkout_fields_wrapper').find('select').select2({
        minimumResultsForSearch: 7,
        dropdownAutoWidth: true
    });
	
    //$(".persons select").val('1').trigger('change');
    //$("#persons").val($("#persons option:first")).trigger('change');

    ////Extra Button
    $(document).on('click', '.custom_checkout_continue', function () {
        $(this).hide();
        $('.custom_checkout_field').addClass('show');
        $('.wpmc-nav-buttons').addClass('show');

        $('input[name="firstname_1"]').val($('input[name="billing_first_name"]').val());
        $('input[name="lastname_1"]').val($('input[name="billing_last_name"]').val());
        $('input[name="email_1"]').val($('input[name="billing_email"]').val());
        $('input[name="phone_1"]').val($('input[name="billing_phone"]').val());
    });
});