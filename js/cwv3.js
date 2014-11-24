/* global cwv3_params */
window.cwv3 = ( function( window, document, $ ){

	var app = {};

	app.cache = function(){
		app.$dialog = $('.cwv3_dialog');
		app.$auth   = app.$dialog.find( '.cwv3.auth' );
		app.$denial = app.$dialog.find( '.cwv3.denied' );
		app.$exit   = app.$dialog.find( '.cwv3_exit' );
		app.$enter  = app.$dialog.find( '.cwv3_enter' );
		app.cookie_name = ( '' !== cwv3_params.cookie_name ) ? 'cwv3_cookie_' + cwv3_params.cookie_name : false;
		app.redirect_url = ( '' === cwv3_params.redirect_url || '#' === cwv3_params.redirect_url ) ? 'http://google.com' : cwv3_params.redirect_url;
	};

	app.init = function(){
		app.cache();

		// Register handlers
		$( 'body' ).on( 'click', '.cwv3_enter', app.enter_handler );
		$( 'body' ).on( 'click', '.cwv3_exit', app.exit_handler );

		if( app.cookie_name ){
			// We need to set a cookie, so show the dialog.
			app.show_popup();
		}

		
	};

	app.enter_handler = function( evt ){
		evt.preventDefault();

	};

	app.exit_handler = function( evt ){
		evt.preventDefault();

	};

	app.set_cookie = function(){
		// Set the cookie
	};

	app.show_popup = function(){
		
	};

	app.dialog_switch = function(){
		var cookie_data = $.cookie( app.cookie_name );
		if( 'denied' === cookie_data ){
			//app.$auth.remove(); // Remove the main dialog
			if( 'redirect' === cwv3_params.denial_method ){
				window.location.replace( app.redirect_url );
			}
		} else if( undefined === cookie_data ){
			app.$denial.remove(); // Remove the denied box instead.
		}
	};

	$( document ).ready( app.init );

	return app;

})( window, document, jQuery );

/*
jQuery(document).ready(function($) {
	var enter = $('#cw_enter_link');
	var exit = $('#cw_exit_link');
	
	if(cwv3_params.sd === "1"){
		$.colorbox(
			{
				scrolling:	false,
				overlayClose:	false,
				escKey:	false,
				inline:	true,
				href: '#cwv3_auth',
				maxWidth: '80%',
				loop: false,
				onLoad: function(){
					$('#cboxClose').remove();
				},
				className: 'cwv3_box',
				opacity:	cwv3_params.opacity
			}
		);
		
		enter.click(function(e){
			if( typeof(e) !== "undefined" ){
				e.preventDefault();
			}

			$.post(cwv3_params.admin_url, {action: cwv3_params.action, nonce: cwv3_params.nonce, id: cwv3_params.id, method: 'enter'}, function(){
				if(cwv3_params.enter === "#"){
					$.colorbox.close();
				}else{
					window.location = cwv3_params.enter;
				}
			});
		});	
			
		exit.click(function(e){
			if( typeof(e) !== "undefined" ){
				e.preventDefault();
			}
			$.post(cwv3_params.admin_url, {action: cwv3_params.action, nonce: cwv3_params.nonce, id: cwv3_params.id, method: 'exit'}, function(){
				window.location = cwv3_params.exit;
			});
		});
	}
	
	
}); */