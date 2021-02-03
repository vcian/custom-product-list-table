<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Uninstall
 *
 * Does delete the created tables and all the plugin options
 * when uninstalling the plugin
 *
 * @packageCategory List Table
 * @since 1.0.0
 */

// check if the plugin really gets uninstalled 
if( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
	exit();
	
	//delete custom main post data
	$post_types = array( 'ww_pro_post' );
	
	foreach ( $post_types as $post_type ) {
		$args = array( 'post_type' => $post_type, 'post_status' => 'any', 'numberposts' => '-1' );
		$all_posts = get_posts( $args );
		foreach ( $all_posts as $post ) {
			wp_delete_post( $post->ID, true);
		}
	}
	
	//delete taxonomies data (categories)
	$gettaxs = get_terms('ww_pro_category');
	foreach ($gettaxs as $terms) {
		wp_delete_term($terms->term_id,'ww_pro_category');
	}
?>