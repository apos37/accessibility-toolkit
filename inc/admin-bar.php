<?php 
/**
 * Admin bar
 */


/**
 * Define Namespaces
 */
namespace Apos37\AccessibilityToolkit;
use Apos37\AccessibilityToolkit\Settings;


/**
 * Exit if accessed directly.
 */
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Instantiate the class
 */
add_action( 'init', function() {
	(new AdminBar())->init();
} );


/**
 * The class
 */
class AdminBar {

    /**
     * Nonce
     *
     * @var string
     */
    private $nonce_alt_text = 'media_library_alt_text';


    /**
     * Load on init
     */
    public function init() {
        
        // Add admin bar menu button
		add_action( 'admin_bar_menu', [ $this, 'menu' ], 100 );

        // Enqueue scripts
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

    } // End init()


    /**
     * Add a button to the admin bar
     * 
     * @return void
     */
    public function menu( $wp_admin_bar ) {
        if ( !current_user_can( 'manage_options' ) || is_admin() ) {
            return;
        }

        // The main toolbar menu item
        $wp_admin_bar->add_node( [
            'id'    => 'a11ytoolkit',
            'title' => '<span class="ab-icon dashicons dashicons-universal-access" title="' . esc_attr( A11YTOOLKIT_NAME ) . '"></span><span class="ab-label">' . __( 'A11y Tools', 'accessibility-toolkit' ) . '</span>',
            'href'  => false,
        ] );

        // AA or AAA for color contrast
        $aa_or_aaa = filter_var( get_option( 'a11ytoolkit_contrast_aaa' ), FILTER_VALIDATE_BOOLEAN ) ? 'AAA' : 'AA';

        // The tools in the dropdown
        $tools = [
            [
                'key'   => 'alt-text',
                'label' => __( 'Missing Alt Text', 'accessibility-toolkit' )
            ],
            [
                'key'   => 'contrast',
                // translators: %s is the WCAG level (AA or AAA)
                'label' => sprintf( __( 'Poor Color Contrast for %s', 'accessibility-toolkit' ), $aa_or_aaa )
            ],
            [
                'key'   => 'vague-link-text',
                'label' => __( 'Vague Link Text', 'accessibility-toolkit' )
            ],
            [
                'key'   => 'heading-hierarchy',
                'label' => __( 'Improper Heading Hierarchy', 'accessibility-toolkit' )
            ],
            [
                'key'   => 'underline-links',
                'label' => __( 'Links Missing Underlines', 'accessibility-toolkit' )
            ],
        ];
        
        foreach ( $tools as $tool ) {
            $wp_admin_bar->add_node( [
                'id'     => 'a11ytoolkit_' . $tool[ 'key' ],
                'parent' => 'a11ytoolkit',
                'title'  => '<label><input type="checkbox" class="a11ytoolkit-toggle" data-tool="' . $tool[ 'key' ] . '"> ' . $tool[ 'label' ] . ' <span class="a11ytoolkit-count" data-tool="' . $tool[ 'key' ] . '"></span></label>',
            ] );
        }
    } // End menu()


    /**
     * Enqueue scripts
     *
     * @return void
     */
    public function enqueue_scripts() {
        if ( !current_user_can( 'administrator' ) || is_admin() ) {
            return;
        }

		$handle = 'a11ytoolkit_admin_bar';
        wp_enqueue_script( 'jquery' );
		wp_enqueue_script( $handle, A11YTOOLKIT_JS_PATH . 'admin-bar.js', [ 'jquery' ], A11YTOOLKIT_SCRIPT_VERSION, true );
		wp_localize_script( $handle, 'admin_bar', [
            'ajaxurl'         => admin_url( 'admin-ajax.php' ),
            'nonce'           => wp_create_nonce( $this->nonce_alt_text ),
            'doing_aaa'       => filter_var( get_option( 'a11ytoolkit_contrast_aaa' ), FILTER_VALIDATE_BOOLEAN ),
            'vague_link_text' => sanitize_textarea_field( get_option( 'a11ytoolkit_meaningful_link_texts', (new Settings())->vague_link_phrases ) ),
            'text'            => [
                'edit'    => __( 'Edit', 'accessibility-toolkit' ),
                'update'  => __( 'Update', 'accessibility-toolkit' ),
                'missing' => __( 'Missing Alt Text', 'accessibility-toolkit' )
            ]
        ] );
		wp_enqueue_style( A11YTOOLKIT_TEXTDOMAIN . '-admin-bar', A11YTOOLKIT_CSS_PATH . 'admin-bar.css', [], A11YTOOLKIT_SCRIPT_VERSION );
    } // End enqueue_scripts()
}
