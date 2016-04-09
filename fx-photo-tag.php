<?php
/**
 * Plugin Name: f(x) Photo Tag
 * Plugin URI: http://genbumedia.com/plugins/fx-photo-tag/
 * Description: Add label and tag to your photo/image just like facebook!
 * Version: 1.0.0
 * Author: David Chandra Purnama
 * Author URI: http://shellcreeper.com/
 * License: GPLv2 or later
 * Text Domain: fx-photo-tag
 * Domain Path: /languages/
**/

/* Do not access this file directly */
if ( ! defined( 'WPINC' ) ) { die; }

/* Constants
------------------------------------------ */

/* Set the version constant. */
define( 'FX_PHOTO_TAG_VERSION', '1.0.0' );

/* Set the debug constant. */
define( 'FX_PHOTO_TAG_DEBUG', apply_filters( 'fx_photo_tag_debug', false ) );

/* Set the constant path to the plugin path. */
define( 'FX_PHOTO_TAG_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );

/* Set the constant path to the plugin directory URI. */
define( 'FX_PHOTO_TAG_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );


/* Plugins Loaded
------------------------------------------ */

/* Load plugin */
add_action( 'plugins_loaded', 'fx_photo_tag_plugins_loaded' );

/**
 * Load plugins functions
 * @since 1.0.0
 */
function fx_photo_tag_plugins_loaded(){

	/* Language */
	load_plugin_textdomain( 'fx-photo-tag', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	/* Utility Functions */
	require_once( FX_PHOTO_TAG_PATH . 'includes/utility.php' );

	/* Register Post Type */
	require_once( FX_PHOTO_TAG_PATH . 'includes/register-post-type.php' );

	/* Shortcodes */
	require_once( FX_PHOTO_TAG_PATH . 'includes/shortcodes.php' );

	/* Admin Stuff */
	if( is_admin() ){

		/* Admin Edit */
		require_once( FX_PHOTO_TAG_PATH . 'includes/admin/photo-edit.php' );

		/* Ajax Callback */
		require_once( FX_PHOTO_TAG_PATH . 'includes/admin/ajax-callback.php' );

		/* Various Admin Mod */
		require_once( FX_PHOTO_TAG_PATH . 'includes/admin/admin-mod.php' );
	}

}


/* Activation and Uninstall
------------------------------------------ */

/* Register activation hook. */
register_activation_hook( __FILE__, 'fx_photo_tag_activation' );


/**
 * Runs only when the plugin is activated.
 * @since 1.0.0
 */
function fx_photo_tag_activation() {

	/* Add temporary data. */
	set_transient( 'fx_photo_tag_activation_notice', "1", 5 );
}

/* Add admin notice */
add_action( 'admin_notices', 'fx_photo_tag_admin_notice' );

/**
 * Admin Notice on Activation.
 * @since 1.0.0
 */
function fx_photo_tag_admin_notice(){
	$transient = get_transient( 'fx_photo_tag_activation_notice' );
	if( "1" === $transient ){
		?>
		<div class="updated notice is-dismissible">
			<p><?php echo sprintf( __( 'Navigate to <a href="%s">Photo Tag (Under Media)</a> to add label or tag to your photos.', 'fx-photo-tag' ), admin_url( 'edit.php?post_type=fx_photo_tag' ) ); ?></p>
		</div>
		<?php
		/* Remove temporary data */
		delete_transient( 'fx_photo_tag_activation_notice' );
	}
}
