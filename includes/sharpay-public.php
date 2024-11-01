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

add_filter('the_content', 'sharpay_content_filter');
function sharpay_content_filter( $content ) {

	if ( ! is_singular() ) {
		return $content;
	}

	$options = get_option('sharpay_options');

	if ( $options !== false && $options['before_content'] ) {

	    if( empty( $options['static_code'] ) ) {
            $content = sharpay_generate_tag('static', $options['site_id'], $options['before_content_options']) . $content;
        }
	    else {
            $content = sharpay_generate_new_tag($options['site_id'], $options['static_code']) . $content;
        }
	}

	if ( $options !== false && $options['after_content'] ) {

        if( empty( $options['static_code'] ) ) {
            $content .= sharpay_generate_tag('static', $options['site_id'], $options['after_content_options']);
        } else {
            $content .= sharpay_generate_new_tag($options['site_id'], $options['static_code']);
        }
	}

	return $content;
}

add_action('wp_footer', 'sharpay_footer');
function sharpay_footer() {

	$options = get_option('sharpay_options');

	if ( $options !== false && $options['floating'] ) {
		echo sharpay_generate_tag('floating', $options['site_id'], $options['floating_options']);
	}
}

add_shortcode('sharpay', 'sharpay_shortcode');
function sharpay_shortcode() {

	$options = get_option('sharpay_options');

	if ( $options !== false && $options['shortcode'] ) {

        if( empty( $options['static_code'] ) ) {
            return sharpay_generate_tag('static', $options['site_id'], $options['shortcode_options']);
        } else {
            return sharpay_generate_new_tag($options['site_id'], $options['static_code']);
        }
	}

	return '';
}


function sharpay_generate_new_tag( $site_id, $options ) {
    $src = '<div class="sharpay_widget_simple" ';
    $src .= ' data-sharpay="'. htmlspecialchars( $site_id ) .'"';

    if( is_string( $options ) ) {
        $options = json_decode($options, true);
    }

    foreach ( $options as $key => $val ) {
        if( preg_match( '/^[a-z0-9]+$/ui', $key ) ) {
            $src .= ' data-' . htmlspecialchars($key) . '="' . htmlspecialchars($val) . '"';
            if ($key === 'align' && $val === 'right') {
                $src .= ' style="text-align="right"';
            }
        }
    }
    $src .= '></div>';
    return $src;
}

function sharpay_generate_tag( $mode, $site_id, $options ) {

	$is_floating = ( $mode === 'floating' );
	$is_static = ( $mode === 'static' );
	$is_custom = ( $mode === 'static' && $options['use_custom_markup'] );

	$parts = explode('_', get_locale());
	$lang = $parts[0];

	$src = $is_custom ? '<a ' : '<div ';
	$marker = $is_floating ? 'sharpay_widget_floating' : ( $is_custom ? 'sharpay_widget_custom' : 'sharpay_widget_button');
	$src .= $is_floating ? "id=\"{$marker}\" " : "class=\"{$marker}\" ";
	$src .= "data-sharpay=\"$site_id\" ";

	if ( $is_floating && $options['position'] === 'left' ) {
		$src .= 'data-left="true" ';
	}
	if ( $is_floating && $options['style'] === 'dark' ) {
		$src .= 'data-dark="true" ';
	}

	if ( $is_static && $options['height'] != 32 ) {
		$src .= "data-height=\"{$options['height']}\" ";
	}

	if ( ! $is_custom && $options['share_counter'] ) {
		$src .= 'data-sharecount="true" ';
	}
	if ( ! $is_custom && $options['share_counter'] && $options['share_counter_mode'] === 'site') {
		$src .= 'data-sharecount-mode="site" ';
	}

	if ( ! empty($options['image']) ) {
		$src .= "data-image=\"{$options['image']}\" ";
	}
	
	if ($is_static && isset($options['use_custom_colors']) && $options['use_custom_colors']) {
		$src .= "data-color-font=\"{$options['color_font']}\" data-color-bg=\"{$options['color_bg']}\"";
	}

	if ( $lang != 'en' ) {
		$src .= "data-lang=\"{$lang}\" ";
	}

	$src .= '>';

	if ( $is_custom ) {
		$src .= $options['custom_markup'];
	}

	$src .= $is_custom ? '</a>' : '</div>';

	return $src;
}

add_action('wp_enqueue_scripts', 'sharpay_scripts');
function sharpay_scripts() {

	$options = get_option('sharpay_options');

	if ( $options === false || empty($options['site_id']) ) {
		return;
	}

	wp_register_script('sharpay-js', 'https://app.sharpay.io/api/script.js', array(), null, true);
	wp_enqueue_script('sharpay-js');
}
