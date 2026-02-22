<?php
if (!defined('ABSPATH')) {
    exit;
}

function cretats_testimonials_shortcode_func($atts)
{
    $atts = shortcode_atts([
        'id' => 0,
    ], $atts);



    $post_id = intval($atts['id']);

    if (!$post_id)
        return 'Invalid shortcode ID';

    $var = cretats_get_shortcode_customization_variables($post_id);
    $layout = $var['layout'];
    $font = $var['font'];
    $color = $var['color'];
    $bg_color = $var['bg_color'];
    $block_bg_color = $var['block_bg_color'];
    $limit = $var['limit'];
    $columns = $var['columns'];
    $exclude = $var['exclude'];
    $header_font_size = $var['header_font_size'];
    $body_font_size = $var['body_font_size'];
    $include_ids = get_post_meta($post_id, '_cretats_sc_result_ids', true);
    $exclude_ids = !empty($exclude) ? array_map('intval', explode(',', $exclude)) : [];

    if (!empty($include_ids)) {
        $ids = array_map('intval', explode(',', $include_ids));
        $args = [
            'post_type' => 'cretats_testimonial',
            'post__in' => $ids,
            'post_status' => 'publish',
            'post__not_in' => $exclude_ids ?? [],
            'posts_per_page' => $limit ?? 1,
            'meta_key' => '_cretats_sequence',
            'orderby' => 'meta_value_num',
            'order' => 'ASC'
        ];
    } else {
        $args = [
            'post_type' => 'cretats_testimonial',
            'posts_per_page' => $limit ?? 1,
            'post_status' => 'publish',
            'post__not_in' => $exclude_ids ?? [],
            'meta_key' => '_cretats_sequence',
            'orderby' => 'meta_value_num',
            'order' => 'ASC'
        ];
    }

    $query = new WP_Query($args);

    ob_start();

    // Enqueue assets based on layout
    cretats_get_enqueue_layout_assets($layout, $bg_color, $columns);
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo '<div class="cretats-testimonial-shortcode"' . cretats_get_inline_style(['font_family' => $font, 'color' => $color, 'block_bg_color' => $block_bg_color]) . '>';


    $layout_file = CRETATS_PATH . 'templates/' . sanitize_key($layout) . '.php';

    if (file_exists($layout_file)) {
        include $layout_file;
    } else {
        echo '<p>Layout not found.</p>';
    }

    echo '</div>';

    return ob_get_clean();
}

add_shortcode('cretats_testimonials_sc', 'cretats_testimonials_shortcode_func');