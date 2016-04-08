jQuery(document).ready(function($){

	function fx_photo_tag_reposition(){
		/* For each photo tag */
		$( '.fx-photo-tag' ).each( function( index ) {

			/* Actual Size Pixel Position */
			var x_pos = $( this ).attr( 'data-x' );
			var y_pos = $( this ).attr( 'data-y' );

			/* Actual Image Size */
			var img_width = $( this ).siblings( '.fx-photo-tag-image' ).attr( 'width' );
			var img_height = $( this ).siblings( '.fx-photo-tag-image' ).attr( 'height' );

			/* Display Image Size */
			var el_width = $( this ).siblings( '.fx-photo-tag-image' ).width();
			var el_height = ( el_width / img_width ) * img_height; // no need to wait image load

			/* Calculate Display Position */
			var left_pos = Math.round( el_width / img_width * x_pos );
			var top_pos = Math.round( el_height / img_height * y_pos );

			/* Add inline CSS */
			$( this ).css({
				"left": left_pos + "px",
				"top": top_pos + "px",
			});
		});
	}
	fx_photo_tag_reposition();
	$( window ).resize( function(){
		fx_photo_tag_reposition();
	});

});