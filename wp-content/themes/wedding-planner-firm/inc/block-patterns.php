<?php
/**
 * Block Patterns
 *
 * @package wedding_planner_firm
 * @since 1.0
 */

function wedding_planner_firm_register_block_patterns() {
	$wedding_planner_firm_block_pattern_categories = array(
		'wedding-planner-firm' => array( 'label' => esc_html__( 'Wedding Planner Firm', 'wedding-planner-firm' ) ),
		'pages' => array( 'label' => esc_html__( 'Pages', 'wedding-planner-firm' ) ),
	);

	$wedding_planner_firm_block_pattern_categories = apply_filters( 'wedding_planner_firm_wedding_planner_firm_block_pattern_categories', $wedding_planner_firm_block_pattern_categories );

	foreach ( $wedding_planner_firm_block_pattern_categories as $name => $properties ) {
		if ( ! WP_Block_Pattern_Categories_Registry::get_instance()->is_registered( $name ) ) {
			register_block_pattern_category( $name, $properties );
		}
	}
}
add_action( 'init', 'wedding_planner_firm_register_block_patterns', 9 );