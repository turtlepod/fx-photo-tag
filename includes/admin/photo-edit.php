<?php
/**
 * Photo Edit Form
 * - Add Fake Meta Box to Upload/Select Image
 * - Create Pop Up to Edit/Add/Delete Tag
 * - Save Meta Data (Image ID and Color Scheme)
 * @since 1.0.0
**/


/* Add plugin repo post meta field. */
add_action( 'edit_form_after_title', 'fx_photo_tag_meta_field', 10, 2 );


/**
 * Add photo tag under title.
 * - Not using "real" meta box, so user can't drag it.
 * - Use the same/similar design as meta box for ux.
 * @since 1.0.0
 */
function fx_photo_tag_meta_field( $post ){

	/* Bail early, if not the right post type. */
	if( 'fx_photo_tag' != get_post_type( $post ) ) return;
	global $hook_suffix;
	$post_id = $post->ID;
?>
	<div class="fx-postarea postarea">
		<div class="postbox">

			<h3 class="fx-mb-title"><?php _e( 'Edit Photo Tag', 'fx-photo-tag' );?></h3>

			<div class="inside">

				<div class="fx-photo-tag-form">


					<p>
						<?php if( "post.php" == $hook_suffix ) fx_photo_tag_form_inputs( $post_id ); ?>
						<?php fx_photo_tag_form_upload_button( $post_id );?>
						<?php if( "post.php" == $hook_suffix ) fx_photo_tag_form_color_select( $post_id ); ?>
						<?php wp_nonce_field( "fx_photo_tag_post_nonce", "_fx_photo_tag_nonce" ) ?>
					</p>

				</div><!-- .fx-photo-tag-form -->

				<div id="fx-photo-tag-wrap" class="fx-photo-tag-color-<?php echo fx_photo_tag_color( $post_id ); ?>">

					<?php echo fx_photo_tag_image( $post_id ); ?>
					<?php echo fx_photo_tag_image_tags( $post_id ); ?>

				</div><!-- #fx-photo-tag-wrap -->

			</div><!-- .inside -->

		</div><!-- .postbox -->
	</div><!-- .fx-postarea.postarea -->

	<?php /* POP UP BOX */
	if( "post.php" == $hook_suffix ){
		add_action( 'admin_footer', 'fx_photo_tag_popup_html' );
	}
	elseif( "post-new.php" == $hook_suffix ){
		add_action( 'admin_footer', 'fx_photo_tag_popup_notice_html' );
	}
	?>

<?php
}

/**
 * Input and Buttons
 * @since 1.0.0
 */
function fx_photo_tag_form_inputs( $post_id ){
	$type = FX_PHOTO_TAG_DEBUG ? "text" : "hidden";
	?>
	<input id="fx-image-tag-id" autocomplete="off" type="<?php echo esc_attr( $type ); ?>" name="image_id" value="<?php echo esc_attr( get_post_meta( $post_id, 'image_id', true ) ); ?>">
	<?php
}

/**
 * Upload Button
 * - Hide Upload Button if Image Already Uploaded.
 * - Hide Remove Button Iif No Image Uploaded.
 * @since 1.0.0
**/
function fx_photo_tag_form_upload_button( $post_id ){
	global $hook_suffix;
	?>

	<?php if( "post.php" == $hook_suffix ){ ?>

		<?php if( get_post_meta( $post_id, 'image_id', true ) ){ ?>

			<a id="fx-upload-button" style="display:none" href="#" class="button"><?php _e( 'Upload/Select Image', 'fx-photo-tag' ); ?></a>
			<a id="fx-remove-button" href="#" class="button"><?php _e( 'Remove Image', 'fx-photo-tag' ); ?></a>

		<?php } else { ?>

			<a id="fx-upload-button" href="#" class="button"><?php _e( 'Upload/Select Image', 'fx-photo-tag' ); ?></a>
			<a id="fx-remove-button" style="display:none;" href="#" class="button"><?php _e( 'Remove Image', 'fx-photo-tag' ); ?></a>

		<?php } //end ?>

	<?php } elseif( "post-new.php" == $hook_suffix ){ ?>

		<a id="fx-upload-button" href="#" class="button fx-box" data-target="#fx-photo-tag-notice-popup" data-title="<?php esc_attr( _e( 'Notice', 'fx-photo-tag' ) );?>" data-width="300px" ><?php _e( 'Upload/Select Image', 'fx-photo-tag' ); ?></a>

	<?php } ?>



	<?php
}

/**
 * Color Select
 * @since 1.0.0
 */
function fx_photo_tag_form_color_select( $post_id ){

	/* Get color schemes */
	$colors = fx_photo_tag_color_schemes();

	/* If color available and post is publish */
	if( is_array( $colors ) && !empty( $colors ) ){ ?>

		<span class="fx-photo-tag-color-option">

			<label for="select-color-scheme"><?php _e( 'Select color:', 'fx-photo-tag' ); ?></label> 
			<select id="select-color-scheme" name="color_scheme" autocomplete="off">
				<?php foreach( $colors as $value => $name ){ ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, get_post_meta( $post_id, 'color_scheme', true ), true ); ?>>
						<?php echo $name; ?>
					</option>
				<?php } ?>
			</select><!-- #select-color-scheme -->

		</span>

	<?php } ?>
	<?php
}


/**
 * Thickbox HTML
 * @since 1.0.0
**/
function fx_photo_tag_popup_html(){
	$type = FX_PHOTO_TAG_DEBUG ? "text" : "hidden";
	$class = FX_PHOTO_TAG_DEBUG ? "fx-photo-tag-popup-debug" : "fx-photo-tag-popup-data";
	?>

	<div id="fx-photo-tag-edit-popup" style="display:none">

		<div class="<?php echo esc_attr( $class ); ?>">

			<label>X:</br>
				<input class="x-pos" autocomplete="off" type="<?php echo esc_attr( $type ); ?>" value="">
			</label>
			<label>Y:</br>
				<input class="y-pos" autocomplete="off" type="<?php echo esc_attr( $type ); ?>" value="">
			</label>
			<label>LEFT:</br>
				<input class="left-pos" autocomplete="off" type="<?php echo esc_attr( $type ); ?>" value="">
			</label>
			<label>TOP:</br>
				<input class="top-pos" autocomplete="off" type="<?php echo esc_attr( $type ); ?>" value="">
			</label>

		</div>

		<div class="fx-photo-tag-popup-input">
			<p>
				<label><strong><?php _e( 'Text:', 'fx-photo-tag' ); ?></strong><br/>
					<input class="tag-text" autocomplete="off" type="text" value="Text">
				</label>
			</p>
			<p>
				<label><strong><?php _e( 'URL:', 'fx-photo-tag' ); ?></strong><br/>
					<input class="tag-url" autocomplete="off" type="text" placeholder="http://" value="">
				</label>
				<label>
					<input class="tag-target" autocomplete="off" type="checkbox" value="1"> <?php _e( 'Open in new tab?', 'fx-photo-tag' ); ?>
				</label>
			</p>
		</div><!-- .fx-photo-tag-popup-input -->

		<p class="fx-photo-tag-popup-action">
			<a id="fx-add-tag" class="save-tag button button-primary" href="#"><?php _e( 'Add Tag', 'fx-photo-tag' ); ?></a>
			<a id="fx-edit-tag" style="display:none;" class="save-tag button button-primary" href="#"><?php _e( 'Edit Tag', 'fx-photo-tag' ); ?></a>
			<a id="fx-delete-tag" style="display:none;" class="delete-tag button" href="#"><?php _e( 'Delete Tag', 'fx-photo-tag' ); ?></a>
			<span id="fx-save-tag-spinner" class="spinner"></span>
		</p><!-- .fx-photo-tag-popup-action -->

		<div class="fx-photo-tag-ajax-message" style="display:none;">
			<?php /* PLACEHOLDER FOR AJAX MESSAGE */ ?>
		</div>

	</div><!-- #fx-photo-tag-edit-popup -->
	<?php
}


/**
 * Thickbox HTML
 * @since 1.0.0
**/
function fx_photo_tag_popup_notice_html(){
	?>
	<div id="fx-photo-tag-notice-popup" style="display:none">
		<p><?php _e( 'Please save entry as draft or publish entry before upload/select image.' ); ?></p>
	</div><!-- #fx-photo-tag-notice-popup -->
	<?php
}


/* ==== SAVE DATA ==== */

/* Save Post Hook */
add_action( 'save_post', 'fx_photo_tag_save_post' );

/**
 * Save Meta Data
 */
function fx_photo_tag_save_post( $post_id ){

	/* Verify nonce */
	if ( ! isset( $_POST['_fx_photo_tag_nonce'] ) || ! wp_verify_nonce( $_POST['_fx_photo_tag_nonce'], 'fx_photo_tag_post_nonce' ) ){
		return $post_id;
	}
	/* Do not save on autosave */
	if ( defined('DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}
	/* Check post type and user caps. */
	if ( 'fx_photo_tag' != $_POST['post_type'] || !current_user_can( 'edit_posts', $post_id ) ) {
		return $post_id;
	}

	/*  == IMAGE ID == */
	$name = 'color_scheme';
	$old_meta = get_post_meta( $post_id, $name, true );
	$new_meta = isset( $_POST[$name] ) ? $_POST[$name] : null;
	$new_meta = fx_photo_tag_sanitize_color( $new_meta );
	if( empty( $new_meta ) || 'default' == $new_meta ){
		delete_post_meta( $post_id, $name );
	}
	elseif( $new_meta != $old_meta ){
		update_post_meta( $post_id, $name, esc_attr( $new_meta ) );
	}

	/*  == IMAGE ID == */
	$name = 'image_id';
	$old_meta = get_post_meta( $post_id, $name, true );
	$new_meta = isset( $_POST[$name] ) ? $_POST[$name] : null;
	if( empty( $new_meta ) ){
		delete_post_meta( $post_id, $name );
	}
	elseif( $new_meta != $old_meta ){
		update_post_meta( $post_id, $name, esc_attr( $new_meta ) );
	}
}


/* ==== SCRIPTS ==== */


/* Load Admin Scripts */
add_action( 'admin_enqueue_scripts', 'fx_photo_tag_admin_scripts' );


/**
 * Admin Scripts
 */
function fx_photo_tag_admin_scripts( $hook ){
	global $post_type;

	/* Thickbox Replacement */
	wp_register_script( 'fx-box', FX_PHOTO_TAG_URI. 'assets/fx-box/jquery.fx-box.js', array( 'jquery' ), FX_PHOTO_TAG_VERSION, true );
	wp_register_style( 'fx-box', FX_PHOTO_TAG_URI . 'assets/fx-box/fx-box.css', array(), FX_PHOTO_TAG_VERSION );

	/* Check post type before loading scripts. */
	if( 'fx_photo_tag' == $post_type ){

		/* New Entry Notice */
		if( "post-new.php" == $hook ){

			/* f(x) Box */
			wp_enqueue_script( 'fx-box' );
			wp_enqueue_style( 'fx-box' );

			wp_enqueue_style( 'fx-photo-tag-post-edit', FX_PHOTO_TAG_URI . 'assets/admin/post-edit.css', array( 'fx-box' ), FX_PHOTO_TAG_VERSION );
		}

		/* Post need to be published first before upload image. */
		elseif( "post.php" == $hook ){

			/* f(x) Box */
			wp_enqueue_script( 'fx-box' );
			wp_enqueue_style( 'fx-box' );

			/* CSS */
			wp_enqueue_style( 'fx-photo-tag-post-edit', FX_PHOTO_TAG_URI . 'assets/admin/post-edit.css', array( 'fx-box' ), FX_PHOTO_TAG_VERSION );

			/* JS */
			if( "post.php" == $hook ){
				wp_enqueue_media(); // need this to upload image.
				wp_enqueue_script( 'fx-photo-tag-post-edit', FX_PHOTO_TAG_URI. 'assets/admin/post-edit.js', array( 'jquery', 'jquery-ui-core', 'media-upload', 'fx-box' ), FX_PHOTO_TAG_VERSION, true );
				wp_localize_script( 'fx-photo-tag-post-edit', 'fx_photo_tag',
					array(
						/* Upload */
						'title'          => __( 'Upload/Select Image', 'fx-photo-tag' ),
						'button_text'    => __( 'Insert Image', 'fx-photo-tag' ),
						/* Pop Up */
						'popup_title'    => __( 'Photo Tag', 'fx-photo-tag' ),
						'tag_default'    => __( 'Label', 'fx-photo-tag' ),
						/* Ajax Related */
						'ajax_url'       => admin_url( 'admin-ajax.php' ),
						'ajax_nonce'     => wp_create_nonce( 'fx_photo_tag_ajax_nonce' ),
						'post_id'        => intval( $_GET["post"] ),
					)
				);
			}
		}
	}
}