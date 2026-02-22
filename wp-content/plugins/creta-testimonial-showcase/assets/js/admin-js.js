jQuery(document).ready(function($) {

    $('#cretats-popup-content .cretats-popup-dismiss, #cretats-popup-content .cretats-popup-template-btn').on('click', function() {
        
        $.ajax({
            url: cretats_ajax_object.ajax_url,
            type: 'POST',
            data: { action: 'cretats_get_notice_dismiss' },
            success: function(response) {
                $('#cretats-popup-overlay').hide();                
            }
        });
    });

    $('.cretats-star').on('click', function() {
        var rating = $(this).data('value');
        $('#cretats_rating').val(rating);

        // Reset all
        $('.cretats-star').removeClass('dashicons-star-filled').addClass('dashicons-star-empty');

        // Fill selected
        $('.cretats-star').each(function() {
            if ($(this).data('value') <= rating) {
                $(this).removeClass('dashicons-star-empty').addClass('dashicons-star-filled');
            }
        });
    });
});

jQuery(document).ready(function($) {
    function updatePreview() {
        let layout = $('input[name="cretats_layout"]:checked').val();
        let font = $('select[name="cretats_font"]').val();
        let color = $('input[name="cretats_color"]').val();
        let bg_color = $('input[name="cretats_bg_color"]').val();
        let block_bg_color = $('input[name="cretats_block_bg_color"]').val();
        let limit = $('input[name="cretats_limit"]').val();
        let columns = $('input[name="cretats_columns"]').val();
        let exclude = $('input[name="cretats_exclude"]').val();
        let header_font_size = $('input[name="cretats_header_font_size"]').val();
        let body_font_size = $('input[name="cretats_body_font_size"]').val();
        

        $.ajax({
            // url: ajaxurl,
            url: cretats_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'cretats_get_preview_html',
                cretats_nonce: cretats_ajax_object.nonce,
                layout: layout,
                font: font,
                color: color,
                bg_color: bg_color,
                block_bg_color: block_bg_color,
                limit: limit,
                columns: columns,
                exclude: exclude,
                header_font_size: header_font_size,
                body_font_size: body_font_size,

            },
            success: function(response) {
                $('.cretats-preview-area').html(response);
            }
        });
    }

    $('input[name="cretats_layout"], select[name="cretats_font"], input[name="cretats_color"], input[name="cretats_bg_color"],input[name="cretats_limit"],input[name="cretats_columns"],input[name="cretats_exclude"],input[name="cretats_header_font_size"],input[name="cretats_body_font_size"]').on('change', updatePreview);

    // Listen color picker event
    $('.bg-color-picker').on('colorPickerChanged', updatePreview);
    $('.block-bg-color-picker').on('colorPickerChanged', updatePreview);
    $('.color-picker').on('colorPickerChanged', updatePreview);


    updatePreview();
});


document.addEventListener('DOMContentLoaded', function () {
    const tabButtons = document.querySelectorAll('.tab-st-button');
    const tabContents = document.querySelectorAll('.tab-st-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const target = button.getAttribute('data-tab');

            // Remove active classes
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));

            // Add active class to selected
            button.classList.add('active');
            document.getElementById(target).classList.add('active');
        });
    });
});


// new add

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.cretats-copy-shortcode').forEach(function(el) {
        el.addEventListener('click', function() {
            const shortcode =  this.getAttribute('data-code');
            navigator.clipboard.writeText(shortcode).then(() => {
                this.innerText = 'Copied!';
                setTimeout(() => {
                    this.innerText = 'ID: ' + shortcode;
                }, 1000);
            });
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.cretats-copy-shortcode').forEach(function(el) {
        el.addEventListener('click', function() {
            const shortcode = this.getAttribute('data-code');
            navigator.clipboard.writeText(shortcode).then(() => {
                this.innerText = 'Copied!';
                setTimeout(() => {
                    this.innerText = shortcode;
                }, 1000);
            });
        });
    });
});

// jQuery(document).ready(function($){
//     initializeTestimonialSlider('.testimonial-slider');
// });