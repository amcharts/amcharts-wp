=== amCharts: Charts and Maps ===
Contributors: martynasma
Tags: charts, maps, amcharts, ammap, javascript charts, javascript maps
Requires at least: 3.0
Tested up to: 4.3.1
Stable tag: 1.0.12
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows to easily add interactive charts and maps using amCharts libraries.

== Description ==

Ever tried getting JavaScript content into your Wordpress posts or pages? Yeah, that's impossible. WP strips down all
the JavaScript.

This makes adding JavaScript Charts and Maps a hassle.

Developed and maintained by amCharts staff, this plugin solves the problem by allowing you to create chart code snippets, that then subsequently can be inserted into the
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

= Do I need to include any JavaScript libraries in my theme code? =

Nope. The plugin will take care of that for you. The required JavaScript libraries will be included in the footer of the page automatically.

Plugin will include only those libraries that are actually required to render the chart. If no charts are there on the page, no additional libraries will be included.

Let's keep the footprint small ;)

= Can I insert a chart directly from my PHP code rather than shortcode? =

Yes. Use the following PHP code:

`<?php amcharts_insert( $chart_id ); ?>`

Or, you can retrieve the chart object using following function:

`<?php amcharts_get( $chart_id ); ?>`

This will return an object with the following properties:

*   title - a chart title
*   post - a reference to original WP post object
*   resources - a list of resource urls
*   html - HTML code
*   javascript - JavaScript code

= Can I pass custom parameters to chart code via shortcode? =

Yes, starting from version 1.0.7.

Any parameter prefixed with "data-" will be passed into chart code via global AmCharts.wpChartData object.

I.e.:

`[amcharts id="pie-1" data-file="data1.csv" data-sort="asc"]`

The above shortcode will insert the following code **before** actual chart code:

`AmCharts.wpChartData = {
  "file": "data1.csv",
  "sort": "asc"
}`

You can then reference those passed variables in your chart code. I.e.:

`alert( AmCharts.wpChartData.file );`

== Screenshots ==

1. Plugin configuration. Either use amCharts-hosted free libraries or your own. Set default code per chart/map type.
2. Easily create new charts or maps using presets right from the Admin menu.
3. Modify resources, HTML or JavaScript portion of the chart. Or apply defaults right from the edit screen.
4. Insert chart easily into body of the post or page using button from the rich editor tool belt. The charts are inserted as Wordpress shortcodes.
5. Select from the available charts or maps. No coding required.
6. Embed a chart created in Live Editor directly into your posts or pages.
7. The chart shortcodes are replaced with the actual charts when page renders.

== Changelog ==

= 1.0.12 =
* Added support for WP installs with non-standard directories

= 1.0.11 =
* Fixed a critical error after upgrade

= 1.0.10 =
* Resources tab can now include .css files

= 1.0.9 =
* Added GANTT chart type support

= 1.0.8 =
* Resource list now contains amCharts plugins

= 1.0.7 =
* Added an option to wrap all chart/map code into exception try/catch block
* Added ability to pass in custom parameters via shortcode

= 1.0.6 =
* Fixed a warning that was being displayed if Wordpress debug mode was enabled

= 1.0.5 =
* Fixed a bug that was causing resource list to break on some PHP setups

= 1.0.4 =
* Added an option to use self-hosted (commercial) amCharts libraries with embedded Live Editor charts

= 1.0.3 =
* Chart insert window now shows recent chart first with ability to live-search all available charts
* Chart shortcodes will now be inserted using user-friendly slug instead of id
* Added ability to insert and embed charts created with Live Editor

= 1.0.2 =
* Added a way to preview the chart while editing it
* Added ability to enter a user-friendly slug/ID for the chart to be used in shortcodes
* Fixed potential conflicts of the same libraries included from different locations on the same page

= 1.0.1 =
* Shortcode column was showing for all post types

= 1.0 =
* Initial release
