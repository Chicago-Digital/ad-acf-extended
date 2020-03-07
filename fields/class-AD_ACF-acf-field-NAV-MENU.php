<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( !class_exists('AD_ACF_acf_field_NAV_MENU') ) :


class AD_ACF_acf_field_NAV_MENU extends acf_field {


	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function __construct( $settings ) {

		/*
		*  name (string) Single word, no spaces. Underscores allowed
		*/

		$this->name = 'nav_menu';


		/*
		*  label (string) Multiple words, can include spaces, visible when selecting a field type
		*/

		$this->label = __('Nav Menu', 'ad-acf-extended');


		/*
		*  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
		*/

		$this->category = 'relational';


		/*
		*  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
		*/

		$this->defaults = array(
			'save_format' => 'id',
			'allow_null'  => 0,
		);


		/*
		*  l10n (array) Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via:
		*  var message = acf._e('FIELD_NAME', 'error');
		*/

		$this->l10n = array(
			'error'	=> __('Error! Please enter a higher value', 'ad-acf-extended'),
		);


		/*
		*  settings (array) Store plugin settings (url, path, version) as a reference for later use with assets
		*/

		$this->settings = $settings;


		// do not delete!
    	parent::__construct();

	}


	/*
	*  render_field_settings()
	*
	*  Create extra settings for your field. These are visible when editing a field
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/

	function render_field_settings( $field ) {

		/*
			*  acf_render_field_setting
			*
			*  This function will create a setting for your field. Simply pass the $field parameter and an array of field settings.
			*  The array of settings does not require a `value` or `prefix`; These settings are found from the $field array.
			*
			*  More than one setting can be added by copy/paste the above code.
			*  Please note that you must also have a matching $defaults value for the field name (font_size)
			*  Renders the Nav Menu Field options seen when editing a Nav Menu Field.
			*
			* @param array $field The array representation of the current Nav Menu Field.
		*/

		// Register the Return Value format setting
    acf_render_field_setting($field, array(
        'label'        => __('Return Value'),
        'instructions' => __('Specify the returned value on front end'),
        'type'         => 'radio',
        'name'         => 'save_format',
        'layout'       => 'horizontal',
        'choices'      => array(
						'id'     => __('Nav Menu ID'),
            'object' => __('Nav Menu Object'),
        ),
    ));

    // Register the Allow Null setting
    acf_render_field_setting($field, array(
        'label'        => __('Allow Null?'),
        'type'         => 'radio',
        'name'         => 'allow_null',
        'layout'       => 'horizontal',
        'choices'      => array(
            1 => __('Yes'),
            0 => __('No'),
        ),
    ));

	}

	/*
		*  render_field()
		*
		*  Create the HTML interface for your field
		*
		*  @param	$field (array) the $field being rendered
		*
		* Renders the Nav Menu Field.
		*
		* @param array $field The array representation of the current Nav Menu Field.
	*/

	function render_field( $field ) {
		/*
		*  Review the data of $field.
		*  This will show what data is available
		*/

		$allow_null = $field['allow_null'];
		$nav_menus  = $this->get_nav_menus($allow_null);

		if (empty($nav_menus)) {
				return;
		} ?>
		<select id="<?php esc_attr($field['id']); ?>" class="<?php echo esc_attr($field['class']); ?>" name="<?php echo esc_attr($field['name']); ?>">
		<?php foreach ($nav_menus as $nav_menu_id => $nav_menu_name) : ?>
			<option value="<?php echo esc_attr($nav_menu_id); ?>" <?php selected($field['value'], $nav_menu_id); ?>>
				<?php echo esc_html($nav_menu_name); ?>
			</option>
		<?php endforeach; ?>
		</select>
		<?php

	}

	/**
     * Gets a list of Nav Menus indexed by their Nav Menu IDs.
     *
     * @param bool $allow_null If true, prepends the null option.
     *
     * @return array An array of Nav Menus indexed by their Nav Menu IDs.
     */
    private function get_nav_menus($allow_null = false) {
        $navs = get_terms('nav_menu', array( 'hide_empty' => false ));

        $nav_menus = array();

        if ($allow_null) {
            $nav_menus[''] = ' - Select - ';
        }

        foreach ($navs as $nav) {
            $nav_menus[ $nav->term_id ] = $nav->name;
        }

        return $nav_menus;
    }

		/*
			*  format_value()
			*
			*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
			*
			* Renders the Nav Menu Field.
			*
			* @param int   $value   The Nav Menu ID selected for this Nav Menu Field.
			* @param int   $post_id The Post ID this $value is associated with.
			* @param array $field   The array representation of the current Nav Menu Field.
			*
			* @return mixed The Nav Menu ID, or the Nav Menu HTML, or the Nav Menu Object, or false.
		*/
		function format_value( $value, $post_id, $field ) {

			// bail early if no value
        if (empty($value)) {
            return false;
        }

        // check format
        if ('object' == $field['save_format']) {
            $wp_menu_object = wp_get_nav_menu_object($value);

            if (empty($wp_menu_object)) {
                return false;
            }

            $menu_object = new stdClass;

            $menu_object->ID    = $wp_menu_object->term_id;
            $menu_object->name  = $wp_menu_object->name;
            $menu_object->slug  = $wp_menu_object->slug;
            $menu_object->count = $wp_menu_object->count;

            return $menu_object;
        }

        // Just return the Nav Menu ID
        return $value;

		}

}


// initialize
new AD_ACF_acf_field_NAV_MENU( $this->settings );


// class_exists check
endif;

?>
