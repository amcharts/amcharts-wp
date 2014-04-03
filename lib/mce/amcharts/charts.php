<?php
// get WP path
$p = explode( 'wp-content', __FILE__ );
require_once( $p[0] . '/wp-load.php' );

// headers
header('Content-Type: text/html; charset=' . get_bloginfo('charset'));
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<title><?php _e( 'Select a chart or map to insert', 'amcharts' ); ?></title>
<?php
wp_admin_css( 'wp-admin', true );
?>
<style type="text/css">
	body {
		min-width: 0;
    min-height: 0;
    height: auto;
    padding: 10px;
	}
  
  .chart {
    font-weight: bold;
    cursor: pointer;
    padding: 3px 6px;
  }
  
  .chart:hover {
    background-color: #eee;
  }

</style>
<script type="text/javascript" src="jquery-1.10.2.min.js"></script>
</head>
<body>
  <ul>
  <?php
  $charts = get_posts( array(
    'post_type' => 'amchart'
  ) );
  foreach( $charts as $chart ) {
		$id = $chart->ID;
		if ( $slug = get_post_meta( $chart->ID, '_amcharts_slug', true ) )
			$id = $slug;
    ?>
    <li class="chart" id="<?php echo $id; ?>"><?php echo $chart->post_title; ?></li>
    <?php
  }
  ?>
  </ul>
  <script>
  jQuery( function( $ ) {
    $( '.chart' ).click( function () {
      window.parent.tinyMCE.activeEditor.execCommand( 'mceInsertContent', false, '[amcharts id="' + this.id + '"]' );
      parent.tinyMCE.activeEditor.windowManager.close( window );
    } );
  } );
  </script>
</body>
</html>
