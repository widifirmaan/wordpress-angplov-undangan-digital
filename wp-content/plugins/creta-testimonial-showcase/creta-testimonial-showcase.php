<?php
 /**
 * Plugin Name: Creta Testimonial Showcase
 * Plugin URI: 
 * Description: Easily display beautiful testimonials using Gutenberg blocks. Customize layout, colors, and animation for maximum engagement.
 * Version: 1.2.4
 * Requires at least: 5.0
 * Author: cretathemes
 * Author URI: https://www.cretathemes.com/
 * Text Domain: creta-testimonial-showcase
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires PHP: 7.2
 */
if (!defined( 'ABSPATH' )) {
    exit; 
}
define('CRETATS_PATH',plugin_dir_path(__FILE__));
define('CRETATS_URL', plugin_dir_url(__FILE__));
define('CRETATS_DUMMYIMG_URL', plugin_dir_url(__FILE__). 'assets/img/dummy-img.png');
define('CRETATS_THEME_BUNDLE_IMAGE_URL', plugin_dir_url(__FILE__). 'assets/img/get-theme-bundle-img.png');
define('CRETATS_THEME_BUNDLE_UPPER_IMAGE_URL', plugin_dir_url(__FILE__). 'assets/img/bundle-banner-top-img.png');
define('CRETATS_ELEMENTO_API_BASE', 'https://license.cretathemes.com/api/public');
define('CRETATS_ELEMENTO_API_MAIN', 'https://www.cretathemes.com/');
define('CRETATS_VERSION', '1.2.4');

require_once CRETATS_PATH .'includes/post-types/register-testimonial.php';
include_once CRETATS_PATH . 'includes/shortcodes/testimonial-shortcode.php';
include_once CRETATS_PATH . 'includes/function.php';

register_activation_hook(__FILE__, 'cretats_plugin_activation_hook');
function cretats_plugin_activation_hook() {
    update_option('cretats_show_activation_popup', true);
}

add_action( 'wp_login', 'cretats_user_login_hook', 10, 2 );
function cretats_user_login_hook( $user_login, $user ) {
  update_option( 'cretats_show_activation_popup', true );
}


add_action('admin_footer', 'cretats_custom_popup_html');
function cretats_custom_popup_html() {
    if (!get_option('cretats_show_activation_popup')) return;
    ?>
    <div id="cretats-popup-overlay">
      <div id="cretats-popup-content">
        <span class="dashicons dashicons-plus-alt2 cretats-popup-dismiss"></span>
        <img src="<?php echo esc_url(CRETATS_THEME_BUNDLE_IMAGE_URL); ?>" alt="Bundle Image">
        <h2><?php echo esc_html('Get Premium Themes Starting From $39'); ?></h2>
        <div class="cretats-popup-wrap">
          <a href="admin.php?page=cretats-theme-showcase" class="button button-primary cretats-popup-template-btn"><?php echo esc_html('View Premium Templates'); ?></a>
          <a href="<?php echo esc_url( CRETATS_ELEMENTO_API_MAIN ) . 'products/wordpress-theme-bundle'; ?>" target="_blank" class="button button-primary cretats-popup-bundle-btn"><?php echo esc_html('Get Theme Bundle'); ?></a>
        </div>
      </div>
    </div>
    <?php
}

function cretats_enqueue_admin_inline_styles( $hook ) {
    global $post_type;
    
    if ( (in_array( $hook, [ 'post.php', 'post-new.php', 'edit.php' ] ) && in_array( $post_type, [ 'cretats_testimonial', 'cretats_tms_sc' ] )) || $hook == 'toplevel_page_cretats-theme-showcase'  ) {
      wp_enqueue_style( 'cretats_admin_inline', CRETATS_URL . 'assets/css/admin-inline.css', [], CRETATS_VERSION);
    }
}
add_action( 'admin_enqueue_scripts', 'cretats_enqueue_admin_inline_styles' );
                                       
if(is_admin()){
    require_once CRETATS_PATH .'admin/class-admin-init.php';
    require_once CRETATS_PATH . 'admin/admin-functions.php';
    require_once CRETATS_PATH . 'admin/class-meta-boxes.php';
    require_once CRETATS_PATH . 'admin/class-meta-shortcode.php';
    require_once CRETATS_PATH . 'admin/themes-page/themes-page.php';
} else {
    require_once CRETATS_PATH . 'public/class-public-init.php';
}

add_action('admin_notices', function () { ?>
  <div class="notice notice-success is-dismissible creta-theme-bundle-banner">
    <div class="bundle-row">
      <div class="bundle-box first-box">
        <h3 class="bundle-heading"><?php echo esc_html('WordPress Theme Bundle'); ?></h3>
        <p class="bundle-subtext"><?php echo esc_html('Collection of 60+ Premium Block Themes'); ?></p>
      </div>
      <div class="bundle-box second-box">
      <p class="bundle-promo-text text-bold"><?php echo esc_html('SUMMER SALE: '); ?><span class="banner-highlight-off"><?php echo esc_html('Extra 20% OFF'); ?></span><?php echo esc_html(' on WordPress Theme Bundle Use Code: '); ?><span class="banner-highlight-code"><?php echo esc_html('“HEAT20”'); ?></span></p>
      <a class="bundle-btn" target="_blank" href="<?php echo esc_url( CRETATS_ELEMENTO_API_MAIN ) . 'products/wordpress-theme-bundle'; ?>"><?php echo esc_html('Get Theme Bundle For '); ?><span class="strike-price"><?php echo esc_html('$86'); ?></span><?php echo esc_html(' $68'); ?></a>
      </div>
      <div class="bundle-box third-box">
        <img src="<?php echo esc_url( CRETATS_THEME_BUNDLE_UPPER_IMAGE_URL )?>" alt="Theme Bundle Preview" class="bundle-img">
      </div>
    </div>
  </div>
<?php });