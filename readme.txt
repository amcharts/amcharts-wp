=== amCharts: Charts and Maps ===
Contributors: martynasma
Tags: charts, maps, amcharts, ammap, javascript charts, javascript maps
Requires at least: 3.5
Tested up to: 6.6.2
Version: 1.4.5
Stable tag: 1.4.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows to easily add interactive charts and maps using amCharts libraries.

== Description ==

= Important disclaimer =

This plugin loads JavaScript libraries from external amCharts CDN which is a free **service** provided by amCharts.

amCharts provides their libraries completely free without any caps to functionality on a sole condition, that small contribution is automatically displayed on all chart produced by amCharts library. The contribution comes from amCharts libraries/service and is not added by this plugin.

Related links:
* [Free usage terms / license of the amCharts library](https://github.com/amcharts/amcharts4/blob/master/dist/script/LICENSE)
* [amCharts privacy policy](https://www.amcharts.com/privacy-policy/)

= What is it? =

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

= Does this plugin work with the free/commercial versions of amCharts libraries? =

Yes. You can either set the plugin to use publically available libraries loaded from www.amcharts.com or from your local server.

= Which version of amCharts library does the plugin use?

Plugin will use amCharts 5 when installed anew. You are able to switch between versions in plugin's settings.

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

= Are there any filters available?

Yes:

*   amcharts_shortcode_data ( $data - parsed data passed in via data-* parameters of the shortcode, $atts )
*   amcharts_shortcode_resources ( $resources - text data from resources box, $atts )
*   amcharts_shortcode_libs ( $libs - array of resouces, $atts )
*   amcharts_shortcode_javascript ( $javascript - JavaScript portion of the chart, $atts )
*   amcharts_shortcode_html ( $html - HTML portion of the chart, $atts )

= Is this plugin WPML-compatible?

Yup. It fully supports WPML. You can create language-specific versions of the charts and maps. The plugin will automatically select proper language when displaying it.

== Screenshots ==

1. Plugin configuration. Either use amCharts-hosted free libraries or your own. Set default code per chart/map type.
2. Easily create new charts or maps using presets right from the Admin menu.
3. Modify resources, HTML or JavaScript portion of the chart. Or apply defaults right from the edit screen.
4. Insert chart easily into body of the post or page using button from the rich editor tool belt. The charts are inserted as Wordpress shortcodes.
5. Select from the available charts or maps. No coding required.
6. Embed a chart created in Live Editor directly into your posts or pages.
7. The chart shortcodes are replaced with the actual charts when page renders.

== Changelog ==

= 1.4.5 =
* Fixed a cross-site scripting vulnerability with chart previews. (CVE-2024-8622)
* Tested up to WP 6.6.2.

= 1.4.4 =
* Fixed a PHP warning.
* Tested up to WP 6.6.1.

= 1.4.3 =
* New setting: "User capabilities". Allows setting which capabilities must a user have in order to be able to edit charts.
* Ciontributor role users are now not allowed to create charts by default. Changeable in Settings.
* Tested up to WP 6.5.2.

= 1.4.2 =
* Tested up to WP 6.5.

= 1.4.1 =
* Minor XSS vulnerability fixed.

= 1.4 =
* Tested up tp WP 6.1.
* Added amCharts 5 support.

= 1.3 =
* Chart popup will now open faster since it uses built-in "thickbox" and in-line code rather than `<iframe>`.
* Some PHP warnings has been fixed in debug mode.
* Plugin now uses CodeMirror library bundled with WP if available.

= 1.2.3 =
* CodeMirror dependency is no longer included in all Admin pages, just the ones that are related to amCharts.

= 1.2.2 =
* Fixed error of passing in data via "data-" shortcode parameters when V4 is enabled.

= 1.2.1 =
* Prevent Chart code to be executed multiple times in case some plugin was causing shortcodes to be processed twice.

= 1.2 =
* Added amCharts 4 support with switcher between two amCharts versions

= 1.1.6 =
* Upgraded CodeMirror to a latest version
* Verified WP 4.9.2 compatibility

= 1.1.5 =
* Fixed Gauge Chart example

= 1.1.4 =
* Made some changes so that plugin can be translated more easily
* Added Lithuanian translation

= 1.1.3 =
* Fixed warnings in WP debug mode

= 1.1.2 =
* Local reseource list refresh was omitting theme files

= 1.1.1 =
* Fixed minor PHP7 incompatibility

= 1.1 =
* Added WPML support

= 1.0.19 =
* Filters now are applied to various parts of the chart inserted via shortcode

= 1.0.18 =
* Fixed unnecessary loading of resource list from amcharts.com on every request
* Introduced option to use relative resource URLs (available in Settings)
* Remote resources now use "//" protocol prefix so that it inherits either http or https depending on what the website uses

= 1.0.17 =
* Decode values of "data-*"" attributes passed in from shorcode

= 1.0.16 =
* Fixed an error passing data that had "=" in it

= 1.0.15 =
* Fixed an error passing data to using "data-*" parameters on newer WPs

= 1.0.14 =
* Added oEmbed support for https-enabled Live Editor URLs

= 1.0.13 =
* Change "%CHART%" meta code to "$CHART$" so that code is valid JavaScript. (old syntax still works)
* Tweaks for better support of settings page markup according to WP 4.4 specs
* Cleaned up default chart templates (will affect only new installs)

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
