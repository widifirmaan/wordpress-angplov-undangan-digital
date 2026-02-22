<?php
/**
 * Block Filters
 *
 * @package wedding_planner_firm
 * @since 1.0
 */

function wedding_planner_firm_block_wrapper( $wedding_planner_firm_block_content, $wedding_planner_firm_block ) {

	if ( 'core/button' === $wedding_planner_firm_block['blockName'] ) {
		
		if( isset( $wedding_planner_firm_block['attrs']['className'] ) && strpos( $wedding_planner_firm_block['attrs']['className'], 'has-arrow' ) ) {
			$wedding_planner_firm_block_content = str_replace( '</a>', wedding_planner_firm_get_svg( array( 'icon' => esc_attr( 'caret-circle-right' ) ) ) . '</a>', $wedding_planner_firm_block_content );
			return $wedding_planner_firm_block_content;
		}
	}

	if( ! is_single() ) {
	
		if ( 'core/post-terms'  === $wedding_planner_firm_block['blockName'] ) {
			if( 'post_tag' === $wedding_planner_firm_block['attrs']['term'] ) {
				$wedding_planner_firm_block_content = str_replace( '<div class="taxonomy-post_tag wp-block-post-terms">', '<div class="taxonomy-post_tag wp-block-post-terms flex">' . wedding_planner_firm_get_svg( array( 'icon' => esc_attr( 'tags' ) ) ), $wedding_planner_firm_block_content );
			}

			if( 'category' ===  $wedding_planner_firm_block['attrs']['term'] ) {
				$wedding_planner_firm_block_content = str_replace( '<div class="taxonomy-category wp-block-post-terms">', '<div class="taxonomy-category wp-block-post-terms flex">' . wedding_planner_firm_get_svg( array( 'icon' => esc_attr( 'category' ) ) ), $wedding_planner_firm_block_content );
			}
			return $wedding_planner_firm_block_content;
		}
		if ( 'core/post-date' === $wedding_planner_firm_block['blockName'] ) {
			$wedding_planner_firm_block_content = str_replace( '<div class="wp-block-post-date">', '<div class="wp-block-post-date flex">' . wedding_planner_firm_get_svg( array( 'icon' => esc_attr( 'calendar' ) ) ), $wedding_planner_firm_block_content );
			return $wedding_planner_firm_block_content;
		}
		if ( 'core/post-author' === $wedding_planner_firm_block['blockName'] ) {
			$wedding_planner_firm_block_content = str_replace( '<div class="wp-block-post-author">', '<div class="wp-block-post-author flex">' . wedding_planner_firm_get_svg( array( 'icon' => esc_attr( 'user' ) ) ), $wedding_planner_firm_block_content );
			return $wedding_planner_firm_block_content;
		}
	}
	if( is_single() ){

		// Add chevron icon to the navigations
		if ( 'core/post-navigation-link' === $wedding_planner_firm_block['blockName'] ) {
			if( isset( $wedding_planner_firm_block['attrs']['type'] ) && 'previous' === $wedding_planner_firm_block['attrs']['type'] ) {
				$wedding_planner_firm_block_content = str_replace( '<span class="post-navigation-link__label">', '<span class="post-navigation-link__label">' . wedding_planner_firm_get_svg( array( 'icon' => esc_attr( 'prev' ) ) ), $wedding_planner_firm_block_content );
			}
			else {
				$wedding_planner_firm_block_content = str_replace( '<span class="post-navigation-link__label">Next Post', '<span class="post-navigation-link__label">Next Post' . wedding_planner_firm_get_svg( array( 'icon' => esc_attr( 'next' ) ) ), $wedding_planner_firm_block_content );
			}
			return $wedding_planner_firm_block_content;
		}
		if ( 'core/post-date' === $wedding_planner_firm_block['blockName'] ) {
            $wedding_planner_firm_block_content = str_replace( '<div class="wp-block-post-date">', '<div class="wp-block-post-date flex">' . wedding_planner_firm_get_svg( array( 'icon' => 'calendar' ) ), $wedding_planner_firm_block_content );
            return $wedding_planner_firm_block_content;
        }
		if ( 'core/post-author' === $wedding_planner_firm_block['blockName'] ) {
            $wedding_planner_firm_block_content = str_replace( '<div class="wp-block-post-author">', '<div class="wp-block-post-author flex">' . wedding_planner_firm_get_svg( array( 'icon' => 'user' ) ), $wedding_planner_firm_block_content );
            return $wedding_planner_firm_block_content;
        }

	}
    return $wedding_planner_firm_block_content;
}
	
add_filter( 'render_block', 'wedding_planner_firm_block_wrapper', 10, 2 );
