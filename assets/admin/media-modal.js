/**
 * Based on this example:
 * https://gist.github.com/Fab1en/4586865
**/
;
(function($){

	/* === Custom State : this controller contains your application logic === */
	wp.media.controller.fxPhotoTag = wp.media.controller.State.extend({
		initialize: function(){
			this.props = new Backbone.Model({ fx_photo_tag_data: '' });
		},
	});

	/* === Custom Content : this view contains the main panel UI === */
	wp.media.view.fxPhotoTag = wp.media.View.extend({
		className: 'fx-photo-tag-browser',
		initialize: function(){
			/* if it's not loaded yet. add "click to load" button */
			if( ! $( '.fx-photo-tag-browser-items' ).length ){
				var fx_photo_tag_content = $(
					'<div id="fx-photo-tag-browser">' +
						'<a id="fx-photo-tag-browser-load" href="#" class="button">' + fx_photo_tag_modal.click_me + '</a>' +
						'<span class="spinner"></span>' +
					'</div>' +
					'<div id="fx-photo-tag-toolbar"><a id="fx-photo-tag-insert" href="#" class="button media-button button-primary button-large disabled">' + fx_photo_tag_modal.insert + '</a></div>'
				);
			}
			/* Already loaded, simply clone and use it. */
			else{
				var items = $( '.fx-photo-tag-browser-items' ).html();
				var fx_photo_tag_content = $(
					'<div id="fx-photo-tag-browser">' +
						'<div class="fx-photo-tag-browser-items">' + items + '</div>' +
					'</div>' +
					'<div id="fx-photo-tag-toolbar"><a id="fx-photo-tag-insert" href="#" class="button media-button button-primary button-large disabled">' + fx_photo_tag_modal.insert + '</a></div>'
				);
			}
			/* Add in this tab content. */
			this.$el.append( fx_photo_tag_content );
		},
	});

	/* === Supersede the default MediaFrame.Post view === */
	var fxPhotoTagMediaFrame = wp.media.view.MediaFrame.Post;
	wp.media.view.MediaFrame.Post = fxPhotoTagMediaFrame.extend({
		initialize: function() {
			fxPhotoTagMediaFrame.prototype.initialize.apply( this, arguments );
			this.states.add([
				new wp.media.controller.fxPhotoTag({
					id:         'fx-photo-tag-action',
					menu:       'default',
					content:    'fx_photo_tag',
					title:      fx_photo_tag_modal.title,
					priority:   200,
					toolbar:    'main-fx-photo-tag-action',
					type:       'link',
				})
			]);
			this.on( 'content:render:fx_photo_tag', this.fxPhotoTagContent, this );
		},

		fxPhotoTagContent: function(){
			this.$el.addClass( 'hide-router' ).addClass( 'hide-toolbar' );
			var view = new wp.media.view.fxPhotoTag({
				controller: this,
				model: this.state().props
			});
			this.content.set( view );
		}

	});

	/* Load Photo List */
	$( document.body ).on( 'click', '#fx-photo-tag-browser-load', function(e){
		$( '#fx-photo-tag-browser .spinner' ).addClass( 'is-active' );
		$( "#fx-photo-tag-browser-load" ).hide();
		if( ! $( ".fx-photo-tag-browser-items" ).length ){
			$.ajax({
				type: "POST",
				url: fx_photo_tag_modal.ajax_url, /* from localized script */
				data:{
					action     : 'fx_photo_tag_modal_init', /* "wp_ajax_*" */
					nonce      : fx_photo_tag_modal.ajax_nonce,
				},
				success: function( data ){
					$( '#fx-photo-tag-browser .spinner' ).remove();
					$( "#fx-photo-tag-browser-load" ).remove();
					$( data ).appendTo( "#fx-photo-tag-browser" );
					$( data ).appendTo( 'body' );
				}
			});
		}
		else{
			$( "#fx-photo-tag-browser-load" ).remove();
			$( '.fx-photo-tag-browser-items' ).clone().appendTo( '#fx-photo-tag-browser' );
		}
	});
	/* Select Photo */
	$( document.body ).on( 'click', '.fx-photo-tag-item', function(e){
		e.preventDefault();
		var photo_id = $( this ).attr( 'data-id' );
		$( this ).addClass( 'selected' );
		$( this ).siblings( '.fx-photo-tag-item' ).removeClass( 'selected' );
		$( "#fx-photo-tag-insert" ).removeClass( 'disabled' ).attr( 'data-id', photo_id );
	});
	/* Insert Shortcode */
	$( document.body ).on( 'click', '#fx-photo-tag-insert', function(e){
		e.preventDefault();
		if( $( this ).hasClass( 'disabled' ) && $( this ).attr( 'data-id' ) ){
			return false;
		}
		else{
			wp.media.editor.insert( '<p>[fx-photo-tag id="' + $( this ).attr( 'data-id' ) + '"]</p>' );
		}
	});

})(jQuery);

