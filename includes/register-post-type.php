<?php
/**
 * Register Post Type
**/

/* add register post type on the 'init' hook */
add_action( 'init', 'fx_photo_tag_register_post_types' );

/**
 * Register Post Type
 * @since  0.1.0
 */
function fx_photo_tag_register_post_types() {

	$cpt_args = array(
		'description'           => '',
		'public'                => false,
		'publicly_queryable'    => false,
		'show_in_nav_menus'     => false,
		'show_in_admin_bar'     => false,
		'exclude_from_search'   => true,
		'show_ui'               => true,
		'show_in_menu'          => false, /* No admin menu, add it manually */
		'can_export'            => true,
		'delete_with_user'      => false,
		'hierarchical'          => false,
		'has_archive'           => false, 
		'query_var'             => true,
		'rewrite'               => false,
		'capability_type'       => 'post',
		'supports'              => FX_PHOTO_TAG_DEBUG ? array( 'title', 'custom-fields' ) : array( 'title' ),
		'labels'                => array(
			'name'                      => _x( 'Photo Tag', 'cpt', 'fx-photo-tag' ),
			'singular_name'             => _x( 'Photo Tag', 'cpt', 'fx-photo-tag' ),
			'add_new'                   => _x( 'Add New', 'cpt', 'fx-photo-tag' ),
			'add_new_item'              => _x( 'Add New Item', 'cpt', 'fx-photo-tag' ),
			'edit_item'                 => _x( 'Edit Item', 'cpt', 'fx-photo-tag' ),
			'new_item'                  => _x( 'New Item', 'cpt', 'fx-photo-tag' ),
			'all_items'                 => _x( 'All Items', 'cpt', 'fx-photo-tag' ),
			'view_item'                 => _x( 'View Item', 'cpt', 'fx-photo-tag' ),
			'search_items'              => _x( 'Search Items', 'cpt', 'fx-photo-tag' ),
			'not_found'                 => _x( 'Not Found', 'cpt', 'fx-photo-tag' ),
			'not_found_in_trash'        => _x( 'Not Found in Trash', 'cpt', 'fx-photo-tag' ), 
			'menu_name'                 => _x( 'Photo Tag', 'cpt', 'fx-photo-tag' ),
		),
	);
	register_post_type( 'fx_photo_tag', $cpt_args );
}
