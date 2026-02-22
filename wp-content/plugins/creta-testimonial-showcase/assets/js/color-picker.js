// jQuery(document).ready(function($) {
//     $('.smart-color-picker').wpColorPicker();
//     $('.iris-square-vert').on('move',function(){
//         alert('n');
//     });
// });

jQuery(document).ready(function($) {
    var $bgColorInput = $('.bg-color-picker');
    let bgcolorChangeTimeout;

    $bgColorInput.wpColorPicker({
        change: function(event, ui) {
            clearTimeout(bgcolorChangeTimeout);
            bgcolorChangeTimeout = setTimeout(function() {
                $bgColorInput.trigger('colorPickerChanged');
            }, 200);
        },
        clear: function() {
            $bgColorInput.trigger('colorPickerChanged');
        }
    });
});


jQuery(document).ready(function($) {
    var colorInput = $('.color-picker');
    let colorChangeTimeout;

    colorInput.wpColorPicker({
        change: function(event, ui) {
            clearTimeout(colorChangeTimeout);
            colorChangeTimeout = setTimeout(function() {
                colorInput.trigger('colorPickerChanged');
            }, 200);
        },
        clear: function() {
            colorInput.trigger('colorPickerChanged');
        }
    });
});


jQuery(document).ready(function($) {
    var $blockbgColorInput = $('.block-bg-color-picker');
    let blockbgcolorChangeTimeout;

    $blockbgColorInput.wpColorPicker({
        change: function(event, ui) {
            clearTimeout(blockbgcolorChangeTimeout);
            blockbgcolorChangeTimeout = setTimeout(function() {
                $blockbgColorInput.trigger('colorPickerChanged');
            }, 200);
        },
        clear: function() {
            $blockbgColorInput.trigger('colorPickerChanged');
        }
    });
});
