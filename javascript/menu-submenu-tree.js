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

	reset: function( $ ) {
		// Change class of menu element to load specific css.
		$( '#menu' ).removeClass( 'no-js' ).addClass( 'js' );

		// Create one array of all menu elements.
		var menuItems = $( '#primary-menu > li' );
		$.merge( menuItems , $( '#widget-area > div' ) );

		// Create new Sub menu container
		var subMenuWrapper = $( '<div id="chang-sub-menu-wrapper"></div>' );

		// Add sub menus to wrapper.
		var counter = 0;
		$.each( menuItems, function() {
			$( this ).data( 'id', counter );
			if( $( this ).children().length > 1 ) {
				$( this ).children().last().addClass( 'chang-sub-' + counter );
				$( this ).children().last().appendTo( subMenuWrapper );
			}	
			counter ++;
		});

		// Add to DOM.
		$( '#menu' ).append( subMenuWrapper );

		// Adjust style.
		CHANG_MENU.adjustLayout( $ );
	},

	adjustLayout: function( $ ) {
		$( '#chang-sub-menu-wrapper' ).children().each( function() {
			$( this ).show();
			if( $( this ).is( 'ul' ) ) {
				CHANG_MENU.adjustStyle( $, $( this ), 0 );
			}
			$( this ).hide();
		});
	},

	adjustStyle: function( $, item, depth ) {
		var numOfColumns = CHANG_MENU.columns.get();
		var targetWidth = 100 / ( numOfColumns - depth );
		var counter = 0;

		item.children().each( function() {
			if( $( this ).children().length > 1 ) {
				$( this ).css({
					'width': '100%',
				});

				var lastChild = $( this ).children().last();
				if( lastChild.is( 'ul' ) ) {
					lastChild.css({
						'width': ( 100 - targetWidth ) + '%',
						'display': 'inline-block',
						'margin-left': '-4px',
						'margin-bottom': '10px',
						// 'box-shadow': '-1px 1px 3px rgba(0,0,0,0.3)',
						// '-moz-box-shadow': '-1px 1px 3px rgba(0,0,0,0.3)',
						// '-webkit-box-shadow': '-1px 1px 3px rgba(0,0,0,0.3)',
					}).addClass( 'chang-sub-menu' );

					CHANG_MENU.adjustStyle( $, lastChild, depth + 1 );

					console.log( $( this ).height() );

					$( this ).children().first().css({
						'padding': 0
					});

					// The 10 in the padding-top value is the margin-bottom in the ul element set above.
					$( this ).children().first().css({
						'width': targetWidth + '%',
						'display': 'inline-block',
						'padding-top': ( $( this ).height() - $( this ).children().first().height() - 10 ) / 2
					});
				}

				counter = 0;
			} else {
				$( this ).css({
					'width': targetWidth + '%',
					'display': 'inline-block',
					'padding': '15px 0px'
				});

				if( counter > 0 && numOfColumns / counter != 1 ) {
					$( this ).css({
						'margin-left': '-4px'
					});
				}

				counter += 1;
			}
		});
	},

	addHandlers: function( $ ) {
		var resizeTimeout = null;
		$( window ).resize( function() {
			if( resizeTimeout ) {
				clearTimeout( resizeTimeout );
				resizeTimeout = setTimeout( function() {
					CHANG_MENU.adjustLayout( $ );
				}, 200 );
			} else {
				resizeTimeout = setTimeout( function() {
					CHANG_MENU.adjustLayout( $ );
				}, 200 );
			}
		});

		// Main Menu
		$( 'body' ).on( 'mouseenter', '.menu-level-1', function() {
			CHANG_MENU.menu.itemEntered( $, $( this ) );
		});

		$( 'body' ).on( 'mouseenter', '.widget', function() {
			CHANG_MENU.menu.itemEntered( $, $( this ) );
		});

		$( 'body' ).on( 'mouseleave', '.menu-level-1', function() {
			CHANG_MENU.menu.itemLeft( $, $( this ) );
		});

		$( 'body' ).on( 'mouseleave', '.widget', function() {
			CHANG_MENU.menu.itemLeft( $, $( this ) );
		});

		$( 'body' ).on( 'mouseenter', '#chang-sub-menu-wrapper', function() {
			CHANG_MENU.menu.wrapperEntered( $ );
		});

		$( 'body' ).on( 'mouseleave', '#chang-sub-menu-wrapper', function() {
			CHANG_MENU.menu.wrapperLeft( $ );
		});
	},

	menu: {
		currentItem: null,
		itemEnteredTimer: null,
		itemLeftTimer: null,
		menuLeftTimer: null,

		itemEntered: function( $, item ) {
			if( $( '.chang-sub-' + item.data( 'id' ) ).length == 0 ) {
				return;
			}

			// Cancel the item entered timer. Stops the menu from closing.
			if( this.itemLeftTimer != null ) {
				clearTimeout( this.itemLeftTimer );
				this.itemLeftTimer = null;
			}

			this.itemEnteredTimer = setTimeout( function() {
				clearTimeout( this.itemEnteredTimer );
				this.itemEnteredTimer = null;

				currentItem = item.data( 'id' );
				
				CHANG_MENU.menu.open( $ );
			}, 300 );
		},

		itemLeft: function( $, item ) {
			// Cancel the item entered timer. Stops the menu from opening.
			if( this.itemEnteredTimer != null ) {
				clearTimeout( this.itemEnteredTimer );
				this.itemEnteredTimer = null;
			}

			this.itemLeftTimer = setTimeout( function() {
				clearTimeout( this.itemLeftTimer );
				this.itemLeftTimer = null;
				currentItem = null;

				CHANG_MENU.menu.close( $ );
			}, 300 );
		},

		wrapperEntered: function( $ ) {
			// Cancel the item entered timer. Stops the menu from closing.
			if( this.itemLeftTimer != null ) {
				clearTimeout( this.itemLeftTimer );
				this.itemLeftTimer = null;
			}
		},

		wrapperLeft: function( $ ) {
			this.itemLeftTimer = setTimeout( function() {
				clearTimeout( this.itemLeftTimer );
				this.itemLeftTimer = null;
				currentItem = null;

				CHANG_MENU.menu.close( $ );
			}, 300 );
		},

		open: function( $ ) {
			var id = currentItem;
			var subMenu = $( '.chang-sub-' + id );

			if( subMenu.length ) {
				subMenu.show();
				var targetHeight = subMenu.height();
				subMenu.css({
					'opacity': '0'
				});

				$( '#chang-sub-menu-wrapper' ).stop( true ).animate({
					'height': targetHeight
				}, 200 );

				if( $( '#chang-sub-menu-wrapper' ).height() != 0 ) {
					$( '#chang-sub-menu-wrapper' ).children().stop( true ).animate({
						'opacity': '0'
					}, 100, function() {
						$( this ).hide();
						subMenu.show().animate({
							'opacity': '1'
						}, 100 );
					})
				} else {
					subMenu.stop( true ).animate({
						'opacity': '1'
					}, 200 );
				}
			}
		},

		close: function( $ ) {
			$( '#chang-sub-menu-wrapper' ).stop( true ).animate({
				'height': 0
			}, 200, function() {
				$( '#chang-sub-menu-wrapper' ).children().hide();
			});
		},
	}
}