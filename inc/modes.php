<?php 
/**
 * Modes
 */


/**
 * Define Namespaces
 */
namespace Apos37\AccessibilityToolkit;
use Apos37\AccessibilityToolkit\Integrations;


/**
 * Exit if accessed directly.
 */
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Initiate the class
 */
add_filter( 'init', function() {
    $visibility = sanitize_key( get_option( 'a11ytoolkit_mode_visibility' ) );
    if ( ( $visibility === 'admins' && current_user_can( 'administrator' ) ) ||
         ( $visibility === 'logged-in' && is_user_logged_in() ) ||
         ( $visibility === 'everyone' ) ) {
        new Modes();
    }
} );


/**
 * The class.
 */
class Modes {

    /**
     * The key that is used to identify the ajax response
     *
     * @var string
     */
    public $ajax_key = 'a11ytoolkit_modes';


    /**
     * Name of nonce used for ajax call
     *
     * @var string
     */
    public $nonce = 'a11ytoolkit_modes_nonce';


    /**
     * The user meta key
     *
     * @var string
     */
    public $meta_key = 'a11ytoolkit_mode';


    /**
	 * Constructor
	 */
	public function __construct() {

        // Add body classes
        add_filter( 'body_class', [ $this, 'body_class' ] );

        // Callback
        $mode_selector = sanitize_key( get_option( 'a11ytoolkit_modes', 'float' ) );
        if ( $mode_selector == 'float' ) {
            add_action( 'wp_footer', [ $this, 'float' ] );
        } elseif ( $mode_selector == 'nav' ) {
            add_filter( 'wp_nav_menu_items', [ $this, 'nav' ], 10, 2 );
        }
        add_shortcode( 'a11ytoolkit_modes', [ $this, 'shortcode' ] );

        // Ajax
        add_action( 'wp_ajax_' . $this->ajax_key, [ $this, 'ajax' ] );
        add_action( 'wp_ajax_nopriv_' . $this->ajax_key, [ $this, 'ajax' ] );

        // Enqueue the script
        add_action( 'wp_enqueue_scripts', [ $this, 'script_enqueuer' ] );
        
	} // End __construct()


    /**
     * Get the modes
     *
     * @return array
     */
    public function modes() {
        $default = [ 'default' => [
            'label' => __( 'Default', 'accessibility-toolkit' ),
            'icon'  => 'f185', // fa-sun
        ] ];

        $modes = apply_filters( 'a11ytoolkit_modes', [
            'dark'          => [
                'label' => __( 'Dark', 'accessibility-toolkit' ),
                'icon'  => 'f186', // fa-moon
            ],
            // 'high-contrast' => [
            //     'label' => __( 'High Contrast', 'accessibility-toolkit' ),
            //     'icon'  => 'f06a', // fa-circle-exclamation
            // ],
            'greyscale'     => [
                'label' => __( 'Greyscale', 'accessibility-toolkit' ),
                'icon'  => 'f042', // fa-circle-half-stroke
            ],
        ] );

        $modes = $default + $modes;
        return $modes;
    } // End modes()


    /**
     * Get the mode icon
     *
     * @param string $mode
     * @return string
     */
    public function get_icon( $mode ) {
        $modes = $this->modes();

        $icon_value = isset( $modes[ $mode ][ 'icon' ] ) ? $modes[ $mode ][ 'icon' ] : '';
        $label      = isset( $modes[ $mode ][ 'label' ] ) ? $modes[ $mode ][ 'label' ] : $mode;
        $data_attr  = 'data-mode="' . esc_attr( $mode ) . '"';

        $classes = [ 'a11ytoolkit-mode' ];

        // If no icon is defined, return label in span
        if ( ! $icon_value ) {
            return '<i class="' . implode( ' ', $classes ) . '" ' . $data_attr . '>' . esc_html( $label ) . '</i>';
        }

        // If full HTML is passed, inject class/data into the first tag
        if ( str_starts_with( trim( $icon_value ), '<' ) ) {
            return preg_replace(
                '/^<([a-z0-9]+)(\s|>)/i',
                '<$1 class="' . implode( ' ', $classes ) . '" ' . $data_attr . '$2',
                $icon_value,
                1
            );
        }

        $is_hex_code = preg_match( '/^[a-f0-9]{3,6}$/i', $icon_value );

        // Cornerstone-specific hex icon
        if ( ( new Integrations() )->is_cornerstone_active() && $is_hex_code ) {
            $classes[] = 'x-icon';
            $classes[] = 'fa';
            return '<i class="' . implode( ' ', $classes ) . '" ' . $data_attr . ' data-x-icon-s="&#x' . esc_attr( $icon_value ) . ';"></i>';
        }

        // Standard hex-based Font Awesome icon
        if ( $is_hex_code ) {
            $classes[] = 'fa';
            return '<i class="' . implode( ' ', $classes ) . '" ' . $data_attr . '>&#x' . esc_attr( $icon_value ) . ';</i>';
        }

        // Font Awesome class (e.g., fa-sun)
        if ( preg_match( '/^fa(-[a-z0-9\-]+)+$/i', $icon_value ) ) {
            $classes[] = 'fa';
            $classes[] = $icon_value;
            return '<i class="' . implode( ' ', $classes ) . '" ' . $data_attr . '></i>';
        }

        // Fallback to label
        return '<i class="' . implode( ' ', $classes ) . '" ' . $data_attr . '>' . esc_html( $label ) . '</i>';
    } // End get_icon()


    /**
     * Get the current user's mode
     *
     * @return string
     */
    public function get_user_mode() {
        // If logged in
        if ( $user_id = get_current_user_id() ) {
            $mode = sanitize_key( get_user_meta( $user_id, $this->meta_key, true ) );
            
        // Or else check their session
        } else {
            if ( session_status() !== PHP_SESSION_ACTIVE ) {
                session_start();
            }

            $session_key = $this->meta_key;
            $mode = isset( $_SESSION[ $session_key ] ) ? sanitize_key( wp_unslash( $_SESSION[ $session_key ] ) ) : '';
        }

        return array_key_exists( $mode, $this->modes() ) ? $mode : 'default';
    } // End get_user_mode()


    /**
     * Add body class
     *
     * @param array $classes
     * @return array
     */
    public function body_class( $classes ) {
        $classes[] = 'a11ytoolkit-' . $this->get_user_mode() . '-mode';
        return $classes;
    } // End body_class()


    /**
     * Get the selector HTML
     *
     * @return string
     */
    public function selector( $type ) {
        $current_mode = $this->get_user_mode();
        $modes = $this->modes();

        if ( !isset( $modes[ $current_mode ] ) ) {
            $current_mode = 'default';
        }

        $icon_html = $this->get_icon( $current_mode );
        $label = $modes[ $current_mode ][ 'label' ];

        ob_start();
        ?>
        <div id="a11ytoolkit-mode-switch" data-type="<?php echo esc_attr( $type ); ?>" data-current="<?php echo esc_attr( $current_mode ); ?>">
            <button id="a11ytoolkit-mode-toggle" aria-label="<?php echo esc_attr( $label ); ?>" title="<?php echo esc_attr( $label ); ?>">
                <?php echo wp_kses_post( $icon_html ); ?>
                <span class="screen-reader-text"><?php echo esc_html( $label ); ?></span>
            </button>
        </div>
        <?php
        return ob_get_clean();
    } // End selector()

    
    /**
     * Add a floating selector
     */
    public function float() {
        echo wp_kses_post( $this->selector( 'float' ) );
    } // End float()


    /**
     * Inject mode selector into the primary nav menu
     *
     * @param string $items
     * @param object $args
     * @return string
     */
    public function nav( $items, $args ) {
        if ( isset( $args->theme_location ) && $args->theme_location === 'primary' ) {
            $items .= '<li class="menu-item a11ytoolkit-mode-menu-item"><div class="a11ytoolkit-nav-wrapper">' . $this->selector( 'nav' ) . '</div></li>';
        }
        return $items;
    } // End nav()


    /**
     * Add a shortcode selector
     *
     * @param array $atts
     * @return string
     */
    public function shortcode( $atts ) {
        $atts = shortcode_atts( [ 'type' => 'default' ], $atts );
        $type = strtolower( trim( $atts[ 'type' ] ) );

        $dropdown_types = [ 'select', 'dropdown', 'drop down' ];
        $current_mode   = $this->get_user_mode();
        $modes          = $this->modes();

        if ( in_array( $type, $dropdown_types, true ) ) {
            ob_start();
            ?>
            <select id="a11ytoolkit-mode-dropdown" aria-label="<?php esc_attr_e( 'Select accessibility mode', 'accessibility-toolkit' ); ?>">
                <?php foreach ( $modes as $key => $mode ) : ?>
                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $current_mode, $key ); ?>>
                        <?php echo esc_html( $mode[ 'label' ] ?? $key ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php
            return ob_get_clean();
        }

        return wp_kses_post( $this->selector( 'shortcode' ) );
    } // End shortcode()


    /**
     * Ajax call
     *
     * @return void
     */
    public function ajax() {
        // Verify nonce
        check_ajax_referer( $this->nonce, 'nonce' );

        // Sanitize and validate mode
        $raw_mode = isset( $_REQUEST[ 'mode' ] ) ? wp_unslash( $_REQUEST[ 'mode' ] ) : '';
        $mode = sanitize_key( $raw_mode );
        $available_modes = array_keys( $this->modes() );

        if ( !in_array( $mode, $available_modes, true ) ) {
            wp_send_json_error( [ 'message' => 'Invalid mode.' ] );
        }

        $user_id = get_current_user_id();

        if ( $user_id > 0 ) {
            // Logged-in user: update user meta
            $updated = update_user_meta( $user_id, $this->meta_key, $mode );
            if ( $updated ) {
                wp_send_json_success();
            } else {
                wp_send_json_error( [ 'message' => 'Could not update user mode.' ] );
            }

        } else {
            // Guest user: use PHP session
            if ( session_status() !== PHP_SESSION_ACTIVE ) {
                session_start();
            }

            $_SESSION[ $this->meta_key ] = $mode;
            wp_send_json_success();
        }

        wp_send_json_error( [ 'message' => 'Unhandled error.' ] );
    } // End ajax()


    /**
     * Enque the JavaScript
     *
     * @return void
     */
    public function script_enqueuer() {
        if ( is_admin() ) {
            return;
        }

        // Replace each mode's icon with the rendered HTML
        $modes = $this->modes();
        foreach ( $modes as $key => &$mode ) {
            $mode[ 'icon' ] = $this->get_icon( $key );
        }

        // JS
        $handle = 'a11ytoolkit_modes_js';
        wp_register_script( $handle, A11YTOOLKIT_JS_PATH . 'modes.js', [ 'jquery' ], A11YTOOLKIT_SCRIPT_VERSION );
        wp_localize_script( $handle, 'a11ytoolkit_modes', [ 
            'nonce'           => wp_create_nonce( $this->nonce ),
            'ajaxurl'         => admin_url( 'admin-ajax.php' ),
            'current_mode'    => $this->get_user_mode(),
            'modes'           => $modes,
            'light_mode_logo' => sanitize_url( get_option( 'a11ytoolkit_light_logo' ) ),
            'dark_mode_logo'  => sanitize_url( get_option( 'a11ytoolkit_dark_logo' ) ),
        ] );
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( $handle );
        
        // CSS
        wp_enqueue_style( 'a11ytoolkit_modes_css', A11YTOOLKIT_CSS_PATH . 'modes.css', [], A11YTOOLKIT_SCRIPT_VERSION );
        wp_enqueue_style( 'a11ytoolkit_modes_greyscale_css', A11YTOOLKIT_CSS_PATH . 'mode-greyscale.css', [], A11YTOOLKIT_SCRIPT_VERSION );
        wp_enqueue_style( 'a11ytoolkit_modes_high_contrast_css', A11YTOOLKIT_CSS_PATH . 'mode-high-contrast.css', [], A11YTOOLKIT_SCRIPT_VERSION );
        wp_enqueue_style( 'a11ytoolkit_modes_dark_css', A11YTOOLKIT_CSS_PATH . 'mode-dark.css', [], A11YTOOLKIT_SCRIPT_VERSION );

        // Integration-specific dark mode CSS
        $INTEGRATIONS = new Integrations();
        foreach ( $INTEGRATIONS->identifiers as $key => $plugin ) {
            if ( isset( $plugin[ 'css' ] ) && $plugin[ 'css' ] && !empty( $plugin[ 'short' ] ) ) {
                $handle = 'a11ytoolkit_modes_dark_css_' . $plugin[ 'short' ];
                $src    = A11YTOOLKIT_CSS_PATH . 'mode-dark-' . $plugin[ 'short' ] . '.css';
                wp_enqueue_style( $handle, $src, [], A11YTOOLKIT_SCRIPT_VERSION );
            }
        }

        // Integration-specific nav bar
        if ( $INTEGRATIONS->is_cornerstone_active() ) {
            wp_enqueue_style( 'a11ytoolkit_modes_nav_css_cs', A11YTOOLKIT_CSS_PATH . 'mode-nav-cs.css', [], A11YTOOLKIT_SCRIPT_VERSION );
        }
        
    } // End script_enqueuer()
}