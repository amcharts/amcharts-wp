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
		'gantt' 	=> __( 'Gantt', 'amcharts' ),
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
		'gantt' 	=> array( 'amcharts.js', 'serial.js', 'gantt.js' ),
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
	} while ( $continue );
	
	return $slug;
}

/**
 * Splits up resources list into array
 */

function amcharts_split_libs ( $resources ) {
	return preg_split( '/\s+/', trim ( $resources ) );
}

/**
 * Returns available resource files
 */

function amcharts_get_available_resources ( $type = 'remote', $paths = '' ) {
  $res = '';
  if ( 'local' == $type ) {
    $dirs = amcharts_split_libs( $paths );
    
    // libraries
    $libs = array();
    foreach ( $dirs as $path ) {
      $libs = array_merge( $libs, amcharts_get_resource_files( ABSPATH . $path, home_url( $path ) ) );
    }
    
    // maps
    reset( $dirs );
    foreach ( $dirs as $path ) {
      $libs = array_merge( $libs, amcharts_get_resource_files( ABSPATH . $path . 'maps/js/', home_url( $path . 'maps/js/' ) ) );
    }

    // plugins
    reset( $dirs );
    foreach ( $dirs as $path ) {
      $libs = array_merge( $libs, amcharts_get_resource_files_deep( ABSPATH . $path . 'plugins/', home_url( $path . 'plugins/' ) ) );
    }
    
    $res = implode( "\n", $libs );
  }
  else {
    // load from amcharts.com (fall back to local hardocded list if url wrappers are disabled in PHP)
    if ( !$res = @file_get_contents( 'http://www.amcharts.com/lib/3/resources.php' ) )
      $res = file_get_contents( AMCHARTS_DIR . '/defaults/resources.txt' );
  }
  return $res;
}

/**
 * Returns resource list that are required
 */

function amcharts_get_resources ( $libs, $resources ) {
  $res = array();
  foreach ( $libs as $lib ) {
    $matches = array();
    $reg = "/.*" . str_replace( '.', '\\.', $lib ) . "/i";
    if ( preg_match( $reg, $resources, $matches ) ) {
      $res[] = $matches[0];
    }
  }
  return implode( "\n", $res );
}

/**
 * Loads the default for specific item
 */

function amcharts_get_default ( $chart_type, $context ) {
  if ( $content = @file_get_contents( AMCHARTS_DIR . '/defaults/' . $chart_type . '-' . $context . '.txt' ) )
    return $content;
  
  return '';
}

/**
 * Returns a list of JS files in a directory
 */

function amcharts_get_resource_files ( $dir, $path = '' ) {
  $res = array();
  if ( !file_exists( $dir ) )
    return $res;
  
  $files = scandir( $dir );
  foreach ( $files as $file ) {
    if ( preg_match( '/\.js|\.css$/', $file ) )
      $res[] = $path . $file;
  }
  return $res;
}

/**
 * Returns a list of JS files in a direcory and it's subdirectories
 */

function amcharts_get_resource_files_deep ( $dir, $path = '' ) {
  $res = array();
  if ( !file_exists( $dir ) )
    return $res;
  
  $files = scandir( $dir );
  foreach ( $files as $file ) {
    if ( is_dir( $dir . $file ) && ! in_array( $file, array( '.', '..') ) )
      $res = array_merge( $res, amcharts_get_resource_files_deep( $dir . $file, $path . $file . '/' ) );
    elseif ( preg_match( '/\.js|\.css$/', $file ) )
      $res[] = $path . $file;

  }
  return $res;
}