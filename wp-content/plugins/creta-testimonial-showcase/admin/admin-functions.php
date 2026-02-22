<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
function cretats_remove_fields_metabox() {
    //testimonial page
    remove_meta_box('postcustom', 'cretats_testimonial', 'normal');
    remove_meta_box('slugdiv', 'cretats_testimonial', 'normal');

    //short code page
    remove_meta_box('postcustom', 'cretats_tms_sc', 'normal');
    remove_meta_box('slugdiv', 'cretats_tms_sc', 'normal');
    remove_post_type_support( 'cretats_tms_sc', 'editor' );
    remove_post_type_support( 'cretats_tms_sc', 'thumbnail' );
}


function cretats_remove_view_link_from_testimonials($actions, $post) {
    if ($post->post_type === 'cretats_testimonial' || $post->post_type == 'cretats_tms_sc') {
        unset($actions['view']);
    }
    return $actions;
}
