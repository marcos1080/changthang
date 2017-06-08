jQuery( document ).ready( function( $ ) {
	if( ! $( 'body' ).hasClass( 'widgets_access' ) ) {
		// Close all items on first load or reload.
		CHANG_SEARCH.addHandlers( $ );
		CHANG_SEARCH.setColorPickers( $ );
		CHANG_SEARCH.removeNonJSFields( $ );
	}

	// Update on AJAX events.
	$( document ).on( 'widget-added widget-updated', function() {
		CHANG_SEARCH.setColorPickers( $ );
		CHANG_SEARCH.removeNonJSFields( $ );
	});
});

var CHANG_SEARCH = {
	// Add colour pickers
	setColorPickers: function ( $ ) {
		$( '#widgets-right .chang-color-picker' ).each( function() {
			$( this ).wpColorPicker();
		});
	},

	// Removes disabled from javascript inputs and removes non javascript elements.
	removeNonJSFields: function( $ ) {
		$( '.hide-if-no-js input' ).removeAttr( 'disabled' );
		$( '.hide-if-js' ).remove();
	},

	addHandlers: function ( $ ) {
		var wp_media_post_id;

		// Media selector.
		$('body').on('click', '.upload-chang-search', function( event ){
			event.preventDefault();

			if( $( this ).attr( 'disabled' ) == 'disabled' ) {
				return;
			}

			// Uploading files
			var file_frame;
			wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
		
			var button = $(this);
			var set_to_post_id = button.closest( '.chang-search-icon' ).find( '.icon-id' ).val(); // Set this
			
			// If the media frame already exists, reopen it.
			if ( file_frame ) {
				// Set the post ID to what we want
				file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
				// Open frame
				file_frame.open();
				return;
			} else {
				// Set the wp.media post id so the uploader grabs the ID we want when initialised
				wp.media.model.settings.post.id = set_to_post_id;
			}
			
			// Create the media frame.
			file_frame = wp.media.frames.file_frame = wp.media({
				title: 'Select a image to upload',
				button: {
					text: 'Use this image',
				},
				multiple: false	// Set to true to allow multiple files to be selected
			});
			
			// When an image is selected, run a callback.
			file_frame.on( 'select', function() {
				// We set multiple to false so only get one image from the uploader
				attachment = file_frame.state().get('selection').first().toJSON();
				// Do something with attachment.id and/or attachment.url here
				button.closest( '.chang-search-icon' ).find( '.chang-search-icon-preview' ).attr( 'src', attachment.url );
				button.closest( '.chang-search-icon' ).find( '.icon-id' ).val( attachment.id );
				button.siblings( '.clear-chang-search' ).removeClass( 'hide-item' );
				// Restore the main post ID
				wp.media.model.settings.post.id = wp_media_post_id;
			});
			
			// Finally, open the modal
			file_frame.open();
		});
		
		// Restore the main ID when the add media button is pressed
		$( 'body' ).on( 'click', 'a.add_media', function() {
			wp.media.model.settings.post.id = wp_media_post_id;
		});

		$( 'body' ).on( 'click', '.clear-chang-search', function( event ) {
			event.preventDefault();
			if( $( this ).attr( 'disabled' ) == 'disabled' ) {
				return;
			}

			var wrapper = $( this ).closest( '.chang-search-icon-hover' );
			wrapper.find( 'input' ).val( '' );
			wrapper.find( 'img' ).attr( 'src', $( '#chang-search-placeholder-icon' ).attr( 'src') );
			$( this ).addClass( 'hide-item' );
		});

		$( 'body' ).on( 'change', '.chang-search-show-hover', function() {
			var wrapper = $( this ).siblings( '.chang-search-icon-hover' );
			if( $( this ).is( ':checked' ) ) {
				wrapper.find( 'label' ).removeClass( 'chang-search-icon-disabled' );
				wrapper.find( 'img' ).removeClass( 'chang-search-icon-disabled' );
				wrapper.find( 'a' ).removeAttr( 'disabled' );
			} else {
				wrapper.find( 'label' ).addClass( 'chang-search-icon-disabled' );
				wrapper.find( 'img' ).addClass( 'chang-search-icon-disabled' );
				wrapper.find( 'a' ).attr( 'disabled', 'disabled' );
			}
		});

		$( 'body' ).on( 'change', '.chang-search-show-icon', function() {
			var icon = $( this ).siblings( '.chang-search-icon-main' );
			var hover = $( this ).siblings( '.chang-search-icon-hover' );
			var show_hover = $( this ).siblings( '.chang-search-show-hover' );
			var show_hover_label = $( this ).siblings( '.chang-search-show-hover-label' );
			
			if( $( this ).is( ':checked' ) ) {
				icon.find( 'label' ).removeClass( 'chang-search-icon-disabled' );
				icon.find( 'img' ).removeClass( 'chang-search-icon-disabled' );
				icon.find( 'a' ).removeAttr( 'disabled' );
				show_hover.removeAttr( 'disabled' );
				show_hover_label.removeClass( 'chang-search-icon-disabled' );

				if( show_hover.is( ':checked' ) ) {
					hover.find( 'label' ).removeClass( 'chang-search-icon-disabled' );
					hover.find( 'img' ).removeClass( 'chang-search-icon-disabled' );
					hover.find( 'a' ).removeAttr( 'disabled' );
				}
			} else {
				icon.find( 'label' ).addClass( 'chang-search-icon-disabled' );
				icon.find( 'img' ).addClass( 'chang-search-icon-disabled' );
				icon.find( 'a' ).attr( 'disabled', 'disabled' );
				show_hover.attr( 'disabled', 'disabled' );
				show_hover_label.addClass( 'chang-search-icon-disabled' );
				hover.find( 'label' ).addClass( 'chang-search-icon-disabled' );
				hover.find( 'img' ).addClass( 'chang-search-icon-disabled' );
				hover.find( 'a' ).attr( 'disabled', 'disabled' );
			}
		});

		// Range text update
		$( 'body' ).on( 'input change', '.chang-search-range', function() {
			var val = $( this ).val();
            $( this ).siblings( 'div' ).html( val + 'px' );
            $( this ).attr( 'value', val );
            console.log(val);
		});

		$( 'body' ).on( 'input change', '.chang-search-range-percent', function() {
			var val = $( this ).val();
            $( this ).siblings( 'div' ).html( val + '%' );
            $( this ).attr( 'value', val );
            console.log(val);
		});

		$( 'body' ).on( 'input change', '.chang-search-range-icon-gap', function() {
			var val = $( this ).val();
			var iconSize = $( this ).closest( 'td' ).find( '.chang-layout-icon' ).outerWidth();
			var borderWidth = $( this ).closest( 'td' ).find( '.chang-layout-search' ).css( 'border-left-width' );
			var offset = iconSize + parseInt( val ) + ( 2 * parseInt( borderWidth ) );

			$( this ).closest( 'td' ).find( '.chang-layout-search' ).css({
				'width': 'calc( 100% - ' + offset + 'px )'
			});
            $( this ).siblings( 'small' ).html( val + 'px' );
            $( this ).val( val );
		});
	},
}