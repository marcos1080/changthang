jQuery(document).ready(function( $ ) {
	if( $('body').hasClass('widgets-php') ) {
		// Close all items on first load or reload.
		$('.list-item-fields .attribution-open').val( 'false' );
		CHANG_ATTRIBUTION.setup( $ );
		CHANG_ATTRIBUTION.addHandlers( $ );
	}

	$(document).ajaxSuccess(function() {
		CHANG_ATTRIBUTION.setup( $ );
	});
});

var CHANG_ATTRIBUTION = {
	setup: function ( $ ) {
		// On an ajax update (after a save) open items that were left open.
		$('.list-item-fields').each( function () {
			if( $( this ).find( '.attribution-open' ).val() == 'false' ) {
				$( this ).addClass('hide-item');
			}
		});

		// Make the list items sortable by dragging.
		$( ".attribution-sortable-list" ).sortable({
			items: '.attribution-list-item',
			opacity: 0.7,
			cursor: 'grabbing',
			axis: 'y',
			handle: '.list-item-title',
			placeholder: '.sortable-placeholder',
			start: function (event, ui) {
				ui.placeholder.height( ui.helper.height() );
			},
			update: function() {
				CHANG_ATTRIBUTION.updateItemOrderElement( $( this ) );
			}
		});
		
		// Stop the possibility of selectable handles being triggered.
		$( ".attribution-sortable-list .list-item-title" ).disableSelection();	
	},

	// Insert the new order of items into the 'item-order' input.
	updateItemOrderElement: function ( self ) {
		var wrapper = self.closest( '.widget-content' );
		wrapper.find( '.item-order' ).val( wrapper.find( '.attribution-sortable-list' ).sortable( 'toArray' ) );
	},

	showAddButtonCheck: function ( $, list ) {
		if( list.find( '.attribution-invalid' ).length ) {
			list.find( '.add-button-row' ).addClass( 'hide-item' );
		} else {
			list.find( '.add-button-row' ).removeClass( 'hide-item' );
		}
	},

	isValidUrl: function ( url ) {
	    return /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test( url );
	},

	isValidItem: function ( $, item ) {
		item.removeClass( 'attribution-error' );
		var valid = true;

		var displayField = item.find( '.attribution-list-item-display' );
		if( displayField.val() == '' ) {
			valid = false;
		} else {
			displayField.removeClass( 'attribution-error-input' );
		}

		var linkPartField = item.find( '.attribution-list-item-link' );
		var linkPart = $.trim( linkPartField.val() );
		var linkPartLabel = item.find( '.attribution-error-label-link' );
		if( displayField.val().indexOf( linkPart ) == -1 ) {
			valid = false;
			linkPartLabel.removeClass( 'hide-item' );
			linkPartField.addClass( 'attribution-error-input' );
			item.addClass( 'attribution-error' );
		} else {
			linkPartLabel.addClass( 'hide-item' );
			linkPartField.removeClass( 'attribution-error-input' );
		}

		var urlField = item.find( '.attribution-list-item-url' );
		var urlLabel = item.find( '.attribution-error-label-url' );
		if( urlField.val() == '' ) {
			urlLabel.addClass( 'hide-item' );
			urlField.removeClass( 'attribution-error-input' );
		} else if ( ! CHANG_ATTRIBUTION.isValidUrl( urlField.val() ) ) {
			valid = false;
			urlField.addClass( 'attribution-error-input' );
			urlLabel.removeClass( 'hide-item' );
			item.addClass( 'attribution-error' );
		} else {
			urlField.removeClass( 'attribution-error-input' );
			urlLabel.addClass( 'hide-item' );
		}

		if( valid ) {
			item.removeClass( 'attribution-invalid attribution-error' );
			item.find( '.attribution-show-icon-checkbox' ).removeAttr( 'disabled' )
		} else {
			item.addClass( 'attribution-invalid' );
			item.find( '.attribution-show-icon-checkbox' ).attr({ 
				'disabled': true,
				'checked': false
			});
		}

		CHANG_ATTRIBUTION.showAddButtonCheck( $, item.closest( '.widget-content' ) );
		return valid;
	},

	addHandlers: function ( $ ) {
		// Used to measure the time the mouse has been clicked down.
		var startTime, endTime;

		$( 'body' ).on( 'mousedown', '.list-item-title', function() {
			startTime = new Date().getTime();
		});

		$( 'body' ).on( 'mouseup', '.list-item-title', function() {
			endTime = new Date().getTime();

			// Only toggle if the mouse was clicked for less that 250 milliseconds.
			// Stops the toggle from occuring when items are dragged for sorting.
			if ( endTime - startTime < 250 ) {
				$(this).parent().find( '.list-item-fields' ).slideToggle( 200, function() {
					var arrow = $(this).parent().find( '.attribution-action' );
					if ( arrow.hasClass( 'flip' ) ) {
						arrow.removeClass( 'flip' );
					} else {
						arrow.addClass( 'flip' );
					}

					// Set open status
					var openState = $( this ).find( '.attribution-open' );
					if( openState.val() == 'true' ) {
						openState.val( 'false' );
					} else {
						openState.val( 'true' );
					}
				} );
			}
		});

		// Remove an item.
		$( 'body' ).on( 'click', '.attribution-remove', function() {
			var wrapper = $(this).closest( '.attribution-sortable-list' );;
			var item = $( this ).closest( '.attribution-list-item' ).fadeOut( 500, function() {
				var deletedId = CHANG_ATTRIBUTION.getIdNumber( $( this ).attr( 'id' ) );
				var itemTotal = $( this ).closest( '.widget-content' ).find( '.attribution-list-item' ).length - 1;
				$( this ).closest( '.widget-content' ).find( '.user-added-item-total' ).val( itemTotal );

				$( this ).remove();			

				// Hide remove link if there is only one item left.
				if ( wrapper.find( '.attribution-remove' ).length == 1 ) {
					wrapper.find( '.attribution-remove' ).addClass( 'hide-item' );
				}

				wrapper.find( '.attribution-list-item' ).each( function() {
					if ( $( this ).attr( 'id' ) != 'attribution-copyright' ) {
						var thisId = CHANG_ATTRIBUTION.getIdNumber( $( this ).attr( 'id' ) );
						
						if ( thisId > deletedId ) {
							$( this ).attr( 'id', CHANG_ATTRIBUTION.decrementIdNumber( $( this ).attr( 'id' ) ) );
							$( this ).find( '.item-title' ).html( 'Item ' + ( thisId - 1 ) );
							$( this ).find( 'label' ).each( function() {
								var newForLabel = CHANG_ATTRIBUTION.decrementIdNumber( $( this ).attr( 'for' ) );
								$( this ).attr( 'for', newForLabel );
							});

							$( this ).find( 'input' ).each( function() {
								var newId = CHANG_ATTRIBUTION.decrementIdNumber( $( this ).attr( 'id' ) );
								var newName = CHANG_ATTRIBUTION.decrementIdNumber( $( this ).attr( 'name' ) );
								$( this ).attr( 'id', newId );
								$( this ).attr( 'name', newName );
							});
						}
					}
				});

				CHANG_ATTRIBUTION.updateItemOrderElement( wrapper );
				CHANG_ATTRIBUTION.showAddButtonCheck( $, wrapper.closest( '.widget-content' ) );
			});
		});

		// Add an item.
		$( 'body' ).on( 'click', '.add-attribution', function( event ) {
			event.preventDefault();
			var widgetWrapper = $(this).closest( '.widget-content' );
			var itemNumber = widgetWrapper.find( '.attribution-list-item' ).length;

			// Create new item then adjust existing values to new.
			var newItem = widgetWrapper.find( '.attribution-list-item-user-added' ).last().clone();

			var newId = CHANG_ATTRIBUTION.setIdNumber( newItem.attr( 'id' ), itemNumber )
			newItem.attr( 'id', newId );
			newItem.find( '.item-title' ).html( 'Item ' + itemNumber );
			newItem.find( '.list-item-fields' ).hide();

			newItem.find( 'label' ).each( function() {
				var newForLabel = CHANG_ATTRIBUTION.setIdNumber( $( this ).attr( 'for' ), itemNumber  );
				$( this ).attr( 'for', newForLabel );
			});

			newItem.find( 'input' ).each( function() {
				var newId = CHANG_ATTRIBUTION.setIdNumber( $( this ).attr( 'id' ), itemNumber  );
				var newName = CHANG_ATTRIBUTION.setIdNumber( $( this ).attr( 'name' ), itemNumber  );
				$( this ).attr( 'id', newId );
				$( this ).attr( 'name', newName );
				$( this ).val( '' );
			});

			newItem.insertAfter( widgetWrapper.find( '.attribution-list-item' ).last() );

			CHANG_ATTRIBUTION.updateItemOrderElement( $( this ) );
			CHANG_ATTRIBUTION.isValidItem( $, newItem );

			var itemTotal = $( this ).closest( '.widget-content' ).find( '.attribution-list-item' ).length;
			$( this ).closest( '.widget-content' ).find( '.item-total' ).val( itemTotal );
			$( this ).closest( '.widget-content' ).find( '.attribution-list-item-user-added .attribution-remove' ).removeClass( 'hide-item' );

			// Trigger open event.
			startTime = new Date().getTime();
			newItem.find( '.attribution-list-item-title' ).trigger( 'mouseup' );
		});

		// Validation handlers
		$( 'body' ).on( 'blur', '.attribution-list-item-display', function() {
			CHANG_ATTRIBUTION.isValidItem( $, $( this ).closest( '.attribution-list-item' ) );
		});

		$( 'body' ).on( 'blur', '.attribution-list-item-link', function() {
			CHANG_ATTRIBUTION.isValidItem( $, $( this ).closest( '.attribution-list-item' ) );
		});

		$( 'body' ).on( 'blur', '.attribution-list-item-url', function() {
			CHANG_ATTRIBUTION.isValidItem( $, $( this ).closest( '.attribution-list-item' ) );
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