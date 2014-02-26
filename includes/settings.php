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
	return array(
		'location'						=> 'local',
		'paths'								=> '',
		'resources' 					=> '',
		'custom_resources'		=> '',
		'default_resources' 	=> '',
		'default_html'				=> '<div id="%CHART%" style="width: 100%; height: 300px;"></div>',
		'default_javascript'	=>
'var %CHART% = AmCharts.makeChart({
	type: ""
});'
	);
}

function amcharts_settings_show () {
  // check permissions
  if (!current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
  
  // load current settings
  $settings = get_option( 'amcharts_options', amcharts_get_defaults() );
  
  // process save
  $errors = array();
  if ( !empty( $_POST ) && wp_verify_nonce( $_POST['amcharts_nonce'], AMCHARTS_NONCE ) ) {
		
		// save previous location setting for checking later
		$prev_location = $settings['location'];
    
    // get submited data
    $settings['location'] 					= isset( $_POST['location'] ) ? trim( $_POST['location'] ) : 'remote';
    $settings['paths'] 							= isset( $_POST['paths'] ) ? trim( $_POST['paths'] ) : '';
    $settings['custom_resources'] 	= isset( $_POST['custom_resources'] ) ? trim( $_POST['custom_resources'] ) : '';
		$settings['default_resources']	= isset( $_POST['default_resources'] ) ? trim( $_POST['default_resources'] ) : '';
		$settings['default_html'] 			= isset( $_POST['paths'] ) ? trim( $_POST['default_html'] ) : '';
		$settings['default_javascript'] = isset( $_POST['paths'] ) ? trim( $_POST['default_javascript'] ) : '';
    
    // strip slashes if any
    if ( !get_magic_quotes_gpc() ) {
      $settings = stripslashes_deep( $settings );
    }
		
		// refresh built-in resources
		if ( ( $prev_location != $settings['location'] ) || ( isset( $_POST['refresh'] ) && '1' == $_POST['refresh'] ) )
			$settings['resources'] = amcharts_get_available_resources( $settings['location'], $settings['paths'] );
    
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
			
			<tr valign="top" id="amcharts-path-group" <?php
				echo 'remote' == $settings['location'] ? 'style="display: none;"' : '';
			?>>
        <th scope="row"><?php _e( 'Local Paths', 'amcharts' ); ?></th>
        <td>
					<fieldset>
						<p>
							<span class="description"><?php echo home_url(); ?></span>/
						</p>
						<p>
							<textarea name="paths" class="widefat" id="amcharts-path"><?php echo esc_textarea( $settings['paths'] ); ?></textarea>
            </p>
						<p class="description"><?php _e( 'Enter a paths to your amCharts folders. Separate them by line breaks. The paths muyst be eelative to your web root.', 'amcharts' ); ?> (<?php echo home_url(); ?>)</p>
						<p class="description"><?php _e( 'If you are not sure what to do, just unzip the amCharts archive you have downloaded and put into some directory under your web root. Then click "Find them for me".', 'amcharts' ); ?></p>
						<p>
							<input type="button" class="button" id="amcharts-find-path" value="<?php echo esc_attr( __( 'Find them for me', 'amcharts' ) ); ?>" />
							<span class="amcharts-working" id="amcharts-path-working" style="display: none;"></span>
							<span class="amcharts-error" id="amcharts-find-path-error" style="display: none;"><?php _e( 'amCharts library was not found on your server', 'amcharts' ); ?></span>
						</p>
          </fieldset>
        </td>
      </tr>
			
			<tr valign="top">
        <th scope="row"><?php _e( 'Resource List', 'amcharts' ); ?></th>
        <td>
					<fieldset>
						<div id="amcharts-resource-list">
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
        <th scope="row"><?php _e( 'Custom Resources', 'amcharts' ); ?></th>
        <td>
					<fieldset>
						<p>
							<textarea name="custom_resources" class="widefat" id="amcharts-custom-resources"><?php echo esc_textarea( $settings['custom_resources'] ); ?></textarea>
            </p>
						<p>
							<p class="description"><?php _e( 'Add your own resources here. Full or relative URLs (we include them the way you have them here). Separate by line break.', 'amcharts' ); ?> (<?php echo home_url(); ?>)</p>
						</p>
          </fieldset>
        </td>
      </tr>
			
			<tr valign="top">
        <th scope="row"><?php _e( 'Default Resources', 'amcharts' ); ?></th>
        <td>
					<fieldset>
						<p>
							<textarea name="default_resources" id="amcharts-default-resources" class="widefat"><?php echo esc_textarea( $settings['default_resources'] ); ?></textarea>
            </p>
						<p>
							<?php
							$libs = preg_split( '/\R/', $settings['resources'] );
							$libs = array_merge( $libs, preg_split( '/\R/', $settings['custom_resources'] ) );
							?>
							<select id="amcharts-select-resource">
								<option value=""><?php _e( 'Select a resource', 'amcharts' ); ?></option>
								<?php
								foreach( $libs as $lib ) {
									?><option value="<?php echo esc_attr( $lib ); ?>"><?php echo $lib; ?></option><?php
								}
								?>
							</select>
							<input type="button" class="button" id="amcharts-add-resource" value="<?php _e( 'Add', 'amcharts' ) ; ?>" />
						</p>
          </fieldset>
        </td>
      </tr>
			
			<tr valign="top">
        <th scope="row"><?php _e( 'Default HTML', 'amcharts' ); ?></th>
        <td>
					<fieldset>
						<p>
							<textarea name="default_html" class="widefat code code-html"><?php echo esc_textarea( $settings['default_html'] ); ?></textarea>
            </p>
						<p>
							<p class="description"><?php _e( 'Enter the default HTML to populate new entries with. Use <strong>%CHART%</strong> symbol for safe and unique chart ids and variables.' ); ?></p>
						</p>
          </fieldset>
        </td>
      </tr>
			
			<tr valign="top">
        <th scope="row"><?php _e( 'Default JavaScript', 'amcharts' ); ?></th>
        <td>
					<fieldset>
						<p>
							<textarea name="default_javascript" class="widefat code code-javascript"><?php echo esc_textarea( $settings['default_javascript'] ); ?></textarea>
            </p>
						<p>
							<p class="description"><?php _e( 'Enter the default JavaScript to populate new entries with. Use <strong>%CHART%</strong> symbol for safe and unique chart ids and variables.' ); ?></p>
						</p>
          </fieldset>
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

/**
 * Returns available resource files
 */

function amcharts_get_available_resources ( $type = 'remote', $paths = '' ) {
	$res = '';
	if ( 'local' == $type ) {
		$dirs = preg_split( '/\R/', $paths );
		
		// libraries
		$libs = array();
		foreach ( $dirs as $path ) {
			$libs = array_merge( $libs, amcharts_get_js_files( ABSPATH . $path, home_url( $path ) ) );
		}
		
		// maps
		reset( $dirs );
		foreach ( $dirs as $path ) {
			$libs = array_merge( $libs, amcharts_get_js_files( ABSPATH . $path . 'maps/js/', home_url( $path . 'maps/js/' ) ) );
		}
		
		$res = implode( "\n", $libs );
	}
	else {
		$res = file_get_contents( 'http://www.amcharts.com/lib/3/resources.php' );
	}
	return $res;
}

function amcharts_get_js_files ( $dir, $path = '' ) {
	$files = scandir( $dir );
	$res = array();
	foreach ( $files as $file ) {
		if ( preg_match( '/\.js$/', $file ) )
			$res[] = $path . $file;
	}
	return $res;
}