<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

//creating custom post type
add_action( 'init', 'ww_clt_reg_create_post_type'); //creating custom post

/**
 * Register Post Type
 *
 * Register Custom Post Type for managing registered taxonomy
 *
 * @package Category List Table
 * @since 1.0.0
 */
function ww_clt_reg_create_post_type() {
	
	$labels = array(
					    'name'				=> __('Product Categories','wwclt'),
					    'singular_name' 	=> __('Category','wwclt'),
					    'add_new' 			=> __('Add New','wwclt'),
					    'add_new_item' 		=> __('Add New Category','wwclt'),
					    'edit_item' 		=> __('Edit Category','wwclt'),
					    'new_item' 			=> __('New Category','wwclt'),
					    'all_items' 		=> __('All Categories','wwclt'),
					    'view_item' 		=> __('View Category','wwclt'),
					    'search_items' 		=> __('Search Category','wwclt'),
					    'not_found' 		=> __('No categories found','wwclt'),
					    'not_found_in_trash'=> __('No categories found in Trash','wwclt'),
					    'parent_item_colon' => '',
					    'menu_name' => __('Categories','wwclt'),
					);
	$args = array(
				    'labels' => $labels,
				    'public' => false,
				    'query_var' => false,
				    'rewrite' => false,//array( 'slug' => WW_CLT_POST_TYPE ),
				    'capability_type' => WW_CLT_POST_TYPE,
				    'hierarchical' => false,
				    'supports' => array( 'title')
				    
				 
			  ); 
	
	register_post_type( WW_CLT_POST_TYPE,$args);
}

//add categories add/update/delete of wordpress without any extra tables
add_action('init','ww_clt_reg_taxonomy');

/**
 * Register Category/Taxonomy
 *
 * Register Category like wordpress
 *
 * @packageCategory List Table
 * @since 1.0.0
 */
function ww_clt_reg_taxonomy() {
				
	// Add new taxonomy, make it hierarchical (like categories)
			$labels = array(
						    'name' => _x( 'Category', 'taxonomy general name','wwclt' ),
						    'singular_name' => _x( 'Category', 'taxonomy singular name','wwclt' ),
						    'search_items' =>  __( 'Search Category' ,'wwclt'),
						    'all_items' => __( 'All Categories' ,'wwclt'),
						    'parent_item' => __( 'Parent Category' ),
						    'parent_item_colon' => __( 'Parent Category:','wwclt' ),
						    'edit_item' => __( 'Edit Category' ,'wwclt'), 
						    'update_item' => __( 'Update Category','wwclt' ),
						    'add_new_item' => __( 'Add New Category','wwclt' ),
						    'new_item_name' => __( 'New Category Name','wwclt' ),
						    'menu_name' => __( 'Categories','wwclt' ),
			  ); 
	$args = array(
				    'hierarchical' => true,
				    'labels' => $labels,
				    'show_ui' => true,
				    'show_admin_column' => true,
				    'query_var' => true,
				    'rewrite' => false//array( 'slug' => WW_CLT_POST_TYPE )
				   );
			
	register_taxonomy(WW_CLT_TAXONOMY,array(WW_CLT_POST_TYPE), $args);
	
}
?>