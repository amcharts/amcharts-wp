<?php
/**
 * Adds admin menu for settings
 */

add_action( 'admin_menu', 'amcharts_admin_menu' );
function amcharts_admin_menu() {
  add_submenu_page( 'options-general.php', 'Charts &amp; Maps', 'Charts &amp; Maps', 'manage_options', 'amcharts', 'amcharts_settings_show' );
}

/**
 * Returns default settings for the plugin
 */

function amcharts_get_defaults () {
  $settings = array(
    'location'            => 'remote',
    'own'                 => '0',
    'paths'               => '',
    'wrap'                => '1',
    'resources'           => amcharts_get_available_resources(),
    'custom_resources'    => '',
    'chart_types'         => array()
  );
  
  $chart_libs = amcharts_get_chart_type_libs();
  foreach ( $chart_libs as $chart_type => $libs ) {
    $settings['chart_types'][$chart_type] = array(
      'default_resources'   => amcharts_get_resources( $libs, $settings['resources'] ),
      'custom_resources'    => 0,
      'default_html'        => amcharts_get_default( $chart_type, 'html' ),
      'default_javascript'  => amcharts_get_default( $chart_type, 'javascript' )
    );
  }
  
  return $settings;
}

function amcharts_settings_show () {
  // check permissions
  if (!current_user_can( 'manage_options' ) )  {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
  }
  
  // unqueue required scripts and styles
  wp_enqueue_style( 'jquery-ui-smoothness', plugins_url( 'lib/jquery-ui/css/smoothness/jquery-ui-1.10.4.custom.min.css', AMCHARTS_BASE ), array(), AMCHARTS_VERSION );
  wp_enqueue_script( 'jquery-ui-tabs' );
  
  // get chart type settings
  $chart_types = amcharts_get_chart_types();
  $chart_type_libs = amcharts_get_chart_type_libs();
  
  // load current settings
  $settings = get_option( 'amcharts_options', amcharts_get_defaults() );
  
  // process save
  $errors = array();
  if ( !empty( $_POST ) && wp_verify_nonce( $_POST['amcharts_nonce'], AMCHARTS_NONCE ) ) {
    
    // save previous location setting for checking later
    $prev_location = $settings['location'];
    
    // get submited data
    $settings['own']                = isset( $_POST['own'] ) && '1' == $_POST['own'] ? '1' : '0';
    $settings['wrap']               = isset( $_POST['wrap'] ) && '1' == $_POST['wrap'] ? '1' : '0';
    $settings['location']           = isset( $_POST['location'] ) ? trim( $_POST['location'] ) : 'remote';
    $settings['paths']              = isset( $_POST['paths'] ) ? trim( $_POST['paths'] ) : '';
    $settings['custom_resources']   = isset( $_POST['custom_resources'] ) ? trim( $_POST['custom_resources'] ) : '';
    
    reset( $chart_types );
    foreach ( $chart_types as $chart_type => $chart_type_name ) {
      $settings['chart_types'][$chart_type] = array(
        'default_resources'   => trim( $_POST['chart_types'][$chart_type]['default_resources'] ),
        'custom_resources'    => (int) $_POST['chart_types'][$chart_type]['custom_resources'],
        'default_html'        => trim( $_POST['chart_types'][$chart_type]['default_html'] ),
        'default_javascript'  => trim( $_POST['chart_types'][$chart_type]['default_javascript'] )
      );
    }
    
    // strip slashes if any
    if ( !get_magic_quotes_gpc() ) {
      $settings = stripslashes_deep( $settings );
    }
    
    // refresh built-in resources
    if ( ( $prev_location != $settings['location'] ) || ( isset( $_POST['refresh'] ) && '1' == $_POST['refresh'] ) ) {
      $settings['resources'] = amcharts_get_available_resources( $settings['location'], $settings['paths'] );
      
      reset( $chart_type_libs );
      foreach ( $chart_type_libs as $chart_type => $libs ) {
        if ( ! $settings['chart_types'][$chart_type]['custom_resources'] )
          $settings['chart_types'][$chart_type]['default_resources'] = amcharts_get_resources( $libs, $settings['resources'] );
      }
    }
    
    // save settings
    if ( 0 === count( $errors ) ) {
      update_option( 'amcharts_options', $settings );
      $success = __( 'Settings were successfully saved!' );
    }
  }
  ?>
  
  <?php screen_icon(); ?>
  <h2><?php echo __( 'amCharts: Settings'); ?></h2>
  
  <?php if ( count($errors) > 0 ) : ?>
  <div class="message error"><?php echo wpautop(implode("\n", $errors)); ?></div>
  <?php endif; ?>
  <?php if ( isset($success) && !empty($success) ) : ?>
  <div class="message updated"><?php echo wpautop($success); ?></div>
  <?php endif; ?>
  
  <h3><?php echo __( 'Resources', 'amcharts' ); ?></h3>
  
  <form method="post" action="">
    
  <table class="form-table">
    <tbody>
      <tr valign="top">
        <th scope="row"><?php _e( 'Resource Storage', 'amcharts' ); ?></th>
        <td>
          <fieldset>
            <p>
              <label><input type="radio" name="location" value="remote" id="amcharts-location-remote" <?php
                echo 'remote' == $settings['location'] ? ' checked="checked"' : '';
              ?> /> <?php _e( 'Resources are stored remotely ', 'amcharts' ); ?></label>
            </p>
            <p class="description"><?php _e( 'The free versions of libraries will be loaded from www.amcharts.com. These are fully functional but will show a small branding link on charts or maps.', 'amcharts' ); ?></p>
            <p class="description"><?php _e( 'Want a link-free version? <a href="http://www.amcharts.com/online-store/" target="_blank">Purchase a commercial version</a> and help the development of this product as well as increase your karma points in the process.', 'amcharts' ); ?></p>
          </fieldset>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row"></th>
        <td>
          <fieldset>
            <p>
              <label><input type="radio" name="location" value="local" id="amcharts-location-local" <?php
                echo 'local' == $settings['location'] ? ' checked="checked"' : ''; ?>
              /> <?php _e( 'Resources are stored locally ', 'amcharts' ); ?></label>
            </p>
            <p class="description"><?php _e( 'This allows you to load the libraries from the local server.', 'amcharts' ); ?></p>
          </fieldset>
        </td>
      </tr>

      <tr valign="top" id="amcharts-live-editor-group" <?php
        echo 'remote' == $settings['location'] ? 'style="display: none;"' : '';
      ?>>
        <th scope="row"><?php _e( 'Live Editor Integration', 'amcharts' ); ?></th>
        <td>
          <fieldset>
            <p>
              <label><input type="checkbox" name="own" value="1" id="amcharts-own-libraries" <?php
                echo '1' == $settings['own'] ? ' checked="checked"' : '';
              ?> /> <?php _e( 'Use local amCharts libraries for Live Editor-hosted charts', 'amcharts' ); ?></label>
            </p>
            <p class="description"><?php _e( 'If you want to display Live Editor-created charts without branding link:', 'amcharts' ); ?></p>
            <p class="description">- <?php _e( 'Install commercial version of amCharts libraries.', 'amcharts' ); ?></p>
            <p class="description">- <?php _e( 'Check "Resources are stored locally" above.', 'amcharts' ); ?></p>
            <p class="description">- <?php _e( 'You may need to also click "Find them for me" button under "Local Paths".', 'amcharts' ); ?></p>
            <p class="description">- <?php _e( 'Check "Use local amCharts libraries" above.', 'amcharts' ); ?></p>
          </fieldset>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row"><?php _e( 'Error Handling', 'amcharts' ); ?></th>
        <td>
          <fieldset>
            <p>
              <label><input type="checkbox" name="wrap" value="1" <?php
                echo '1' == $settings['wrap'] ? ' checked="checked"' : '';
              ?> /> <?php _e( 'Use exception handling on chart and map code (recommended)', 'amcharts' ); ?></label>
            </p>
            <p class="description"><?php _e( 'If checked, the code for charts and maps will be wrapped with exception handling code (try / catch).', 'amcharts' ); ?></p>
            <p class="description"><?php _e( 'This will make sure a faulty code does not prevent other JavaScript on the same page executing properly.', 'amcharts' ); ?></p>
          </fieldset>
        </td>
      </tr>
      
      <tr valign="top" id="amcharts-path-group" <?php
        echo 'remote' == $settings['location'] ? 'style="display: none;"' : '';
      ?>>
        <th scope="row">
          <?php _e( 'Local Paths', 'amcharts' ); ?>
          <p class="description"><?php _e( 'Enter paths to your amCharts folders. Separate them by line breaks. The paths must be relative to your web root.', 'amcharts' ); ?> (<?php echo home_url(); ?>)</p>
        </th>
        <td>
          <fieldset>
            <p>
              <span class="description"><?php echo home_url(); ?></span>/
            </p>
            <p>
              <textarea name="paths" class="widefat" id="amcharts-path"><?php echo esc_textarea( $settings['paths'] ); ?></textarea>
              <p class="description"><?php _e( 'If you are not sure what to do, just unzip the amCharts archive you have downloaded and put into some directory under your web root. Then click "Find them for me".', 'amcharts' ); ?></p>
            </p>
            <p>
              <input type="button" class="button" id="amcharts-find-path" value="<?php echo esc_attr( __( 'Find them for me', 'amcharts' ) ); ?>" />
              <span class="amcharts-working" id="amcharts-path-working" style="display: none;"></span>
              <span class="amcharts-error" id="amcharts-find-path-error" style="display: none;"><?php _e( 'amCharts library was not found on your server', 'amcharts' ); ?></span>
            </p>
          </fieldset>
        </td>
      </tr>
      
      <tr valign="top">
        <th scope="row">
          <?php _e( 'Resource List', 'amcharts' ); ?>
          <p class="description"><?php _e( 'This is a list of resources available for use. You will be able to select them while creating charts or maps.', 'amcharts' ); ?></p>
        </th>
        <td>
          <fieldset>
            <div class="amcharts-resource-list">
              <?php echo nl2br( $settings['resources'] ); ?>
            </div>
            <p>
              <input type="submit" class="button" id="amcharts-rescan-resources" value="<?php echo esc_attr( __( 'Refresh the list', 'amcharts' ) ); ?>" />
              <span class="description"><?php _e( 'Attention! Pressing this button will save current settings.', 'amcharts' ); ?></span>
            </p>
          </fieldset>
        </td>
      </tr>
      
      <tr valign="top">
        <th scope="row">
          <?php _e( 'Custom Resources', 'amcharts' ); ?>
          <p class="description"><?php _e( 'Add your own resources here. Full or relative URLs (we include them the way you have them here). Separate by line break.', 'amcharts' ); ?></p>
        </th>
        <td>
          <fieldset>
            <p>
              <textarea name="custom_resources" class="widefat" id="amcharts-custom-resources"><?php echo esc_textarea( $settings['custom_resources'] ); ?></textarea>
            </p>
          </fieldset>
        </td>
      </tr>
      
      <tr valign="top">
        <th scope="row">
          <?php _e( 'Defaults', 'amcharts' ); ?>
          <p class="description"><?php _e( 'This section allows setting default resources, HTML and JavaScript code per chart/map type.', 'amcharts' ); ?></p>
          <p class="description"><?php _e( 'When creating new chart or map entry, you will be able to select from one of these presets.', 'amcharts' ); ?></p>
        </th>
        <td>
          <div id="amcharts-tabs">
            <ul>
              <?php
              reset( $chart_types );
              foreach ( $chart_types as $chart_type => $chart_type_name ) {
                ?><li><a href="#tabs-<?php echo $chart_type; ?>"><?php echo $chart_type_name; ?></a></li><?php
              }
              ?>
            </ul>
            <?php
            reset( $chart_types );
            foreach ( $chart_types as $chart_type => $chart_type_name ) {
              
              // let the user edit resource list?
              $edit = $settings['chart_types'][$chart_type]['custom_resources'] ? true : false;
              
              ?><div id="tabs-<?php echo $chart_type; ?>" class="amcharts-tab">

              <h4><?php _e( 'Resources', 'amcharts' ); ?></h4>
              
              <div class="amcharts-resource-list" style="display: <?php echo $edit ? 'none' : 'block'; ?>;">
                <?php if ( '' == $settings['chart_types'][$chart_type]['default_resources'] && 'local' == $settings['location'] ) { ?>
                  <p class="amcharts-notice"><?php _e( 'We did not find libraries required for this chart type in your local storage.', 'amcharts' ); ?></p>
                  <p class="amcharts-notice"><?php _e( 'Make sure you hit the "Refresh the list" button higher on this page when you install them.', 'amcharts' ); ?></p>
                <?php } else { ?>
                  <?php echo nl2br( $settings['chart_types'][$chart_type]['default_resources'] ); ?>
                <?php } ?>
              </div>
              
              <fieldset class="amcharts-resource-group" style="display: <?php echo $edit ? 'block' : 'none'; ?>;">
                <p>
                  <textarea name="chart_types[<?php echo $chart_type; ?>][default_resources]" rows="4" class="widefat amcharts-resources"><?php echo esc_textarea( $settings['chart_types'][$chart_type]['default_resources'] ); ?></textarea>
                </p>
                <p>
                  <?php
                  $libs = amcharts_split_libs( $settings['resources'] );
                  $libs = array_merge( $libs, amcharts_split_libs( $settings['custom_resources'] ) );
                  ?>
                  <select class="amcharts-select-resource">
                    <option value=""><?php _e( 'Select a resource', 'amcharts' ); ?></option>
                    <?php
                    foreach( $libs as $lib ) {
                      ?><option value="<?php echo esc_attr( $lib ); ?>"><?php echo $lib; ?></option><?php
                    }
                    ?>
                  </select>
                  <input type="button" class="button amcharts-add-resource" value="<?php _e( 'Add', 'amcharts' ) ; ?>" />
                </p>
              </fieldset>
              
              <p>
                <label><input type="checkbox" name="chart_types[<?php echo $chart_type; ?>][custom_resources]" class="amcharts-edit-myself" value="1" <?php
                  if ( $settings['chart_types'][$chart_type]['custom_resources'] ) {
                    ?>checked="checked"<?php
                  }
                ?>/> <?php _e( 'I want to manage resource list myself' , 'amcharts' ); ?></label>
              </p>
              
              <p class="description">
                <?php _e( "If the above box unchecked, plugin will manage required resources and their urls for you. If this is checked, you're on your own ;)", 'amcharts' ); ?>
              </p>
              
              
              <h4><?php _e( 'HTML', 'amcharts' ); ?></h4>
              <p>
                <textarea name="chart_types[<?php echo $chart_type; ?>][default_html]" class="widefat code code-html"><?php echo esc_textarea( $settings['chart_types'][$chart_type]['default_html'] ); ?></textarea>
              </p>
              <p>
                <p class="description"><?php _e( 'Enter the default HTML to populate new entries with. Use <strong>%CHART%</strong> symbol for safe and unique chart ids and variables.' ); ?></p>
              </p>
              
              <h4><?php _e( 'JavaScript', 'amcharts' ); ?></h4>
              
              <p>
                <textarea name="chart_types[<?php echo $chart_type; ?>][default_javascript]" class="widefat code code-javascript"><?php echo esc_textarea( $settings['chart_types'][$chart_type]['default_javascript'] ); ?></textarea>
              </p>
              <p>
                <p class="description"><?php _e( 'Enter the default JavaScript to populate new entries with. Use <strong>%CHART%</strong> symbol for safe and unique chart ids and variables.' ); ?></p>
              </p>
              
            </div><?php
            }
            ?>
          </div>
        </td>
      </tr>
      
    </tbody>
  </table>
  
  <p>
    <input type="submit" class="button-primary" value="Save Settings &raquo;" />
    <input type="hidden" id="amcharts-refresh" name="refresh" value="0" />
    <?php wp_nonce_field( AMCHARTS_NONCE, 'amcharts_nonce' ); ?>
  </p>
  
  </form>
  
  <?php
}

/**
 * A handler for AJAX call: amcharts_find_me
 */

add_action( 'wp_ajax_amcharts_find_me', 'amcharts_find_me' );

function amcharts_find_me () {
  amcharts_find_me_branch( '' );
  die();
}

function amcharts_find_me_branch ( $path, $paths = false ) {
  //echo $path . "\n";
  if ( false === $paths )
    $paths = array();
  $dir = ABSPATH . $path;
  $files = scandir( $dir );
  foreach ( $files as $file ) {
    if ( in_array( $file, array( '.', '..' ) ) ) {
      continue;
    }
    elseif ( in_array( $file, array( 'amcharts.js', 'ammap.js' ) ) ) {
      if ( sizeof( $paths ) )
        echo "\n";
      
      if ( !in_array( $path, $paths ) ) {
        echo $path;
        $paths[] = $path;
      }
    }
    elseif ( is_dir( $dir . $file . '/' ) ) {
      amcharts_find_me_branch( $path . $file . '/', $paths );
    }
  }
}