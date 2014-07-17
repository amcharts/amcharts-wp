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
  
  .post {
    font-weight: bold;
    cursor: pointer;
    padding: 3px 6px;
  }
  
  .post:hover {
    background-color: #eee;
  }

</style>
<script type="text/javascript" src="jquery-1.10.2.min.js"></script>
</head>
<body>
  <input type="text" value="" id="post-search" placeholder="<?php _e( 'Start typing to search', 'amcharts' ); ?>" class="widefat" />
  <div id="results"></div>
  <script>
  var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
  jQuery( function( $ ) {
    amchartsUpdateSearchResults();
    $( '#results').on( 'click', '.post', function () {
      window.parent.tinyMCE.activeEditor.execCommand( 'mceInsertContent', false, '[amcharts id="' + this.id + '"]' );
      parent.tinyMCE.activeEditor.windowManager.close( window );
    } );
    $( '#post-search' ).on('keyup change', function () {
      amchartsUpdateSearchResults();
    });
  } );
  function amchartsUpdateSearchResults () {
    var query = jQuery( '#post-search' ).val();
    var data = {
      'action': 'amcharts_get_posts',
      'query': query
    };
    jQuery.post(ajaxurl, data, function(response) {
      jQuery('#results').html(response);
    });
  }
  </script>
</body>
</html>
