<?php 
/**
 * Structural Enhancements
 */


/**
 * Define Namespaces
 */
namespace Apos37\AccessibilityToolkit;


/**
 * Exit if accessed directly.
 */
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Initiate the class
 */
new Structural();


/**
 * The class.
 */
class Structural {

    /**
	 * Constructor
	 */
	public function __construct() {

        // Skip to content link
        add_action( 'wp_body_open', [ $this, 'skip_to_content_link' ], 5 );

        // Scripts and styles
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue' ] );

	} // End __construct()


    /**
     * Output the skip to content link immediately after <body>
     *
     * @return void
     */
    public function skip_to_content_link() {
        echo '<a class="a11y-skip-link" href="#content">' . esc_html__( 'Skip to main content', 'accessibility-toolkit' ) . '</a>';
    } // End skip_to_content_link()


    /**
     * Enqueue frontend assets
     */
    public function enqueue() {
        if ( is_admin() ) {
            return;
        }
        
        $handle = 'a11ytoolkit-structural';
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( $handle, A11YTOOLKIT_JS_PATH . 'structural.js', [ 'jquery' ], A11YTOOLKIT_SCRIPT_VERSION, true );
        wp_enqueue_style( $handle, A11YTOOLKIT_CSS_PATH . 'structural.css', [], A11YTOOLKIT_SCRIPT_VERSION );
    } // End enqueue()

}