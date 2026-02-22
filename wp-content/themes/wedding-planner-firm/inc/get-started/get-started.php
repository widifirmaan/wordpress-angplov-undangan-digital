<?php
add_action( 'admin_menu', 'wedding_planner_firm_getting_started' );
function wedding_planner_firm_getting_started() {
	add_theme_page( esc_html__('Get Started', 'wedding-planner-firm'), esc_html__('Get Started', 'wedding-planner-firm'), 'edit_theme_options', 'wedding-planner-firm-guide-page', 'wedding_planner_firm_test_guide');
}

// Add a Custom CSS file to WP Admin Area
function wedding_planner_firm_admin_theme_style() {
   wp_enqueue_style('custom-admin-style', esc_url(get_template_directory_uri()) . '/inc/get-started/get-started.css');
   wp_enqueue_script( 'admin-notice-script', get_template_directory_uri() . '/get-started/js/admin-notice-script.js', array( 'jquery' ) );
}
add_action('admin_enqueue_scripts', 'wedding_planner_firm_admin_theme_style');

//guidline for about theme
function wedding_planner_firm_test_guide() { 
	//custom function about theme customizer
	$return = add_query_arg( array()) ;
	$theme = wp_get_theme( 'wedding-planner-firm' );
?>
	<div class="wrapper-outer">
		<div class="left-main-box">
			<div class="intro"><h3><?php echo esc_html( $theme->Name ); ?></h3></div>
			<div class="left-inner">
				<div class="about-wrapper">
					<div class="col-left">
						<p><?php echo esc_html( $theme->get( 'Description' ) ); ?></p>
					</div>
					<div class="col-right">
						<img role="img" src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/get-started/images/screenshot.jpg" alt="" />
					</div>
				</div>
				<div class="link-wrapper">
					<h4><?php esc_html_e('Important Links', 'wedding-planner-firm'); ?></h4>
					<div class="link-buttons">
						<a href="<?php echo esc_url( WEDDING_PLANNER_FIRM_THEME_DOC ); ?>" target="_blank"><?php esc_html_e('Free Setup Guide', 'wedding-planner-firm'); ?></a>
						<a href="<?php echo esc_url( WEDDING_PLANNER_FIRM_SUPPORT ); ?>" target="_blank"><?php esc_html_e('Support Forum', 'wedding-planner-firm'); ?></a>
						<a href="<?php echo esc_url( WEDDING_PLANNER_FIRM_PRO_DEMO ); ?>" target="_blank"><?php esc_html_e('Live Demo', 'wedding-planner-firm'); ?></a>
						<a href="<?php echo esc_url( WEDDING_PLANNER_FIRM_PRO_THEME_DOC ); ?>" target="_blank"><?php esc_html_e('Pro Setup Guide', 'wedding-planner-firm'); ?></a>
					</div>
				</div>
				<div class="support-wrapper">
					<div class="editor-box">
						<i class="dashicons dashicons-admin-appearance"></i>
						<h4><?php esc_html_e('Theme Customization', 'wedding-planner-firm'); ?></h4>
						<p><?php esc_html_e('Effortlessly modify & maintain your site using editor.', 'wedding-planner-firm'); ?></p>
						<div class="support-button">
							<a class="button button-primary" href="<?php echo esc_url( admin_url( 'site-editor.php' ) ); ?>" target="_blank"><?php esc_html_e('Site Editor', 'wedding-planner-firm'); ?></a>
						</div>
					</div>
					<div class="support-box">
						<i class="dashicons dashicons-microphone"></i>
						<h4><?php esc_html_e('Need Support?', 'wedding-planner-firm'); ?></h4>
						<p><?php esc_html_e('Go to our support forum to help you in case of queries.', 'wedding-planner-firm'); ?></p>
						<div class="support-button">
							<a class="button button-primary" href="<?php echo esc_url( WEDDING_PLANNER_FIRM_SUPPORT ); ?>" target="_blank"><?php esc_html_e('Get Support', 'wedding-planner-firm'); ?></a>
						</div>
					</div>
					<div class="review-box">
						<i class="dashicons dashicons-star-filled"></i>
						<h4><?php esc_html_e('Leave Us A Review', 'wedding-planner-firm'); ?></h4>
						<p><?php esc_html_e('Are you enjoying Our Theme? We would Love to hear your Feedback.', 'wedding-planner-firm'); ?></p>
						<div class="support-button">
							<a class="button button-primary" href="<?php echo esc_url( WEDDING_PLANNER_FIRM_REVIEW ); ?>" target="_blank"><?php esc_html_e('Rate Us', 'wedding-planner-firm'); ?></a>
						</div>
					</div>
				</div>
			</div>
			<div class="go-premium-box">
				<h4><?php esc_html_e('Why Go For Premium?', 'wedding-planner-firm'); ?></h4>
				<ul class="pro-list">
					<li><?php esc_html_e('Advanced Customization Options', 'wedding-planner-firm');?></li>
					<li><?php esc_html_e('One-Click Demo Import', 'wedding-planner-firm');?></li>
					<li><?php esc_html_e('WooCommerce Integration & Enhanced Features', 'wedding-planner-firm');?></li>
					<li><?php esc_html_e('Performance Optimization & SEO-Ready', 'wedding-planner-firm');?></li>
					<li><?php esc_html_e('Premium Support & Regular Updates', 'wedding-planner-firm');?></li>
				</ul>
			</div>
		</div>
		<div class="right-main-box">
			<div class="right-inner">
				<div class="pro-boxes">
					<h4><?php esc_html_e('Get Theme Bundle', 'wedding-planner-firm'); ?></h4>
					<p><?php esc_html_e('60+ Premium WordPress Themes', 'wedding-planner-firm'); ?></p>
					<p class="main-bundle-price" ><strong class="cancel-bundle-price"><?php esc_html_e('$2340', 'wedding-planner-firm'); ?></strong><span class="bundle-price"><?php esc_html_e('$86', 'wedding-planner-firm'); ?></span></p>
					<img role="img" src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/get-started/images/bundle.png" alt="bundle image" />
					<p><?php esc_html_e('SUMMER SALE: ', 'wedding-planner-firm'); ?><strong><?php esc_html_e('Extra 20%', 'wedding-planner-firm'); ?></strong><?php esc_html_e(' OFF on WordPress Theme Bundle Use Code: ', 'wedding-planner-firm'); ?><strong><?php esc_html_e('“HEAT20”', 'wedding-planner-firm'); ?></strong></p>
					<a href="<?php echo esc_url( WEDDING_PLANNER_FIRM_PRO_THEME_BUNDLE ); ?>" target="_blank"><?php esc_html_e('Get Theme Bundle For ', 'wedding-planner-firm'); ?><span><?php esc_html_e('$86', 'wedding-planner-firm'); ?></a>
				</div>
				<div class="pro-boxes pro-theme-container">
					<h4><?php esc_html_e('Wedding Planner Firm Pro', 'wedding-planner-firm'); ?></h4>
					<p class="pro-theme-price" ><?php esc_html_e('$39', 'wedding-planner-firm'); ?></p>
					<img role="img" src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/get-started/images/premium.png" alt="premium image" />
					<p><?php esc_html_e('SUMMER SALE: ', 'wedding-planner-firm'); ?><strong><?php esc_html_e('Extra 25%', 'wedding-planner-firm'); ?></strong><?php esc_html_e(' OFF on WordPress Block Themes! Use Code: ', 'wedding-planner-firm'); ?><strong><?php esc_html_e('“SUMMER25”', 'wedding-planner-firm'); ?></strong></p>
					<a href="<?php echo esc_url( WEDDING_PLANNER_FIRM_BUY_NOW ); ?>" target="_blank"><?php esc_html_e('Upgrade To Pro At Just at $29.25', 'wedding-planner-firm'); ?></a>
				</div>
				<div class="pro-boxes last-pro-box">
					<h4><?php esc_html_e('View All Our Themes', 'wedding-planner-firm'); ?></h4>
					<img role="img" src="<?php echo esc_url(get_template_directory_uri()); ?>/inc/get-started/images/all-themes.png" alt="all themes image" />
					<a href="<?php echo esc_url( WEDDING_PLANNER_FIRM_PRO_ALL_THEMES ); ?>" target="_blank"><?php esc_html_e('View All Our Premium Themes', 'wedding-planner-firm'); ?></a>
				</div>
			</div>
		</div>
	</div>
<?php } ?>