/* global cwv3_params */
// opacity cookie_path cookie_name cookie_time denial_enabled denial_method redirect_url   


window.cwv3 = ( function( window, document, $ ){

	var app = {};

	app.cache = function(){
		app.$dialog 	 = $('.cwv3_dialog');
		app.$content     = app.$dialog.find( '.cwv3_content' );
		app.$auth   	 = app.$dialog.find( '.cwv3.auth' );
		app.$denial 	 = app.$dialog.find( '.cwv3.denied' );
		app.$exit   	 = app.$dialog.find( '.cwv3_exit' );
		app.$enter  	 = app.$dialog.find( '.cwv3_enter' );
		app.$buttons     = app.$dialog.find( '.cwv3_btns' );
		app.$title       = app.$dialog.find( '.cwv3_title' );
		app.$overlay     = $( '.cwv3.dialog-overlay' );
		app.cookie_name  = ( '' !== cwv3_params.cookie_name ) ? 'cwv3_cookie_' + cwv3_params.cookie_name : false;
		app.redirect_url = ( '' === cwv3_params.redirect_url || '#' === cwv3_params.redirect_url ) ? 'http://google.com' : cwv3_params.redirect_url;
		app.cookie_data  = $.cookie( app.cookie_name );
		app.diag         = {
			max_percent_width  : 50,
			max_percent_height : 50,
		};
		app.timeout = '';
	};

	app.init = function(){
		app.cache();

		// Register handlers
		$( 'body' ).on( 'click', '.cwv3_enter', app.enter_handler );
		$( 'body' ).on( 'click', '.cwv3_exit', app.exit_handler );
	
		// Don't resize EVERY time, set it to an interval of half a second
		$( window ).resize( function(){
			clearTimeout( app.timeout );
			app.timeout = setTimeout( app.center_dialog, 500 );
		});

		if( app.cookie_name ){
			// We need to set a cookie, so show the dialog.
			app.dialog_switch();
		}
	};

	app.center_dialog = function(){
		app.$content.css( {'height' : ''} );
		
		var diag ={
				x: app.$dialog.width(),
				y: app.$dialog.height(),
			},
			vp = {
				x: window.innerWidth,
				y: window.innerHeight,
			};

		// Remove the 'height' property from the content

		var diag_pos = {
				left: ( vp.x - diag.x ) * 0.5,
				top : ( vp.y - diag.y ) * 0.5,
			},
			content_height = ( app.$dialog.height() - 10 ) - app.$buttons.outerHeight( true ) - app.$title.outerHeight( true );

		app.$content.animate( { height: content_height }, 250, 'swing', function(){
			app.$dialog.animate( diag_pos, 250, 'swing', app.cache );
		} );

		// app.cache();
	};

	app.enter_handler = function( evt ){
		evt.preventDefault();
		// Have to set the cookie first.
		app.set_cookie( 'enter' );
		var $enter_url = app.$enter.find( 'a' ).attr( 'href' );
		if( '#' === $enter_url || '' === $enter_url ){
			app.close_handler();
		} else {
			window.location.replace( $enter_url );
		}
	};

	app.exit_handler = function( evt ){
		evt.preventDefault();
		if( 'denied' !== app.cookie_data ){
			app.set_cookie( 'exit' );
		}
		var $exit_url = app.$exit.find( 'a' ).attr( 'href' );
		if( '#' === $exit_url || '' === $exit_url ){
			window.location.replace( 'http://google.com' );
		} else {
			window.location.replace( $exit_url );
		}
	};

	app.set_cookie = function( method ){
		// Set the cookie
		method = method === 'exit' ? 'denied' : true;
		var cookie_data = {
			expires : parseInt( cwv3_params.cookie_time ),
			path    : cwv3_params.cookie_path,
		};

		// Should work with sessions
		if ( 0 === cookie_data.expires ) {
			cookie_data.expires = null;
		}

		$.cookie( app.cookie_name, method, cookie_data );
	};

	app.log = function() {
		if ( window.console ) {
			window.console.log( Array.prototype.slice.call( arguments ) );
		}
	};

	app.close_handler = function(){
		app.$dialog.fadeOut( 100, function(){
			app.$overlay.fadeOut( 100 );
		});		
	};

	app.dialog_switch = function(){
		if( 'denied' === app.cookie_data ){
			app.$auth.remove(); // Remove the main dialog
			if( 'redirect' === cwv3_params.denial_method && '' !== cwv3_params.denial_enabled  ){
				window.location.replace( app.redirect_url );
			} else {
				app.show_popup();
			}
		} else if( undefined === app.cookie_data ){
			app.$denial.remove(); // Remove the denied box instead.
			app.show_popup();
		}
	};

	app.show_popup = function(){
		app.$overlay.fadeIn( 200, function(){
			app.$dialog.fadeIn( 100, function(){
				app.center_dialog();
			} );
		});
	};

	$( document ).ready( app.init );

	return app;

})( window, document, jQuery );