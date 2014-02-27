<?php
/**
 * Check if this is a new post being created
 */

function amcharts_is_new_post () {
	global $pagenow;
	return 'post-new.php' == $pagenow;
}

/**
 * Returns the list of available chart types
 */

function amcharts_get_chart_types () {
	return array(
		'serial' 	=> __( 'Serial', 'amcharts' ),
		'pie' 		=> __( 'Pie', 'amcharts' ),
		'xy' 			=> __( 'XY', 'amcharts' ),
		'funnel' 	=> __( 'Funnel', 'amcharts' ),
		'radar' 	=> __( 'Radar', 'amcharts' ),
		'gauge' 	=> __( 'Gauge', 'amcharts' ),
		'stock' 	=> __( 'Stock', 'amcharts' ),
		'map' 		=> __( 'Map', 'amcharts' )
	);
}

/**
 * Returns the list of the charts and their default depenecies
 */

function amcharts_get_chart_type_libs () {
	return array(
		'serial' 	=> array( 'amcharts.js', 'serial.js' ),
		'pie' 		=> array( 'amcharts.js', 'pie.js' ),
		'xy' 			=> array( 'amcharts.js', 'xy.js' ),
		'funnel' 	=> array( 'amcharts.js', 'funnel.js' ),
		'radar' 	=> array( 'amcharts.js', 'radar.js' ),
		'gauge' 	=> array( 'amcharts.js', 'gauge.js' ),
		'stock' 	=> array( 'amcharts.js', 'serial.js', 'amstock.js' ),
		'map' 		=> array( 'ammap.js', 'worldLow.js' )
	);
}