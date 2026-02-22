<?php
if (!defined('ABSPATH')) {
    exit;
}
function cretats_get_shortcode_customization_variables($post_id, $columns = 1, $exclude = '')
{
    $layout = get_post_meta($post_id, '_cretats_sc_layout', true) ?: 'layout-1';
    $font = get_post_meta($post_id, '_cretats_sc_font', true) ?: 'Arial';
    $color = get_post_meta($post_id, '_cretats_sc_color', true) ?: null;
    $bg_color = get_post_meta($post_id, '_cretats_sc_bg_color', true) ?: null;
    $block_bg_color = get_post_meta($post_id, '_cretats_sc_block_bg_color', true) ?: null;
    $limit = get_post_meta($post_id, '_cretats_sc_limit', true) ?: 4;
    $columns = get_post_meta($post_id, '_cretats_sc_columns', true) ?: $columns;
    $header_font_size = get_post_meta($post_id, '_cretats_sc_header_font_size', true) ?: null;
    $body_font_size = get_post_meta($post_id, '_cretats_sc_body_font_size', true) ?: null;

    $designation = get_post_meta(get_the_ID(), '_cretats_designation', true);
    $location = get_post_meta(get_the_ID(), '_cretats_location', true);
    $rating = get_post_meta(get_the_ID(), '_cretats_rating', true);
    $info = get_post_meta(get_the_ID(), '_cretats_info', true);
    $image_url = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
    $bootstrap_col = 12 / max(1, $columns);
    $exclude = get_post_meta($post_id, '_cretats_sc_exclude', true) ?: $exclude;
    $exclude_ids = !empty($exclude) ? array_map('intval', explode(',', $exclude)) : [];

    return [
        'layout' => $layout,
        'font' => $font,
        'color' => $color,
        'bg_color' => $bg_color,
        'block_bg_color' => $block_bg_color,
        'limit' => $limit,
        'columns' => $columns,
        'bootstrap_col' => $bootstrap_col,

        'designation' => $designation,
        'location' => $location,
        'rating' => $rating,
        'info' => $info,
        'image_url' => $image_url,
        'exclude' => $exclude,
        'exclude_ids' => $exclude_ids,
        'header_font_size' => $header_font_size,
        'body_font_size' => $body_font_size,


    ];
}

//get layouts inline css
function cretats_get_inline_style($styles = [])
{
    $style_rules = [];

    if (isset($styles['font_size']) && $styles['font_size'] != '' && $styles['font_size'] != null) {
        $style_rules[] = 'font-size: ' . esc_attr($styles['font_size']) . 'px !important';
    }

    if (isset($styles['font_family']) && $styles['font_family'] != '' && $styles['font_family'] != null) {
        $style_rules[] = 'font-family: ' . esc_attr($styles['font_family']) . ' !important';
    }

    if (isset($styles['color']) && $styles['color'] != '' && $styles['color'] != null) {
        $style_rules[] = 'color: ' . esc_attr($styles['color']) . ' !important';
    }

    if (isset($styles['bg_color']) && $styles['bg_color'] != '' && $styles['bg_color'] != null) {
        $style_rules[] = 'background-color: ' . esc_attr($styles['bg_color']) . ' !important';
    }

    if (isset($styles['background']) && $styles['background'] != '' && $styles['background'] != null) {
        $style_rules[] = 'background: ' . esc_attr($styles['background']) . ' !important';
    }

    if (isset($styles['block_bg_color']) && $styles['block_bg_color'] != '' && $styles['block_bg_color'] != null) {
        $style_rules[] = 'background: ' . esc_attr($styles['block_bg_color']) . ' !important';
        $style_rules[] = 'padding: ' . esc_attr('2%') . ' !important';
        $style_rules[] = 'border-radius: ' . esc_attr('20px') . ' !important';
    }

    if (isset($styles['border_color']) && $styles['border_color'] != '' && $styles['border_color'] != null) {
        $style_rules[] = 'border-color: ' . esc_attr($styles['border_color']) . ' !important';
    }

    if (isset($styles['box_shadow']) && $styles['box_shadow'] != '' && $styles['box_shadow'] != null) {
        $style_rules[] = 'box-shadow: ' . esc_attr($styles['box_shadow']) . ' !important';
    }

    if (isset($styles['border']) && $styles['border'] != '' && $styles['border'] != null) {
        $style_rules[] = 'border: ' . esc_attr($styles['border']) . ' !important';
    }

    if (!empty($style_rules)) {
        return 'style="' . implode('; ', $style_rules) . ';"';
    }

    return '';
}


function cretats_get_star_avg_rating($rating, $fillColor = '#FFBA01', $position = 'center', $starSize = 24, $margin = 3)
{
    $unique_id = get_the_ID();

    $allowed_positions = ['start', 'center', 'end'];
    $position = in_array($position, $allowed_positions) ? $position : 'center';

    $margin_class = 'mt-' . esc_attr($margin);
    $position_class = 'justify-content-' . esc_attr($position);


    echo '<div class="remove-star-p d-flex ' . esc_attr($position_class) . ' ' . esc_attr($margin_class) . '">';


    for ($i = 1; $i <= 5; $i++) {
        $fillPercent = 0;

        if ($rating >= $i) {
            $fillPercent = 100;
        } elseif ($rating > ($i - 1)) {
            $fillPercent = ($rating - ($i - 1)) * 100;
        }

        $gradient_id = 'grad' . esc_attr($unique_id . '_' . $i);

        echo '
        <svg class="star" width="' . esc_attr($starSize) . '" height="' . esc_attr($starSize) . '" viewBox="0 0 24 24" fill="none"
             xmlns="http://www.w3.org/2000/svg" style="margin-right: 2px;">
            <defs>
                <linearGradient id="' . esc_attr($gradient_id) . '" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="' . esc_attr($fillPercent) . '%" stop-color="' . esc_attr($fillColor) . '"/>
                    <stop offset="' . esc_attr($fillPercent) . '%" stop-color="#e4e5e9"/>
                </linearGradient>
            </defs>
            <path fill="url(#' . esc_attr($gradient_id) . ')" d="M12 .587l3.668 7.431 8.2 1.192-5.934 5.787 
                  1.4 8.167L12 18.897l-7.334 3.867 1.4-8.167L.132 9.21l8.2-1.192z"/>
        </svg>';
    }

    echo '</div>';
}


function cretats_get_enqueue_layout_assets($layout, $bg_color, $columns)
{

    if ($layout === 'layout-5') {
        wp_enqueue_style('cretats-layout-5', CRETATS_URL . 'assets/css/layout-css/layout-5.css', [], CRETATS_VERSION);
        if (is_admin()) {
            wp_enqueue_script('owl-carousel', CRETATS_URL . 'assets/js/owl.carousel.min.js', ['jquery'], CRETATS_VERSION, true);
        }
        wp_enqueue_script('cretats-slider-common', CRETATS_URL . 'assets/js/layout-js/comman-slider.js', ['jquery'], CRETATS_VERSION, true);

        $bg_color = $bg_color ? $bg_color : '#E9D5FF';

        $css = " .owl-theme .owl-controls .owl-page span {
                    background: {$bg_color} !important;
                } ";

        wp_add_inline_style('cretats-layout-5', $css);


        $js = "
            jQuery(document).ready(function($){
                initializeTestimonialSlider('.testimonial-slider', {$columns},30,true,true,false,['&laquo;', '&raquo;'],800,true);
            }); ";
        wp_add_inline_script('cretats-slider-common', $js);
    }

    if ($layout === 'layout-6') {
        wp_enqueue_style('cretats-layout-6', CRETATS_URL . 'assets/css/layout-css/layout-6.css', [], CRETATS_VERSION);
        wp_enqueue_script('cretats-layout-6', CRETATS_URL . 'assets/js/layout-js/layout-6.js', ['jquery'], CRETATS_VERSION, true);
        if (is_admin()) {
            wp_enqueue_script('owl-carousel', CRETATS_URL . 'assets/js/owl.carousel.min.js', ['jquery'], CRETATS_VERSION, true);
        }

        $js = "
        jQuery(document).ready(function($){
            initializeTestimonialSlider('.testimonial-slider');
        });";

        wp_add_inline_script('cretats-layout-6', $js);

    }

    if ($layout === 'layout-7') {
        wp_enqueue_style('cretats-layout-7', CRETATS_URL . 'assets/css/layout-css/layout-7.css', [], CRETATS_VERSION);
        // wp_enqueue_script('cretats-slider-common', CRETATS_URL . 'assets/js/layout-js/layout-7.js', ['jquery'], CRETATS_VERSION, true);
        if (is_admin()) {
            // wp_enqueue_script('owl-carousel', CRETATS_URL . 'assets/js/owl.carousel.min.js', ['jquery'], CRETATS_VERSION, true);
        }
    }
}


function cretats_lighten_color($hex, $percent)
{
    $hex = str_replace('#', '', $hex);

    if (strlen($hex) == 3) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }

    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    $r = min(255, intval($r + (255 - $r) * $percent / 100));
    $g = min(255, intval($g + (255 - $g) * $percent / 100));
    $b = min(255, intval($b + (255 - $b) * $percent / 100));

    return sprintf("#%02x%02x%02x", $r, $g, $b);
}

function cretats_darken_color($hex, $percent)
{
    $hex = str_replace('#', '', $hex);

    if (strlen($hex) == 3) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }

    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    $r = max(0, intval($r - ($r * $percent / 100)));
    $g = max(0, intval($g - ($g * $percent / 100)));
    $b = max(0, intval($b - ($b * $percent / 100)));

    return sprintf("#%02x%02x%02x", $r, $g, $b);
}