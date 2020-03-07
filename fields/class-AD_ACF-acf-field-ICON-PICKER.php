<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( !class_exists('AD_ACF_acf_field_ICON_PICKER') ) :


class AD_ACF_acf_field_ICON_PICKER extends acf_field {


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

		$this->name = 'icon-picker';


		/*
		*  label (string) Multiple words, can include spaces, visible when selecting a field type
		*/

		$this->label = __('Icon Picker', 'ad-acf-extended');


		/*
		*  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
		*/

		$this->category = 'choice';


		/*
		*  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
		*/

		$this->defaults = array();


		/*
		*  l10n (array) Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via:
		*  var message = acf._e('icon-picker', 'error');
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
		*/

	}



	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field (array) the $field being rendered
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/

	function render_field( $field ) {
		/*
		*  Review the data of $field.
		*  This will show what data is available
		*/

		?>
		<!-- <input type="text" name="<?php echo $field['name']; ?>" value="<?php echo $field['value']; ?>"> -->
		<select name="<?php echo $field['name']; ?>" class="picker-icon input-<?php echo $field['key']; ?>" data-active-theme="<?php echo get_template_directory_uri(); ?>" data-selected="<?php echo esc_attr($field['value']); ?>">
			<option value>-- Please Select --</option>
		</select>
		<?php
	}

	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
	*
	*  @param	$value (mixed) the value which was loaded from the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*
	*  @return	$value (mixed) the modified value
	*/



	function format_value( $value, $post_id, $field ) {

		// bail early if no value
		if( empty($value) ) {
			$value = "";
		}

		return $value;

	}

	/*
	*  update_value()
	*
	*  This filter is applied to the $value before it is saved in the db
	*
	*  @param	$value (mixed) the value found in the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*  @return	$value
	*/

	function update_value( $value, $post_id, $field ) {

		// Check if value is an empty array and convert to empty string.
		if( empty($value) ) {
			$value = "";
		}

		// return
		return $value;

	}


	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add CSS + JavaScript to assist your render_field() action.
	*
	*  @type	action (admin_enqueue_scripts)
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function input_admin_enqueue_scripts() {

		// vars
		$url = $this->settings['url'];
		$version = $this->settings['version'];


		// register & include JS
		wp_register_script('ad-acf-extended', "{$url}assets/js/input.js", array('acf-input'), $version);
		wp_enqueue_script('ad-acf-extended');

		wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css' );
		wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array('jquery') );


		// register & include CSS
		wp_register_style('ad-acf-extended', "{$url}assets/css/input.css", array('acf-input'), $version);
		wp_enqueue_style('ad-acf-extended');
		wp_register_style('ad-acf-extended-icon-picker', get_template_directory_uri() . "/assets/icons/style.css", array('acf-input'), $version);
		wp_enqueue_style('ad-acf-extended-icon-picker');

	}

}


// initialize
new AD_ACF_acf_field_ICON_PICKER( $this->settings );


// class_exists check
endif;

?>
