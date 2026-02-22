<?php
/**
 * Wedding Planner Firm: Customizer
 *
 * @package Wedding Planner Firm
 * @subpackage wedding_planner_firm
 */

/**
 * Singleton class for handling the theme's customizer integration.
 *
 * @since  1.0.0
 * @access public
 */
final class Wedding_Planner_Firm_Customize {

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self;
			$instance->setup_actions();
		}

		return $instance;
	}

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Sets up initial actions.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function setup_actions() {

		// Register panels, sections, settings, controls, and partials.
		add_action( 'customize_register', array( $this, 'sections' ) );

		// Register scripts and styles for the controls.
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_control_scripts' ), 0 );
	}

	/**
	 * Sets up the customizer sections.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object  $manager
	 * @return void
	 */
	public function sections( $manager ) {

		// Load custom sections.
		load_template( trailingslashit( get_template_directory() ) . '/inc/section-pro.php' );

		// Register custom section types.
		$manager->register_section_type( 'Wedding_Planner_Firm_Customize_Section_Pro' );

		// Register sections.
		$manager->add_section(
			new Wedding_Planner_Firm_Customize_Section_Pro(
				$manager,
				'wedding_planner_firm_section_pro',
				array(
					'priority'   => 9,
					'title'    => esc_html__( 'Wedding Planner Firm Pro', 'wedding-planner-firm' ),
					'pro_text' => esc_html__( 'GET PRO', 'wedding-planner-firm' ),
					'pro_url'  => esc_url( 'https://www.cretathemes.com/products/wedding-wordpress-theme', 'wedding-planner-firm' ),
				)
			)
		);

	}

	/**
	 * Loads theme customizer CSS.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue_control_scripts() {

		wp_enqueue_script( 'wedding-planner-firm-customize-controls', trailingslashit( esc_url( get_template_directory_uri() ) ) . '/assets/js/customize-controls.js', array( 'customize-controls' ) );

		wp_enqueue_style( 'wedding-planner-firm-customize-controls', trailingslashit( esc_url( get_template_directory_uri() ) ) . '/assets/css/customize-controls.css' );
	}
}

// Doing this customizer thang!
Wedding_Planner_Firm_Customize::get_instance();