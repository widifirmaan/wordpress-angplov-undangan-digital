<?php
/**
 * Wedding Planner Firm functions and definitions
 *
 * @package wedding_planner_firm
 * @since 1.0
 */

if ( ! function_exists( 'wedding_planner_firm_support' ) ) :
	function wedding_planner_firm_support() {

		load_theme_textdomain( 'wedding-planner-firm', get_template_directory() . '/languages' );

		// Add support for block styles.
		add_theme_support( 'wp-block-styles' );

		add_theme_support('woocommerce');

		// Enqueue editor styles.
		add_editor_style(get_stylesheet_directory_uri() . '/assets/css/editor-style.css');

		/* Theme Credit link */
		define('WEDDING_PLANNER_FIRM_BUY_NOW',__('https://www.cretathemes.com/products/wedding-wordpress-theme','wedding-planner-firm'));
		define('WEDDING_PLANNER_FIRM_PRO_DEMO',__('https://pattern.cretathemes.com/wedding-planner-firm/','wedding-planner-firm'));
		define('WEDDING_PLANNER_FIRM_THEME_DOC',__('https://pattern.cretathemes.com/free-guide/wedding-planner-firm/','wedding-planner-firm'));
		define('WEDDING_PLANNER_FIRM_PRO_THEME_DOC',__('https://pattern.cretathemes.com/pro-guide/wedding-planner-firm/','wedding-planner-firm'));
		define('WEDDING_PLANNER_FIRM_SUPPORT',__('https://wordpress.org/support/theme/wedding-planner-firm/','wedding-planner-firm'));
		define('WEDDING_PLANNER_FIRM_REVIEW',__('https://wordpress.org/support/theme/wedding-planner-firm/reviews/#new-post','wedding-planner-firm'));
		define('WEDDING_PLANNER_FIRM_PRO_THEME_BUNDLE',__('https://www.cretathemes.com/products/wordpress-theme-bundle','wedding-planner-firm'));
		define('WEDDING_PLANNER_FIRM_PRO_ALL_THEMES',__('https://www.cretathemes.com/collections/wordpress-block-themes','wedding-planner-firm'));
	}
endif;

add_action( 'after_setup_theme', 'wedding_planner_firm_support' );

if ( ! function_exists( 'wedding_planner_firm_styles' ) ) :
	function wedding_planner_firm_styles() {
		// Register theme stylesheet.
		$wedding_planner_firm_theme_version = wp_get_theme()->get( 'Version' );

		$wedding_planner_firm_version_string = is_string( $wedding_planner_firm_theme_version ) ? $wedding_planner_firm_theme_version : false;
		wp_enqueue_style(
			'wedding-planner-firm-style',
			get_template_directory_uri() . '/style.css',
			array(),
			$wedding_planner_firm_version_string
		);

		wp_enqueue_style( 'dashicons' );

		wp_enqueue_style( 'animate-css', esc_url(get_template_directory_uri()).'/assets/css/animate.css' );

		wp_enqueue_script( 'jquery-wow', esc_url(get_template_directory_uri()) . '/assets/js/wow.js', array('jquery') );

		wp_style_add_data( 'wedding-planner-firm-style', 'rtl', 'replace' );

		//font-awesome
		wp_enqueue_style( 'fontawesome', get_template_directory_uri() . '/inc/fontawesome/css/all.css'
			, array(), '6.7.0' );


		// Enqueue Swiper CSS
		wp_enqueue_style(
		    'swiper-bundle-style',
		    get_template_directory_uri() . '/assets/css/swiper-bundle.css',
		    array(),
		    $wedding_planner_firm_version_string
		);

		// Enqueue Swiper JS
		wp_enqueue_script(
		    'swiper-bundle-scripts',
		    get_template_directory_uri() . '/assets/js/swiper-bundle.js',
		    array('jquery'),
		    $wedding_planner_firm_version_string,
		    true
		);

		// Enqueue Custom Script (depends on Swiper too)
		wp_enqueue_script(
		    'wedding-planner-firm-custom-script',
		    get_template_directory_uri() . '/assets/js/custom-script.js',
		    array('jquery', 'swiper-bundle-scripts'),
		    $wedding_planner_firm_version_string,
		    true
		);

		// Enqueue Swiper CSS
		wp_enqueue_style(
		    'slick-style',
		    get_template_directory_uri() . '/assets/css/slick.css',
		    array(),
		    $wedding_planner_firm_version_string
		);

		// Enqueue Swiper JS
		wp_enqueue_script(
		    'slick-scripts',
		    get_template_directory_uri() . '/assets/js/slick.js',
		    array('jquery'),
		    $wedding_planner_firm_version_string,
		    true
		);
	}
endif;

add_action( 'wp_enqueue_scripts', 'wedding_planner_firm_styles' );


/* Enqueue admin-notice-script js */
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'appearance_page_wedding-planner-firm') return;

    wp_enqueue_script('admin-notice-script', get_template_directory_uri() . '/get-started/js/admin-notice-script.js', ['jquery'], null, true);
    wp_localize_script('admin-notice-script', 'pluginInstallerData', [
        'ajaxurl'     => admin_url('admin-ajax.php'),
        'nonce'       => wp_create_nonce('install_cretatestimonial_nonce'), // Match this with PHP nonce check
        'redirectUrl' => admin_url('themes.php?page=wedding-planner-firm-guide-page'),
    ]);
});

add_action('wp_ajax_check_creta_testimonial_activation', function () {
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
    $wedding_planner_firm_plugin_file = 'wordclever-ai-content-writer/wordclever.php';

    if (is_plugin_active($wedding_planner_firm_plugin_file)) {
        wp_send_json_success(['active' => true]);
    } else {
        wp_send_json_success(['active' => false]);
    }
});


// Add block patterns
require get_template_directory() . '/inc/block-patterns.php';

// Add block styles
require get_template_directory() . '/inc/block-styles.php';

// Block Filters
require get_template_directory() . '/inc/block-filters.php';

// Svg icons
require get_template_directory() . '/inc/icon-function.php';

// TGM plugin
require get_template_directory() . '/inc/tgm/tgm.php';

// Customizer
require get_template_directory() . '/inc/customizer.php';

// Get Started.
require get_template_directory() . '/inc/get-started/get-started.php';


// Add Getstart admin notice
function wedding_planner_firm_admin_notice() { 
    global $pagenow;
    $theme_args      = wp_get_theme();
    $meta            = get_option( 'wedding_planner_firm_admin_notice' );
    $name            = $theme_args->__get( 'Name' );
    $current_screen  = get_current_screen();

    if( !$meta ){
	    if( is_network_admin() ){
	        return;
	    }

	    if( ! current_user_can( 'manage_options' ) ){
	        return;
	    } if($current_screen->base != 'appearance_page_wedding-planner-firm-guide-page' && $current_screen->base != 'toplevel_page_cretats-theme-showcase' ) { ?>

	    <div class="notice notice-success dash-notice">
	        <h1><?php esc_html_e('Hey, Thank you for installing Wedding Planner Firms Theme!', 'wedding-planner-firm'); ?></h1>
	        <p> <a href="javascript:void(0);" id="install-activate-button" class="button admin-button info-button get-start-btn">
				   <?php echo __('Nevigate Getstart', 'wedding-planner-firm'); ?>
				</a>

				<script type="text/javascript">
				document.getElementById('install-activate-button').addEventListener('click', function () {
				    const wedding_planner_firm_button = this;
				    const wedding_planner_firm_redirectUrl = '<?php echo esc_url(admin_url("themes.php?page=wedding-planner-firm-guide-page")); ?>';
				    // First, check if plugin is already active
				    jQuery.post(ajaxurl, { action: 'check_creta_testimonial_activation' }, function (response) {
				        if (response.success && response.data.active) {
				            // Plugin already active — just redirect
				            window.location.href = wedding_planner_firm_redirectUrl;
				        } else {
				            // Show Installing & Activating only if not already active
				            wedding_planner_firm_button.textContent = 'Nevigate Getstart';

				            jQuery.post(ajaxurl, {
				                action: 'install_and_activate_creta_testimonial_plugin',
				                nonce: '<?php echo wp_create_nonce("install_activate_nonce"); ?>'
				            }, function (response) {
				                if (response.success) {
				                    window.location.href = wedding_planner_firm_redirectUrl;
				                } else {
				                    alert('Failed to activate the plugin.');
				                    wedding_planner_firm_button.textContent = 'Try Again';
				                }
				            });
				        }
				    });
				});
				</script>


	        	<a class="button button-primary site-edit" href="<?php echo esc_url( admin_url( 'site-editor.php' ) ); ?>"><?php esc_html_e('Site Editor', 'wedding-planner-firm'); ?></a> 
				<a class="button button-primary buy-now-btn" href="<?php echo esc_url( WEDDING_PLANNER_FIRM_BUY_NOW ); ?>" target="_blank"><?php esc_html_e('Buy Pro', 'wedding-planner-firm'); ?></a>
				<a class="button button-primary bundle-btn" href="<?php echo esc_url( WEDDING_PLANNER_FIRM_PRO_THEME_BUNDLE ); ?>" target="_blank"><?php esc_html_e('Get Bundle', 'wedding-planner-firm'); ?></a>
	        </p>
	        <p class="dismiss-link"><strong><a href="?wedding_planner_firm_admin_notice=1"><?php esc_html_e( 'Dismiss', 'wedding-planner-firm' ); ?></a></strong></p>
	    </div>
	    <?php

	}?>
	    <?php

	}
}

add_action( 'admin_notices', 'wedding_planner_firm_admin_notice' );

if( ! function_exists( 'wedding_planner_firm_update_admin_notice' ) ) :
/**
 * Updating admin notice on dismiss
*/
function wedding_planner_firm_update_admin_notice(){
    if ( isset( $_GET['wedding_planner_firm_admin_notice'] ) && $_GET['wedding_planner_firm_admin_notice'] = '1' ) {
        update_option( 'wedding_planner_firm_admin_notice', true );
    }
}
endif;
add_action( 'admin_init', 'wedding_planner_firm_update_admin_notice' );

//After Switch theme function
add_action('after_switch_theme', 'wedding_planner_firm_getstart_setup_options');
function wedding_planner_firm_getstart_setup_options () {
    update_option('wedding_planner_firm_admin_notice', FALSE );
}
add_action('admin_bar_menu', 'your_plugin_adminbar_link', 100);
function your_plugin_adminbar_link($wp_admin_bar) {
    $wp_admin_bar->add_node([
        'id'    => 'yourplugin_upgrade',
        'title' => ' Upgrade to Pro',
        'href'  => 'https://www.cretathemes.com/products/wedding-wordpress-theme',
        'meta'  => array(
            'target' => '_blank',
        )
    ]);
}