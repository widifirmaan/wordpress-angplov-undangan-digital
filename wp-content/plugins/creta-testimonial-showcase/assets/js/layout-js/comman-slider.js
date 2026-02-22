function initializeTestimonialSlider(selector, columns) {
    jQuery(document).ready(function($) {
        $(selector).owlCarousel({
            items: columns,
            itemsDesktop: [1000, 1],
            itemsDesktopSmall: [979, 1],
            itemsTablet: [768, 1],
            pagination: true,
            navigation: false,
            navigationText: ["", ""],
            slideSpeed: 1000,
            autoPlay: true
        });
    });
}

