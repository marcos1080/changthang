jQuery( document ).ready( function( $ ) {
	CHANG_MENU.reset( $ );
	CHANG_MENU.addHandlers( $ );
});

var CHANG_MENU = {
	slideSpeed: 300,

	columns: {
		value: null,

		get: function() {
			var width = jQuery( '#wrapper' ).width();

			if( width > 1600 ) {
				return 9;
			} else if ( width > 1400 ) {
				return 8;
			} else if ( width > 1200 ) {
				return 7;
			} else if ( width > 1000 ) {
				return 6;
			} else if ( width > 800 ) {
				return 5;
			} else if ( width > 600 ) {
				return 4;
			} else if ( width > 400 ) {
				return 3;
			} else if ( width > 200 ) {
				return 2;
			} else {
				return 1;
			}
		},

		set: function ( value ) {
			this.value = value;
		}
	},

	changTimer: function() {
		this.timer = null;

		this.isSet = function() {
			if( this.timer == null ) {
				return false;
			} else {
				return true;
			}
		}

		this.set = function( timeoutAction ) {
			clearTimeout( this.timer );
			this.timer = timeoutAction;
		}

		this.unset = function() {
			clearTimeout( this.timer );
			this.timer = null;
		}
	},

	changRGBa: function() {
		this.red = 0;
		this.green = 0;
		this.blue = 0;
		this.alpha = 1;

		this.parseColor = function( cssColor ) {
			var values = cssColor.substring( cssColor.indexOf( '(' ) +1, cssColor.length -1 ).split( ', ' );
			this.red = values[0];
			this.green = values[1];
			this.blue = values[2];

			if( values.length == 4 ) {
				this.alpha = values[3];
			}
		}

		this.getString = function() {
			var colorString = 'rgba(' + this.red + ', ' + this.green + ', ' + this.blue + ', ' + this.alpha + ')'
			return colorString;
		}

		this.print = function() {
			console.log( 'r: ' + this.red );
			console.log( 'g: ' + this.green );
			console.log( 'b: ' + this.blue );
			console.log( 'a: ' + this.alpha );
		}

		this.reduceBrightness = function( percentage ) {
			if( percentage <= 0 ) {
				return;
			}

			if( percentage > 100 ) {
				percentage = 100;
			}

			var maxColorValue = 255;
			var colorStep = maxColorValue * ( percentage / 100 );

			this.red -= colorStep;
			if( this.red < 0 ) {
				this.red = 0;
			}

			this.green -= colorStep;
			if( this.green < 0 ) {
				this.green = 0;
			}

			this.blue -= colorStep;
			if( this.blue < 0 ) {
				this.blue = 0;
			}
		}
	},

	changMenuItem: function() {
		// properties
		this.parent = null;
		this.element = null;
		this.kind = '';
		this.hasSub = false;
		this.sub = null;
		this.subElement = null;
		this.closeTimer = new CHANG_MENU.changTimer();
		this.openTimer = new CHANG_MENU.changTimer();

		// Dim each parent submenu
		this.backgroundCascade = function( color ) {
			// Set top sub menu to background colour of the menu wrapper.
			this.subElement.css({
				'background-color': color
			});

			// Stop the main menu from being effected
			if( this.parent != null ) {
				// Create new colour and dim by 10%
				var newColor = new CHANG_MENU.changRGBa();
				newColor.parseColor( color );
				newColor.reduceBrightness( 10 );

				// Call this function on the parent element
				this.parent.backgroundCascade( newColor.getString() );

				// Set style for current tab.
				this.element.parent().css({
					'background-color': color,
					'border-radius': '10px 10px 0px 0px',
					'box-shadow': '0px -1px 1px rgba(0,0,0,0.3)',
					'-moz-box-shadow': '0px -1px 1px rgba(0,0,0,0.3)',
					'-webkit-box-shadow': '0px -1px 1px rgba(0,0,0,0.3)'
				});
			}
		}

		// Used to reset tabs.
		this.resetElementColor = function() {
			this.element.parent().css({
				'background-color': 'transparent',
				'border-radius': 'none',
				'box-shadow': 'none',
				'-moz-box-shadow': 'none',
				'-webkit-box-shadow': 'none'
			});
		}
	},

	menuObject: [],

	reset: function( $ ) {
		// Change class of menu element to load specific css.
		$( '#menu' ).removeClass( 'no-js' ).addClass( 'js' );

		// Create one array of all menu elements.
		var menuItems = $( '#primary-menu > li' );
		$.merge( menuItems , $( '#widget-area > div' ) );

		// Create the menu object.
		this.menuObject = this.addMainMenuObjects( $, menuItems );
		console.log( this.menuObject );

		// Create new Sub menu container
		$( '#menu' ).append( $( '<div id="chang-sub-menu-wrapper"></div>' ) );
	},

	addMainMenuObjects: function( $, menuItems ) {
		console.log( 'creating main menu objects' );

		var mainMenuObjectArray = [];
		var count = 0;
		menuItems.each( function() {
			if( $( this ).is( 'li' ) ) {
				// Case for primary menu.

				var link = $( this ).children( 'a' );
				var newMenuItem = new CHANG_MENU.changMenuItem();

				newMenuItem.element = link;
				newMenuItem.kind = 'link';

				// Add newMenuItem to the element to allow access to menuObject.
				link.data( 'menuObject', newMenuItem );

				var subMenu = $( this ).children( 'ul' );
				if( subMenu.length ) {
					// There is a sub menu.
					newMenuItem.hasSub = true;
					newMenuItem.sub = CHANG_MENU.addSubToMenuObject( $, subMenu, newMenuItem );
				}

				mainMenuObjectArray.push( newMenuItem );
				subMenu.remove();
			} else if( $( this ).is( 'div' ) ) {
				// Case for widget menu.
				var newMenuItem = new CHANG_MENU.changMenuItem;
				var title = $( this ).children( '.widget-title' );

				if( title.length ) {
					// Get the non title object.
					var subMenu = $( this ).children().not( title );

					newMenuItem.element = title;
					newMenuItem.kind = 'title';
					newMenuItem.hasSub = true;
					newMenuItem.sub = CHANG_MENU.addSubToMenuObject( $, subMenu, newMenuItem );

					title.data( 'menuObject', newMenuItem );
					subMenu.remove();
				} else {
					newMenuItem.element = title;
					newMenuItem.kind = 'element';

					$( this ).children().data( 'menuObject', newMenuItem );
				}

				mainMenuObjectArray.push( newMenuItem );
			} else {
				// Case for neither. Should never happen.
				console.log( 'warning, unrecognised element in main menu!' );
			}
		});

		return mainMenuObjectArray;
	},

	addSubToMenuObject: function( $, subMenu, parent ) {
		var newSubMenuItems = [];
		if( subMenu.is( 'ul' ) ) {
			// Case for submenu of links.

			subMenu.children( 'li' ).each( function() {
				var newSubItem = new CHANG_MENU.changMenuItem;
				newSubItem.parent = parent;

				var link = $( this ).children( 'a' );
				if( link.length ) {
					newSubItem.element = link;
					newSubItem.kind = 'link';
					link.data( 'menuObject', newSubItem );
				} else {
					// Just in case something non usual is done in a widget.
					var element = $( this ).children().not( $( this ).children( 'ul' ) );
					newSubItem.element = element;
					newSubItem.kind = 'element';
					element.data( 'menuObject', newSubItem );
				}

				// Check for next level of submenu.
				var newSubMenu = $( this ).children( 'ul' );
				if( newSubMenu.length ) {
					// Add recursively.
					newSubItem.hasSub = true;
					newSubItem.sub = CHANG_MENU.addSubToMenuObject( $, newSubMenu, newSubItem );
				}

				newSubMenuItems.push( newSubItem );
			});
		} else {
			// Case for widget object.
			var newSubItem = new CHANG_MENU.changMenuItem;
			newSubItem.parent = parent;
			newSubItem.element = subMenu;
			newSubItem.kind = 'element';
			newSubItem.hasSub = false;

			subMenu.data( 'menuObject', newSubItem );
			newSubMenuItems.push( newSubItem );
		}

		return newSubMenuItems;
	},

	makeMainMenu: function( $, itemObject ) {
		if( itemObject.hasSub ) {
			var wrapper = $( '<div class="chang-sub-menu"></div>' );
			itemObject.subElement = wrapper;
			CHANG_MENU.makeSubMenuItem( itemObject, wrapper );

			return wrapper;
		}
	},

	makeSubMenuItem: function( item, wrapper ) {
		var numOfItems = item.sub.length;
		var numberOfColumns = this.columns.get();
		var numberOfRows = Math.ceil( numOfItems / numberOfColumns );
		var itemCount = 0;

		for( var i = 0; i < numberOfRows; i++ ) {
			var row = jQuery( '<div class="chang-sub-menu-row"></div>' );
			row.appendTo( wrapper );

			// Create each element in the row.
			for( var j = 0; j < numberOfColumns && itemCount < numOfItems; j++, itemCount++ ) {
				var itemObject = item.sub[itemCount];
				var menuItem;

				if( itemObject.kind == 'element' ) {
					menuItem = row;
				} else {
					menuItem = jQuery( '<div class="chang-sub-menu-item"></div>' );
					menuItem.css({
						'display': 'inline-block',
						'width': ( 100 / numberOfColumns ) + '%'
					}).appendTo( row );
				}

				itemObject.element.appendTo( menuItem );

				itemObject.element.data( 'menuObject', itemObject );
			}
		}
	},

	addHandlers: function( $ ) {
		var resizeTimeout = null;
		$( window ).resize( function() {
			if( resizeTimeout ) {
				clearTimeout( resizeTimeout );
				resizeTimeout = setTimeout( function() {
					CHANG_MENU.makeMainMenu();
				}, 200 );
			} else {
				resizeTimeout = setTimeout( function() {
					CHANG_MENU.makeMainMenu();
				}, 200 );
			}
		});

		// Main Menu
		$( 'body' ).on( 'mouseenter', '.menu-level-1', function() {
			CHANG_MENU.menu.itemEntered( $, $( this ).children( 'a' ) );
			//console.log( $( this ).children().data( 'menuObject' ) );
		});

		$( 'body' ).on( 'mouseenter', '.widget', function() {
			CHANG_MENU.menu.itemEntered( $, $( this ).children( '.widget-title' ) );
			//console.log( $( this ).children().data( 'menuObject' ) );
		});

		$( 'body' ).on( 'mouseleave', '.menu-level-1', function() {
			CHANG_MENU.menu.itemLeft( $, $( this ).children( 'a' ) );
		});

		$( 'body' ).on( 'mouseleave', '.widget', function() {
			CHANG_MENU.menu.itemLeft( $, $( this ).children( '.widget-title' ) );
		});

		$( 'body' ).on( 'mouseenter', '#chang-sub-menu-wrapper', function() {
			CHANG_MENU.menu.wrapperEntered( $ );
		});

		$( 'body' ).on( 'mouseleave', '#chang-sub-menu-wrapper', function() {
			CHANG_MENU.menu.wrapperLeft( $ );
		});

		// Sub Menu
		$( 'body' ).on( 'mouseenter', '.chang-sub-menu-item a', function() {
			CHANG_MENU.menu.sub.itemEntered( $, $( this ) );
		});

		$( 'body' ).on( 'mouseleave', '.chang-sub-menu-item a', function() {
			CHANG_MENU.menu.sub.itemLeft( $, $( this ) );
		});

		$( 'body' ).on( 'mouseenter', '.sub-menu-open', function() {
			CHANG_MENU.menu.sub.rowEntered( $, $( this ) );
		});

		$( 'body' ).on( 'mouseleave', '.sub-menu-open', function() {
			CHANG_MENU.menu.sub.rowLeft( $, $( this ) );
		});

	},

	menu: {
		currentItem: null,
		lastItem: null,
		active: false,
		itemEnteredTimer: null,
		itemLeftTimer: null,
		menuLeftTimer: null,
		menuHeight: 0,

		itemEntered: function( $, item ) {
			// Check if item has a submenu
			var itemObject = item.data( 'menuObject' );
			if( itemObject.hasSub ) {
				// Cancel item left timer. Stops the menu from closing.
				if( this.itemLeftTimer != null ) {
					clearTimeout( this.itemLeftTimer );
					this.itemLeftTimer = null;
				}

				// Cancel the menu left timer. Stops the menu from closing.
				if( this.menuLeftTimer != null ) {
					clearTimeout( this.menuLeftTimer );
					this.menuLeftTimer = null;
				}
			} else {
				return;
			}

			var delayTime = 100;
			if( ! this.active ) {
				delayTime = 200;
			}

			// Only fire open if the cursor is on the element for a certain time.
			this.itemEnteredTimer = setTimeout( function() {
				// Don't open sub menu if it is already open.
				if( CHANG_MENU.menu.currentItem == itemObject ) {
					return;
				}

				// Store previous submenu id.
				if( CHANG_MENU.menu.currentItem != null ) {
					CHANG_MENU.menu.lastItem = CHANG_MENU.menu.currentItem;
				}

				// Set state for menu.
				CHANG_MENU.menu.currentItem = itemObject;

				// Open the menu.
				CHANG_MENU.menu.open( $ );
			}, delayTime );
		},

		itemLeft: function( $, item ) {
			// Cancel the item entered timer. Stops the menu from opening.
			if( this.itemEnteredTimer != null ) {
				clearTimeout( this.itemEnteredTimer );
				this.itemEnteredTimer = null;
			}

			this.itemLeftTimer = setTimeout( function() {
				CHANG_MENU.menu.itemLeftTimer = null;
				CHANG_MENU.menu.close( $ );
			}, 5 );
		},

		wrapperEntered: function( $ ) {
			//console.log( 'Menu entered.' );

			if( this.itemLeftTimer != null ) {
				clearTimeout( this.itemLeftTimer );
				//console.log( this.currentItem + ' item timer cancelled.' );
				this.itemLeftTimer = null;
			}
		},

		wrapperLeft: function( $ ) {
			this.menuLeftTimer = setTimeout( function() {
				//console.log( 'Menu left timer triggered.')
				CHANG_MENU.menu.menuLeftTimer = null;
				CHANG_MENU.menu.close( $ );
			}, 5 );
		},

		open: function( $ ) {
			var newMenuWrapper = CHANG_MENU.makeMainMenu( $, this.currentItem );

			$( '.chang-sub-menu' ).addClass( 'to-remove' );
			newMenuWrapper.appendTo( $( '#chang-sub-menu-wrapper' ) );
			newMenuWrapper.data( 'menuObject', this.currentItem );
			var targetHeight = newMenuWrapper.height();
			newMenuWrapper.hide();

			$( '#chang-sub-menu-wrapper' ).stop( true ).animate({
				'height': targetHeight
			}, 200, function() {
				$( this ).css({
					'height': 'auto'
				});
			});

			if( this.active ) {
				$( '.chang-sub-menu-row' ).stop( true ).animate({
					'opacity': '0'
				}, 100, function() {
					$( '.to-remove' ).remove();
					newMenuWrapper.show();
					newMenuWrapper.children( '.chang-sub-menu-row' ).animate({
						'opacity': '1'
					}, 100 );
				});
			} else {
				newMenuWrapper.show();
				newMenuWrapper.children( '.chang-sub-menu-row' ).animate({
					'opacity': '1'
				}, 200 );
				CHANG_MENU.menu.active = true;
			}
		},

		close: function( $ ) {
			this.currentItem = null;
			this.lastItem = null;
			this.active = false;

			$( '#chang-sub-menu-wrapper' ).stop( true ).animate({
				'height': '0px'
			}, 200, function() {
				$( '#chang-sub-menu-wrapper' ).empty();
			});

			$( '.chang-sub-menu-row' ).stop( true ).animate({
				'opacity': '0'
			}, 200 );
		},

		sub: {
			extendedHeight: 0,
			delayTimer: null,
			itemLeftTimer: null,
			subOpenLeftTimer: null,

			itemEntered: function( $, item ) {
				var itemObject = item.data( 'menuObject' );

				// Leave if no sub menu detected
				if( ! itemObject.hasSub ) {
					return;
				}

				// Reset timers
				if( itemObject.openTimer.isSet() ) {
					itemObject.openTimer.unset();
				}

				if( itemObject.closeTimer.isSet() ) {
					itemObject.closeTimer.unset();
					// If the close timer was set it means the menu is open.
					return;
				}

				itemObject.openTimer.set( setTimeout( function() {
					itemObject.openTimer.unset();

					// Determine whether to open new or replace menu.
					var currentRow = item.closest( '.chang-sub-menu-row' );
					if( currentRow.next().hasClass( 'sub-menu-open' ) ) {
						// Replace.
						CHANG_MENU.menu.sub.replace( $, itemObject, currentRow.next() );
					} else {
						// New.
						CHANG_MENU.menu.sub.open( $, itemObject );
					}
				}, 300 ));
			},

			itemLeft: function( $, item ) {
				var itemObject = item.data( 'menuObject' );

				// Remove timer.
				if( itemObject.openTimer.isSet() ) {
					itemObject.openTimer.unset();
				}

				// Check if there is an open submenu below the current row.
				var currentRow = itemObject.element.closest( '.chang-sub-menu-row' );
				if( currentRow.next().hasClass( 'sub-menu-open' ) ) {
					var subMenu = currentRow.next();
					itemObject.closeTimer.set( setTimeout( function() {
						itemObject.closeTimer.unset();
						CHANG_MENU.menu.sub.close( $, subMenu );
					}, 400 ));
				}
			},

			rowEntered: function( $, item ) {
				// Used to stop submenu from closing after item left.
				var itemObject = item.data( 'menuObject' );

				if( itemObject.closeTimer.isSet() ) {
					itemObject.closeTimer.unset();
				}
			},

			rowLeft: function( $, item ) {
				var itemObject = item.data( 'menuObject' );

				itemObject.closeTimer.set( setTimeout( function() {
					itemObject.closeTimer.unset();
					CHANG_MENU.menu.sub.close( $, item );
				}, 400 ));
			},

			open: function( $, itemObject ) {
				// Create new sub menu
				var subMenuWrapper = $( '<div class="sub-menu-open"></div>' );
				CHANG_MENU.makeSubMenuItem( itemObject, subMenuWrapper );
				subMenuWrapper.insertAfter( itemObject.element.closest( '.chang-sub-menu-row' ) );
				// Add menu object connection
				subMenuWrapper.data( 'menuObject', itemObject );
				itemObject.subElement = subMenuWrapper;

				// Show submenu rows to get target height.
				var subMenuItems = subMenuWrapper.children();
				subMenuItems.show();

				// Get height then set back to 0 to allow for slide down
				var subMenuHeight = subMenuWrapper.height();
				subMenuWrapper.height( 0 );

				// Animate slide
				subMenuWrapper.stop( true ).animate({
					'height': subMenuHeight
				}, 200, function() {
					// Important!
					$( this ).css({
						'height': 'auto'
					});
				});

				// Animate opacity
				subMenuItems.stop( true ).animate({
					'opacity': '1',
				}, 200 );

				// Dim the background submenus.
				itemObject.backgroundCascade( $( '#chang-sub-menu-wrapper' ).css( 'background-color' ) );
			},

			replace: function( $, newItemObject, subMenu ) {
				var oldItemObject = subMenu.data( 'menuObject' );

				// Remove timer.
				if( oldItemObject.closeTimer.isSet() ) {
					oldItemObject.closeTimer.unset();
				}

				// Stop current animation.
				subMenu.stop();
				subMenu.children().stop();

				// Create new temporary sub menu wrapper.
				var subMenuWrapper = $( '<div></div>' );
				CHANG_MENU.makeSubMenuItem( newItemObject, subMenuWrapper );
				subMenuWrapper.insertAfter( newItemObject.element.closest( '.chang-sub-menu-row' ) );

				// Get the height of temp sub menu and hide it.
				var targetHeight = subMenuWrapper.height();
				subMenuWrapper.hide();

				// Fade out the current menu then replace the contents and fade back in.
				subMenu.animate({
					'opacity': '0'
				}, 100, function() {
					subMenu.css({
						'opacity': '1'
					}).empty();
					// Reset the menu object connection.
					subMenu.data( 'menuObject', newItemObject );
					newItemObject.subElement = subMenu;
					// Replace the menu contents
					subMenuWrapper.children().appendTo( subMenu );
					// Remove the temp menu.
					subMenuWrapper.remove();
					subMenu.children().animate({
						'opacity': '1'
					}, 100 );

					oldItemObject.resetElementColor();
					newItemObject.backgroundCascade( $( '#chang-sub-menu-wrapper' ).css( 'background-color' ) );
				});

				// Adjust the height.
				subMenu.animate({
					'height': targetHeight
				}, 200, function() {
					$( this ).css({
						'height': 'auto'
					});
				});
				
			},

			close: function( $, subMenu ) {
				var itemObject = subMenu.data( 'menuObject' );
				// If undefined then the main menu close method has been called and removed submenu.
				if( itemObject === undefined ) {
					return;
				}

				itemObject.subElement = null;

				var subMenuItems = subMenu.children();

				subMenu.animate({
					'height': 0
				}, 200 ),

				subMenuItems.stop( true ).animate({
					'opacity': '0',
				}, 200, function() {
					subMenu.remove();
				});

				itemObject.resetElementColor();
				subMenu.css({
					'box-shadow': 'none',
					'-moz-box-shadow': 'none',
					'-webkit-box-shadow': 'none'
				});
				itemObject.parent.backgroundCascade( $( '#chang-sub-menu-wrapper' ).css( 'background-color' ) );
			}
		}
	}
}