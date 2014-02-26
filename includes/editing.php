<?php
/**
 * Add meta boxes for amchart CPT
 */

add_action( 'add_meta_boxes', 'amcharts_meta_boxes' );

function amcharts_meta_boxes () {
  add_meta_box(
    'amcharts_resources_box',
    __( 'Resources', 'amcharts' ),
    'amcharts_resources_box',
    'amchart'
  );
	
	add_meta_box(
    'amcharts_html_box',
    __( 'HTML', 'amcharts' ),
    'amcharts_html_box',
    'amchart'
  );
	
	add_meta_box(
    'amcharts_havascript_box',
    __( 'JavaScript', 'amcharts' ),
    'amcharts_javascript_box',
    'amchart'
  );
}

/**
 * Resources meta box
 */

function amcharts_resources_box ( $post ) {
  // nonce field
  wp_nonce_field( AMCHARTS_NONCE, 'amcharts_nonce' );
  
  // get post data
  $post_resources = get_post_meta( $post->ID, '_amcharts_resources', true );
	
	// get available resources
	$settings = get_option( 'amcharts_options', amcharts_get_defaults() );
	
	// get libs
	$libs = preg_split( '/\R/', $settings['resources'] );
	$libs = array_merge( $libs, preg_split( '/\R/', $settings['custom_resources'] ) );
	
	// new?
	if ( amcharts_is_new_post() ) {
		$post_resources = $settings['default_resources'];
	}
  ?>
	
	<p>
		<textarea name="resources" class="widefat" id="amcharts-resources"><?php echo esc_textarea( $post_resources ); ?></textarea>
	</p>
	<select id="amcharts-select-resource">
		<option value=""><?php _e( 'Select a resource', 'amcharts' ); ?></option>
		<?php
		foreach( $libs as $lib ) {
			?><option value="<?php echo esc_attr( $lib ); ?>"><?php echo $lib; ?></option><?php
		}
		?>
	</select>
	<input type="button" class="button" id="amcharts-add-resource" value="<?php _e( 'Add', 'amcharts' ) ; ?>" />
  
  <?php
}

/**
 * HTML meta box
 */

function amcharts_html_box ( $post ) {
  // nonce field
  wp_nonce_field( AMCHARTS_NONCE, 'amcharts_nonce' );
  
  // get post data
  $html = get_post_meta ( $post->ID, '_amcharts_html', true );
	
	// get settings
	$settings = get_option( 'amcharts_options', amcharts_get_defaults() );
	
	// new?
	if ( amcharts_is_new_post() ) {
		$html = $settings['default_html'];
	}
  ?>
	
	<p>
		<textarea name="html" class="widefat code code-html"><?php echo esc_textarea( $html ); ?></textarea>
	</p>
	
	<p class="description">
		<?php _e( 'Please use the following code <strong>%CHART%</strong> in place of the chart IDs or variables. It will be replaced with the proper, safe and unique chart ID when generating the page', 'amcharts' ); ?>
	</p>
  
  <?php
}

/**
 * JavaScript meta box
 */

function amcharts_javascript_box ( $post ) {
  // nonce field
  wp_nonce_field( AMCHARTS_NONCE, 'amcharts_nonce' );
  
  // get post data
  $javascript = get_post_meta ( $post->ID, '_amcharts_javascript', true );
	
	// get available resources
	$settings = get_option( 'amcharts_options', amcharts_get_defaults() );
	
	// new?
	if ( amcharts_is_new_post() ) {
		$javascript = $settings['default_javascript'];
	}
  ?>
	
	<p>
		<textarea name="javascript" class="widefat code code-javascript"><?php echo esc_textarea( $javascript ); ?></textarea>
	</p>
	
	<p class="description">
		<?php _e( 'Please use the following code <strong>%CHART%</strong> in place of the chart IDs or variables. It will be replaced with the proper, safe and unique chart ID when generating the page', 'amcharts' ); ?>
	</p>
  
  <?php
}

/**
 * Save custom fields
 */

add_action( 'save_post', 'amcharts_save_post', 70 );
function amcharts_save_post ( $post_id ) {
  // checks
	if ( !in_array( $_POST['post_type'], array( 'amchart' ) ) ) return;
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
  if ( !wp_verify_nonce( $_POST['amcharts_nonce'], AMCHARTS_NONCE ) ) return;
  if ( !current_user_can( 'edit_page', $post_id ) && !current_user_can( 'edit_post', $post_id ) ) return;
  
  // revision
  if ( $tmp_post_id = wp_is_post_revision( $post_id ) ) {
    // we need to do this because revision is saved BEFORE the actual post
    // so the custom fields need to be saved at the point when the revision
    // is saved so it's properly saved into revision data
    $post_id = $tmp_post_id;
  }
  
  // now let's save
	update_post_meta( $post_id, '_amcharts_resources', trim( $_POST['resources'] ) );
	update_post_meta( $post_id, '_amcharts_html', trim( $_POST['html'] ) );
	update_post_meta( $post_id, '_amcharts_javascript', trim( $_POST['javascript'] ) );
}

/**
 * Add custom admin columns
 */

add_filter( 'manage_posts_columns', 'amcharts_manage_posts_columns', 100, 2 );
function amcharts_manage_posts_columns ( $posts_columns, $post_type = 'post' ) {
  $posts_columns['amcharts_shortcode'] = __( 'Shortcode', 'amcharts' );
  return $posts_columns;
}

add_filter( 'manage_posts_custom_column', 'amcharts_manage_posts_custom_column', 100, 2 );
function amcharts_manage_posts_custom_column ( $column_name, $post_id ) {
  if ( 'amcharts_shortcode' == $column_name ) {
    echo '[amcharts id="' . $post_id . '"]';
  }
}

/**
 * Add a button to editor to easily insert shortcodes
 */

add_action( 'init', 'amcharts_add_mce_plugins' );

function amcharts_add_mce_plugins() {
  if ( ( current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' ) ) && get_user_option( 'rich_editing' ) ) {
		add_filter( 'mce_external_plugins', 'amcharts_tinymce_plugin' );
		add_filter( 'mce_buttons', 'amcharts_register_myplugin_button' );
  }
}
 
function amcharts_register_myplugin_button( $buttons ) {
  array_push( $buttons, 'separator', 'amcharts' );
  return $buttons;
}
 
function amcharts_tinymce_plugin( $plugin_array ) {
  $plugin_array['amcharts'] = AMCHARTS_BASE_URL . '/lib/mce/amcharts/editor_plugin.js';
  return $plugin_array;
}

/**
 * Add localized strings to be used by static scripts
 */

add_action( 'admin_head', 'amcharts_admin_head' );
function amcharts_admin_head () {
	?>
	<script>
		var amcharts_prompts = {
			'insert_chart': '<?php echo esc_js( __( 'Insert chart or map', 'amcharts' ) ); ?>',
			'select_chart': '<?php echo esc_js( __( 'Select a chart or map to insert', 'amcharts' ) ); ?>',
		};
	</script>
	<?php
}