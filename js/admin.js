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
			imgUploadBtn: $( '.upload_image_button' ),
			select2Objects: $( '.cwv2_select2' ),
			colorSelectors: $( '.color_select' ),
		};
	};

	// Combine all events
	app.bindEvents = function() {
		app.$c.imgUploadBtn.on( 'click', app.handleImageUploader );
		app.$c.window.on( 'load', app.windowLoad );
	};

	app.windowLoad = function() {
		app.$c.select2Objects.select2();
		app.$c.colorSelectors.spectrum({
			clickoutFiresChange: true,
			showInput: true,
			preferredFormat: 'hex6'
		});
	};

	// Do we meet the requirements?
	app.meetsRequirements = function() {
		return app.$c.imgUploadBtn.length && app.$c.select2Objects.length;
	};

	/**
	 * Handles Media Uploads
	 *
	 * @returns {boolean}
	 */
	app.handleImageUploader = function() {
		var btnObj = $( this );

		window.uploadID = btnObj.data( 'target-id' );
		if ( window.file_frame ) {
			window.file_frame.open();
			return true;
		}

		window.file_frame = wp.media.frames.file_frame = wp.media( {
			title: btnObj.data( 'uploader-title' ),
			button: {
				text: btnObj.data( 'uploader-btn-txt' )
			},
			multiple: false,
		} );

		window.file_frame.on( 'select', function() {
			var attachment = window.file_frame.state().get( 'selection' ).first().toJSON();
			$( '#' + window.uploadID ).val( attachment.url );

			window.console.log( attachment );

		});

		window.file_frame.open();
	};

	// Engage
	$( app.init );

})( window, jQuery, window.cwv2Admin );