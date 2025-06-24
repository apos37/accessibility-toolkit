( function( $ ) {
	'use strict';

	// Hiding and showing inputs based on conditions
	$( document ).on( 'change', '.a11ytoolkit-toggle input[type="checkbox"]', function() {
		const labelEl = $( this ).siblings( 'span' ).find( '.label' );
		labelEl.text( this.checked ? a11ytoolkit_settings.on : a11ytoolkit_settings.off );
	} );

	$( document ).ready( function() {
		const options = typeof a11ytoolkit_settings !== 'undefined' ? a11ytoolkit_settings.options : [];
		// console.log( options );

		function updateVisibility() {
			const values = {};
			$( 'input[id], select[id], textarea[id]' ).each( function() {
				const id = $( this ).attr( 'id' );
				if ( id ) {
					if ( this.type === 'checkbox' ) {
						values[ id ] = $( this ).is( ':checked' );
					} else {
						values[ id ] = $( this ).val();
					}
				}
			} );

			$( '.a11ytoolkit-box-content' ).each( function() {
				const fieldEl = $( this );
				const inputEl = fieldEl.find( 'input[id], select[id], textarea[id]' ).first();
				const optionKey = inputEl.attr( 'id' );

				const optionData = options.find( function( o ) { return o.key === optionKey; } );

				if ( !optionData || !optionData.conditions ) return;

				const visible = optionData.conditions.every( function( dep ) {
					return values[ dep ];
				} );

				fieldEl.toggleClass( 'not-applicable', !visible );
			} );
		}

		updateVisibility();

		$( document ).on( 'change input', 'input, select, textarea', function() {
			updateVisibility();
		} );
	} );

	// Add copy shortcode button
	function maybeAddShortcodeCopyButton() {
		const select = $( '#a11ytoolkit_modes' );
		if ( select.val() === 'shortcode' && !$( '#a11ytoolkit_shortcode_copy_wrapper' ).length ) {
			const copyHTML = `
				<div id="a11ytoolkit_shortcode_copy_wrapper" style="display: inline-block; margin-left: 1rem;">
					<button type="button" id="a11ytoolkit_copy_shortcode" class="button button-secondary">Copy Shortcode</button>
				</div>
			`;
			select.closest( '.a11ytoolkit-box-right' ).append( copyHTML );
		}
	}

	// Run on page load
	$( document ).ready( function() {
		maybeAddShortcodeCopyButton();
	} );

	// Run on change
	$( document ).on( 'change', '#a11ytoolkit_modes', function() {
		$( '#a11ytoolkit_shortcode_copy_wrapper' ).remove();
		maybeAddShortcodeCopyButton();
	} );

	// Handle copy action
	$( document ).on( 'click', '#a11ytoolkit_copy_shortcode', function() {
		const shortcode = '[a11ytoolkit_modes]';
		navigator.clipboard.writeText( shortcode ).then( function() {
			alert( 'Shortcode copied to clipboard: ' + shortcode );
		} );
	} );

	
} )( jQuery );
