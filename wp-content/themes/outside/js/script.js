jQuery(document).ready(function ($ = jQuery) {

    if ($(window).width() < 576) {
        if (!$('.my-slider').hasClass('slick-initialized')) {
            $(".event-slider").slick({
                dots: false,
                arrow: true,
                infinite: false,
                slidesToShow: 1,
                slidesToScroll: 1,
                prevArrow: '<button type="button" class="slick-prev"><</button>',
                nextArrow: '<button type="button" class="slick-next">></button>',
                responsive: [
                {
                    breakpoint: 576,
                    settings: {
                    slidesToShow: 1
                    }
                }
                ]
            });
        }
    } else {
        if ($('.my-slider').hasClass('slick-initialized')) {
            $('.my-slider').slick('unslick');
        }
    }

    var ajax_url = js_obj.ajax_url;
    var ajax_nonce = js_obj.ajax_nonce;
    var ajax_loader = js_obj.ajax_loader;

    $('body').on('click', '.plus-icon-wrap', function () {
        $(this).closest('.card').hide();
        $(this).closest('.card').siblings('.card-details').show();
    });
    $('body').on('click', '.close-btn', function () {
        $(this).closest('.card-details').hide();
        $(this).closest('.card-details').siblings('.card').show();
    });


    $('body').on('click', '.pagination-link', function (e) {
        e.preventDefault();

        var page = $(this).data('page');
        var $this = $(this);

        $.ajax({
            type: 'post',
            url: ajax_url,
            data: {
                action: 'ajax_pagination',
                paged: page,
            },
            beforeSend: function (xhr) {
                $('#ajax-posts').html('<img src="'+ajax_loader+'"/><p>Loading...</p>');
            },
            success: function (res) {
                console.log(res)
                $('#ajax-posts').html(res);
            },
            error: function () {
                alert('Something went wrong. Please try again.');
            },
        });
    });


    
});