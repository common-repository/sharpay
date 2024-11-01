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

/*
Plugin Name: Sharpay
Plugin URI: https://github.com/sharpay-io/wordpress
Description: Sharpay is multisharing button with blockchain profit. It allows you to reward site's visitors for sharing content to their audience via Facebook, Twitter, Medium, Reddit and many other social networks.
Version: 1.9.2
Author: Sharpay Inc
Author URI: https://sharpay.io
Licence: GPLv3
Text Domain: sharpay-plugin
Domain Path: /languages/
*/

define('SHARPAY_PLUGIN_VERSION', '1.9.2');

register_activation_hook(__FILE__, 'sharpay_activate');
function sharpay_activate() {

	global $wp_version;
	$required_version = '3.5';

	if ( version_compare($wp_version, $required_version, '<') ) {
		$error = sprintf(__('This plugin requires WordPress version %s or higher', 'sharpay-plugin'), $required_version);
		wp_die($error);
	}
}

register_uninstall_hook(__FILE__, 'sharpay_uninstall');
function sharpay_uninstall() {
	delete_option('sharpay_options');
}

// Initialization --------------------------------------------------------------

add_action('init', 'sharpay_init');
function sharpay_init() {
	$basename = plugin_basename( dirname(__FILE__) . '/languages' );
	load_plugin_textdomain('sharpay-plugin', false, $basename);
}

// Settings --------------------------------------------------------------------

if ( is_admin() ) {
	require_once( dirname(__FILE__) . '/includes/sharpay-admin.php');
}

// Button rendering ------------------------------------------------------------

if ( ! is_admin() ) {
	require_once( dirname(__FILE__) . '/includes/sharpay-public.php');
}

// Widget ----------------------------------------------------------------------

require_once( dirname(__FILE__) . '/includes/class-sharpay-widget.php');
add_action('widgets_init', 'sharpay_widget_init');
function sharpay_widget_init() {
	register_widget('Sharpay_Widget');
}

// Add settings link on plugin page --------------------------------------------

function sharpay_acl_links($links) {
    $acl_settings_link = '<a href="options-general.php?page=sharpay-settings">'. __('Settings', 'sharpay-plugin') .'</a>';
    array_unshift($links, $acl_settings_link);
    return $links;
}
$acl_plugin_name = plugin_basename(__FILE__);
add_filter("plugin_action_links_$acl_plugin_name", 'sharpay_acl_links' );
