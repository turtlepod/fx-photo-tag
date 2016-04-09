<?php
/**
 * Admin Mod
 * - Enqueue Admin Scripts
 * - Admin Menu (Add Under "Media")
 * - Add Shortcode Meta Box
 * - Custom Column
 * @since 1.0.0
**/

/* ===== ADMIN SCRIPTS ===== */

/* Load Admin Scripts */
add_action( 'admin_enqueue_scripts', 'fx_photo_tag_admin_mod_scripts' );


/**
 * Admin Scripts
 */
function fx_photo_tag_admin_mod_scripts( $hook ){
	global $post_type;

	/* Check post type before loading scripts. */
	if( 'fx_photo_tag' == $post_type ){

		/* Edit Columns */
		if( "edit.php" == $hook ){

			wp_enqueue_script( 'fx-photo-tag-admin-column', FX_PHOTO_TAG_URI. 'assets/admin/columns.js', array( 'jquery' ), FX_PHOTO_TAG_VERSION, true );
			wp_enqueue_style( 'fx-photo-tag-admin-column', FX_PHOTO_TAG_URI . 'assets/admin/columns.css', array(), FX_PHOTO_TAG_VERSION );
		}
	}
}


/* ===== ADMIN MENU ===== */


/* Admin Menu */
add_action( 'admin_menu', 'fx_photo_tag_admin_menu' );

/**
 * Add admin menu,
 * @since 0.1.0
 */
function fx_photo_tag_admin_menu(){
	$cpt = 'fx_photo_tag';
	$cpt_obj = get_post_type_object( $cpt );
	add_submenu_page(
		'upload.php',                      // parent slug
		$cpt_obj->labels->name,            // page title
		$cpt_obj->labels->menu_name,       // menu title
		$cpt_obj->cap->edit_posts,         // capability
		'edit.php?post_type=' . $cpt       // menu slug
	);
}

/* Parent Menu Fix */
add_filter( 'parent_file', 'fx_photo_tag_parent_file' );

/**
 * Fix Parent Admin Menu Item
 * @since 0.1.0
 */
function fx_photo_tag_parent_file( $parent_file ){
	$cpt = 'fx_photo_tag';
	global $current_screen, $self;
	if ( in_array( $current_screen->base, array( 'post', 'edit' ) ) && $cpt == $current_screen->post_type ) {
		$parent_file = 'upload.php';
	}
	return $parent_file;
}

/* ===== SHORTCODE META BOX ===== */

/* Add meta boxes */
add_action( 'add_meta_boxes', 'fx_photo_tag_shortcode_add_meta_box' );

/**
 * Add meta boxes
 */
function fx_photo_tag_shortcode_add_meta_box(){
	add_meta_box (
		'fx-photo-tag-shortcode-mb',
		__( 'Shortcode', 'fx-photo-tag' ),
		'fx_photo_tag_shortcode_meta_box_callback',
		'fx_photo_tag',
		'side',
		'default'
	);
}


/**
 * Meta Box Callback
 */
function fx_photo_tag_shortcode_meta_box_callback( $post_id ){
	if( 'publish' === get_post_status( $post_id ) ){
		$sc_tag = 'fx-photo-tag';
		$get_post = get_post( $post_id );
		$slug = $get_post->post_name;
		$shortcode = htmlentities( '[' . $sc_tag . ' id="' . $slug . '"]' );
		echo '<input type="text" class="fx-sc-input widefat" readonly="readonly" value="' . $shortcode . '" />';
		echo wpautop( __( 'You can change the ID using "Slug" Meta Box.', 'fx-photo-tag' ) );
	}
	else{
		echo wpautop( __( 'Shortcode not available yet. Please publish photo.', 'fx-photo-tag' ) );
	}
}


/* ===== CUSTOM COLUMNS ===== */


/* Manage post column */
add_filter( 'manage_fx_photo_tag_posts_columns', 'fx_photo_tag_post_columns' );
add_action( 'manage_fx_photo_tag_posts_custom_column', 'fx_photo_tag_custom_column', 5, 2 );

/**
 * Post Column
 */
function fx_photo_tag_post_columns( $columns ){

	/* Remove date columns */
	unset( $columns['date'] );

	/* Add shortcode */
	$columns['shortcode'] = __( 'Shortcode', 'fx-photo-tag' );
	$columns['thumbnail'] = __( 'Thumbnail', 'fx-photo-tag' );

	/* Add thumbnail */
	//$columns = array_slice( $columns, 0, 1, true ) + array( 'thumbnail' => '' ) + array_slice( $columns, 1, NULL, true );

	/* return it. */
	return $columns;
}


/**
 * Shortcode Columns
 */
function fx_photo_tag_custom_column( $column, $post_id ){
	global $post;
	switch ( $column ) {

		case 'thumbnail':
			$image_id = get_post_meta( $post_id, 'image_id', true );
			$image_data = wp_get_attachment_image_src( $image_id, 'thumbnail' );
			if( $image_data ){
				?>
				<div class="fx-photo-tag-thumbnail">
					<img src="<?php echo $image_data[0]; ?>" width="80" height="80"/>
					<?php /* TODO: Add tag info and color scheme */ ?>
				</div>
				<?php
			}
		break;

		case 'shortcode':
			if( 'publish' === get_post_status( $post_id ) ){
				$sc_tag = 'fx-photo-tag';
				$get_post = get_post( $post_id );
				$slug = $get_post->post_name;
				$shortcode = htmlentities( '[' . $sc_tag . ' id="' . $slug . '"]' );
				echo '<input type="text" class="fx-sc-input" readonly="readonly" value="' . $shortcode . '" />';
			}
		break;
	}
}
