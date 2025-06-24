jQuery( $ => {
    console.log( 'Admin Bar JS Loaded...' );

    
    /**
     * Toggle the checkboxes in the admin bar
     */
    $( '#wp-admin-bar-a11ytoolkit input[type="checkbox"]' ).on( 'change', function() {
		const tool = $( this ).data( 'tool' );
		const state = $( this ).is( ':checked' );

        // Image Alt Text
		if ( tool === 'alt-text' ) {
            if ( state ) {
                showAltBubbles();
            } else {
                removeAltBubbles();
            }

        // Poor Color Contrast
        } else if ( tool == 'contrast' ) {
            if ( state ) {
                showColorContrast();
            } else {
                removeColorContrast();
            }

        // Vague Link Texts
        } else if ( tool == 'vague-link-text' ) {
            if ( state ) {
                showVagueLinkTexts();
            } else {
                removeVagueLinkTexts();
            }

        // Heading Hierarchy
        } else if ( tool == 'heading-hierarchy' ) {
            if ( state ) {
                showHeadings();
            } else {
                removeHeadings();
            }
        
        // Underlined Links
        } else if ( tool == 'underline-links' ) {
            if ( state ) {
                showUnderlineIssues();
            } else {
                removeUnderlineIssues();
            }
        }
	} );


    /**
     * Image Alt Text
     */
    function showAltBubbles() {
        let count = 0;

        $( 'img' ).not( '#wpadminbar img' ).each( function() {
            const img = $( this );
            const alt = img.attr( 'alt' ) || '';

            if ( !alt.trim() && !img.parent().hasClass( 'a11y-missing-wrapper' ) ) {
                img.wrap( `<div class="a11y-missing-wrapper" data-label="⚠️ ${admin_bar.text.missing}"></div>` );
                count++;
            }
        } );

        $( '.a11ytoolkit-count[data-tool="alt-text"]' ).text( count > 0 ? `(${count})` : '(0)' );
    }

    function removeAltBubbles() {
        $( '.a11y-missing-wrapper' ).each( function() {
            const wrapper = $( this );
            const img = wrapper.find( 'img' );
            img.unwrap();
        } );

        $( '.a11ytoolkit-count[data-tool="alt-text"]' ).text( '' );
    }


    /**
     * Poor Color Contrast
     */
    function showColorContrast() {
        const useAAA = admin_bar.doing_aaa;
        let count = 0;

        $( '*:visible' ).not( '#wpadminbar *, #a11ytoolkit-mode-switch *' ).each( function() {
            const $el = $( this );
            if ( $el.children().length ) return;

            const text = $el.text().trim();
            if ( !text ) return;

            const fg = getComputedStyle( this ).color;
            const bg = getEffectiveBackgroundColor( this );
            if ( !fg || !bg ) return;

            const ratio = getContrastRatio( fg, bg );
            if ( ratio < 4.5 ) {
                const isLarge = isLargeText( this );
                let failAA = false;
                let failAAA = false;

                if ( isLarge ) {
                    failAA = ratio < 3;
                    failAAA = ratio < 4.5;
                } else {
                    failAA = ratio < 4.5;
                    failAAA = ratio < 7;
                }

                // If AAA checking enabled, only show if it fails AAA
                if ( useAAA && !failAAA ) {
                    return;
                }

                // If AAA disabled, show if it fails AA
                if ( !useAAA && !failAA ) {
                    return;
                }

                // console.log( text, fg, bg );

                const fgHex = rgbToHex( fg );
                const bgHex = rgbToHex( bg );
                const url = `https://webaim.org/resources/contrastchecker/?fcolor=${fgHex}&bcolor=${bgHex}`;

                $el.addClass( 'a11y-poor-contrast' );

                const offset = $el.offset();

                // Badge text with ratio and fail level
                let levelText = '';
                if ( useAAA ) {
                    levelText = failAAA ? 'AAA' : ( failAA ? 'AA' : '' );
                } else {
                    levelText = failAA ? 'AA' : '';
                }

                let shouldBe = '';

                if ( isLarge ) {
                    shouldBe = useAAA ? '4.5' : '3';
                } else {
                    shouldBe = useAAA ? '7' : '4.5';
                }

                const badge = $( '<a>' )
                    .addClass( 'a11y-contrast-badge' )
                    .attr( 'href', url )
                    .attr( 'target', '_blank' )
                    .attr( 'title', `${levelText} fail for ${isLarge ? 'large' : 'normal'} text, should be ≥ ${shouldBe}` )
                    .css({
                        top: offset.top,
                        left: offset.left
                    })
                    .text( ratio.toFixed(2) );

                $( 'body' ).append( badge );

                count++;
            }
        } );

        $( '.a11ytoolkit-count[data-tool="contrast"]' ).text( count > 0 ? `(${count})` : '(0)' );
    }

    function isLargeText( el ) {
        const style = getComputedStyle( el );
        const fontSize = parseFloat( style.fontSize );
        const fontWeight = style.fontWeight;

        const isBold = ( fontWeight === 'bold' || parseInt( fontWeight ) >= 700 );
        // 14pt ≈ 18.66px; 18pt ≈ 24px (approximate)
        return ( ( fontSize >= 24 ) || ( fontSize >= 18.66 && isBold ) );
    }

    function removeColorContrast() {
        $( '.a11y-poor-contrast' ).removeClass( 'a11y-poor-contrast' ).removeAttr( 'data-contrast-ratio' );
        $( '.a11y-contrast-badge' ).remove();
        $( '.a11ytoolkit-count[data-tool="contrast"]' ).text( '' );
    }

    function getEffectiveBackgroundColor( el ) {
        let bg = getComputedStyle( el ).backgroundColor;
        if ( !bg || bg === 'transparent' || bg === 'rgba(0, 0, 0, 0)' ) {
            const parent = el.parentElement;
            if ( parent && parent !== document ) {
                return getEffectiveBackgroundColor( parent );
            }
            return 'rgb(255,255,255)'; // fallback
        }

        const rgba = parseRGBA( bg );
        if ( rgba.a < 1 ) {
            const parentBg = getEffectiveBackgroundColor( el.parentElement );
            const parentRgba = parseRGBA( parentBg );
            const composite = compositeColors( rgba, parentRgba );
            return `rgb(${composite.r}, ${composite.g}, ${composite.b})`;
        }

        return `rgb(${rgba.r}, ${rgba.g}, ${rgba.b})`;
    }


    function getContrastRatio( fg, bg ) {
        const l1 = getLuminance( fg );
        const l2 = getLuminance( bg );
        return ( Math.max( l1, l2 ) + 0.05 ) / ( Math.min( l1, l2 ) + 0.05 );
    }

    function getLuminance( color ) {
        const { r, g, b } = parseRGBA( color );
        const rgb = [ r, g, b ];
        const a = rgb.map( c => {
            c = c / 255;
            return c <= 0.03928 ? c / 12.92 : Math.pow( ( c + 0.055 ) / 1.055, 2.4 );
        } );
        return 0.2126 * a[0] + 0.7152 * a[1] + 0.0722 * a[2];
    }

    function parseRGBA( color ) {
        const match = color.match( /rgba?\((\d+), ?(\d+), ?(\d+)(?:, ?([\d.]+))?\)/ );
        if ( match ) {
            return {
                r: parseInt( match[1] ),
                g: parseInt( match[2] ),
                b: parseInt( match[3] ),
                a: match[4] !== undefined ? parseFloat( match[4] ) : 1
            };
        }
        return { r: 255, g: 255, b: 255, a: 1 };
    }

    function compositeColors( fg, bg ) {
        const alpha = fg.a + bg.a * (1 - fg.a);
        const r = Math.round( (fg.r * fg.a + bg.r * bg.a * (1 - fg.a)) / alpha );
        const g = Math.round( (fg.g * fg.a + bg.g * bg.a * (1 - fg.a)) / alpha );
        const b = Math.round( (fg.b * fg.a + bg.b * bg.a * (1 - fg.a)) / alpha );
        return { r, g, b };
    }

    function rgbToHex( rgb ) {
        const result = rgb.match( /\d+/g );
        if ( !result || result.length < 3 ) return '000000';
        return result.slice( 0, 3 ).map( x => {
            const hex = parseInt( x ).toString( 16 );
            return hex.length === 1 ? '0' + hex : hex;
        } ).join( '' ).toUpperCase();
    }


    /**
     * Vague Link Text
     */
    function showVagueLinkTexts() {
        const vaguePhrases = admin_bar.vague_link_text
            .split( ',' )
            .map( phrase => phrase.trim().toLowerCase() )
            .filter( phrase => phrase.length > 0 );

        let count = 0;

        $( 'a:visible' ).not( '#wpadminbar a' ).each( function() {
            const link = this;
            const $link = $( link );
            const linkText = $link.text().trim();

            if ( !linkText ) return;

            if ( vaguePhrases.includes( linkText.toLowerCase() ) ) {
                if ( ! $link.hasClass( 'a11y-vague-link-text' ) ) {
                    $link.addClass( 'a11y-vague-link-text' );
                    $link.attr( 'title', 'Vague link text: "' + linkText + '"' );
                    count++;
                }
            }
        } );

        $( '.a11ytoolkit-count[data-tool="vague-link-text"]' ).text( count > 0 ? `(${count})` : '(0)' );
    }

    function removeVagueLinkTexts() {
        $( '.a11y-vague-link-text' ).each( function() {
            const $link = $( this );
            $link.removeClass( 'a11y-vague-link-text' );
            $link.removeAttr( 'title' );
        } );

        $( '.a11ytoolkit-count[data-tool="vague-link-text"]' ).text( '' );
    }


    /**
     * Heading Hierarchy
     */
    function showHeadings() {
        const headings = $( 'h1, h2, h3, h4, h5, h6' ).filter( ':visible' );
        let lastLevel = 0;
        let errorCount = 0;

        headings.each( function() {
            const heading = $( this );
            const tag = heading.prop( 'tagName' ).toUpperCase();
            const level = parseInt( tag.replace( 'H', '' ) );

            if ( heading.find( '.a11y-heading-label' ).length > 0 ) {
                return;
            }

            const label = $( '<span>' )
                .addClass( 'a11y-heading-label' )
                .text( tag );

            if ( lastLevel && level > lastLevel + 1 ) {
                label.addClass( 'a11y-error' ).attr( 'title', `Skipped heading level (last was H${lastLevel})` );
                heading.addClass( 'a11y-heading-error' );
                errorCount++;
            }

            lastLevel = level;
            heading.append( label );
        } );

        $( '.a11ytoolkit-count[data-tool="heading-hierarchy"]' ).text( errorCount > 0 ? `(${errorCount})` : '(0)' );
    }

    function removeHeadings() {
        $( '.a11y-heading-label' ).remove();
        $( '.a11y-heading-error' ).removeClass( 'a11y-heading-error' );
        $( '.a11ytoolkit-count[data-tool="heading-hierarchy"]' ).text( '' );
    }


    /**
     * Links Missing Underlines
     */
    function showUnderlineIssues() {
        let count = 0;

        $( 'a:visible' ).each( function() {
            const link = this;
            const $link = $( link );

            // Skip if inside #wpadminbar
            if ( $link.closest( '#wpadminbar' ).length ) return;

            // Skip if a button or nav
            if (
                link.className.match( /button/i ) ||
                $link.hasClass( 'btn' ) ||
                $link.closest( 'nav' ).length
            ) {
                return;
            }

            // Skip if inside a button element
            if ( $link.closest( 'button' ).length ) return;

            const text = $link.text().trim();
            if ( !text ) return;

            const computed = window.getComputedStyle( link );
            const decoration = computed.textDecorationLine || computed.textDecoration;

            if ( decoration !== 'underline' ) {
                count++;

                if ( !$link.hasClass( 'a11y-underline-issue' ) ) {
                    $link.addClass( 'a11y-underline-issue' );
                    const label = $( '<span>' )
                        .addClass( 'a11y-underline-label' )
                        .attr( 'title', 'Link is not underlined' )
                        .text( '⚠️' );
                    $link.append( label );
                }
            }
        } );

        $( '.a11ytoolkit-count[data-tool="underline-links"]' ).text( count > 0 ? `(${count})` : '' );
    }

    function removeUnderlineIssues() {
        $( '.a11y-underline-issue' ).removeClass( 'a11y-underline-issue' );
        $( '.a11y-underline-label' ).remove();
        $( '.a11ytoolkit-count[data-tool="underline-links"]' ).text( '' );
    }

} );
