<?php
/**
 * Media Modal Box
**/

/* === SCRIPTS === */

/* Load Scripts */
add_action('admin_enqueue_scripts', 'fx_photo_tag_media_modal_scripts');

/**
 * Enqueue Scripts in Post Edit Screen.
 * @since 1.1.0
 */
function fx_photo_tag_media_modal_scripts( $hook ){

	if( "post.php" == $hook || "post-new.php" == $hook ){

		wp_enqueue_style( 'fx-photo-tag-media-modal', FX_PHOTO_TAG_URI. 'assets/admin/media-modal.css', array(), FX_PHOTO_TAG_VERSION );

		wp_enqueue_script( 'fx-photo-tag-media-modal', FX_PHOTO_TAG_URI. 'assets/admin/media-modal.js', array( 'media-views', 'jquery' ), FX_PHOTO_TAG_VERSION, true );

		wp_localize_script( 'fx-photo-tag-media-modal', 'fx_photo_tag_modal',
			array(
				'insert'         => __( 'Insert shortcode', 'fx-photo-tag' ),
				'title'          => __( 'Photo Tag', 'fx-photo-tag' ),
				'ajax_url'       => admin_url( 'admin-ajax.php' ),
				'ajax_nonce'     => wp_create_nonce( 'fx_photo_tag_modal_nonce' ),
			)
		);
	}
}

/* === AJAX CALLBACK === */
add_action( 'wp_ajax_fx_photo_tag_modal_init', 'fx_photo_tag_media_modal_ajax_items' );


/**
 * WP Query to get all photo tag on load.
 * @since 1.1.0
 */
function fx_photo_tag_media_modal_ajax_items(){

	/* stripslashes() Data */
	$request = stripslashes_deep( $_REQUEST );

	/* Verify Nonce */
	if ( ! wp_verify_nonce( $request['nonce'], 'fx_photo_tag_modal_nonce' ) ){
		die(-1);
	}
	?>
	<div class="fx-photo-tag-items fx-mm-items">
	<?php
		$args = array(
			'post_type'      => 'fx_photo_tag',
			'posts_per_page' => -1,
		);
		$the_query = new WP_Query( $args );

		// The Loop
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$post_id = get_the_ID();
				$get_post = get_post( $post_id );
				$slug = $get_post->post_name;
				$image_id = get_post_meta( $post_id, 'image_id', true );
				$image_data = wp_get_attachment_image_src( $image_id, 'medium' );
				if( $image_data ){
					?>
					<div class="fx-photo-tag-item fx-mm-item" data-id="<?php echo esc_attr( $slug ); ?>">
						<div class="fx-mm-item-wrap">
							<div class="fx-mm-item-image" style="background-image:url('<?php echo esc_url( $image_data[0] ); ?>')"></div>
							<div class="fx-mm-item-caption"><?php echo $slug; ?></div>
						</div><!-- .fx-mm-item-wrap -->
					</div><!-- .fx-mm-item -->
					<?php
				}
			}
		}
		else {
			?>
				<p><?php _e( 'No Photo Tag Found.' , 'fx-photo-tag' );?></p>
			<?php
		}
		wp_reset_postdata();
	?>
	</div><!-- .fx-mm-items -->
	<?php
	die();
}