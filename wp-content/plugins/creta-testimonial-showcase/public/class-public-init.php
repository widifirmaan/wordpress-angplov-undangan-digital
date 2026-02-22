<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
if(!class_exists('Cretats_Public_Init')){
    class Cretats_Public_Init {
        public function __construct() {
            // Frontend assets
            add_action( 'wp_enqueue_scripts', [ $this, 'cretats_enqueue_front_assets' ] );
            add_filter('the_content', [ $this,'cretats_shortcode_remove_wpautop']);
        }
    
        public function cretats_enqueue_front_assets() {
            wp_enqueue_style( 'cretats-public-bs-css', CRETATS_URL . 'assets/css/bootstrap.min.css', [], CRETATS_VERSION );
            wp_enqueue_script('cretats-public-bs-js',CRETATS_URL . 'assets/js/bootstrap.bundle.min.js',[ 'jquery' ],CRETATS_VERSION,true 
            );
            
            wp_enqueue_style( 'cretats-public-font-awesome', CRETATS_URL . 'assets/css/all.min.css',[],CRETATS_VERSION );


            wp_enqueue_style('cretats-public-carousel', CRETATS_URL . 'assets/css/owl.carousel.min.css',[], CRETATS_VERSION);
            wp_enqueue_style('cretats-public-theme', CRETATS_URL . 'assets/css/owl.theme.min.css',[], CRETATS_VERSION);
            wp_enqueue_script('cretats-public-owl-carousel', CRETATS_URL . 'assets/js/owl.carousel.min.js',  array('jquery'), CRETATS_VERSION, true);
            wp_enqueue_style( 'cretats-public-bootstrap-icons', CRETATS_URL . 'assets/css/bootstrap-icons.css',[], CRETATS_VERSION);
        }

        public function cretats_shortcode_remove_wpautop($content) {
            if (is_front_page()) {
                remove_filter('the_content', 'wpautop');
            }
            return $content;
        }
    }
    new Cretats_Public_Init();
}
