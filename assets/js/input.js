(function($, undefined){

	var Field = acf.Field.extend({

		type: 'link-extended',

		events: {
			'click a[data-name="add"]': 	'onClickEdit',
			'click a[data-name="edit"]': 	'onClickEdit',
			'click a[data-name="remove"]':	'onClickRemove',
			'change .link-node':			'onChange',
		},

		$control: function(){
			return this.$('.acf-link');
		},

		$node: function(){
			return this.$('.link-node');
		},

		getValue: function(){

			// vars
			var $node = this.$node();

			// return false if empty
			if( !$node.attr('href') ) {
				return false;
			}

			// return
			return {
				title:	$node.html(),
				class:	$node.attr("data-class"),
				url:	$node.attr('href'),
				target:	$node.attr('target')
			};
		},

		setValue: function( val ){
			// default
			val = acf.parseArgs(val, {
				title:	'',
				class:	'',
				type: '',
				url:	'',
				target:	''
			});

			// vars
			var $div = this.$control();
			var $node = this.$node();

			// remove class
			$div.removeClass('-value -external');

			// add class
			if( val.url ) $div.addClass('-value');
			if( val.target === '_blank' ) $div.addClass('-external');

			// update text
			this.$('.link-title').html(val.title );
			this.$('.link-url').attr('href', val.url).html( val.url );
			this.$('.link-class').html(val.class);
			// Match Button/Link Type Class to Name
			if ($node.data('types') !== "") {
				$.each($node.data('types'), function(key, type) {
					if (key === val.class) {
						val.type = type;
					}
				});
				this.$('.link-type').html(val.type);
			}			

			// update node
			$node.html(val.title);
			$node.attr('href', val.url);
			$node.attr('target', val.target);
			$node.attr('data-class', val.class);

			// update inputs
			this.$('.input-title').val( val.title );
			this.$('.input-target').val( val.target );
			this.$('.input-class').val( val.class );
			this.$('.input-url').val( val.url ).trigger('change');
		},

		onClickEdit: function( e, $el ){
			var $node = this.$node();
			acf.wpLink.open( $node );
			if ($(".has-class-field").length === 0) {
				$("#wp-link-wrap").addClass("has-class-field");
				if ($node.attr('data-types') !== "") {
					$(".wp-link-text-field").after('<div class="wp-link-type-field"><label><span>Link Type</span><select id="wp-link-class"><option value>--Please Select--</option></select></label></div>');
					$.each($node.data('types'), function(key, type) {
						$("#wp-link-class").append('<option value="'+key+'">'+type+'</option>')
					});
					$("#wp-link-class").val($node.attr('data-class'));
					$("#wp-link-type").val(this.$('.link-class').text());
				}
				else {
					$(".wp-link-text-field").after('<div class="wp-link-class-field"><label><span>Link Class</span><input id="wp-link-class" type="text" class=""></label></div>');
					$("#wp-link-class").val(this.$('.link-class').text());
				}


			}
		},

		onClickRemove: function( e, $el ){
			this.setValue( false );
		},

		onChange: function( e, $el ){
			// get the changed value
			var val = this.getValue();

			// update inputs
			this.setValue(val);
		}

	});

	acf.registerFieldType( Field );


	// manager
	acf.wpLink = new acf.Model({

		getNodeValue: function(){
			var $node = this.get('node');
			return {
				title:	acf.decode( $node.html() ),
				class: $node.attr("data-class"),
				url:	$node.attr('href'),
				target:	$node.attr('target')
			};
		},

		setNodeValue: function( val ){
			var $node = this.get('node');
			$node.text( val.title );
			$node.attr('data-class', val.class );
			$node.attr('href', val.url);
			$node.attr('target', val.target);
			$node.trigger('change');
		},

		getInputValue: function(){
			return {
				title:	$('#wp-link-text').val(),
				class:	$('#wp-link-class').val(),
				url:	$('#wp-link-url').val(),
				target:	$('#wp-link-target').prop('checked') ? '_blank' : ''
			};
		},

		setInputValue: function( val ){
			$('#wp-link-text').val( val.title );
			$('#wp-link-class').val( val.class );
			$('#wp-link-url').val( val.url );
			$('#wp-link-target').prop('checked', val.target === '_blank' );
		},

		open: function( $node ){

			// add events
			this.on('wplink-open', 'onOpen');
			this.on('wplink-close', 'onClose');

			// set node
			this.set('node', $node);

			// create textarea
			var $textarea = $('<textarea id="acf-link-textarea" style="display:none;"></textarea>');
			$('body').append( $textarea );

			// vars
			var val = this.getNodeValue();

			// open popup
			wpLink.open( 'acf-link-textarea', val.url, val.title, null );

		},

		onOpen: function(){

			// always show title (WP will hide title if empty)
			$('#wp-link-wrap').addClass('has-text-field');

			// set inputs
			var val = this.getNodeValue();
			this.setInputValue( val );
		},

		close: function(){
			wpLink.close();
		},

		onClose: function(){

			// bail early if no node
			// needed due to WP triggering this event twice
			if( !this.has('node') ) {
				return false;
			}

			// remove events
			this.off('wplink-open');
			this.off('wplink-close');

			// set value
			var val = this.getInputValue();
			this.setNodeValue( val );

			// remove textarea
			$('#acf-link-textarea').remove();

			// reset
			this.set('node', null);

		}
	});

})(jQuery);

(function($){

  var Field = acf.Field.extend({
      type: 'icon-picker',
      events: {
          'keyup input[type="text"]': 'onkeyup'
      },
      $control: function(){
          return this.$('.acf-input');
      },
      $input: function(){
          return this.$('input[type="text"]');
      },
      initialize: function(){
					var $icon_picker_wrapper = this.$control(),
						$icon_picker = $icon_picker_wrapper.find(".picker-icon"),
						icon_picker_selected = $icon_picker.attr("data-selected"),
						icon_directory = "/assets/icons/selection.json",
						icon_json_file = $icon_picker_wrapper.find(".picker-icon").attr("data-active-theme") + icon_directory;

					$.getJSON( icon_json_file, function(data) {
						var icons = data.icons;
						$.each( icons, function( key, icon ) {
							icon_class_name = "icon-" + icon.properties.name;
							icon_selected = "";
							if (icon_class_name === icon_picker_selected) {
								icon_selected = 'selected="selected"';
							}
							$icon_picker.append('<option '+icon_selected+' value="'+icon_class_name+'">'+icon_class_name+' <span class="'+icon_class_name+'"></span></option>');
						});
						$icon_picker.select2({
							allowClear: true,
							placeholder: "-- Please Select --",
							templateResult: function(data) {
								if (!data.id) {
									return data.text;
								}
								var icon_selection = $('<div class="picker-icon"><span class="picker-icon__icon '+data.text+'"></span> <span class="picker-icon__label">'+data.text+'</span></div>');
								return icon_selection;
							},
							templateSelection: function(data) {
								if (!data.id) {
									return data.text;
								}
								var icon_selection = $('<div class="picker-icon"><span class="picker-icon__icon '+data.text+'"></span> <span class="picker-icon__label">'+data.text+'</span></div>');
								return icon_selection;
							}
						});


					}).fail(function() {
				    console.log( "error" );
				  });

          this.render();
      },
      isValid: function(){

          // vars
          var val = this.val();

					/*

          // url
          if( val.indexOf('://') !== -1 ) {
              return true;
          }

          // protocol relative url
          if( val.indexOf('//') === 0 ) {
              return true;
          }

					*/

          // return
          return false;
      },
      render: function(){
          // add class
          if( this.isValid() ) {
              this.$control().addClass('-valid');
          } else {
              this.$control().removeClass('-valid');
          }
      },
      onkeyup: function( e, $el ){
          this.render();
      }
  });

  acf.registerFieldType( Field );

})(jQuery);


(function($){
	/**
	*  initialize_field
	*
	*  This function will initialize the $field.
	*
	*  @date	30/11/17
	*  @since	5.6.5
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function initialize_field( $field ) {

	}


	if( typeof acf.add_action !== 'undefined' ) {

		/*
		*  ready & append (ACF5)
		*
		*  These two events are called when a field element is ready for initizliation.
		*  - ready: on page load similar to $(document).ready()
		*  - append: on new DOM elements appended via repeater field or other AJAX calls
		*
		*  @param	n/a
		*  @return	n/a
		*/

		acf.add_action('ready_field/type=link-extended', initialize_field);
		acf.add_action('append_field/type=link-extended', initialize_field);
	}

})(jQuery);
