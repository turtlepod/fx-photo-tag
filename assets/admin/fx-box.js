/* Open Box */
function fx_box_open( box_id ){
	/* Open Box */
	jQuery( box_id ).show();
	jQuery( ".fx-box-overlay" ).show();
	/* Fit Size */
	var fx_box_content_height = jQuery( box_id ).height() - 46;
	jQuery( box_id + " .fx-box-content" ).css( "height", fx_box_content_height + "px" );
}
/* Close Box */
jQuery(document).ready(function($){
	var fx_box_content_height = $( '.fx-box' ).height() - 46;
	$( ".fx-box-content" ).css( "height", fx_box_content_height + "px" );
	$( '.fx-box-close,.fx-box-overlay' ).click( function(e){
		e.preventDefault();
		$( ".fx-box" ).hide();
		$( ".fx-box-overlay" ).hide();
	});
});