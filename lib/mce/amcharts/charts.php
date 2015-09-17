<?php
// get WP path
$webroot_dir = explode( 'wp-content', __FILE__ );
$webroot_dir = $webroot_dir[0];
if ( ! file_exists( "{$webroot_dir}wp-load.php" ) ) {
  // WP is installed in a different directory
  // retrieve the path from wp-config.php
  require_once( "{$webroot_dir}wp-config.php" );
  require_once( ABSPATH . "wp-load.php" );
}
else {
  require_once( "{$webroot_dir}wp-load.php" );
}

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

  .amcharts-tabs {
    border-bottom: 2px solid #fff;
    overflow: hidden;
    margin-bottom: 10px;
  }

  .amcharts-tabs ul, .amcharts-tabs li {
    margin: 0;
  }

  .amcharts-tabs a {
    float: left;
    padding: 5px 15px;
    text-decoration: none;
    color: inherit;
  }

  .amcharts-tabs a.active {
    background: #fff;
  }

  #live-editor-width, #live-editor-height {
    width: 35%;
  }

  #live-editor-url.error {
    border: 1px solid #d00;
  }

  input {
    font-size: 13px;
  }

</style>
<script type="text/javascript" src="jquery-1.10.2.min.js"></script>
</head>
<body>
  <div class="amcharts-tabs">
    <ul>
      <li><a href="#tabs-local" class="active"><?php _e( 'Local Charts', 'amcharts' ); ?></a></li>
      <li><a href="#tabs-live"><?php _e( 'Live Editor', 'amcharts' ); ?></a></li>
    </ul>
  </div>
  <div id="tabs-live" style="display: none;">
    <p>
      <input type="text" value="" id="live-editor-url" placeholder="<?php _e( 'Enter a URL to a chart created in Live Editor', 'amcharts' ); ?>" class="widefat" />
    </p>
    <p class="description"><?php _e( 'Once you publish the chart in Live Editor, copy and paste it\'s URL into field above.', 'amcharts' ); ?></p>
    <p class="description"><?php _e( 'I.e.: http://live.amcharts.com/NmU2Z/', 'amcharts' ); ?></p>
    <p>
      <input type="text" value="" id="live-editor-width" placeholder="<?php _e( 'Width (default: 400px)', 'amcharts' ); ?>" class="fat" />
      <input type="text" value="" id="live-editor-height" placeholder="<?php _e( 'Height (default: 300px)', 'amcharts' ); ?>" class="fat" />
      <input type="button" id="live-editor-ok" value="<?php _e( 'Insert', 'amcharts' ); ?>" class="button button-primary" />
    </p>
    <p>
      <input type="button" value="<?php _e( 'Open Live Editor', 'amcharts' ); ?>" onclick="window.open('http://live.amcharts.com/');" class="button" />
      <span class="description"><?php _e( 'This will open Live Editor in a new window', 'amcharts' ); ?></span>
    </p>

  </div>
  <div id="tabs-local">
    <input type="text" value="" id="post-search" placeholder="<?php _e( 'Start typing to search', 'amcharts' ); ?>" class="widefat" />
    <div id="results"></div>
  </div>
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
    var currentTab = 'tabs-local';
    $( '.amcharts-tabs a').on( 'click', function (e) {
      var target = this.href.split('#')[1];
      e.preventDefault();
      if ( target == currentTab )
        return;
      $( '#' + currentTab ).hide();
      $( '#' + target ).show();
      $( '.amcharts-tabs a' ).removeClass( 'active' );
      $( this ).addClass( 'active' );
      currentTab = target;
    });
    $('#live-editor-ok').on('click', function () {
      var url = $('#live-editor-url').val();
      if ( '' == url ) {
        $( '#live-editor-url' ).addClass( 'error' ).prop( 'placeholder', '<?php echo esc_js( __( 'Please enter a chart URL', 'amcharts' ) ); ?>' );
        return;
      }
      var embed = '[embed';
      var width = $( '#live-editor-width' ).val();
      var height = $( '#live-editor-height' ).val();
      if ( '' != width ) embed += ' width="' + width + '"';
      if ( '' != height ) embed += ' height="' + height + '"';
      <?php
      // use our own libraries?
      $settings = get_option( 'amcharts_options', array( 'own' => 0, 'paths' => '' ) );
      if ( '1' == $settings['own'] ) {
        $paths = amcharts_split_libs( $settings['paths'] );
        $path = array_shift( $paths );
        if ( '' == $path )
          break;
        $path = home_url( $path );
        ?>
        embed += ' src="<?php echo $path; ?>" tkn="replaceDefault"';
        <?php
      }
      ?>
      embed += ']' + url + '[/embed]'
      window.parent.tinyMCE.activeEditor.execCommand( 'mceInsertContent', false, embed );
      parent.tinyMCE.activeEditor.windowManager.close( window );
    });
    $( '#live-editor-url' ).on('keyup change', function () {
      $(this).removeClass( 'error' );
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
