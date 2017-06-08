jQuery(document).ready(function( $ ) {
	if( ! $('body').hasClass('widgets_access') ) {
		// Close all items on first load or reload.
		$('.social-media-list-item-fields .social-media-open').val( 'false' );
		CHANG_SOCIALMEDIA.setup( $ );
		CHANG_SOCIALMEDIA.addHandlers( $ );
	}

	$(document).ajaxSuccess(function() {
		CHANG_SOCIALMEDIA.setup( $ );
	});
});

var CHANG_SOCIALMEDIA = {
	setup: function ( $ ) {
		// On an ajax update (after a save) open items that were left open.
		$('.social-media-list-item-fields').each( function () {
			if( $( this ).find( '.social-media-open' ).val() == 'false' ) {
				$( this ).addClass('hide-item');
			}
		});

		// Make the list items sortable by dragging.
		$( ".social-media-sortable-list" ).sortable({
			items: '.social-media-list-item',
			opacity: 0.7,
			cursor: 'grabbing',
			axis: 'y',
			handle: '.social-media-list-item-title',
			placeholder: '.sortable-placeholder',
			start: function (event, ui) {
				ui.placeholder.height( ui.helper.height() );
			},
			update: function() {
				CHANG_SOCIALMEDIA.updateItemOrderElement( $( this ) );
			}
		});
		
		// Stop the possibility of selectable handles being triggered.
		$( ".social-media-sortable-list .social-media-list-item-title" ).disableSelection();	
	},

	// Insert the new order of items into the 'item-order' input.
	updateItemOrderElement: function ( self ) {
		var wrapper = self.closest( '.widget-content' );
		wrapper.find( '.item-order' ).val( wrapper.find( '.social-media-sortable-list' ).sortable( 'toArray' ) );
	},

	showAddButtonCheck: function ( $, list ) {
		if( list.find( '.social-media-invalid' ).length ) {
			list.find( '.add-button-row' ).addClass( 'hide-item' );
		} else {
			list.find( '.add-button-row' ).removeClass( 'hide-item' );
		}
	},

	isValidItem: function ( $, item ) {
		var valid = true;

		if( item.find( '.social-media-icon-main .icon-id' ).val() == '' ) {
			valid = false;
		} else {
			item.find( '.social-media-error-label-icon' ).addClass( 'hide-item' );
		}

		var nameField = item.find( '.social-media-name' );
		if( nameField.val() == '' ) {
			valid = false;
			item.find( '.item-title' ).html( 'Unnamed Icon' );
		} else {
			item.find( '.item-title' ).html( nameField.val() );
			nameField.removeClass( 'social-media-error-input' );
		}

		var urlField = item.find( '.social-media-url' );
		var urlLabel = item.find( '.social-media-error-label-url' );
		if( urlField.val() == '' ) {
			valid = false;
			urlLabel.addClass( 'hide-item' );
			urlField.removeClass( 'social-media-error-input' );
		} else if ( ! CHANG_SOCIALMEDIA.isValidUrl( urlField.val() ) ) {
			valid = false;
			urlField.addClass( 'social-media-error-input' );
			urlLabel.removeClass( 'hide-item' );
		} else {
			urlField.removeClass( 'social-media-error-input' );
			urlLabel.addClass( 'hide-item' );
		}

		if( valid ) {
			item.removeClass( 'social-media-invalid social-media-error' );
			item.find( '.social-media-show-icon-checkbox' ).removeAttr( 'disabled' )
		} else {
			item.addClass( 'social-media-invalid' );
			item.find( '.social-media-show-icon-checkbox' ).attr({ 
				'disabled': true,
				'checked': false
			});
		}

		CHANG_SOCIALMEDIA.showAddButtonCheck( $, item.closest( '.widget-content' ) );
		return valid;
	},

	isValidUrl: function ( url ) {
	    return /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test( url );
	},

	addHandlers: function ( $ ) {
		// Used to measure the time the mouse has been clicked down.
		var startTime, endTime;

		$( 'body' ).on( 'mousedown', '.social-media-list-item-title', function() {
			startTime = new Date().getTime();
		});

		$( 'body' ).on( 'mouseup', '.social-media-list-item-title', function() {
			endTime = new Date().getTime();

			// Only toggle if the mouse was clicked for less that 250 milliseconds.
			// Stops the toggle from occuring when items are dragged for sorting.
			if ( endTime - startTime < 250 ) {
				$(this).parent().find( '.social-media-list-item-fields' ).slideToggle( 200, function() {
					var arrow = $(this).parent().find( '.social-media-action' );
					if ( arrow.hasClass( 'flip' ) ) {
						arrow.removeClass( 'flip' );
					} else {
						arrow.addClass( 'flip' );
					}

					// Set open status
					var openState = $( this ).find( '.social-media-open' );
					if( openState.val() == 'true' ) {
						openState.val( 'false' );
					} else {
						openState.val( 'true' );
					}
				} );
			}
		});

		// Remove an item.
		$( 'body' ).on( 'click', '.social-media-remove', function() {
			var wrapper = $(this).closest( '.social-media-sortable-list' );;
			var item = $( this ).closest( '.social-media-list-item' ).fadeOut( 500, function() {
				var deletedId = CHANG_SOCIALMEDIA.getIdNumber( $( this ).attr( 'id' ) );
				var itemTotal = $( this ).closest( '.widget-content' ).find( '.social-media-list-item' ).length - 1;
				$( this ).closest( '.widget-content' ).find( 'item-total' ).val( itemTotal );

				$( this ).remove();			

				// Hide remove link if there is only one item left.
				if ( wrapper.find( '.social-media-remove' ).length == 1 ) {
					wrapper.find( '.social-media-remove' ).addClass( 'hide-item' );
				}

				wrapper.find( '.social-media-list-item' ).each( function() {
					var thisId = CHANG_SOCIALMEDIA.getIdNumber( $( this ).attr( 'id' ) );
					
					if ( thisId > deletedId ) {
						$( this ).attr( 'id', CHANG_SOCIALMEDIA.decrementIdNumber( $( this ).attr( 'id' ) ) );
						$( this ).find( 'label' ).each( function() {
							var newForLabel = CHANG_SOCIALMEDIA.decrementIdNumber( $( this ).attr( 'for' ) );
							$( this ).attr( 'for', newForLabel );
						});

						$( this ).find( 'input' ).each( function() {
							var newId = CHANG_SOCIALMEDIA.decrementIdNumber( $( this ).attr( 'id' ) );
							var newName = CHANG_SOCIALMEDIA.decrementIdNumber( $( this ).attr( 'name' ) );
							$( this ).attr( 'id', newId );
							$( this ).attr( 'name', newName );
						});
					}
				});

				CHANG_SOCIALMEDIA.updateItemOrderElement( wrapper );
				CHANG_SOCIALMEDIA.showAddButtonCheck( $, wrapper.closest( '.widget-content' ) );
			});
		});

		// Add an item.
		$( 'body' ).on( 'click', '.add-social-media', function( event ) {
			event.preventDefault();
			var widgetWrapper = $(this).closest( '.widget-content' );
			var itemNumber = widgetWrapper.find( '.social-media-list-item' ).length;

			// Create new item then adjust existing values to new.
			var newItem = widgetWrapper.find( '.social-media-list-item' ).last().clone();

			var newId = CHANG_SOCIALMEDIA.setIdNumber( newItem.attr( 'id' ), itemNumber )
			newItem.attr( 'id', newId );
			newItem.find( '.item-title' ).html( 'Unnamed Icon' );
			newItem.find( '.social-media-list-item-fields' ).hide();

			newItem.find( 'label' ).each( function() {
				var newForLabel = CHANG_SOCIALMEDIA.setIdNumber( $( this ).attr( 'for' ), itemNumber  );
				$( this ).attr( 'for', newForLabel );
			});

			newItem.find( 'input' ).each( function() {
				var newId = CHANG_SOCIALMEDIA.setIdNumber( $( this ).attr( 'id' ), itemNumber  );
				var newName = CHANG_SOCIALMEDIA.setIdNumber( $( this ).attr( 'name' ), itemNumber  );
				$( this ).attr( 'id', newId );
				$( this ).attr( 'name', newName );
				$( this ).val( '' );
			});

			var placeholderIcon = $( '#social-media-placeholder-icon' );
			newItem.find( '.social-media-icon-preview' ).attr( 'src', placeholderIcon.attr( 'src' ) );

			newItem.insertAfter( widgetWrapper.find( '.social-media-list-item' ).last() );

			CHANG_SOCIALMEDIA.updateItemOrderElement( $( this ) );
			CHANG_SOCIALMEDIA.isValidItem( $, newItem );

			var itemTotal = $( this ).closest( '.widget-content' ).find( '.social-media-list-item' ).length;
			$( this ).closest( '.widget-content' ).find( '.item-total' ).val( itemTotal );
			$( this ).closest( '.widget-content' ).find( '.social-media-remove' ).removeClass( 'hide-item' );

			// Trigger open event.
			startTime = new Date().getTime();
			newItem.find( '.social-media-list-item-title' ).trigger( 'mouseup' );
		});

		var wp_media_post_id;

		// Media selector.
		$('body').on('click', '.upload-social-media', function( event ){
			event.preventDefault();
			// Uploading files
			var file_frame;
			wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
		
			var button = $(this);
			var set_to_post_id = button.closest( '.social-media-icon' ).find( '.icon-id' ).val(); // Set this
			
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
				button.closest( '.social-media-icon' ).find( '.social-media-icon-preview' ).attr( 'src', attachment.url );
				button.closest( '.social-media-icon' ).find( '.icon-id' ).val( attachment.id );
				button.siblings( '.clear-social-media' ).show();
				CHANG_SOCIALMEDIA.isValidItem( $, button.closest( '.social-media-list-item' ) );
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

		$( 'body' ).on( 'click', '.clear-social-media', function( event ) {
			event.preventDefault();
			var wrapper = $( this ).closest( '.social-media-icon-hover' );
			wrapper.find( 'input' ).val( '' );
			wrapper.find( 'img' ).attr( 'src', '' );
			$( this ).hide();
		});

		$( 'body' ).on( 'change', '.social-media-show-hover', function() {
			var wrapper = $( this ).siblings( '.social-media-icon-hover' );
			if( $( this ).is( ':checked' ) ) {
				wrapper.find( 'label' ).removeClass( 'social-media-icon-disabled' );
				wrapper.find( 'img' ).removeClass( 'social-media-icon-disabled' );
				wrapper.find( 'a' ).removeAttr( 'disabled' );
			} else {
				wrapper.find( 'label' ).addClass( 'social-media-icon-disabled' );
				wrapper.find( 'img' ).addClass( 'social-media-icon-disabled' );
				wrapper.find( 'a' ).attr( 'disabled', 'disabled' );
			}
		});

		// Validation handlers
		$( 'body' ).on( 'blur', '.social-media-name', function() {
			CHANG_SOCIALMEDIA.isValidItem( $, $( this ).closest( '.social-media-list-item' ) );
		});

		$( 'body' ).on( 'blur', '.social-media-url', function() {
			CHANG_SOCIALMEDIA.isValidItem( $, $( this ).closest( '.social-media-list-item' ) );
		});

		// Range text update
		jQuery( 'body' ).on( 'input change', '.social-media-range', function() {
			var val = jQuery( this ).val();
            jQuery( this ).siblings( 'div' ).html( val + 'px' );
            jQuery( this ).val( val )
		});
	},

	incrementIdNumber: function ( id ) {
		var idParts = id.split( '-' );
		if ( idParts[idParts.length - 1].indexOf( ']' ) == -1 ) {
			idParts[idParts.length - 1] = parseInt( idParts[idParts.length - 1] ) + 1;
		} else {
			idParts[idParts.length - 1] = ( parseInt( idParts[idParts.length - 1] ) + 1 ).toString() + ']';
		}
		var newId = idParts.join( '-' );

		return newId;
	},

	decrementIdNumber: function ( id ) {
		var idParts = id.split( '-' );
		if ( idParts[idParts.length - 1].indexOf( ']' ) == -1 ) {
			idParts[idParts.length - 1] = parseInt( idParts[idParts.length - 1] ) - 1;
		} else {
			idParts[idParts.length - 1] = ( parseInt( idParts[idParts.length - 1] ) - 1 ).toString() + ']';
		}
		var newId = idParts.join( '-' );

		return newId;
	},

	getIdNumber: function ( idString ) {
		var idParts = idString.split( '-' );

		return parseInt( idParts[idParts.length - 1] );
	},

	setIdNumber: function ( idString, newId ) {
		var idParts = idString.split( '-' );
		if ( idParts[idParts.length - 1].indexOf( ']' ) == -1 ) {
			idParts[idParts.length - 1] = newId;
		} else {
			idParts[idParts.length - 1] = newId + ']';
		}
		var newId = idParts.join( '-' );

		return newId;
	}
}