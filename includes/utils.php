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

/**
 * Strips slashes from submitted data
 */

function amcharts_stripslashes ( $str ) {
	// if ( get_magic_quotes_gpc() )
	// WP seems to add slashes regardless of the above setting
	$str = stripslashes( $str );
	return $str;
}

/**
 * Returns unique chart slug
 */

function amcharts_generate_slug ( $type = 'chart' ) {
	if ( ! $type )
		$type = 'chart';
	
	$continue = true;
	$i = 0;
	do {
		$i++;
		$slug = $type . '-' . $i;
		if ( ! get_posts( array(
			'post_type' 			=> 'amchart',
			'fields'					=> 'ids',
			'posts_per_page'	=> 1,
			'meta_query' 			=> array(
				array(
					'key' 	=> '_amcharts_slug',
					'value' => $slug,
				)
			)
		) ) )
			$continue = false;
	} while ( $contine );
	
	return $slug;
}