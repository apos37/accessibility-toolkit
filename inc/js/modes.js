jQuery( $ => {
	console.log( 'Modes JS Loaded...' );

	let currentMode = a11ytoolkit_modes.current_mode;
	const modes = a11ytoolkit_modes.modes;
	const modeKeys = Object.keys( modes );

	maybeSwapLogo();

	// Main update function
	function setMode( newModeKey ) {
		const newMode = modes[ newModeKey ];

		if ( !newMode ) {
			return;
		}

		const newLabel = newMode.label;
		const newSwitch = newMode.switch;
		const newActive = newMode.active;
		const newIcon = newMode.icon;

		// Update body class
		$( 'body' )
			.removeClass( `a11ytoolkit-${ currentMode }-mode` )
			.addClass( `a11ytoolkit-${ newModeKey }-mode` );

		// Update current mode tracker
		currentMode = newModeKey;

		// Update switch visual and state if present
		const $switch = $( '#a11ytoolkit-mode-switch' );
		if ( $switch.length ) {
			$switch.attr( 'data-current', newModeKey );

			const $button = $switch.find( '#a11ytoolkit-mode-toggle' );
			
			// Get next mode for aria-label
			const nextIndex   = ( modeKeys.indexOf( currentMode ) + 1 ) % modeKeys.length;
			const nextSwitch  = modes[ modeKeys[ nextIndex ] ].switch;

			$button.attr( 'aria-label', nextSwitch );
			$button.attr( 'title', nextSwitch );
			$button.find( 'i' ).remove();
			$button.prepend( $( newIcon ) ); // Inject HTML icon safely
			$button.find( '.screen-reader-text' ).text( newActive );

			$( '#a11ytoolkit-mode-live' ).text( newActive );

		}

		// Swap logos if needed
		maybeSwapLogo();

		// Save mode server-side
		$.ajax( {
			type: 'post',
			dataType: 'json',
			url: a11ytoolkit_modes.ajaxurl,
			data: {
				action: 'a11ytoolkit_modes',
				nonce: a11ytoolkit_modes.nonce,
				mode: newModeKey,
			},
			success: function( response ) {
				if ( response.type === 'error' ) {
					console.log( 'Failed to update the user profile or session.' );
				}
			}
		} );
	}

	// Switch button
	$( '#a11ytoolkit-mode-toggle' ).on( 'click', function( event ) {
		event.preventDefault();
		const currentIndex = modeKeys.indexOf( currentMode );
		const nextIndex = ( currentIndex + 1 ) % modeKeys.length;
		setMode( modeKeys[ nextIndex ] );
	} );

	// Drop-down selector
	$( '#a11ytoolkit-mode-dropdown' ).on( 'change', function() {
		const newModeKey = $( this ).val();
		setMode( newModeKey );
	} );

	// Swap logo based on mode
	function maybeSwapLogo() {
		const lightLogo = a11ytoolkit_modes.light_mode_logo;
		const darkLogo = a11ytoolkit_modes.dark_mode_logo;

		if ( !lightLogo || !darkLogo ) {
			return;
		}

		const lightPath = new URL( lightLogo, window.location.origin ).pathname;
		const darkPath = new URL( darkLogo, window.location.origin ).pathname;

		$( 'img' ).each( function() {
			const $img = $( this );
			const srcPath = new URL( $img.attr( 'src' ), window.location.origin ).pathname;

			if ( $( 'body' ).hasClass( 'a11ytoolkit-dark-mode' ) && srcPath === lightPath ) {
				$img.attr( 'src', darkLogo );
			} else if ( srcPath === darkPath ) {
				$img.attr( 'src', lightLogo );
			}
		} );
	}
} );
