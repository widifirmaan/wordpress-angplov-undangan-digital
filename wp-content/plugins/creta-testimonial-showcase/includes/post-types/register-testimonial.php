<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'init', 'cretats_testimonial_register_post_type' );

function cretats_testimonial_register_post_type() {
    if (!post_type_exists( 'cretats_testimonial' ) ) {
        $labels = array(
            'name'               => 'Testimonials',
            'singular_name'      => 'Testimonial',
            'all_items'          =>  'All Testimonial',
            'menu_name'          => 'Testimonials',
            'add_new_item'       => 'Add New Testimonial',
            'edit_item'          => 'Edit Testimonial',
            'new_item'           => 'New Testimonial',
            'view_item'          => 'View Testimonial',
            'search_items'       => 'Search Testimonials',
            'not_found'          => 'No Testimonials found',
            'not_found_in_trash' => 'No Testimonials found in Trash',
        );
        
        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'has_archive'        => true,
            'show_ui'            => true,
            'menu_position'      => 20,
            'menu_icon'          => CRETATS_URL . 'assets/img/logo.png',
            'supports'           => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'revisions'),
            'show_in_rest'       => false,
            'hierarchical' => false,
        );
     
        register_post_type( 'cretats_testimonial', $args );
    }
    
    if (!post_type_exists( 'cretats_tms_sc' ) ) {
        
        $labels = array(
            'name'               => 'ShortCodes',
            'singular_name'      => 'ShortCode',
            'all_items'          =>  'Shortcodes',
            'menu_name'          => 'Shortcodes',
            'add_new_item'       => 'Add New ShortCode',
            'edit_item'          => 'Edit ShortCode',
            'new_item'           => 'New ShortCode',
            'view_item'          => 'View Show',
            'search_items'       => 'Search Shortcodes',
            'not_found'          => 'No Shortcodes found',
            'not_found_in_trash' => 'No Shortcodes found in Trash',
        );
        
        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'has_archive'        => false,
            'show_ui'            => true,
            'show_in_menu'       => 'edit.php?post_type=cretats_testimonial', 
            'supports'           => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'revisions'),
            'show_in_rest'       => false,
            'hierarchical' => false,
        );
     
        register_post_type( 'cretats_tms_sc', $args );
    }
}
