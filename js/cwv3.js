/* global cwv3_params*/


window.cwv3 = ( function( window, document, $ ){

	var app = {};

	app.cache = function(){
		app.$dialog 	 = $('.cwv3_dialog');
		app.$auth   	 = app.$dialog.find( '.cwv3.auth' );
		app.$denial 	 = app.$dialog.find( '.cwv3.denied' );
		app.$exit   	 = app.$dialog.find( '.cwv3_exit' );
		app.$enter  	 = app.$dialog.find( '.cwv3_enter' );
		app.$overlay     = $( '.cwv3.dialog-overlay' );
		app.cookie_name  = ( '' !== cwv3_params.cookie_name ) ? 'cwv3_cookie_' + cwv3_params.cookie_name : false;
		app.redirect_url = ( '' === cwv3_params.redirect_url || '#' === cwv3_params.redirect_url ) ? 'http://google.com' : cwv3_params.redirect_url;
		app.cookie_data  = $.cookie( app.cookie_name );
	};

	app.init = function(){
		app.cache();

		// Register handlers
		$( 'body' ).on( 'click', '.cwv3_enter', app.enter_handler );
		$( 'body' ).on( 'click', '.cwv3_exit', app.exit_handler );


		if( app.cookie_name ){
			// We need to set a cookie, so show the dialog.
			app.dialog_switch();
		}		
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

// opacity cookie_path cookie_name cookie_time denial_enabled denial_method redirect_url   
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
		$.cookie( app.cookie_name, method, { 
			expires : parseInt( cwv3_params.cookie_time ),
			path    : cwv3_params.cookie_path,
		});
	};

	app.close_handler = function(){
		app.$dialog.fadeOut( 100, function(){
			app.$overlay.fadeOut( 100 );
		});		
	};

	app.dialog_switch = function(){
		if( 'denied' === app.cookie_data ){
			app.$auth.remove(); // Remove the main dialog
			if( 'redirect' === cwv3_params.denial_method ){
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
			app.$dialog.fadeIn( 100 );
		});
	};

	$( document ).ready( app.init );

	return app;

})( window, document, jQuery );