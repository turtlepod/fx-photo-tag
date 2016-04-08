<?php
/**
 * Shortcodes
 * @since 1.0.0
**/

/* Load Admin Scripts */
add_action( 'wp_enqueue_scripts', 'fx_photo_tag_scripts' );

/**
 * Admin Scripts
 */
function fx_photo_tag_scripts(){

	/* CSS */
	if( apply_filters( 'fx_photo_tag_load_front_css', true ) ){
		wp_enqueue_style( 'fx-photo-tag', FX_PHOTO_TAG_URI . 'assets/fx-photo-tag.css', array(), FX_PHOTO_TAG_VERSION );
	}

	/* JS */
	wp_register_script( 'fx-photo-tag', FX_PHOTO_TAG_URI. 'assets/fx-photo-tag.js', array( 'jquery' ), FX_PHOTO_TAG_VERSION, true );
}


/* Register Shortcodes on Init Hook */
add_action( 'init', 'fx_photo_tag_shortcode_register' );

/**
 * Register Shortcodes
 * @since 1.0.0
 */
function fx_photo_tag_shortcode_register(){

	/* [fx-photo-tag id="slug"] shortcode */
	add_shortcode( 'fx-photo-tag', 'fx_photo_tag_shortcode' );
}

/**
 * Shortcode Callback
 */
function fx_photo_tag_shortcode( $attr ){

	/* Shortcode parameter */
	$attr = shortcode_atts( array( 'id' => '' ), $attr );

	/* Bail if no id specified */
	if ( empty( $attr['id'] ) ) return false;

	/* Loop args */
	$args = array(
		'name'                => $attr['id'],
		'post_type'           => 'fx_photo_tag',
		'post_status'         => 'publish',
		'posts_per_page'      => 1,
	);

	/* Get Posts Data */
	$posts = get_posts( $args );

	/* If data found */
	if( isset( $posts[0]->ID ) ){
		wp_enqueue_script( 'fx-photo-tag' );
		return fx_photo_tag( $posts[0]->ID );
	}
	else{
		return false;
	}
}












