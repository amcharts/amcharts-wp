<?php
/**
 * @package amcharts
 * @version 1.4.5
 */
/*
Plugin Name: amCharts: Charts and Maps
Description: Use this plugin to easily add interactive charts and maps using amChart's JavaScript Charts and JavaScript Maps products
Author: amCharts
Requires at least: 3.5
Tested up to: 6.6.2
Version: 1.4.5
Stable tag: 1.4.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: amcharts
Domain Path: /langs

Copyright 2013-2024 Martynas Majeris (email : martynas@amcharts.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// defaults
define( 'AMCHARTS_VERSION', '1.4.5' );
define( 'AMCHARTS_BASE', __FILE__ );
define( 'AMCHARTS_DIR', plugin_dir_path( __FILE__ ) );
define( 'AMCHARTS_BASE_URL', plugins_url( '', __FILE__ ) );
define( 'AMCHARTS_NONCE', plugin_basename( __FILE__ ) );

// universal includes
require 'includes/utils.php';
require 'includes/setup.php';

// admin-only includes
if ( is_admin() ){
  require 'includes/settings.php';
  require 'includes/editing.php';
}