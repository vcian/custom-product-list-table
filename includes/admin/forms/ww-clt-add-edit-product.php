<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Add/Edit product
 *
 * Handle Add / Edit product
 * 
 * @packageCategory List Table
 * @since 1.0.0
 */

	global $ww_clt_model, $errmsg, $error; //make global for error message to showing errors
	
	$model = $ww_clt_model;
	$prefix = WW_CLT_META_PREFIX;	
	
	//set default value as blank for all fields
	//preventing notice and warnings
	$data = array( 
					'ww_clt_product_title'		=>	'',
					'ww_clt_product_desc' 		=>	'',
					'ww_clt_product_cat' 		=>	'', 
					'ww_clt_product_avail' 		=>	array(), 
					'ww_clt_featured_product'	=>	'0',
					'ww_clt_product_color'		=>	'',
					'ww_clt_product_status'		=>	'0'
				);
	
	if(isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['clt_id']) && !empty($_GET['clt_id'])) { //check action & id is set or not
		
		//product page title
		$product_lable = __('Edit Product', 'wwclt');
		
		//product page submit button text either it is Add or Update
		$product_btn = __('Update', 'wwclt');
		
		//get the product id from url to update the data and get the data of product to fill in editable fields
		$post_id = $_GET['clt_id'];
		
		//get the data from product id
		$getpost = get_post( $post_id );
		if($error != true) { //if error is not occured then fill with database values
			//assign retrived data to current page fields data to show filled in fields
			$data['ww_clt_product_title'] = $getpost->post_title;
			$data['ww_clt_product_desc'] = $getpost->post_content;
			//$data['ww_clt_product_cat'] = get_post_meta($post_id,$prefix.'product_cat',true);
			$data['ww_clt_product_status'] = get_post_meta($post_id,$prefix.'product_status',true);
			$data['ww_clt_product_avail'] = get_post_meta($post_id,$prefix.'product_avail',true);
			$data['ww_clt_featured_product'] = get_post_meta($post_id,$prefix.'featured_product',true);
			
			$product_terms = wp_get_object_terms($post_id, WW_CLT_TAXONOMY);
			$data['ww_clt_product_cat']	= !empty($product_terms[0]->term_id) ? $product_terms[0]->term_id : '';
		} else {
			$data = $_POST;
		}		 
		
	} else {
		
		//product page title
		$product_lable = __('Add New Product', 'wwclt');
		
		//product page submit button text either it is Add or Update
		$product_btn = __('Save', 'wwclt');
		
		//if when error occured then assign $_POST to be field fields with none error fields
		if($_POST) { //check if $_POST is set then set all $_POST values
			$data = $_POST;
		}
	}	
	
	//when product availablity array is null then after submitting then assign with blank array
	if (empty($data['ww_clt_product_avail'])) { //check if product avail is empty
		$data['ww_clt_product_avail'] = array();
	}
?>

	<div class="wrap">
		<?php echo screen_icon('options-general'); ?>
	
		<h2> <?php echo __( $product_lable , 'wwclt'); ?>	
			<a class="add-new-h2" href="admin.php?page=ww_clt_products"><?php echo __('Back to List','wwclt') ?></a>
		</h2>
	
	<!-- beginning of the product meta box -->

		<div id="ww-clt-product" class="post-box-container">
		
			<div class="metabox-holder">	
		
				<div class="meta-box-sortables ui-sortable">
		
					<div id="product" class="postbox">	
		
						<div class="handlediv" title="<?php echo __( 'Click to toggle', 'wwclt' ) ?>"><br />
						</div>
		
						<!-- product box title -->				
						<h3 class="hndle">				
							<span style="vertical-align: top;"><?php echo __( $product_lable, 'wwclt' ) ?></span>				
						</h3>
		
						<div class="inside">
					
							<form action="" method="POST" id="ww-clt-add-edit-form">
								<input type="hidden" name="page" value="ww_clt_add_form" />
								
								<div id="ww-clt-require-message">
									<strong>(</strong> <span class="ww-clt-require">*</span> <strong>)<?php echo __( 'Required fields', 'wwclt' ) ?></strong>
								</div>
								
								<table class="form-table ww-clt-product-box"> 
									<tbody>
														
										<tr>
											<th scope="row">
												<label>
													<strong><?php echo __( 'Title:', 'wwclt' ) ?></strong>
													<span class="ww-clt-require"> * </span>
												</label>
											</th>
											<td><input type="text" id="ww_clt_product_title" name="ww_clt_product_title" value="<?php echo $model->ww_clt_escape_attr($data['ww_clt_product_title']) ?>" class="large-text"/><br />
												<span class="description"><?php echo __( 'Enter the product title.', 'wwclt' ) ?></span>
											</td>
											<td class="ww-clt-product-error">
												<?php
												if(isset($errmsg['product_title']) && !empty($errmsg['product_title'])) { //check error message for product title
													echo '<div>'.$errmsg['product_title'].'</div>';
												}
												?>
											</td>
										 </tr>										
								
										<tr>
											<th scope="row">
												<label>
													<strong><?php echo __( 'Description:', 'wwclt' ) ?></strong>
													<span class="ww-clt-require"> * </span>
												</label>
											</th>
											<td  width="35%">
												<textarea id="ww_clt_product_desc" name="ww_clt_product_desc" rows="4" class="large-text"><?php echo $model->ww_clt_escape_attr($data['ww_clt_product_desc']) ?></textarea><br />
												<span class="description"><?php echo __( 'Enter the product description.', 'wwclt' ) ?></span>
											</td>
											<td class="ww-clt-product-error">
												<?php
												if(isset($errmsg['product_desc']) && !empty($errmsg['product_desc'])) { //check error message for product content
													echo '<div>'.$errmsg['product_desc'].'</div>';
												}
												?>
											</td>
										</tr>										
								
										<tr>
											<th scope="row">
												<label>
													<strong><?php echo __( 'Status:', 'wwclt' ) ?></strong>
												</label>
											</th>
											<td  width="35%">
												<select id="ww_clt_product_status" name="ww_clt_product_status">
													<?php
													$type_arr = array(
																	'0'	=>	__( 'Pending', 'wwclt' ),
																	'1'	=>	__( 'Approved', 'wwclt' ),
																	'2'	=>	__( 'Cancelled', 'wwclt' ),
																);
													foreach ($type_arr as $key => $value) {
														echo '<option value="'.$key.'" '.selected($data['ww_clt_product_status'],$key,false).'>'.$value.'</option>';
													}
													?>
												</select><br />
												<span class="description"><?php echo __( 'Select the product category.', 'wwclt' ) ?></span>
											</td>
										</tr>										
								
										<tr>
											<th scope="row">
												<label>
													<strong><?php echo __( 'Category:', 'wwclt' ) ?></strong>
												</label>
											</th>
											<td  width="35%">
												<select id="ww_clt_product_cat" name="ww_clt_product_cat">
													<span class="ww-clt-require"> * </span>
													<?php
													$catargs = array(	
																		'type'		 	=> 'post',
																		'child_of'	 	=> '0',
																		'parent'     	=> '',
																		'orderby'    	=> 'name',
																		'order'      	=> 'ASC',
																		'hide_empty' 	=> '0',
																		'hierarchical'	=> '1',
																		'exclude'		=> '',
																		'include'       => '',
																		'number'        => '',
																		'taxonomy'      => WW_CLT_TAXONOMY,
																		'pad_counts'    => false );
																		
													$type_arr = get_categories($catargs);
													echo '<option value="">'.__('Select Category','wwclt').'</option>';
													foreach ($type_arr as $cat) {
														echo '<option value="'.$cat->cat_ID.'" '.selected($data['ww_clt_product_cat'],$cat->cat_ID,false).'>'.$cat->name.'</option>';
													}
													?>
												</select><br />
												<span class="description"><?php echo __( 'Select the product category.', 'wwclt' ) ?></span>
											</td>
											<td class="ww-clt-product-error">
												<?php
												if(isset($errmsg['product_type']) && !empty($errmsg['product_type'])) { //check error message for product content
													echo '<div>'.$errmsg['product_cat'].'</div>';
												}
												?>
											</td>
										</tr>
								
										<tr>
											<th scope="row">
												<label>
													<strong><?php echo __( 'Availability:', 'wwclt' ) ?></strong>
												</label>
											</th>
											<td class="ww-clt-avail-chk" width="35%">
												<input type="checkbox" name="ww_clt_product_avail[]" value="Client"<?php echo checked(in_array('Client', $data['ww_clt_product_avail']), true, false) ?>/>
												<label><?php echo __( ' Client', 'wwclt' ) ?></label>
												<input type="checkbox" name="ww_clt_product_avail[]" value="Distributor"<?php echo checked(in_array('Distributor', $data['ww_clt_product_avail']), true, false) ?>/>
												<label><?php echo __( ' Distributor', 'wwclt' ) ?></label>
												<br />
												<span class="description"><?php echo __( 'Choose the product availability.', 'wwclt' ) ?></span>
											</td>
										</tr>
								
										<tr>
											<th scope="row">
												<label>
													<strong><?php echo __( 'Featured product:', 'wwclt' ) ?></strong>
												</label>
											</th>
											<td width="35%">
												<input type="radio" id="ww_clt_featured_product" name="ww_clt_featured_product" value="1"<?php echo checked('1',$data['ww_clt_featured_product'],false) ?>/><?php echo __('Yes','wwclt') ?>
												<input type="radio" id="ww_clt_featured_product" name="ww_clt_featured_product" value="0"<?php echo checked('0',$data['ww_clt_featured_product'],false) ?>/><?php echo __('No','wwclt') ?>
												<br /><span class="description"><?php echo __( 'Enter the featured product.', 'wwclt' ) ?></span>
											</td>
										</tr>
										<tr>
											<td colspan="3">
												<input type="submit" class="button-primary margin_button" name="ww_clt_product_save" id="ww_clt_product_save" value="<?php echo $product_btn ?>" />
											</td>
										</tr>
										
									</tbody>
								</table>
								
							</form>
					
						</div><!-- .inside -->
			
					</div><!-- #product -->
		
				</div><!-- .meta-box-sortables ui-sortable -->
		
			</div><!-- .metabox-holder -->
		
		</div><!-- #wps-product-general -->
			
	<!-- end of the product meta box -->
	
	</div><!-- .wrap -->