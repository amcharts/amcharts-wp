<?php
/**
 * Check if this is a new post being created
 */
function amcharts_is_new_post() {
  global $pagenow;
  return 'post-new.php' == $pagenow;
}

/**
 * Returns the list of available chart types
 */
function amcharts_get_chart_types($version = '3') {
  if ( $version == '4' ) {
    return array(
      'xy'      => __( 'XY', 'amcharts' ),
      'pie'     => __( 'Pie', 'amcharts' ),
      'sliced'  => __( 'Sliced', 'amcharts' ),
      'sankey'  => __( 'Sankey Diagram', 'amcharts' ),
      'radar'   => __( 'Radar', 'amcharts' ),
      'gauge'   => __( 'Gauge', 'amcharts' ),
      'chord'   => __( 'Chord Diagram', 'amcharts' ),
      'treemap' => __( 'Treemap', 'amcharts' ),
      'map'     => __( 'Map', 'amcharts' )
    );
  }
  return array(
    'serial'  => __( 'Serial', 'amcharts' ),
    'pie'     => __( 'Pie', 'amcharts' ),
    'xy'      => __( 'XY', 'amcharts' ),
    'funnel'  => __( 'Funnel', 'amcharts' ),
    'radar'   => __( 'Radar', 'amcharts' ),
    'gauge'   => __( 'Gauge', 'amcharts' ),
    'gantt'   => __( 'Gantt', 'amcharts' ),
    'stock'   => __( 'Stock', 'amcharts' ),
    'map'     => __( 'Map', 'amcharts' )
  );
}

/**
 * Returns the list of the charts and their default depenecies
 */
function amcharts_get_chart_type_libs($version = '3') {
  if ( $version == '4' ) {
    return array(
      'xy'      => array( 'core.js', 'charts.js' ),
      'pie'     => array( 'core.js', 'charts.js' ),
      'sliced'  => array( 'core.js', 'charts.js' ),
      'sankey'  => array( 'core.js', 'charts.js' ),
      'radar'   => array( 'core.js', 'charts.js' ),
      'gauge'   => array( 'core.js', 'charts.js' ),
      'chord'   => array( 'core.js', 'charts.js' ),
      'treemap' => array( 'core.js', 'charts.js' ),
      'map'     => array( 'core.js', 'maps.js', 'worldLow.js' )
    );
  }
  return array(
    'serial'  => array( 'amcharts.js', 'serial.js' ),
    'pie'     => array( 'amcharts.js', 'pie.js' ),
    'xy'      => array( 'amcharts.js', 'xy.js' ),
    'funnel'  => array( 'amcharts.js', 'funnel.js' ),
    'radar'   => array( 'amcharts.js', 'radar.js' ),
    'gauge'   => array( 'amcharts.js', 'gauge.js' ),
    'gantt'   => array( 'amcharts.js', 'serial.js', 'gantt.js' ),
    'stock'   => array( 'amcharts.js', 'serial.js', 'amstock.js' ),
    'map'     => array( 'ammap.js', 'worldLow.js' )
  );
}

/**
 * Strips slashes from submitted data
 */
function amcharts_stripslashes( $str ) {
  // if ( get_magic_quotes_gpc() )
  // WP seems to add slashes regardless of the above setting
  $str = stripslashes( $str );
  return $str;
}

/**
 * Returns unique chart slug
 */
function amcharts_generate_slug( $type = 'chart' ) {
  if ( ! $type )
    $type = 'chart';
  
  $continue = true;
  $i = 0;
  do {
    $i++;
    $slug = $type . '-' . $i;
    if ( ! get_posts( array(
      'post_type'       => 'amchart',
      'fields'          => 'ids',
      'posts_per_page'  => 1,
      'meta_query'      => array(
        array(
          'key'   => '_amcharts_slug',
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
function amcharts_split_libs( $resources ) {
  return preg_split( '/\s+/', trim ( $resources ) );
}

/**
 * Returns available resource files
 */
function amcharts_get_available_resources( $type = 'remote', $paths = '', $relative = false, $version = '3' ) {
  $res = '';
  if ( 'local' == $type ) {
    $dirs = amcharts_split_libs( $paths );

    // libraries
    $libs = array();
    foreach ( $dirs as $path ) {
      $libs = array_merge( $libs, amcharts_get_resource_files( ABSPATH . $path, home_url( $path ) ) );
    }

    // themes
    reset( $dirs );
    foreach ( $dirs as $path ) {
      $libs = array_merge( $libs, amcharts_get_resource_files_deep( ABSPATH . $path . 'themes/', home_url( $path . 'themes/' ) ) );
    }
    
    // maps
    reset( $dirs );
    foreach ( $dirs as $path ) {
      if ( $version == '4' ) {
        $libs = array_merge( $libs, amcharts_get_resource_files( ABSPATH . $path . 'geodata/', home_url( $path . 'geodata/' ) ) );
      }
      else {
        $libs = array_merge( $libs, amcharts_get_resource_files( ABSPATH . $path . 'maps/js/', home_url( $path . 'maps/js/' ) ) );
      }
    }

    // plugins
    if ( $version == '3' ) {
      reset( $dirs );
      foreach ( $dirs as $path ) {
        $libs = array_merge( $libs, amcharts_get_resource_files_deep( ABSPATH . $path . 'plugins/', home_url( $path . 'plugins/' ) ) );
      }
    }

    // language
    reset( $dirs );
    foreach ( $dirs as $path ) {
      $libs = array_merge( $libs, amcharts_get_resource_files_deep( ABSPATH . $path . 'lang/', home_url( $path . 'lang/' ) ) );
    }

    // make URLs relative if necessary
    if ( $relative ) {
      reset( $libs );
      foreach ( $libs as $i => $path ) {
        $libs[$i] = amcharts_make_relative( $path );
      }
    }
    
    $res = implode( "\n", $libs );
  }
  else {
    // load from amcharts.com (fall back to local hardocded list if url wrappers are disabled in PHP)
    if ( !$res = @file_get_contents( 'http://www.amcharts.com/lib/' . $version . '/resources.php' ) ) {
      if ( $version == '4' ) {
        $res = file_get_contents( AMCHARTS_DIR . '/defaults/4/resources.txt' );
      }
      else {
        $res = file_get_contents( AMCHARTS_DIR . '/defaults/resources.txt' );
      }
    }
  }
  return $res;
}

/**
 * Strips the protocol/host part of the URL
 */
function amcharts_make_relative( $url ) {
  return str_replace( site_url(), '', $url );
}

/**
 * Returns resource list that are required
 */
function amcharts_get_resources( $libs, $resources ) {
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
function amcharts_get_default ( $chart_type, $context, $version = '3' ) {

  if ( $version == "4" ) {
    $path = AMCHARTS_DIR . '/defaults/4/' . $chart_type . '-' . $context . '.txt';
  }
  else {
    $path = AMCHARTS_DIR . '/defaults/' . $chart_type . '-' . $context . '.txt';
  }

  if ( $content = @file_get_contents( $path ) ) {
    return $content;
  }
  
  return '';
}

/**
 * Returns a list of JS files in a directory
 */
function amcharts_get_resource_files( $dir, $path = '' ) {
  $res = array();
  if ( !file_exists( $dir ) )
    return $res;
  
  $files = scandir( $dir );
  foreach ( $files as $file ) {
    if ( preg_match( '/\.js$|\.css$/', $file ) )
      $res[] = $path . $file;
  }
  return $res;
}

/**
 * Returns a list of JS files in a direcory and it's subdirectories
 */
function amcharts_get_resource_files_deep( $dir, $path = '' ) {
  $res = array();
  if ( !file_exists( $dir ) )
    return $res;
  $files = scandir( $dir );
  foreach ( $files as $file ) {
    if ( is_dir( $dir . $file ) && ! in_array( $file, array( '.', '..') ) )
      $res = array_merge( $res, amcharts_get_resource_files_deep( $dir . $file, $path . $file . '/' ) );
    elseif ( preg_match( '/\.js$|\.css$/', $file ) )
      $res[] = $path . $file;

  }
  return $res;
}

/**
 * Returns version of the amCharts library in use.
 */
function amcharts_get_lib_version() {
  $settings = get_option( 'amcharts_options', array() );

  // handle situation where version is not (yet) set
  if ( !isset( $settings['version'] ) ) {
    $settings['version'] = '4';
  }

  return $settings['version'];
}