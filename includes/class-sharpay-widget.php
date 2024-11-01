<?php
/*
Copyright (c) 2018 Sharpay Inc.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class Sharpay_Widget extends WP_Widget {

	private $no_site_ID_msg;

	function __construct() {

		$widget_options = array(
			'classname' => 'sharpay-widget',
			'description' => 'Widget which displays Sharpay multishare button'
		);

		$this->no_site_ID_msg = __('To use Sharpay multishare button widget you need to set up site ID in plugin\'s settings first.', 'sharpay-plugin');

		parent::__construct('Sharpay_Widget', 'Sharpay Multishare Button', $widget_options);
	}

	function form( $instance ) {

		$default = array(
			'height' => 32,
			'share_counter' => false,
			'share_counter_mode' => 'page',
			'use_custom_markup' => false,
			'custom_markup' => 'Sharpay multishare button',
			'image' => '',
			'modal' => true
		);
		$instance = wp_parse_args( (array) $instance, $default );
		$options = get_option('sharpay_options');
		$id = $this->id;
		if ( $options === false || empty($options['site_id']) ) {
			?>
				<div class="sharpay-widget-settings">
					<div class="sharpay-error">
						<?php echo $this->no_site_ID_msg ?>
					</div>
				</div>
			<?php
		} else {

		}
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$height = (int) $new_instance['height'];
		if ( in_array($height, array(32,24,16)) ) {
			$instance['height'] = $height;
		}
		else {
			throw new Exception("Wrong value of Sharpay widget setting. Value of height = $height, but only 32,24,16 are acceptable.");
		}

		$instance['share_counter'] = (bool) $new_instance['share_counter'];

		$share_counter_mode = sanitize_text_field($new_instance['share_counter_mode']);
		if ( in_array($share_counter_mode, array('page', 'site')) ) {
			$instance['share_counter_mode'] = $share_counter_mode;
		}
		else {
			throw new Exception("Wrong value of Sharpay widget setting. Value of share_counter_mode = $share_counter_mode, but only 'page' and 'site' are acceptable.");
		}

		$instance['use_custom_markup'] = (bool) $new_instance['use_custom_markup'];
		$instance['custom_markup'] = $new_instance['custom_markup'];

		$instance['image'] = sanitize_text_field($new_instance['image']);
		
		$instance['modal'] = (bool) $new_instance['modal'];

		return $instance;
	}

	function widget( $args, $instance ) {
		extract($args);
		echo $before_widget;

		$options = get_option('sharpay_options');
		if ( $options === false || empty($options['site_id']) ) {
			echo '<span style="color: red; background: yellow">' . $this->no_site_ID_msg . '</span>';
		}
		else {
            if( empty( $options['static_code'] ) ) {
                echo sharpay_generate_tag('static', $options['site_id'], $instance );
            } else {
                echo sharpay_generate_new_tag($options['site_id'], $options['static_code']);
            }
		}

		echo $after_widget;
	}

}
