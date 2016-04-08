<?php
/**
 * Utility Functions
 * @since 1.0.0
**/

/* ======= OUTPUT ======= */

/**
 * HTML Output (Only use on front end)
 * @since 1.0.0
 */
function fx_photo_tag( $post_id ){

	/* Image ID */
	$image_id = get_post_meta( $post_id, 'image_id', true );
	$image_data = wp_get_attachment_metadata( $image_id );
	$out = '';

	if( $image_data ){
		$out .= '<div class="fx-photo-tag-wrap fx-photo-tag-color-' . esc_attr( fx_photo_tag_color( $post_id ) ) . '">';
			$out .= fx_photo_tag_image( $post_id );
			$out .= fx_photo_tag_image_tags( $post_id );
		$out .= '</div><!-- .fx-photo-tag-wrap -->';
	}
	return $out;
}


/**
 * Image Element
 * @param $post_id Photo tag entry ID
 * @since 1.0.0
 */
function fx_photo_tag_image( $post_id ){

	/* Image ID */
	$image_id = get_post_meta( $post_id, 'image_id', true );
	$image_data = wp_get_attachment_metadata( $image_id );

	/* If image data available */
	if( $image_data ){

		/* Use meta data from photo tag entry */
		$url = wp_get_attachment_url( $image_id );
		$width = $image_data['width'];
		$height = $image_data['height'];

		$image_tag = '<img class="fx-photo-tag-image alignnone size-full wp-image-' . intval( $image_id ) . '" src="' . esc_url( $url ) . '" width="' . intval( $width ) . '" height="' . intval( $height ) . '">';

		/* Make it responsive (WP 4.4+) */
		if( function_exists('wp_make_content_images_responsive') ) {
			$image_tag = wp_make_content_images_responsive( $image_tag );
		}

		return apply_filters( 'fx_photo_tag_image', $image_tag );
	}

	return apply_filters( 'fx_photo_tag_image', false );
}


/**
 * Tags HTML
 * @since 1.0.0
 */
function fx_photo_tag_image_tags( $post_id ){

	/* Only if image is available. */
	$image_id = get_post_meta( $post_id, 'image_id', true );
	$image_data = wp_get_attachment_metadata( $image_id );
	if( !$image_data ) return false;

	/* Get all tags in entry */
	$tags = get_post_meta( $post_id, 'tags', true );
	if( !$tags ) return false;

	/* Make it an array, each tag format: x_y */
	$tags = explode( ",", $tags );

	/* Outout HTML */
	$out = '';

	/* For each tags, create HTML */
	foreach( $tags as $tag ){

		/* Get individual tag data */
		$tag_datas = get_post_meta( $post_id, 'tag-' . $tag, true );

		/* Only if tag data available */
		if( $tag_datas ){

			/* String to array. */
			$tag_datas = unserialize( $tag_datas );

			$out .= fx_photo_tag_single_tag_html( $tag_datas );
		}
	}
	return $out;
}

/**
 * Tags
 * @since 1.0.0
 */
function fx_photo_tag_single_tag_html( $args ){

	$default = array(
		'x'      => 0,
		'y'      => 0,
		'text'   => 'Text',
		'url'    => '',
		'target' => '',
	);
	$data = wp_parse_args( $args, $default );

	/* Style: Add position directly, if needed (e.g: initial creation in admin) */
	$style = '';
	if( isset( $data['left'] ) ){
		$style .= 'left:' . intval( $data['left'] ) . 'px;';
	}
	if( isset( $data['top'] ) ){
		$style .= 'top:' . intval( $data['top'] ) . 'px;';
	}

	$out  = '<span class="fx-photo-tag fx-photo-tag-' . esc_attr( $data['x'] . '_' . $data['y'] ) . '" data-x="' . intval( $data['x'] ). '" data-y="' . intval( $data['y'] ). '" data-text="' . esc_attr( $data['text'] ) . '" data-url="' . esc_url( $data['url'] ). '" data-target="' . esc_attr( $data['target'] ). '" style="' . esc_attr( $style ) . '">';

		/* If has URL, use it. */
		if( $data['url'] ){
			$out .= '<a class="fx-photo-tag-text" target="' . fx_photo_tag_sanitize_url_target( $data['target'] ). '" href="' . esc_url( $data['url'] ). '">';
				$out .= esc_attr( $data['text'] );
			$out .= '</a>';
		}

		/* No URL, use span to wrap. */
		else{
			$out .= '<span class="fx-photo-tag-text">';
				$out .= esc_attr( $data['text'] );
			$out .= '</span>';
		}

	$out .= '</span>';

	return $out;
}


/**
 * Sanitize URL target
 * @since 1.0.0
 */
function fx_photo_tag_sanitize_url_target( $input ){
	$targets = array( '_self', '_blank' );
	$default = '_self';
	if( in_array( $input, $targets ) ){
		return esc_attr( $input );
	}
	return esc_attr( $default );
}


/* ======= COLOR SCHEMES ======= */

/**
 * Color Schemes
 * @since 1.0.0
**/
function fx_photo_tag_color_schemes(){
	$colors = array(
		'default'   => __( 'Default', 'fx-photo-tag' ), /* must be available */
		'white'     => __( 'White', 'fx-photo-tag' ),
	);
	return apply_filters( 'fx_photo_tag_color_schemes', $colors );
}

/**
 * Sanitize Color
 * @since 1.0.0
 */
function fx_photo_tag_sanitize_color( $input ){
	$color_schemes = fx_photo_tag_color_schemes();
	if( array_key_exists( $input, $color_schemes ) ){
		return esc_attr( $input );
	}
	return 'default';
}

/**
 * Get Current Color Scheme
 * @since 1.0.0
 */
function fx_photo_tag_color( $post_id ){
	return fx_photo_tag_sanitize_color( get_post_meta( $post_id, 'color_scheme', true ) );
}













