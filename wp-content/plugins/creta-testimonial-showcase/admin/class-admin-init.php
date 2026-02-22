<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
if(!class_exists('Cretats_Admin_Init')){
    
    class Cretats_Admin_Init{
        public function __construct(){
            add_action( 'admin_enqueue_scripts', [ $this, 'cretats_enqueue_admin_assets' ] );

        }

        public function cretats_enqueue_admin_assets($hook) {
            wp_enqueue_style( 'cretats-admin-css', CRETATS_URL . 'assets/css/admin-style.css', [], CRETATS_VERSION );
            wp_enqueue_script( 'cretats-admin-js',CRETATS_URL . 'assets/js/admin-js.js', [ 'jquery' ], CRETATS_VERSION, true );
            wp_localize_script( 'cretats-admin-js', 'cretats_ajax_object', array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'cretats_preview_action' ),
            ) );

             // new add 
            wp_enqueue_style( 'cretats-theme-admin-style', CRETATS_URL . 'assets/css/admin-theme-page.css', [], CRETATS_VERSION );
            if ($hook == 'toplevel_page_cretats-theme-showcase') {
                wp_enqueue_script( 'cretats-theme-admin-js', CRETATS_URL . 'assets/js/admin-theme-page.js', [ 'jquery' ], CRETATS_VERSION, true);
            }

            // end

            $screen = get_current_screen();
            if ( (isset( $screen->post_type ) && $screen->post_type === 'cretats_testimonial' ) || (isset( $screen->post_type ) && $screen->post_type === 'cretats_tms_sc' ) || $screen->id == 'toplevel_page_cretats-theme-showcase'  ) {
                wp_enqueue_style( 'cretats-admin-bs-css', CRETATS_URL . 'assets/css/bootstrap.min.css', [], CRETATS_VERSION );
                wp_enqueue_script('cretats-admin-bs-js', CRETATS_URL . 'assets/js/bootstrap.bundle.min.js', [ 'jquery' ], CRETATS_VERSION, true );

                wp_enqueue_style('cretats-admin-carousel', CRETATS_URL . 'assets/css/owl.carousel.min.css',[], CRETATS_VERSION);
                wp_enqueue_style('cretats-admin-theme', CRETATS_URL . 'assets/css/owl.theme.min.css',[], CRETATS_VERSION);
                wp_enqueue_script('cretats-admin-owl-carousel', CRETATS_URL . 'assets/js/owl.carousel.min.js',  array('jquery'), CRETATS_VERSION, true);

                //font
                wp_enqueue_style( 'cretats-admin-font-awesome', CRETATS_URL . 'assets/css/all.min.css',[],CRETATS_VERSION );
                wp_enqueue_style( 'cretats-admin-bootstrap-icons', CRETATS_URL . 'assets/css/bootstrap-icons.css',[], CRETATS_VERSION);


                //color picker
                global $post_type;
                if($post_type === 'cretats_tms_sc' ){
                    wp_enqueue_style('wp-color-picker');
                    wp_enqueue_script('wp-color-picker');
                    wp_enqueue_script('cretats-tms-admin',CRETATS_URL . 'assets/js/color-picker.js',array('wp-color-picker'), CRETATS_VERSION,true);
                }
            }
            
        }
    }
    
    new Cretats_Admin_Init();
}