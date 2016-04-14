/*
 * jQuery fxBox v.1.0.0
 * Thickbox Alternative.
 * Author: David Chandra
 * Copyright 2016 Genbu Media
 * @link http://html.shellcreeper.com/layout-collection/fx-box/
 */
;
(function($){

	/* jQuery Object Method */
	$.fxBox = {
		createBox:function(){
			if( ! $( '#fx-box' ).length ){
				$( '<div id="fx-box-overlay" style="display:none;"></div>' +
					'<div id="fx-box" style="display:none;">' +
						'<div id="fx-box-container">' +
							'<div id="fx-box-title"><span class="fx-box-close"></span></div>' +
							'<div id="fx-box-content"></div>' +
						'</div>' +
					'</div>'
				).appendTo( 'body' );
			}
		},
		openBox:function( options ){
			if ( options === undefined ) { options = {}; }
			var default_options = {
				title     : '',
				target    : '',
				id        : '',
				content   : false,
				width     : '300px',
				height    : 'auto',
			};
			options = $.extend( default_options, options );
			$( "#fx-box-title" ).prepend( options.title );
			$( "#fx-box" ).attr( 'data-target', options.target );
			if( '' !== options.id ){
				$( "#fx-box" ).attr( 'data-content', '#' + options.id );
			}
			if( ! $( options.target ).length && false !== options.content ){
				$( '<div id="' + options.id + '"></div>' ).html( options.content ).prependTo( "#fx-box-content" );
			}
			else{
				$( options.target ).show().prependTo( "#fx-box-content" );
			}
			$( "#fx-box" ).css({ "width":options.width,"height":options.height });
			$( "#fx-box-overlay,#fx-box" ).show();
			$( "body" ).addClass( "fx-box-stop-scoll" );
			this.resizeBox();
		},
		closeBox:function(){
			$( "#fx-box-title" ).html( '<span class="fx-box-close"></span>' );
			$( $( "#fx-box" ).attr( "data-content" ) ).remove();
			$( $( "#fx-box" ).attr( "data-target" ) ).hide().appendTo( "body" );
			$( "#fx-box-content" ).empty().css({ "height": "" });
			$( "#fx-box" ).attr( "data-target", '' ).attr( "data-content", '' ).css({ "width": "", "height": "" });
			$( "#fx-box,#fx-box-overlay" ).hide();
			$( "body" ).removeClass( "fx-box-stop-scoll" );
		},
		resizeBox:function(){
			$( "#fx-box-content" ).css( "height", ( $( '#fx-box' ).height() - $( '.fx-box-close' ).height() ) + "px" );
		},
	};

	/* Create Box */
	$.fxBox.createBox();

	/* Open the box */
	$( document.body ).on( 'click', '.fx-box', function(e){
		e.preventDefault();
		var options = {};
		options.title = $( this ).attr( 'data-title' );
		options.target = $( this ).attr( 'data-target' );
		options.height = $( this ).attr( 'data-height' );
		options.width = $( this ).attr( 'data-width' );
		$.fxBox.openBox( options );
	});

	/* Close the box */
	$( document.body ).on( 'click', '.fx-box-close,#fx-box-overlay', function(e){
		e.preventDefault();
		$.fxBox.closeBox();
	});

	/* Resize Box */
	$( window ).resize( function(){
		$.fxBox.resizeBox();
	});

})(jQuery);