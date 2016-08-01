<?php
/**
 * Ajax Callback to Add, Edit, and Delete tag
 * @since 1.0.0
**/

/* ===== ADD TAG ===== */
add_action( 'wp_ajax_fx_photo_tag_add', 'fx_photo_tag_ajax_add' );

/**
 * Add Tag
 * @since 1.0.0
 */
function fx_photo_tag_ajax_add(){

	/* stripslashes() Data */
	$request = stripslashes_deep( $_REQUEST );

	/* Verify Nonce */
	if ( ! wp_verify_nonce( $request['nonce'], 'fx_photo_tag_ajax_nonce' ) ){
		die(-1);
	}

	/* Post ID */
	$post_id = esc_attr( $request['post_id'] );

	/* Check post type and user caps. */
	if ( 'fx_photo_tag' != get_post_type( $post_id ) || !current_user_can( 'edit_posts', $post_id ) ) {
		die(-1);
	}

	/* Ajax */
	$output = array(
		'tag'     => '', // html of the tag to insert
		'message' => __( 'Error. Please try again.', 'fx-photo-tag' ),
	);

	/* Var */
	$request_tag_datas = $request['tag_datas'];
	$default = array(
		'top'    => 0, // only for admin
		'left'   => 0, // only for admin
		'x'      => 0,
		'y'      => 0,
		'text'   => 'Text',
		'url'    => '#',
		'target' => '',
	);
	$tag_datas = wp_parse_args( $request_tag_datas, $default );

	/* URL target data */
	if( 'true' == $tag_datas['target'] ){
		$tag_datas['target'] = '_blank';
	}
	else{
		$tag_datas['target'] = '_self';
	}

	/* Tag ID (num) */
	$tag_id = esc_attr( $tag_datas['x'] . '_' . $tag_datas['y'] );

	/* Save Tag ID in Tag List */
	$tag_list_added = false;
	$tags = get_post_meta( $post_id, 'tags', true );
	if( !$tags ){
		$tag_list_added = update_post_meta( $post_id, 'tags', $tag_id );
	}
	else{
		$tags = explode( ',', $tags );
		if( !in_array( $tag_id, $tags ) ){
			$tags[] = esc_attr( trim( $tag_id ) );
			$tag_list_added = update_post_meta( $post_id, 'tags', implode( ',', $tags ) );
		}
	}

	/* Add individual tag in post meta */
	$tag_added = false;
	if( false !== $tag_list_added ){
		$clean_tag_datas = $tag_datas;
		unset( $clean_tag_datas['top'] );
		unset( $clean_tag_datas['left'] );
		$tag_added = update_post_meta( $post_id, 'tag-' . $tag_id, serialize( $clean_tag_datas ) );
	}
	if( false !== $tag_added ){
		$output['tag'] = fx_photo_tag_single_tag_html( $tag_datas );
		$output['message'] = __( 'Photo tag added.', 'fx-photo-tag' );
	}

	/* Print */
	echo json_encode( $output );
	die();
}


/* ===== EDIT TAG ===== */
add_action( 'wp_ajax_fx_photo_tag_edit', 'fx_photo_tag_ajax_edit' );

/**
 * Add Tag
 * @since 1.0.0
 */
function fx_photo_tag_ajax_edit(){

	/* stripslashes() Data */
	$request = stripslashes_deep( $_REQUEST );

	/* Verify Nonce */
	if ( ! wp_verify_nonce( $request['nonce'], 'fx_photo_tag_ajax_nonce' ) ){
		die(-1);
	}

	/* Post ID */
	$post_id = esc_attr( $request['post_id'] );

	/* Check post type and user caps. */
	if ( 'fx_photo_tag' != get_post_type( $post_id ) || !current_user_can( 'edit_posts', $post_id ) ) {
		die(-1);
	}

	/* Ajax */
	$output = array(
		'tag'     => '', // html of the tag to insert
		'message' => __( 'Photo tag updated.', 'fx-photo-tag' ),
	);

	/* Var */
	$request_tag_datas = $request['tag_datas'];
	$default = array(
		'top'    => 0, // only for admin
		'left'   => 0, // only for admin
		'x'      => 0,
		'y'      => 0,
		'text'   => 'Text',
		'url'    => '#',
		'target' => '',
	);
	$tag_datas = wp_parse_args( $request_tag_datas, $default );

	/* URL target data */
	if( 'true' == $tag_datas['target'] ){
		$tag_datas['target'] = '_blank';
	}
	else{
		$tag_datas['target'] = '_self';
	}

	/* Tag ID (num) */
	$tag_id = esc_attr( $tag_datas['x'] . '_' . $tag_datas['y'] );

	/* Add individual tag in post meta */
	$clean_tag_datas = $tag_datas;
	unset( $clean_tag_datas['top'] );
	unset( $clean_tag_datas['left'] );
	$tag_added = update_post_meta( $post_id, 'tag-' . $tag_id, serialize( $clean_tag_datas ) );

	/* Always output tag */
	$output['tag'] = fx_photo_tag_single_tag_html( $tag_datas );

	/* Print */
	echo json_encode( $output );
	die();
}


/* ===== DELETE TAG ===== */
add_action( 'wp_ajax_fx_photo_tag_delete', 'fx_photo_tag_ajax_delete' );

/**
 * Add Tag
 * @since 1.0.0
 */
function fx_photo_tag_ajax_delete(){

	/* stripslashes() Data */
	$request = stripslashes_deep( $_REQUEST );

	/* Verify Nonce */
	if ( ! wp_verify_nonce( $request['nonce'], 'fx_photo_tag_ajax_nonce' ) ){
		die(-1);
	}

	/* Post ID */
	$post_id = esc_attr( $request['post_id'] );

	/* Check post type and user caps. */
	if ( 'fx_photo_tag' != get_post_type( $post_id ) || !current_user_can( 'edit_posts', $post_id ) ) {
		die(-1);
	}

	/* Ajax */
	$output = array(
		'tag'     => '', // html of the tag to insert
		'message' => __( 'Error. Please try again.', 'fx-photo-tag' ),
	);

	/* Var */
	$request_tag_datas = $request['tag_datas'];
	$default = array(
		'x'      => 0,
		'y'      => 0,
	);
	$tag_datas = wp_parse_args( $request_tag_datas, $default );

	/* Tag ID (num) */
	$tag_id = esc_attr( $tag_datas['x'] . '_' . $tag_datas['y'] );

	/* Remove in tag list */
	$tag_list_updated = false;
	$tags = get_post_meta( $post_id, 'tags', true );
	if( $tags ){
		$tags = explode( ',', $tags );
		if( in_array( $tag_id, $tags ) ){
			$key = array_search( $tag_id, $tags );
			unset( $tags[$key] );
			$tag_list_updated = update_post_meta( $post_id, 'tags', implode( ',', $tags ) );
		}
	}

	/* Remove individual tag. */
	$tag_removed = delete_post_meta( $post_id, 'tag-' . $tag_id );


	if( $tag_list_updated && $tag_removed ){
		$output['message'] = __( 'Tag deleted.', 'fx-photo-tag' );
	}

	/* Print */
	echo json_encode( $output );
	die();
}




