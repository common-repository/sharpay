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

add_action('admin_menu', 'sharpay_create_admin_menu');
function sharpay_create_admin_menu() {

	add_options_page(
		__('Sharpay Plugin Settings', 'sharpay-plugin'),
		__('Sharpay', 'sharpay-plugin'),
		'manage_options', 'sharpay-settings', 'sharpay_settings_page');

	add_action('admin_init', 'sharpay_register_settings');
}

function sharpay_register_settings() {
	register_setting('sharpay-settings-group', 'sharpay_options', 'sharpay_sanitize_options');
}

function sharpay_default_options() {

	$floating_default_options = array(
		'position' => 'right',
		'style' => 'dark',
		'share_counter' => false,
		'share_counter_mode' => 'page',
		'image' => '',
		'modal' => true,
	);

	return array(
		'site_id' => '',

		'floating' => false,
		'floating_options' => $floating_default_options,
		'before_content' => false,
		'after_content' => true,
		'shortcode' => false,
        'static_code' => '{}',
        'static_model' => '{}',
	);
}

function sharpay_settings_page() {

	$sharpay_options = get_option('sharpay_options');

	if ( empty($sharpay_options) ) {
		$sharpay_options = sharpay_default_options();
	}

	$static_buttons = array('before_content_options', 'after_content_options', 'shortcode_options');
	foreach($static_buttons as $sb) {
		if (! isset($sharpay_options[$sb]['use_custom_colors'])) {
			$sharpay_options[$sb]['use_custom_colors'] = false;
		}
		if (! isset($sharpay_options[$sb]['color_font'])) {
			$sharpay_options[$sb]['color_font'] = 'ffffff';
		}
		if (! isset($sharpay_options[$sb]['color_bg'])) {
			$sharpay_options[$sb]['color_bg'] = 'ff9933';
		}
	}

	?>
	<div id="sharpay-settings" class="wrap">
		<h2><?php echo __('Sharpay Settings', 'sharpay-plugin'); ?></h2>
		<form method="post" action="options.php">
			<?php settings_fields('sharpay-settings-group'); ?>

			<table class="form-table">
				<tr>
					<th scope="row"><?php echo __('Sharpay project ID', 'sharpay-plugin'); ?></th>
					<td>
						<input type="text" id="sharpay-site-code" name="sharpay_options[site_id]"
							value="<?php echo esc_attr($sharpay_options['site_id']); ?>" <?php if( empty( $sharpay_options['site_id'] ) ) { echo 'class="sharpay-code-error"'; } ?>/>
						<a id="sharpay-select-site" href="#">
							<?php echo __('Get your project ID or register new project for your site in Sharpay', 'sharpay-plugin'); ?>
						</a>
						<script>

						</script>
						<p class="description">
							<?php echo __('To make Sharpay multisharing button work you need to register your site as a project in <a href="https://app.sharpay.io" target="_blank">app.sharpay.io</a> and provide project\'s ID', 'sharpay-plugin') ?>
						</p>

                        <?php submit_button(); ?>
					</td>
				</tr>

				<tr<?php print empty($sharpay_options['site_id']) ?' style="display:none"':'';?>>
					<th scope="row">
						<?php echo __('Static button', 'sharpay-plugin'); ?>
					</th>
					<td>
						<h3>
							<input type="checkbox" name="sharpay_options[before_content]" id="before_content"
								<?php echo $sharpay_options['before_content'] ? 'checked' : '' ?> >
							<label for="before_content"><?php echo __('Share button at the TOP of every post/page', 'sharpay-plugin'); ?></label>
						</h3>
						<h3>
							<input type="checkbox" name="sharpay_options[after_content]" id="after_content"
								<?php echo $sharpay_options['after_content'] ? 'checked' : '' ?> >
							<label for="after_content"><?php echo __('Share button at the BOTTOM of every post/page', 'sharpay-plugin'); ?></label>
						</h3>
						<h3>
							<input type="checkbox" name="sharpay_options[shortcode]" id="shortcode"
								<?php echo $sharpay_options['shortcode'] ? 'checked' : '' ?> >
							<label for="shortcode"><?php echo __('Share button anywhere INSIDE CONTENT using [sharpay] shortcode', 'sharpay-plugin'); ?></label>
						</h3>

                        <input type="hidden" id="sharpay-static-code" name="sharpay_options[static_code]" value="<?php echo esc_attr($sharpay_options['static_code']); ?>">
                        <input type="hidden" id="sharpay-static-model" name="sharpay_options[static_model]" value="<?php echo esc_attr($sharpay_options['static_model']); ?>">

                        <?php submit_button(); ?>

                        <iframe id="sharpay-construct-iframe" class="sharpay-construct" src="https://app.sharpay.io/wpcode?lang=<?php echo substr( get_locale(), 0, 2 );?>&code=<?php echo urlencode($sharpay_options['site_id']); ?>&model=<?php echo urlencode( $sharpay_options['static_model'] ); ?>"></iframe>

                        <?php submit_button(); ?>
					</td>
				</tr>
                <tr<?php print empty($sharpay_options['site_id']) ?' style="display:none"':'';?>>
                    <th scope="row">
                        <?php echo __('Floating button', 'sharpay-plugin'); ?>
                    </th>
                    <td>
                        <h3>
                            <input type="checkbox" name="sharpay_options[floating]" id="floating"
                                <?php echo $sharpay_options['floating'] ? 'checked' : '' ?> >
                            <label for="floating"><?php echo __('Floating button at the BOTTOM of the page', 'sharpay-plugin'); ?></label>
                        </h3>
                        <div class="settings" data-for="floating"  style="<?php echo $sharpay_options['floating'] ? '' : 'display:none;' ?>">
                            <?php sharpay_floating_button_settings($sharpay_options); ?>
                        </div>

                        <?php submit_button(); ?>
                    </td>
                </tr>

			</table>

            <div class="notice notice-info">
                <p><strong><?php echo __('If you want to use the advanced settings of Sharpay widgets, use the html embed code from your Sharpay <a href="https://app.sharpay.io/webmaster">webmaster area</a>.', 'sharpay-plugin')?></strong></p>
            </div>

		</form>
	</div>
	<?php
}

function sharpay_floating_button_settings( $sharpay_options ) {

	?>
	<div class="group">
		<input type="radio" name="sharpay_options[floating_options][position]" value="left" id="left"
			<?php echo $sharpay_options['floating_options']['position'] === 'left' ? 'checked' : '' ?> >
		<label for="left"><?php echo __('Left', 'sharpay-plugin'); ?></label>
		<input type="radio" name="sharpay_options[floating_options][position]" value="right" id="right"
			<?php echo $sharpay_options['floating_options']['position'] === 'right' ? 'checked' : '' ?> >
		<label for="right"><?php echo __('Right', 'sharpay-plugin'); ?></label>
		<p class="description"><?php echo __('Select floating button position at the bottom of the page.', 'sharpay-plugin') ?></p>
	</div>

	<div class="group">
		<input type="radio" name="sharpay_options[floating_options][style]" value="light" id="light"
			<?php echo $sharpay_options['floating_options']['style'] === 'light' ? 'checked' : '' ?> >
		<label for="light"><?php echo __('Light', 'sharpay-plugin'); ?></label>
		<input type="radio" name="sharpay_options[floating_options][style]" value="dark" id="light"
			<?php echo $sharpay_options['floating_options']['style'] === 'dark' ? 'checked' : '' ?> >
		<label for="light"><?php echo __('Dark', 'sharpay-plugin'); ?></label>
		<p class="description"><?php echo __('Select floating button style.', 'sharpay-plugin') ?></p>
	</div>

	<div class="group">
		<input type="checkbox" name="sharpay_options[floating_options][share_counter]" id="share-counter" class="share-counter"
			<?php echo $sharpay_options['floating_options']['share_counter'] === true ? 'checked' : '' ?> >
		<label for="share-counter"><?php echo __('Share counter', 'sharpay-plugin'); ?></label>

		<span class="share-counter-mode" style="<?php echo $sharpay_options['floating_options']['share_counter'] === true ? '' : 'display:none;' ?>">
			<input type="radio" name="sharpay_options[floating_options][share_counter_mode]" value="page" id="page"
				<?php echo $sharpay_options['floating_options']['share_counter_mode'] === 'page' ? 'checked' : '' ?> >
			<label for="page"><?php echo __('Separate for every page', 'sharpay-plugin'); ?></label>
			<input type="radio" name="sharpay_options[floating_options][share_counter_mode]" value="site" id="site"
				<?php echo $sharpay_options['floating_options']['share_counter_mode'] === 'site' ? 'checked' : '' ?> >
			<label for="site"><?php echo __('Common for all site', 'sharpay-plugin'); ?></label>
		</span>
		<p class="description"><?php echo __('Check if you want our button to show how many times your page was shared via Sharpay.', 'sharpay-plugin'); ?></p>
	</div>
	
	<div class="group">
		<input type="text" id="image" name="sharpay_options[floating_options][image]"
			value="<?php echo $sharpay_options['floating_options']['image'] ?>"
			placeholder="<?php echo __('.img | #img | http://...', 'sharpay-plugin')?>">
		<p class="description"><?php echo __('Enter class, id or URL of an image to make it selected by default in sharing window.', 'sharpay-plugin'); ?></p>
	</div>

	<?php
}

function sharpay_static_button_settings( $sharpay_options, $prop ) {
	$options = "sharpay_options[$prop]"
	?>
	<div class="group"  style="<?php echo $sharpay_options[$prop]['use_custom_markup'] === true ? 'display:none;' : '' ?>">
		<input type="radio" name="<?php echo $options; ?>[height]" value="32" id="<?php echo "{$prop}_h32" ?>"
			<?php echo $sharpay_options[$prop]['height'] === 32 ? 'checked' : '' ?> >
		<label for="<?php echo "{$prop}_h32" ?>"><?php echo __('32px', 'sharpay-plugin'); ?></label>
		<input type="radio" name="<?php echo $options; ?>[height]" value="24" id="<?php echo "{$prop}_h24" ?>"
			<?php echo $sharpay_options[$prop]['height'] === 24 ? 'checked' : '' ?> >
		<label for="<?php echo "{$prop}_h24" ?>"><?php echo __('24px', 'sharpay-plugin'); ?></label>
		<input type="radio" name="<?php echo $options; ?>[height]" value="16" id="<?php echo "{$prop}_h16" ?>"
			<?php echo $sharpay_options[$prop]['height'] === 16 ? 'checked' : '' ?> >
		<label for="<?php echo "{$prop}_h16" ?>"><?php echo __('16px', 'sharpay-plugin'); ?></label>
		<p class="description"><?php echo __('Set button height to make button smaller. Button width will change accordingly.', 'sharpay-plugin') ?></p>
	</div>

	<div class="group" style="<?php echo $sharpay_options[$prop]['use_custom_markup'] === true ? 'display:none;' : '' ?>">
		<input type="checkbox" name="<?php echo $options; ?>[share_counter]" id="<?php echo "{$prop}-share-counter" ?>" class="share-counter"
			<?php echo $sharpay_options[$prop]['share_counter'] === true ? 'checked' : '' ?> >
		<label for="<?php echo "{$prop}-share-counter" ?>"><?php echo __('Share counter', 'sharpay-plugin'); ?></label>

		<span class="share-counter-mode" style="<?php echo $sharpay_options[$prop]['share_counter'] === true ? '' : 'display:none;' ?>">
			<input type="radio" name="<?php echo $options; ?>[share_counter_mode]" value="page" id="<?php echo "{$prop}_page" ?>"
				<?php echo $sharpay_options[$prop]['share_counter_mode'] === 'page' ? 'checked' : '' ?> >
			<label for="<?php echo "{$prop}_page" ?>"><?php echo __('Separate for every page', 'sharpay-plugin'); ?></label>
			<input type="radio" name="<?php echo $options; ?>[share_counter_mode]" value="site" id="<?php echo "{$prop}_site" ?>"
				<?php echo $sharpay_options[$prop]['share_counter_mode'] === 'site' ? 'checked' : '' ?> >
			<label for="<?php echo "{$prop}_site" ?>"><?php echo __('Common for all site', 'sharpay-plugin'); ?></label>
		</span>
		<p class="description"><?php echo __('Check if you want our button to show how many times your page was shared via Sharpay.', 'sharpay-plugin'); ?></p>
	</div>

	<div class="group">
		<input type="checkbox" name="<?php echo $options; ?>[use_custom_colors]"
		       id="<?php echo "{$prop}-use-custom-colors" ?>" class="use-custom-colors"
			<?php echo $sharpay_options[$prop]['use_custom_colors'] === true ? 'checked' : '' ?> >
		<label for="<?php echo "{$prop}-use-custom-colors" ?>">
			<?php echo __('Would you like to use custom colors for Sharpay button?', 'sharpay-plugin'); ?></label>

		<div class="colors" style="<?php echo $sharpay_options[$prop]['use_custom_colors'] === true ? '' : 'display:none;' ?>">
			<label for="<?php echo "{$prop}-font-color" ?>">
				<?php echo __('Font color', 'sharpay-plugin'); ?>
			</label>
			<span>#</span>
			<input type="text" value="<?php echo $sharpay_options[$prop]['color_font'] ?>"
			       name="<?php echo $options; ?>[color_font]"
			       id="<?php echo "{$prop}-font-color" ?>">

			<label for="<?php echo "{$prop}-bg-color" ?>">
				<?php echo __('Background color', 'sharpay-plugin'); ?>
			</label>
			<span>#</span>
			<input type="text" value="<?php echo $sharpay_options[$prop]['color_bg'] ?>"
			       name="<?php echo $options; ?>[color_bg]"
			       id="<?php echo "{$prop}-bg-color" ?>">
		</div>

	</div>

	<div class="group">
		<input type="checkbox" name="<?php echo $options; ?>[use_custom_markup]" id="<?php echo "{$prop}-use-custom-markup" ?>" class="use-custom-markup"
			<?php echo $sharpay_options[$prop]['use_custom_markup'] === true ? 'checked' : '' ?> >
		<label for="<?php echo "{$prop}-use-custom-markup" ?>"><?php echo __('Would you like to use your own HTML markup for Sharpay button?', 'sharpay-plugin'); ?></label>
	</div>

	<div class="group" style="<?php echo $sharpay_options[$prop]['use_custom_markup'] === true ? '' : 'display:none;' ?>">
		<textarea name="<?php echo $options; ?>[custom_markup]" rows="5">
			<?php echo $sharpay_options[$prop]['custom_markup']; ?></textarea>
		<p class="description"><?php echo __('Enter HTML markup which represents your custom multishare button.', 'sharpay-plugin'); ?></p>
	</div>
	
	<div class="group always-visible">
		<input type="text" id="image" name="<?php echo $options; ?>[image]"
			value="<?php echo $sharpay_options[$prop]['image'] ?>"
			placeholder="<?php echo __('.img | #img | http://...', 'sharpay-plugin')?>">
		<p class="description"><?php echo __('Enter class, id or URL of an image to make it selected by default in sharing window.', 'sharpay-plugin'); ?></p>
	</div>
	<?php
}

function sharpay_sanitize_options( $input ) {

	$input['site_id'] = sanitize_text_field($input['site_id']);

	$input['floating'] = sharpay_sanitize_checkbox($input, 'floating');
	$input['floating_options'] = sharpay_sanitize_floating_options($input['floating_options']);

	$input['before_content'] = sharpay_sanitize_checkbox($input, 'before_content');
	//$input['before_content_options'] = sharpay_sanitize_static_options($input['before_content_options'], 'before');

	$input['after_content'] = sharpay_sanitize_checkbox($input, 'after_content');
	//$input['after_content_options'] = sharpay_sanitize_static_options($input['after_content_options'], 'after');

	$input['shortcode'] = sharpay_sanitize_checkbox($input, 'shortcode');
	//$input['shortcode_options'] = sharpay_sanitize_static_options($input['shortcode_options'], 'shortcode');

	return $input;
}

function sharpay_sanitize_floating_options( $input ) {

	$input = sharpay_sanitize_common_options( $input );

	$position = sanitize_text_field($input['position']);
	if ( ! in_array($position, array('left', 'right')) ) {
		wp_die('Wrong position value!');
	}
	$input['position'] = $position;

	$style = sanitize_text_field($input['style']);
	if ( ! in_array($style, array('light', 'dark')) ) {
		wp_die('Wrong style value!');
	}
	$input['style'] = $style;

	return $input;
}

function sharpay_sanitize_static_options( $input, $tag )
{
	$input = sharpay_sanitize_common_options( $input );

	$height = (int) sanitize_text_field($input['height']);
	if ( ! in_array($height, array(32, 24, 16)) ) {
		wp_die('Wrong height value!');
	}
	$input['height'] = $height;

	if (isset($input['use_custom_colors'])) {
		$val = $input['use_custom_colors'];
		$input['use_custom_colors'] = ($val === 'on' || $val === true) ? true : false;
	}
	else {
		$input['use_custom_colors'] = false;
	}

	$input['color_font'] = sanitize_text_field($input['color_font']);
	$input['color_bg'] = sanitize_text_field($input['color_bg']);

	if (isset($input['use_custom_markup'])) {
		$val = $input['use_custom_markup'];
		$input['use_custom_markup'] = ($val === 'on' || $val === true) ? true : false;
	}
	else {
		$input['use_custom_markup'] = false;
	}

	return $input;
}

function sharpay_sanitize_common_options( $input ) {

	$input['share_counter'] = sharpay_sanitize_checkbox($input, 'share_counter');

	$share_counter_mode = sanitize_text_field($input['share_counter_mode']);
	if ( ! in_array($share_counter_mode, array('page', 'site')) ) {
		wp_die("Wrong share_counter_mode value - $share_counter_mode!");
	}
	$input['share_counter_mode'] = $share_counter_mode;

	$input['image'] = sanitize_text_field($input['image']);
	
	$input['modal'] = sharpay_sanitize_checkbox($input, 'modal');

	return $input;
}

function sharpay_sanitize_checkbox( $input, $field ) {

	if ( ! isset($input[$field]) ) {
		return false;
	}
	// We need this because sanitize function is called twice on the first save of options in DB,
	// so it must be able to handle boolean field values as well.
	elseif ( is_bool($input[$field]) ) {
		return $input[$field];
	}
	elseif ( sanitize_text_field($input[$field]) === 'on' ) {
		return true;
	}
	else {
		wp_die("Wrong value {$input[$field]} for $field field!");
	}
}

add_action('admin_enqueue_scripts', 'sharpay_admin_scripts');
function sharpay_admin_scripts() {
	$dir = dirname(__FILE__);

	wp_register_style('sharpay-admin-styles', plugins_url('assets/css/sharpay-admin.css', dirname(__FILE__)), array(), SHARPAY_PLUGIN_VERSION);
	wp_enqueue_style( 'sharpay-admin-styles' );

	wp_register_script('sharpay-admin-js', plugins_url('assets/js/sharpay-admin.js', dirname(__FILE__)), array('jquery'), SHARPAY_PLUGIN_VERSION);
	wp_enqueue_script('sharpay-admin-js');
}
