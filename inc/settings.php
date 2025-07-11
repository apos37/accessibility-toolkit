<?php 
/**
 * Plugin settings
 */


/**
 * Define Namespaces
 */
namespace Apos37\AccessibilityToolkit;
// use Apos37\AccessibilityToolkit\Clear;


/**
 * Exit if accessed directly.
 */
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Instantiate the class
 */
add_action( 'init', function() {
	(new Settings())->init();
} );


/**
 * The class
 */
class Settings {

    /**
	 * The options group
	 *
	 * @var string
	 */
	private $group = A11YTOOLKIT_TEXTDOMAIN;


    /**
     * Default value link texts
     *
     * @var string
     */
    public $vague_link_phrases = 'click here, read more, more info, learn more, details, here, more, info, link, see more, find out, read, go, continue, next, view, visit, download, watch, signup, register';
    

    /**
     * Load on init
     */
    public function init() {
        
		// Submenu
        add_action( 'admin_menu', [ $this, 'submenu' ] );

		// Register the options
        add_action( 'admin_init', [  $this, 'register' ] );

        // JQuery and CSS
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );

    } // End init()


	/**
     * Submenu
     *
     * @return void
     */
    public function submenu() {
        add_submenu_page(
            'tools.php',
            A11YTOOLKIT_NAME . ' — ' . __( 'Settings', 'accessibility-toolkit' ),
            A11YTOOLKIT_NAME,
            'manage_options',
            A11YTOOLKIT__TEXTDOMAIN,
            [ $this, 'page' ]
        );
    } // End submenu()

    
    /**
     * The page
     *
     * @return void
     */
    public function page() {
        global $current_screen;
        if ( $current_screen->id !== A11YTOOLKIT_SETTINGS_SCREEN_ID ) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_attr( get_admin_page_title() ) ?></h1>

			<form method="post" action="options.php">
				<?php settings_fields( $this->group ); ?>
				<div class="a11ytoolkit-settings-wrapper">
					<div class="a11ytoolkit-box-sections">
						<?php $this->sections(); ?>
					</div>
					<div class="a11ytoolkit-sidebar">
						<div class="a11ytoolkit-box-row">
							<div class="a11ytoolkit-box-column">
								<header class="a11ytoolkit-box-header"><h2><?php echo esc_html__( 'Save Settings', 'accessibility-toolkit' ); ?></h2></header>
								<div class="a11ytoolkit-box-content">
									<p><?php echo esc_html__( 'Once you are satisfied with your settings, click the button below to save them.', 'accessibility-toolkit' ); ?></p>
									<?php submit_button( __( 'Update', 'accessibility-toolkit' ) ); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
        <?php
    } // End page()


    /**
	 * Get the settings sections
	 */
	public function sections() {
		$sections = [
			'structural' => __( 'Structural', 'accessibility-toolkit' ),
			'images'     => __( 'Images', 'accessibility-toolkit' ),
            'previewer'  => __( 'Previewer', 'accessibility-toolkit' ),
            'modes'      => __( 'Modes', 'accessibility-toolkit' ),
		];

		// Iter the sections
        foreach ( $sections as $key => $title ) {
			?>
			<div class="a11ytoolkit-box-row">
				<div class="a11ytoolkit-box-column">
					<header class="a11ytoolkit-box-header"><h2><?php echo esc_html( $title ); ?></h2></header>

                    <?php if ( $key == 'modes' ) { ?>
                        <p class="inst"><?php echo wp_kses_post( __( 'Modes include Dark Mode and Greyscale Mode. Enabling Dark Mode applies a few basic style adjustments automatically, such as background and text color on some standard elements. However, every theme is different, and you will likely need to write additional CSS to ensure your design works as intended. When Dark Mode is active, a <code>a11ytoolkit-dark-mode</code> class is added to the <code>&lt;body&gt;</code> element. You can use this as a starting point for targeting specific elements. Additionally, any element with the <code>dark-mode</code> class will automatically receive a <code>#222222</code> background and <code>#ffffff</code> text color.', 'accessibility-toolkit' ) ); ?></p>
                        <p class="inst">Example CSS: <code>.a11ytoolkit-dark-mode .element { background: #222222; color: #ffffff; }</code></p>
                    <?php } ?>

					<?php $this->fields( $key ); ?>
				</div>
			</div>
			<?php
        }
	} // End sections()


    /**
	 * The options to register
	 *
	 * @return array
	 */
	public function options() {
		$options = [
            [
                'section'   => 'structural',
                'type'      => 'checkbox',
                'sanitize'  => 'sanitize_checkbox',
                'key'       => 'a11ytoolkit_skip_link',
                'title'     => __( 'Skip to Content Link', 'accessibility-toolkit' ),
                'desc'      => __( 'Adds a visually hidden "Skip to main content" link at the top of every page for improved keyboard navigation.', 'accessibility-toolkit' ),
                'default'   => TRUE,
            ],
            [
                'section'   => 'images',
                'type'      => 'checkbox',
                'sanitize'  => 'sanitize_checkbox',
                'key'       => 'a11ytoolkit_media_library_alt_text',
                'title'     => __( 'Alt Text Column & Inline Editing', 'accessibility-toolkit' ),
                'desc'      => __( 'Adds a sortable Alt Text column to the Media Library list view. Alt text can be updated directly within the table.', 'accessibility-toolkit' ),
                'default'   => TRUE,
            ],
            [
                'section'   => 'images',
                'type'      => 'checkbox',
                'sanitize'  => 'sanitize_checkbox',
                'key'       => 'a11ytoolkit_media_library_other_cols',
                'title'     => __( 'Additional Media Columns', 'accessibility-toolkit' ),
                'desc'      => __( 'Adds columns for Dimensions, MIME Type (e.g. image/png), and File Size to the Media Library list view.', 'accessibility-toolkit' ),
                'default'   => TRUE,
            ],
            [
                'section'   => 'previewer',
                'type'      => 'checkbox',
                'sanitize'  => 'sanitize_checkbox',
                'key'       => 'a11ytoolkit_admin_bar',
                'title'     => __( 'Toolbar Toggles', 'accessibility-toolkit' ),
                'desc'      => __( 'Adds a menu to the admin bar on the front end with tools you can toggle to show errors on the page.', 'accessibility-toolkit' ),
                'default'   => TRUE,
            ],
            [
                'section'   => 'previewer',
                'type'      => 'checkbox',
                'sanitize'  => 'sanitize_checkbox',
                'key'       => 'a11ytoolkit_contrast_aaa',
                'title'     => __( 'Use WCAG AAA Color Contrast Compliance', 'accessibility-toolkit' ),
                'desc'      => __( 'When enabled, color contrast checks will enforce stricter AAA standards in addition to the default AA criteria, ensuring higher accessibility compliance on your site.', 'accessibility-toolkit' ),
                'default'   => FALSE,
                'conditions' => [ 'a11ytoolkit_admin_bar' ]
            ],
            [
                'section'   => 'previewer',
                'type'      => 'textarea',
                'sanitize'  => 'sanitize_textarea_field',
                'key'       => 'a11ytoolkit_meaningful_link_texts',
                'title'     => __( 'Vague Link Texts', 'accessibility-toolkit' ),
                'desc'      => __( 'Comma-separated list of vague or generic link texts to check for, e.g. "click here, read more, more info".', 'accessibility-toolkit' ),
                'default'   => $this->vague_link_phrases,
                'revert'    => TRUE,
                'conditions'=> [ 'a11ytoolkit_admin_bar' ],
            ],
            [
                'section'   => 'modes',
                'type'      => 'select',
                'sanitize'  => 'sanitize_key',
                'key'       => 'a11ytoolkit_mode_visibility',
                'title'     => __( 'Mode Visibility', 'accessibility-toolkit' ),
                'desc'      => __( 'Controls who can see the mode switcher on the front end. Choose to limit visibility to administrators, logged-in users, or show it to everyone.', 'accessibility-toolkit' ),
                'options'   => [
                    ''          => __( 'Disabled', 'accessibility-toolkit' ),
                    'admins'    => __( 'Administrators Only', 'accessibility-toolkit' ),
                    'logged-in' => __( 'Logged-In Only', 'accessibility-toolkit' ),
                    'everyone'  => __( 'Everyone', 'accessibility-toolkit' ),
                ],
                'default'   => '',
            ],
            [
                'section'   => 'modes',
                'type'      => 'select',
                'sanitize'  => 'sanitize_key',
                'key'       => 'a11ytoolkit_modes',
                'title'     => __( 'Mode Selector', 'accessibility-toolkit' ),
                'desc'      => __( 'Adds a selector for switching to dark mode or greyscale mode.', 'accessibility-toolkit' ),
                'options'   => [
                    'float'     => __( 'Floating Switch', 'accessibility-toolkit' ),
                    'nav'       => __( 'Navigation Menu', 'accessibility-toolkit' ),
                    'shortcode' => __( 'Shortcode [a11ytoolkit_modes]', 'accessibility-toolkit' ),
                ],
                'default'   => 'float',
                'conditions'=> [ 'a11ytoolkit_mode_visibility' ],
            ],
            [
                'section'   => 'modes',
                'type'      => 'url',
                'sanitize'  => 'sanitize_url',
                'key'       => 'a11ytoolkit_light_logo',
                'title'     => __( 'Default Logo URL', 'accessibility-toolkit' ),
                'desc'      => __( 'Optional. Provide the URL of a logo that should be replaced with the logo specified below when dark mode is enabled.', 'accessibility-toolkit' ),
                'default'   => '',
                'conditions'=> [ 'a11ytoolkit_mode_visibility' ],
            ],
            [
                'section'   => 'modes',
                'type'      => 'url',
                'sanitize'  => 'sanitize_url',
                'key'       => 'a11ytoolkit_dark_logo',
                'title'     => __( 'Alternative Logo for Dark Mode', 'accessibility-toolkit' ),
                'desc'      => __( 'Optional. Provide the URL of a high-contrast or light-colored logo to be used automatically when dark mode is enabled.', 'accessibility-toolkit' ),
                'default'   => '',
                'conditions'=> [ 'a11ytoolkit_mode_visibility' ],
            ],
        ];

        // Apply filter to allow developers to add custom fields
        $options = apply_filters( 'a11ytoolkit_custom_settings', $options );

        return $options;
	} // End options()


    /**
	 * Register the options
	 *
	 * @return array
	 */
	public function register() {
		$options = $this->options();
		foreach ( $options as $option ) {
			register_setting( $this->group, $option[ 'key' ], $option[ 'sanitize' ] );
		}
	} // End register()


	/**
	 * Get the setting fields
	 *
	 * @param string $section
	 */
	public function fields( $section ) {
		$options = $this->options();

		foreach ( $options as $option ) {
			if ( $option[ 'section' ] !== $section ) {
				continue;
			}

			// Determine visibility based on conditions
			$not_applicable = false;
			if ( isset( $option[ 'conditions' ] ) && is_array( $option[ 'conditions' ] ) ) {
				foreach ( $option[ 'conditions' ] as $condition_key ) {
					$condition_option = array_filter( $options, fn( $opt ) => $opt[ 'key' ] === $condition_key );
					$condition = reset( $condition_option );
					$val = sanitize_text_field( get_option( $condition_key, $condition[ 'default' ] ?? '' ) );
					if ( !filter_var( $val, FILTER_VALIDATE_BOOLEAN ) ) {
						$not_applicable = true;
						break;
					}
				}
			}

			$classes = 'a11ytoolkit-box-content has-fields';
			if ( $not_applicable ) {
				$classes .= ' not-applicable';
			}
			?>
			<div class="<?php echo esc_attr( $classes ); ?>">
				<div class="a11ytoolkit-box-left">
					<label for="<?php echo esc_html( $option[ 'key' ] ); ?>"><?php echo esc_html( $option[ 'title' ] ); ?></label>
					<?php if ( isset( $option[ 'desc' ] ) ) { ?>
						<p class="a11ytoolkit-box-desc"><?php echo esc_html( $option[ 'desc' ] ); ?></p>
					<?php } ?>
				</div>
				
				<div class="a11ytoolkit-box-right">
					<?php
					$add_field = 'settings_field_' . $option[ 'type' ];
					$this->$add_field( $option );
					?>
				</div>
			</div>
			<?php
		}
	} // End fields()
  
    
    /**
     * Custom callback function to print text field
     *
     * @param array $args
     * @return void
     */
    public function settings_field_text( $args ) {
        $width = isset( $args[ 'width' ] ) ? $args[ 'width' ] : '43rem';
        $default = isset( $args[ 'default' ] )  ? $args[ 'default' ] : '';
        $value = sanitize_text_field( get_option( $args[ 'key' ], $default ) );
        if ( isset( $args[ 'revert' ] ) && $args[ 'revert' ] == true && trim( $value ) == '' ) {
            $value = $default;
        }
        $comments = isset( $args[ 'comments' ] ) ? '<br><p class="description">' . $args[ 'comments' ] . '</p>' : '';

        printf(
            // Translators: %1$s is the input field id, %2$s is the input field name, %3$s is the current value of the field, %4$s is the CSS width, %5$s is comments HTML.
            '<input type="text" id="%1$s" name="%2$s" value="%3$s" style="width: %4$s;" />%5$s',
            esc_attr( $args[ 'key' ] ),
            esc_attr( $args[ 'key' ] ),
            esc_html( $value ),
            esc_attr( $width ),
            wp_kses_post( $comments )
        );
    } // settings_field_text()


    /**
     * Custom callback function to print URL field
     *
     * @param array $args
     * @return void
     */
    public function settings_field_url( $args ) {
        $width   = isset( $args[ 'width' ] ) ? $args[ 'width' ] : '43rem';
        $default = isset( $args[ 'default' ] ) ? $args[ 'default' ] : '';
        $value   = esc_url( get_option( $args[ 'key' ], $default ) );

        if ( isset( $args[ 'revert' ] ) && $args[ 'revert' ] === true && trim( $value ) === '' ) {
            $value = esc_url( $default );
        }

        $comments = isset( $args[ 'comments' ] ) ? '<br><p class="description">' . $args[ 'comments' ] . '</p>' : '';

        printf(
            // Translators: %1$s is the input field id, %2$s is the input field name, %3$s is the current URL value, %4$s is the CSS width, %5$s is comments HTML.
            '<input type="url" id="%1$s" name="%2$s" value="%3$s" style="width: %4$s;" />%5$s',
            esc_attr( $args[ 'key' ] ),
            esc_attr( $args[ 'key' ] ),
            esc_url( $value ),
            esc_attr( $width ),
            wp_kses_post( $comments )
        );
    } // settings_field_url()


    /**
     * Custom callback function to print textarea field
     *
     * @param array $args
     * @return void
     */
    public function settings_field_textarea( $args ) {
        $width = isset( $args[ 'width' ] ) ? $args[ 'width' ] : '43rem';
        $height = isset( $args[ 'height' ] ) ? $args[ 'height' ] : '6rem';
        $default = isset( $args[ 'default' ] ) ? $args[ 'default' ] : '';
        $value = sanitize_textarea_field( get_option( $args[ 'key' ], $default ) );
        if ( isset( $args[ 'revert' ] ) && $args[ 'revert' ] === true && trim( $value ) === '' ) {
            $value = $default;
        }
        $comments = isset( $args[ 'comments' ] ) ? '<br><p class="description">' . $args[ 'comments' ] . '</p>' : '';

        printf(
            // Translators: %1$s is the textarea field id, %2$s is the textarea field name, %3$s is the CSS width, %4$s is the CSS height, %5$s is the current textarea value, %6$s is comments HTML.
            '<textarea id="%1$s" name="%2$s" style="width: %3$s; height: %4$s;">%5$s</textarea>%6$s',
            esc_attr( $args[ 'key' ] ),
            esc_attr( $args[ 'key' ] ),
            esc_attr( $width ),
            esc_attr( $height ),
            esc_textarea( $value ),
            wp_kses_post( $comments )
        );
    } // settings_field_textarea()


    /**
     * Custom callback function to print checkbox field
     *
     * @param array $args
     * @return void
     */
    public function settings_field_checkbox( $args ) {
		$value = filter_var( get_option( $args[ 'key' ], $args[ 'default' ] ), FILTER_VALIDATE_BOOLEAN );
		$id    = esc_attr( $args[ 'key' ] );
		$label = $value ? __( 'On', 'accessibility-toolkit' ) : __( 'Off', 'accessibility-toolkit' );

		printf(
			'<label class="a11ytoolkit-toggle">
				<input type="checkbox" id="%1$s" name="%1$s"%2$s />
				<span>
					<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
						<path fill="none" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10"
							d="M17,4.3c3,1.7,5,5,5,8.7 c0,5.5-4.5,10-10,10S2,18.5,2,13c0-3.7,2-6.9,5-8.7"
							stroke-linejoin="miter"></path>
						<line fill="none" stroke-width="2" stroke-linecap="square" stroke-miterlimit="10"
							x1="12" y1="1" x2="12" y2="8" stroke-linejoin="miter"></line>
					</svg>
					<span class="label">%3$s</span>
				</span>
			</label>',
			esc_attr( $id ),
			checked( $value, 1, false ),
			esc_html( $label )
		);
	} // End settings_field_checkbox()


    /**
     * Sanitize checkbox
     *
     * @param int $value
     * @return boolean
     */
    public function sanitize_checkbox( $value ) {
        return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
    } // End sanitize_checkbox()


    /**
     * Custom callback function to print select field
     *
     * @param array $args
     * @return void
     */
    public function settings_field_select( $args ) {
        $width   = isset( $args[ 'width' ] ) ? $args[ 'width' ] : '20rem';
        $options = isset( $args[ 'options' ] ) ? $args[ 'options' ] : [];
        $default = isset( $args[ 'default' ] ) ? $args[ 'default' ] : '';
        $value   = sanitize_key( get_option( $args[ 'key' ], $default ) );
        if ( isset( $args[ 'revert' ] ) && $args[ 'revert' ] === true && trim( $value ) === '' ) {
            $value = $default;
        }
        $comments = isset( $args[ 'comments' ] ) ? '<br><p class="description">' . $args[ 'comments' ] . '</p>' : '';

        printf(
            // Translators: %1$s is the select field id, %2$s is the select field name, %3$s is the CSS width style, %4$s is the rendered <option> tags, %5$s is comments HTML.
            '<select id="%1$s" name="%2$s" style="width: %3$s;">%4$s</select>%5$s',
            esc_attr( $args[ 'key' ] ),
            esc_attr( $args[ 'key' ] ),
            esc_attr( $width ),
            wp_kses(
                $this->render_select_options( $options, $value ),
                [
                    'option' => [
                        'value'    => true,
                        'selected' => true,
                    ],
                ]
            ),
            wp_kses_post( $comments )
        );
    } // End settings_field_select()


    /**
     * Renders <option> tags for a select field
     *
     * @param array  $options
     * @param string $selected
     * @return string
     */
    private function render_select_options( $options, $selected ) {
        $html = '';
        foreach ( $options as $val => $label ) {
            $html .= sprintf(
                // Translators: %1$s is the option value, %2$s is 'selected' if this is the current value, %3$s is the label text.
                '<option value="%1$s"%2$s>%3$s</option>',
                esc_attr( $val ),
                selected( $selected, $val, false ),
                esc_html( $label )
            );
        }
        return $html;
    } // End render_select_options()


    /**
     * Enqueue
     *
     * @return void
     */
    public function enqueue( $hook ) {
        // Check if we are on the correct admin page
        if ( $hook !== A11YTOOLKIT_SETTINGS_SCREEN_ID ) {
            return;
        }

        // Get the options
		$options_with_conditions = array_values( array_filter( $this->options(), function( $option ) {
			return isset( $option[ 'conditions' ] );
		} ) );

		// JS
		$handle = 'a11ytoolkit_settings';
		wp_enqueue_script( $handle, A11YTOOLKIT_JS_PATH . 'settings.js', [ 'jquery' ], A11YTOOLKIT_SCRIPT_VERSION, true );
		wp_localize_script( $handle, $handle, [
			'on'      => __( 'On', 'accessibility-toolkit' ),
			'off'     => __( 'Off', 'accessibility-toolkit' ),
			'options' => array_map( function( $option ) {
				return [
					'key'        => $option[ 'key' ],
					'conditions' => $option[ 'conditions' ],
				];
			}, $options_with_conditions ),
		] );

		// CSS
		wp_enqueue_style( A11YTOOLKIT_TEXTDOMAIN . '-settings', A11YTOOLKIT_CSS_PATH . 'settings.css', [], A11YTOOLKIT_SCRIPT_VERSION );
    } // End enqueue()

}
