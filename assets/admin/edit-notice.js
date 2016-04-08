jQuery(document).ready( function($){
	/* Open Notice */
	$( '#fx-upload-button' ).click( function(e){
		e.preventDefault();
		fx_box_open( '#fx-photo-tag-edit-popup' );
	});
});