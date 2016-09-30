window.cwv2Admin = {};
( function( window, $, app ) {

	// Constructor
	app.init = function() {
		app.cache();

		if ( app.meetsRequirements() ) {
			app.bindEvents();
		}
	};

	// Cache all the things
	app.cache = function() {
		app.$c = {
			window: $(window),
			// fooSelector: $( '.foo' ),
		};
	};

	// Combine all events
	app.bindEvents = function() {
		// app.$c.window.on( 'load', app.doFoo );
	};

	// Do we meet the requirements?
	app.meetsRequirements = function() {
		// Basically check
		// return app.$c.fooSelector.length;
	};

	// Some function
	// app.doFoo = function() {
		// do stuff
	// };

	// Engage
	$( app.init );

})( window, jQuery, window.cwv2Admin );