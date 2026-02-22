function initializeTestimonialSlider(selector, columns) {
    jQuery(document).ready(function($) {
        $(selector).owlCarousel({
            items: columns,
            margin: 30,
            loop: true,
            nav: true,
            dots: false,
            navText: ["&laquo;", "&raquo;"],
            smartSpeed: 800,
            autoHeight: true,
        });
    });
}

jQuery(document).ready(function($) {
    var owl = $(".testimonial-slider").owlCarousel({
        items: 1, // each item contains 4 testimonials
        slideSpeed: 1000,
        autoplay: false,
        pagination: false,
        nav: false,
        responsive: {
            0: {
                items: 1
            },
            480: {
                items: 1
            },
            768: {
                items: 1
            },
            992: {
                items: 1
            }
        }
    });

    $(".circle-btn.left").click(function() {
        owl.trigger('prev.owl.carousel');
    });

    $(".circle-btn.right").click(function() {
        owl.trigger('next.owl.carousel');
    });
});
