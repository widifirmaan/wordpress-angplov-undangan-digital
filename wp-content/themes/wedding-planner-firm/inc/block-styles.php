<?php
/**
 * Block Styles
 *
 * @package wedding_planner_firm
 * @since 1.0
 */

if ( function_exists( 'register_block_style' ) ) {
	function wedding_planner_firm_register_block_styles() {

		//Wp Block Padding Zero
		register_block_style(
			'core/group',
			array(
				'name'  => 'wedding-planner-firm-padding-0',
				'label' => esc_html__( 'No Padding', 'wedding-planner-firm' ),
			)
		);

		//Wp Block Post Author Style
		register_block_style(
			'core/post-author',
			array(
				'name'  => 'wedding-planner-firm-post-author-card',
				'label' => esc_html__( 'Theme Style', 'wedding-planner-firm' ),
			)
		);

		//Wp Block Button Style
		register_block_style(
			'core/button',
			array(
				'name'         => 'wedding-planner-firm-button',
				'label'        => esc_html__( 'Plain', 'wedding-planner-firm' ),
			)
		);

		//Post Comments Style
		register_block_style(
			'core/post-comments',
			array(
				'name'         => 'wedding-planner-firm-post-comments',
				'label'        => esc_html__( 'Theme Style', 'wedding-planner-firm' ),
			)
		);

		//Latest Comments Style
		register_block_style(
			'core/latest-comments',
			array(
				'name'         => 'wedding-planner-firm-latest-comments',
				'label'        => esc_html__( 'Theme Style', 'wedding-planner-firm' ),
			)
		);


		//Wp Block Table Style
		register_block_style(
			'core/table',
			array(
				'name'         => 'wedding-planner-firm-wp-table',
				'label'        => esc_html__( 'Theme Style', 'wedding-planner-firm' ),
			)
		);


		//Wp Block Pre Style
		register_block_style(
			'core/preformatted',
			array(
				'name'         => 'wedding-planner-firm-wp-preformatted',
				'label'        => esc_html__( 'Theme Style', 'wedding-planner-firm' ),
			)
		);

		//Wp Block Verse Style
		register_block_style(
			'core/verse',
			array(
				'name'         => 'wedding-planner-firm-wp-verse',
				'label'        => esc_html__( 'Theme Style', 'wedding-planner-firm' ),
			)
		);
	}
	add_action( 'init', 'wedding_planner_firm_register_block_styles' );
}
