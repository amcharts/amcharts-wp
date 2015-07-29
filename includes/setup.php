<?php
/**
 * Register custom post types
 */

add_action( 'init', 'amcharts_register_cpt' );

function amcharts_register_cpt() {

  // --------------------------------------------------------------------------
  // amchart CPT
  // --------------------------------------------------------------------------
  
  $labels = array( 
    'name'                => _x( 'Charts &amp; Maps', 'amcharts' ),
    'singular_name'       => _x( 'Chart or Map', 'amcharts' ),
    'add_new'             => _x( 'Add New', 'amcharts' ),
    'add_new_item'        => _x( 'Add New Chart or Map', 'amcharts' ),
    'edit_item'           => _x( 'Edit Chart or Map', 'amcharts' ),
    'new_item'            => _x( 'New Chart or Map', 'amcharts' ),
    'view_item'           => _x( 'View Chart or Map', 'amcharts' ),
    'search_items'        => _x( 'Search Charts &amp; Maps', 'amcharts' ),
    'not_found'           => _x( 'No charts or maps found', 'amcharts' ),
    'not_found_in_trash'  => _x( 'No charts or maps found in Trash', 'amcharts' ),
    'menu_name'           => _x( 'Charts &amp; Maps', 'amcharts' ),
  );

  $args = array( 
    'labels'              => $labels,
    'menu_icon'           => AMCHARTS_BASE_URL.'/images/icon_charts2.png',
    'hierarchical'        => false,
    'supports'            => array( 'title' ),
    'taxonomies'          => array( ),
    'public'              => false,
    'show_ui'             => true,
    'menu_position'       => 20,
    'show_in_nav_menus'   => false,
    'publicly_queryable'  => false,
    'exclude_from_search' => true,
    'has_archive'         => false,
    'query_var'           => false,
    'can_export'          => true,
    'capability_type'     => 'post'
  );

  register_post_type( 'amchart', $args );
  
}

/**
 * Set multilanguage option
 */

add_action( 'plugins_loaded', 'amcharts_plugins_loaded' );
function amcharts_plugins_loaded() {
  load_plugin_textdomain( 'amcharts', false, dirname( plugin_basename( AMCHARTS_BASE ) ) . '/langs/' ); 
}

/**
 * Inserts chart code
 */

function amcharts_insert ( $chart_id ) {
  echo amcharts_shortcode( array(
    'id' => $chart_id
  ) );
}

/**
 * Returns an object with chart data
 */

function amcharts_get ( $chart_id ) {
  $chart = new stdClass();
  
  if ( ! $chart_post = get_post( $chart_id ) )
    return false;

  // increment instance
  amcharts_increment_instance();  

  $chart->title = $chart_post->post_title;
  $chart->post = &$chart_post;
  $chart->resources = get_post_meta( $chart_id, '_amcharts_resources', true );
  $chart->html = amcharts_parse_code( get_post_meta( $chart_id, '_amcharts_html', true ) );
  $chart->javascript = amcharts_parse_code( get_post_meta( $chart_id, '_amcharts_javascript', true ) );
  
  return $chart;
}


/**
 * Register a shortcode
 */

add_shortcode( 'amcharts' , 'amcharts_shortcode' );
function amcharts_shortcode ( $atts ) {
  
  extract( shortcode_atts( array(
    'id' => ''
  ), $atts ) );
  
  // try loading by slug first
  if ( $chart = get_posts( array(
      'post_type'       => 'amchart',
      'fields'          => 'ids',
      'posts_per_page'  => 1,
      'meta_query'      => array(
        array(
          'key'   => '_amcharts_slug',
          'value' => $id,
        )
      )
    ) ) ) {
    $id = $chart[0];
  }
  else if ( !$chart = get_post( $id ) )
    return '';
  
  // increment instance
  amcharts_increment_instance();
  
  // get meta
  $resources  = get_post_meta( $id, '_amcharts_resources', true );
  $html       = amcharts_parse_code( get_post_meta( $id, '_amcharts_html', true ) );
  $javascript = amcharts_parse_code( get_post_meta( $id, '_amcharts_javascript', true ) );

  // add data passed via shortcode
  $pass = array();
  foreach ( $atts as $att ) {
    if ( 0 === strpos( $att , 'data-' ) ) {
      list( $key, $val ) = explode( '=', $att );
      $pass[ substr( $key, 5 ) ] = str_replace( '"', '', $val );
    }
  }
  if ( sizeof( $pass ) )
    $javascript = "AmCharts.wpChartData = " . json_encode( $pass ) . ";\n" . $javascript;

  // wrap with exception code if necessary
  $settings = get_option( 'amcharts_options' );
  if ( isset( $settings['wrap'] ) && '1' == $settings['wrap'] )
    $javascript = "try {\n" . $javascript . "\n}\ncatch( err ) { console.log( err ); }";
  
  // enqueue resources
  $libs = amcharts_split_libs( $resources );
  foreach ( $libs as $lib ) {
    if ( preg_match( '/\\.css/i', $lib ) )
      wp_enqueue_style( 'amcharts-external-' . md5( basename( $lib ) ), $lib, array(), AMCHARTS_VERSION );
    else
      wp_enqueue_script( 'amcharts-external-' . md5( basename( $lib ) ), $lib, array(), AMCHARTS_VERSION, true );
  }
  
  // enqueue JavaScript part
  amcharts_enqueue_javascript( $javascript );
  
  return $html;
}


/**
 * Add scripts and styles
 */

if ( is_admin() )
  add_action( 'init', 'amcharts_enqueue_scripts' );

function amcharts_enqueue_scripts() {
  // own
  wp_enqueue_style( 'amcharts-admin', plugins_url( 'lib/amcharts_admin.css', AMCHARTS_BASE ), array(), AMCHARTS_VERSION );
  wp_enqueue_script( 'amcharts-admin', plugins_url( 'lib/amcharts_admin.js', AMCHARTS_BASE ), array( 'jquery' ), AMCHARTS_VERSION );
  
  // codemirror
  wp_enqueue_style( 'codemirror', plugins_url( 'lib/codemirror/codemirror.css', AMCHARTS_BASE ), array(), AMCHARTS_VERSION );
  wp_enqueue_script( 'codemirror', plugins_url( 'lib/codemirror/codemirror.js', AMCHARTS_BASE ), array( 'amcharts-admin' ), AMCHARTS_VERSION );
  wp_enqueue_script( 'codemirror-javascript', plugins_url( 'lib/codemirror/mode/javascript/javascript.js', AMCHARTS_BASE ), array( 'codemirror' ), AMCHARTS_VERSION );
  wp_enqueue_script( 'codemirror-css', plugins_url( 'lib/codemirror/mode/css/css.js', AMCHARTS_BASE ), array( 'codemirror' ), AMCHARTS_VERSION );
  wp_enqueue_script( 'codemirror-xml', plugins_url( 'lib/codemirror/mode/xml/xml.js', AMCHARTS_BASE ), array( 'codemirror' ), AMCHARTS_VERSION );
  wp_enqueue_script( 'codemirror-htmlmixed', plugins_url( 'lib/codemirror/mode/htmlmixed/htmlmixed.js', AMCHARTS_BASE ), array( 'codemirror', 'codemirror-css', 'codemirror-xml' ), AMCHARTS_VERSION );
}


/**
 * Initialize global state variables
 *
 * $amcharts_scripts array will be populated by shortcode handler
 */

$amcharts_scripts = array();
$amcharts_current_instance = 0;

/**
 * Enqueue chart javascript code to be shown in the footer
 */

function amcharts_enqueue_javascript ( $js ) {
  global $amcharts_scripts;
  $amcharts_scripts[] = $js;
}

/**
 * Increments current instance
 */

function amcharts_increment_instance () {
  global $amcharts_current_instance;
  $amcharts_current_instance++;
  return $amcharts_current_instance;
}

/**
 * Returns current instance
 */

function amcharts_get_current_instance () {
  global $amcharts_current_instance;
  return $amcharts_current_instance;
}

/**
 * Parses HTML and JavaScript for special meta codes
 */

function amcharts_parse_code ( $code ) {
  $instance = 'amchart' . amcharts_get_current_instance();
  return str_replace( '%CHART%', $instance, $code );
}

/**
 * wp_footer hook
 */

add_action( 'wp_footer', 'amcharts_wp_footer', 1000 );
function amcharts_wp_footer () {
  global $amcharts_scripts;
  if ( sizeof( $amcharts_scripts ) ) { ?>
    <script>
      <?php echo implode( "\n", $amcharts_scripts ); ?>
    </script>
    <?php
  }
}

/**
 * Redirect to our specific template for chart previews
 */

add_filter( 'template_include', 'amcharts_preview_template', 99 );
function amcharts_preview_template( $template ) {
  
  if ( isset( $_GET['amcharts_preview'] ) )
    $template = AMCHARTS_DIR . '/includes/preview.php';

  return $template;
}

/**
 * (Pseudo) Activation/deactivation hooks (workaround for Multisite)
 */

register_activation_hook( AMCHARTS_BASE, 'amcharts_activate' );
register_deactivation_hook( AMCHARTS_BASE, 'amcharts_deactivate' );
register_uninstall_hook( AMCHARTS_BASE, 'amcharts_deactivate' );

add_action( 'admin_init', 'amcharts_check_activation' );
function amcharts_check_activation () {
  if ( ! get_option( 'amcharts_activated' ) ) {
    amcharts_activate();
  }
}

function amcharts_activate () {
  // security checks
  if ( ! current_user_can( 'activate_plugins' ) )
    return;
  
  // set defaults
  $settings = amcharts_get_defaults();
  
  // update options
  update_option( 'amcharts_options', $settings );
  update_option( 'amcharts_activated', true );
}

function amcharts_deactivate () {
  // TODO: do this on all sites on Multisite install
  delete_option( 'amcharts_activated' );
}

/**
 * Adding support for oEmbed Live Editor
 */

wp_oembed_add_provider( 'http://live.amcharts.com/*', 'http://live.amcharts.com/oembed/' );

/**
 * A filter to add custom parameters to oEmbed url
 */

add_filter( 'oembed_fetch_url', 'amcharts_oembed_fetch_url', 10, 3 );
function amcharts_oembed_fetch_url( $provider, $url, $args ) {
  if ( false !== strpos( $provider, 'live.amcharts.com' ) && !empty( $args['src'] ) ) {
    $provider = add_query_arg( 'src', $args['src'], $provider );
  }
  return $provider;
}

/**
 * Manage plugin version upgrades
 */
add_action( 'plugins_loaded', 'amcharts_check_version' );
function amcharts_check_version () {
  
  $version = get_option( 'amcharts_version', '1.0.0' );
  if ( $version != AMCHARTS_VERSION ) {
    
    // get numeric representation
    $version = str_replace( '.', '', $version );

    // the version does not match
    // run necessary checks
    $settings = get_option( 'amcharts_options', array() );
    $chart_libs = amcharts_get_chart_type_libs();
    $resources = amcharts_get_available_resources();

    // 1.0.8 and down
    if ( $version <= 108 ) {

      // populate gantt chart type defaults
      $settings['chart_types']['gantt'] = array(
        'default_resources'   => amcharts_get_resources( $chart_libs['gantt'], $resources ),
        'custom_resources'    => 0,
        'default_html'        => amcharts_get_default( 'gantt', 'html' ),
        'default_javascript'  => amcharts_get_default( 'gantt', 'javascript' )
      );

    }

    // 1.0.9 and down
    if ( $version <= 109 ) {
      // refresh resource list (to include CSS files)
      $settings['resources'] = amcharts_get_available_resources( $settings['location'], $settings['paths'] );
    }

    update_option( 'amcharts_options', $settings );

    // update the version
    update_option( 'amcharts_version', AMCHARTS_VERSION );
    
  }
}
