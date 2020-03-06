<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( !class_exists('AD_ACF_acf_field_LINK_EXTENDED') ) :


class AD_ACF_acf_field_LINK_EXTENDED extends acf_field {


	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function __construct( $settings ) {

		/*
		*  name (string) Single word, no spaces. Underscores allowed
		*/

		$this->name = 'link-extended';


		/*
		*  label (string) Multiple words, can include spaces, visible when selecting a field type
		*/

		$this->label = __('Link (Extended)', 'ad-acf-extended');


		/*
		*  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
		*/

		$this->category = 'relational';


		/*
		*  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
		*/
		$this->defaults = array(
			'choices'			=> array(),
			'default_value'		=> '',
			'allow_null' 		=> 0,
			'return_format'	=> 'array'
		);


		/*
		*  l10n (array) Array of strings that are used in JavaScript. This allows JS strings to be translated in PHP and loaded via:
		*  var message = acf._e('link-extended', 'error');
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
	*  get_link function from main ACF Plugin
	*
	*  description
	*
	*  @type	function
	*  @since	1.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/

	function get_link( $value = '' ) {

		// vars
		$link = array(
			'title'		=> '',
			'url'		=> '',
			'class'		=> '',
			'target'	=> ''
		);


		// array (ACF 5.6.0)
		if( is_array($value) ) {

			$link = array_merge($link, $value);

		// post id (ACF < 5.6.0)
		} elseif( is_numeric($value) ) {

			$link['title'] = get_the_title( $value );
			$link['url'] = get_permalink( $value );

		// string (ACF < 5.6.0)
		} elseif( is_string($value) ) {

			$link['url'] = $value;

		}

		// return
		return $link;

	}


	/*
	*  render_field_settings()
	*
	*  Create extra settings for your field. These are visible when editing a field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
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

		// encode choices (convert from array)
		$field['choices'] = acf_encode_choices($field['choices']);


		// choices
		acf_render_field_setting( $field, array(
			'label'			=> __('Choices','ad-acf-extended'),
			'instructions'	=> __('Leave blank to give user ability to enter in class name. If you want to provide a link type dropdown enter each choice on a new line.','ad-acf-extended') . '<br /><br />' . __('See below for entering link class and name:','ad-acf-extended'). '<br /><br />' . __('popup-video : Video','ad-acf-extended'),
			'type'			=> 'textarea',
			'name'			=> 'choices',
		));


		// allow_null
		acf_render_field_setting( $field, array(
			'label'			=> __('Allow Null?','ad-acf-extended'),
			'instructions'	=> '',
			'name'			=> 'allow_null',
			'type'			=> 'true_false',
			'ui'			=> 1,
		));


		// default_value
		acf_render_field_setting( $field, array(
			'label'			=> __('Default Value','ad-acf-extended'),
			'instructions'	=> __('Appears when creating a new post','ad-acf-extended'),
			'type'			=> 'text',
			'name'			=> 'default_value',
		));

	}



	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field (array) the $field being rendered
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/

	function render_field( $field ) {

		$field['choices'] = acf_decode_choices($field['choices']);
		$has_choices = !empty($field['choices']);

		// vars
		$div = array(
			'id'	=> $field['id'],
			'class'	=> $field['class'] . ' acf-link',
		);

		// render scripts/styles
		acf_enqueue_uploader();

		// get link
		$link = $this->get_link( $field['value'] );

		// classes
		if( $link['url'] ) {
			$div['class'] .= ' -value';
		}

		if( $link['target'] === '_blank' ) {
			$div['class'] .= ' -external';
		}

		?>
		<div <?php acf_esc_attr_e($div); ?>>

			<div class="acf-hidden">
				<a class="link-node" data-types='<?php if ($has_choices) {echo htmlspecialchars(json_encode($field['choices']));} ?>' data-class="<?php echo esc_attr($link['class']); ?>" href="<?php echo esc_url($link['url']); ?>" target="<?php echo esc_attr($link['target']); ?>"><?php echo esc_html($link['title']); ?></a>
				<?php foreach( $link as $k => $v ): ?>
					<?php acf_hidden_input(array( 'class' => "input-$k", 'name' => $field['name'] . "[$k]", 'value' => $v )); ?>
				<?php endforeach; ?>
			</div>

			<a href="#" class="button" data-name="add" target=""><?php _e('Select Link', 'acf'); ?></a>

			<div class="link-wrap">
				<span class="link-title"><?php echo esc_html($link['title']); ?></span>
				<a class="link-url" href="<?php echo esc_url($link['url']); ?>" target="_blank"><?php echo esc_html($link['url']); ?></a>
				<?php if ($has_choices) : ?>
				<span class="link-type"><?php echo esc_html($field['choices'][$link['class']]); ?></span>
				<?php else : ?>
				<span class="link-class"><?php echo esc_html($link['class']); ?></span>
				<?php endif; ?>
				<i class="acf-icon -link-ext acf-js-tooltip" title="<?php _e('Opens in a new window/tab', 'acf'); ?>"></i><?php
				?><a class="acf-icon -pencil -clear acf-js-tooltip" data-name="edit" href="#" title="<?php _e('Edit', 'acf'); ?>"></a><?php
				?><a class="acf-icon -cancel -clear acf-js-tooltip" data-name="remove" href="#" title="<?php _e('Remove', 'acf'); ?>"></a>
			</div>

		</div>
		<?php

	}

	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value which was loaded from the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*
	*  @return	$value (mixed) the modified value
	*/



	function format_value( $value, $post_id, $field ) {

		// bail early if no value
		if( empty($value) ) return $value;

		// get link
		$link = $this->get_link( $value );

		// return link
		return $link;

	}

	/*
	*  validate_value()
	*
	*  This filter is used to perform validation on the value prior to saving.
	*  All values are validated regardless of the field's required setting. This allows you to validate and return
	*  messages to the user if the value is not correct
	*
	*  @type	filter
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$valid (boolean) validation status based on the value and the field's required setting
	*  @param	$value (mixed) the $_POST value
	*  @param	$field (array) the field array holding all the field options
	*  @param	$input (string) the corresponding input name for $_POST value
	*  @return	$valid
	* --
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/



	function validate_value( $valid, $value, $field, $input ){

		// bail early if not required
		if( !$field['required'] ) return $valid;

		// URL is required
		if( empty($value) ) {
			return false;
		}

		// return
		return $valid;

	}


	/*
	*  update_value()
	*
	*  This filter is applied to the $value before it is saved in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
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


	function input_admin_enqueue_scripts() {

		// vars
		$url = $this->settings['url'];
		$version = $this->settings['version'];

		// register & include JS
		wp_register_script('ad-acf-extended', "{$url}assets/js/input.js", array('acf-input'), $version);
		wp_enqueue_script('ad-acf-extended');

		// register & include CSS
		wp_register_style('ad-acf-extended', "{$url}assets/css/input.css", array('acf-input'), $version);
		wp_enqueue_style('ad-acf-extended');

	}


}


// initialize
new AD_ACF_acf_field_LINK_EXTENDED( $this->settings );


// class_exists check
endif;

?>
