=== amCharts: Charts and Maps ===
Contributors: martynasma
Tags: charts, maps, amcharts, ammap, javascript charts, javascript maps
Requires at least: 3.0
Tested up to: 3.8.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows to easily add interactive charts and maps using amCharts libraries.

== Description ==

Ever tried getting JavaScript content into your Wordpress posts or pages? Yeah, that's impossible. WP strips down all
the JavaScript.

This makes adding JavaScript Charts and Maps a hassle.

This plugin solves the problem by allowing you to create chart code snippets, that then subsequently can be inserted into the
posts or pages as a shortcode. (helpful button in TinyMCE is also there)

You can also use a built-in PHP function to invoke the chart anywhere in your template:

`<?php amcharts_insert( $chart_id ); ?>`

The plugin has also many helpful functions:

*   Easy switching between CDN-hosted or local chart/map libraries storage
*   Setting defaults per chart type
*   Automatically locating installed libraries and available resources

== Installation ==

Use Wordpress Plugin page to search and install the amCharts plugin.

If you choose to install in manually, make sure all the files from the downloaded archive are placed into your `/wp-content/plugins/amcharts/` directory.

== Frequently Asked Questions ==

= Does this plugin work with the free/commercial versions of amCharts libraries =

Yes. You can either set the plugin to use publically available libraries loaded from www.amcharts.com or from your local server.

= Will I be able to visually edit settings and data? =

No. This plugin allows editing a code directly in Wordpress admin as well as inserting the charts into posts or pages using shortcodes.

It's not a fully fledged chart/map editor. It's a plugin designed to get your chart/map code into Wordpress easily.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets 
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png` 
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 1.0 =
* Initial release
