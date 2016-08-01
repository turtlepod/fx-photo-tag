jQuery(document).ready(function($){

	/* Click to select shortcode input */
	$( ".fx-sc-input" ).click( function(){
		$( this ).select();
	});

	/* Media Modal var */
	var file_frame;

	/* === CLICK: UPLOAD/SELECT IMAGE BUTTON === */
	$( document.body ).on( 'click', '#fx-upload-button', function(e){
		e.preventDefault();
		if ( file_frame ) { file_frame.open(); return; }

		file_frame = wp.media.frames.file_frame = wp.media({
			className: 'media-frame fx-media-frame',
			frame: 'select',
			title: fx_photo_tag.title,
			library: { type: 'image' },
			button: { text:  fx_photo_tag.button_text },
			multiple: false,
		});

		file_frame.on( 'select', function(){
			var this_attachment = file_frame.state().get('selection').first().toJSON();
			var img_url = this_attachment.url;
			var img_width = this_attachment.width;
			var img_height = this_attachment.height;

			/* Insert Data + Image */
			$( '#fx-image-tag-id' ).val( this_attachment.id );
			$( "#fx-photo-tag-wrap" ).append( '<img class="fx-photo-tag-image" src="' + img_url + '" width="' + img_width + '" height="' + img_height + '">' );

			/* Hide/Show Button */
			$( "#fx-upload-button" ).hide();
			$( "#fx-remove-button" ).show();

		});
		file_frame.open();
	});

	/* === CLICK: REMOVE IMAGE BUTTON === */
	$( document.body ).on( 'click', '#fx-remove-button', function(e){
		e.preventDefault();

		/* Remove input + Image */
		$( '#fx-image-tag-id' ).val( '' );
		$( "#fx-photo-tag-wrap" ).empty();

		/* Hide/Show Button */
		$( "#fx-upload-button" ).show();
		$( "#fx-remove-button" ).hide();
	});

	/* === SELECT: COLOR OPTIONS === */
	$( '#select-color-scheme' ).change(function(){
		var this_color = $( this ).val();
		$( '#fx-photo-tag-wrap' ).removeClass().addClass( 'fx-photo-tag-color-' + this_color );
	});

	/* === FUNCTION: TAG REPOSITION === */
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
	/* On page load */
	fx_photo_tag_reposition();
	/* On column change (screen option) */
	$( 'input[name="screen_columns"]' ).change( function(){
		fx_photo_tag_reposition();
	});
	/* On window resize */
	$( window ).resize( function(){
		fx_photo_tag_reposition();
	});


	/* === CLICK: IMAGE (SHOW ADD TAG POP UP) === */
	$( document.body ).on( 'click', '.fx-photo-tag-image', function(e){
		e.preventDefault();

		/* Coordinate Offset */
		var offset_top = $( this ).offset().top - $( window ).scrollTop();
		var offset_left = $( this ).offset().left - $( window ).scrollLeft();
		/* Coordinate Display Position */
		var left_pos = e.clientX - offset_left;
		var top_pos = e.clientY - offset_top;
		/* Coordinate Actual Size */
		var x_pos = Math.round( left_pos / $( this ).width() * $( this ).attr( 'width' ) );
		var y_pos = Math.round( top_pos / $( this ).height() * $( this ).attr( 'height' ) );

		/* Clean Up Pop Up */
		$( '.fx-photo-tag-popup-input' ).show();
		$( '.fx-photo-tag-ajax-message' ).empty().hide();

		/* Clean Up Input */
		$( '.tag-text' ).val( fx_photo_tag.tag_default ); // "Label"
		$( '.tag-url' ).val('');
		$( '.tag-target' ).prop( 'checked', false );

		/* Add Coordinate Data + ID */
		$( '.top-pos' ).val( Math.round( top_pos ) );
		$( '.left-pos' ).val( Math.round( left_pos ) );
		$( '.x-pos' ).val( x_pos );
		$( '.y-pos' ).val( y_pos );

		/* Hide/Show Buttons (Hide Edit and Delete) */
		$( '.fx-photo-tag-popup-action' ).show();
		$( '#fx-add-tag' ).show();
		$( '#fx-edit-tag' ).hide();
		$( '#fx-delete-tag' ).hide();

		/* Open pop-up */
		$.fxBox.openBox({
			title  : fx_photo_tag.popup_title,
			target : '#fx-photo-tag-edit-popup',
			width  : '350px',
			height : '300px',
		});

		/* Reposition Tags */
		fx_photo_tag_reposition();
	});

	/* === AJAX: CREATE NEW TAG === */
	$( '#fx-add-tag' ).click( function(e){
		e.preventDefault();

		/* Add Spinner */
		$( '#fx-save-tag-spinner' ).addClass( 'is-active' );

		/* Var Object */
		var this_tag_datas = {
			left   : $( '.left-pos' ).val(),
			top    : $( '.top-pos' ).val(),
			x      : $( '.x-pos' ).val(),
			y      : $( '.y-pos' ).val(),
			text   : $( '.tag-text' ).val(),
			url    : $( '.tag-url' ).val(),
			target : $( '.tag-target' ).prop( 'checked' ),
		};
		/* Ajax */
		$.ajax({
			type: "POST",
			url: fx_photo_tag.ajax_url, /* from localized script */
			data:{
				action     : 'fx_photo_tag_add', /* "wp_ajax_*" */
				nonce      : fx_photo_tag.ajax_nonce,
				post_id    : fx_photo_tag.post_id,
				tag_datas  : this_tag_datas,
			},
			dataType: 'json',
			success: function( data ){
				/* Remove Spinner */
				$( '#fx-save-tag-spinner' ).removeClass( 'is-active' );

				/* Hide "Add" button, show "Edit" and "Delete" */
				$( '#fx-add-tag' ).hide();
				$( '#fx-edit-tag' ).show();
				$( '#fx-delete-tag' ).show();

				/* Add message to dom (?) */
				$( '.fx-photo-tag-ajax-message' ).append( '<p>' + data.message + '</p>' ).show();

				/* Add tag html to dom */
				$( '#fx-photo-tag-wrap' ).append( data.tag );

				/* Reposition Tags */
				fx_photo_tag_reposition();
			}
		});
	});

	/* === CLICK: TAG (SHOW EDIT TAG POP UP) === */
	$( document.body ).on( 'click', '.fx-photo-tag', function(e){
		e.preventDefault();

		/* Clean Up Pop Up */
		$( '.fx-photo-tag-popup-input' ).show();
		$( '.fx-photo-tag-ajax-message' ).empty().hide();

		/* Update Input */
		$('.tag-text').val( $( this ).attr( 'data-text' ) );
		$('.tag-url').val( $( this ).attr( 'data-url' ) );
		$('.left-pos').val( $( this ).css( 'left' ).replace( 'px', '' ) );
		$('.top-pos').val( $( this ).css( 'top' ).replace( 'px', '' ) );
		$('.x-pos').val( $( this ).attr( 'data-x' ) );
		$('.y-pos').val( $( this ).attr( 'data-y' ) );
		if( '_blank' == $( this ).attr( 'data-target' ) ){
			$('.tag-url').prop('checked', true);
		}
		else{
			$('.tag-url').prop('checked', false);
		}

		/* Hide/Show Buttons */
		$( '.fx-photo-tag-popup-action' ).show();
		$( '#fx-add-tag' ).hide();
		$( '#fx-edit-tag' ).show();
		$( '#fx-delete-tag' ).show();

		/* Open pop-up */
		$.fxBox.openBox({
			title  : fx_photo_tag.popup_title,
			target : '#fx-photo-tag-edit-popup',
			width  : '350px',
			height : '300px',
		});

		/* Reposition Tags */
		fx_photo_tag_reposition();
	});

	/* === AJAX: EDIT TAG === */
	$( '#fx-edit-tag' ).click( function(e){
		e.preventDefault();

		/* Add Spinner */
		$( '#fx-save-tag-spinner' ).addClass( 'is-active' );

		/* Var Object */
		var x_pos = $( '.x-pos' ).val();
		var y_pos = $( '.y-pos' ).val();
		var this_tag_datas = {
			left   : $( '.left-pos' ).val(),
			top    : $( '.top-pos' ).val(),
			x      : x_pos,
			y      : y_pos,
			text   : $( '.tag-text' ).val(),
			url    : $( '.tag-url' ).val(),
			target : $( '.tag-target' ).prop( 'checked' ),
		};

		/* This tag var */
		var this_tag = $( ".fx-photo-tag-" + x_pos + '_' + y_pos  );

		/* Ajax */
		$.ajax({
			type: "POST",
			url: fx_photo_tag.ajax_url, /* from localized script */
			data:{
				action     : 'fx_photo_tag_edit', /* "wp_ajax_*" */
				nonce      : fx_photo_tag.ajax_nonce,
				post_id    : fx_photo_tag.post_id,
				tag_datas  : this_tag_datas,
			},
			dataType: 'json',
			success: function( data ){
				/* Remove Spinner */
				$( '#fx-save-tag-spinner' ).removeClass( 'is-active' );

				/* Hide "Add" button, show "Edit" and "Delete" */
				$( '.fx-photo-tag-popup-action' ).show();
				$( '#fx-add-tag' ).hide();
				$( '#fx-edit-tag' ).show();
				$( '#fx-delete-tag' ).show();

				/* Add message to dom (?) */
				$( '.fx-photo-tag-ajax-message' ).append( '<p>' + data.message + '</p>' ).show();

				/* Remove tag html and re-add to dom */
				this_tag.remove();
				$( '#fx-photo-tag-wrap' ).append( data.tag );

				/* Reposition Tags */
				fx_photo_tag_reposition();
			}
		});
	});

	/* === AJAX: DELETE TAG === */
	$( '#fx-delete-tag' ).click( function(e){
		e.preventDefault();

		/* Add Spinner */
		$( '#fx-save-tag-spinner' ).addClass( 'is-active' );

		/* Var Object */
		var x_pos = $( '.x-pos' ).val();
		var y_pos = $( '.y-pos' ).val();
		var this_tag_datas = {
			x      : x_pos,
			y      : y_pos,
		};

		/* This tag var */
		var this_tag = $( ".fx-photo-tag-" + x_pos + '_' + y_pos  );

		/* Ajax */
		$.ajax({
			type: "POST",
			url: fx_photo_tag.ajax_url, /* from localized script */
			data:{
				action     : 'fx_photo_tag_delete', /* "wp_ajax_*" */
				nonce      : fx_photo_tag.ajax_nonce,
				post_id    : fx_photo_tag.post_id,
				tag_datas  : this_tag_datas,
			},
			dataType: 'json',
			success: function( data ){
				/* Remove Spinner */
				$( '#fx-save-tag-spinner' ).removeClass( 'is-active' );

				/* Hide Pop Up Content */
				$( '.fx-photo-tag-popup-input' ).hide();
				$( '.fx-photo-tag-popup-action' ).hide();

				/* Add message to dom (?) */
				$( '.fx-photo-tag-ajax-message' ).append( '<p>' + data.message + '</p>' ).show();

				/* Remove tag html */
				this_tag.remove();

				/* Reposition Tags */
				fx_photo_tag_reposition();
			}
		});
	});

});
