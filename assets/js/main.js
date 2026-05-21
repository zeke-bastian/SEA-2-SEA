$(document).ready(function () {

    // live search — filters cards by title as user types
    $('#search-input').on('keyup', function () {
        var query = $(this).val().toLowerCase();

        // loop through each listing card and show/hide based on title match
        $('.listing-card').each(function () {
            var title = $(this).data('title').toLowerCase();
            if (title.includes(query)) {
                $(this).parent().show();
            } else {
                $(this).parent().hide();
            }
        });
    });

    // category filter — filters cards by selected category
    $('#category-filter').on('change', function () {
        var selected = $(this).val().toLowerCase();

        $('.listing-card').each(function () {
            var category = $(this).data('category').toLowerCase();
            if (selected === 'all' || category === selected) {
                $(this).parent().show();
            } else {
                $(this).parent().hide();
            }
        });
    });

});

// payment method selector — highlight selected and toggle card/paypal fields
$(document).on('change', '.payment-radio', function () {
    // remove highlight from all options
    $('.payment-radio').closest('label').removeClass('border-primary bg-light');

    // highlight the selected one
    $(this).closest('label').addClass('border-primary bg-light');

    // show paypal message or card fields depending on selection
    if ($(this).val() === 'paypal') {
        $('#card-fields').addClass('d-none');
        $('#paypal-fields').removeClass('d-none');
    } else {
        $('#card-fields').removeClass('d-none');
        $('#paypal-fields').addClass('d-none');
    }
});