<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Save products
 *
 * Handle product save and edit products
 * 
 * @packageCategory List Table
 * @since 1.0.0
 */
	global $errmsg, $wpdb, $user_ID,$ww_clt_model,$error;

	$model = $ww_clt_model;
	$prefix = WW_CLT_META_PREFIX;
	// save for product data
	if(isset($_POST['ww_clt_product_save']) && !empty($_POST['ww_clt_product_save'])) { //check submit button click

		$error = '';
		
		if(isset($_POST['ww_clt_product_title']) && empty($_POST['ww_clt_product_title'])) { //check product title
			
			$errmsg['product_title'] = __('Please Enter Product title.','wwclt');
			$error = true;
		}
		if(isset($_POST['ww_clt_product_desc']) && empty($_POST['ww_clt_product_desc'])) { //check product content
			
			$errmsg['product_desc'] = __('Please Enter Product description.','wwclt');
			$error = true;
		}
		
		/*if(isset($_POST['ww_clt_product_cat']) && empty($_POST['ww_clt_product_cat'])) { //check product category
			
			$errmsg['product_cat'] = __('Please Select Product Category.','wwclt');
			$error = true;
		}*/
		if(isset($_POST['ww_clt_product_avail']) && !empty($_POST['ww_clt_product_avail'])) { //check product availibility
			
			$ww_clt_available = implode(',', $_POST['ww_clt_product_avail']);
			
		}
		
		if(isset($_GET['clt_id']) && !empty($_GET['clt_id']) && $error != true) { //check no error and product id is set in url
			
			$postid = $_GET['clt_id'];
			
			//data needs to update for product
			$update_post = array(
									'ID'			=>  $postid,
									'post_title'    =>	$_POST['ww_clt_product_title'],
									'post_content'  =>	$_POST['ww_clt_product_desc'],
									'post_status'   =>	'publish',
									'post_author'   =>	$user_ID,
								);
			
			//update product data
			wp_update_post( $model->ww_clt_escape_slashes_deep($update_post));
			
			if(isset($_POST['ww_clt_product_cat']) && !empty($_POST['ww_clt_product_cat'])) {
				
				$cat_ids = array($_POST['ww_clt_product_cat']);
				$cat_ids = array_map('intval', $cat_ids); // to make sure the terms IDs is integers:
				$cat_ids = array_unique( $cat_ids );
				
			} else {
				$cat_ids = NULL;
			}
			
			wp_set_object_terms( $postid, $cat_ids, WW_CLT_TAXONOMY );
			
			//update_post_meta( $postid, $prefix.'product_cat',$_POST['ww_clt_product_cat']);
			update_post_meta( $postid, $prefix.'product_avail',isset($_POST['ww_clt_product_avail']) ? $_POST['ww_clt_product_avail'] : array());
			update_post_meta( $postid, $prefix.'featured_product',$_POST['ww_clt_featured_product']);
			update_post_meta( $postid, $prefix.'product_status',$_POST['ww_clt_product_status']);
			
			// get redirect url
			$redirect_url = add_query_arg( array( 'page' => 'ww_clt_products', 'message' => '2' ), admin_url( 'admin.php' ) );
			wp_redirect( $redirect_url );
			exit;
			
		} else {
		
			if($error != true) { //check there is no error then insert data in to the table
			
				// Create post object
				$product_arr = array(
									  'post_title'    =>	$_POST['ww_clt_product_title'],
									  'post_content'  =>	$_POST['ww_clt_product_desc'],
									  'post_status'   =>	'publish',
									  'post_author'   =>	$user_ID,
									  'post_type'     =>	WW_CLT_POST_TYPE
									);
				
				// Insert the post into the database
				$result = wp_insert_post( $model->ww_clt_escape_slashes_deep($product_arr) );
				
				if($result) { //check inserted product id
					
					//save category
					if(isset($_POST['ww_clt_product_cat']) && !empty($_POST['ww_clt_product_cat'])) {
				
						$cat_ids = array($_POST['ww_clt_product_cat']);
						$cat_ids = array_map('intval', $cat_ids); // to make sure the terms IDs is integers:
						$cat_ids = array_unique( $cat_ids );
						
					} else {
						$cat_ids = NULL;
					}
					
					wp_set_object_terms( $result, $cat_ids, WW_CLT_TAXONOMY );
					
					update_post_meta( $result, $prefix.'product_avail',isset($_POST['ww_clt_product_avail']) ? $_POST['ww_clt_product_avail'] : array());
					update_post_meta( $result, $prefix.'featured_product',$_POST['ww_clt_featured_product']);
					update_post_meta( $result, $prefix.'product_status',$_POST['ww_clt_product_status']);
				
					// get redirect url
					$redirect_url = add_query_arg( array( 'page' => 'ww_clt_products', 'message' => '1' ), admin_url( 'admin.php' ) );
					wp_redirect( $redirect_url );
					exit;
					
				}
			}
		}
	}
?>