// jQuery(document).ready(function($) {
//     var owl = $(".testimonial-slider").owlCarousel({
//         items: 1, // each item has 4 testimonials
//         slideSpeed: 1000,
//         autoPlay: false,
//         pagination: false,
//         navigation: false
//     });

//     $(".circle-btn.left").click(function() {
//         owl.trigger('owl.prev');
//     });

//     $(".circle-btn.right").click(function() {
//         owl.trigger('owl.next');
//     });
// });


function initializeTestimonialSlider(selector, columns = 1) {
    jQuery(document).ready(function($) {
        $(selector).owlCarousel({
            items: columns,
            slideSpeed: 1000,
            autoPlay: false,
            pagination: false,
            navigation: false
        });

        $(".circle-btn.left").click(function() {
            owl.trigger('owl.prev');
        });
    
        $(".circle-btn.right").click(function() {
            owl.trigger('owl.next');
        });
    });
}
