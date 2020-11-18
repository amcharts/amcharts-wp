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
		'amcharts_javascript_box',
		__( 'JavaScript', 'amcharts' ),
		'amcharts_javascript_box',
		'amchart'
	);
	
	add_meta_box(
		'amcharts_defaults_box',
		__( 'Apply default code', 'amcharts' ),
		'amcharts_defaults_box',
		'amchart'
	);
	
	add_meta_box(
		'amcharts_misc_box',
		__( 'Chart tools', 'amcharts' ),
		'amcharts_misc_box',
		'amchart',
		'side'
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
	$libs = amcharts_split_libs( $settings['resources'] );
	$libs = array_merge( $libs, amcharts_split_libs( $settings['custom_resources'] ) );
	
	// new?
	if ( amcharts_is_new_post() && ! empty( $_GET['chart_type'] ) ) {
		$post_resources = $settings['chart_types'][$_GET['chart_type']]['default_resources'];
	}
	?>

	<fieldset class="amcharts-resource-group">
		<p>
			<textarea name="resources" id="amcharts-resources" class="widefat amcharts-resources"><?php echo esc_textarea( $post_resources ); ?></textarea>
		</p>
	
		<select class="amcharts-select-resource">
			<option value=""><?php _e( 'Select a resource', 'amcharts' ); ?></option>
			<?php
			foreach( $libs as $lib ) {
				?><option value="<?php echo esc_attr( $lib ); ?>"><?php echo $lib; ?></option><?php
			}
			?>
		</select>
		<input type="button" class="button amcharts-add-resource" value="<?php _e( 'Add', 'amcharts' ) ; ?>" />
	</fieldset>
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
	if ( amcharts_is_new_post() && ! empty( $_GET['chart_type'] ) ) {
		$html = $settings['chart_types'][$_GET['chart_type']]['default_html'];
	}
	?>
	
	<p>
		<textarea name="html" class="widefat code code-html" id="amcharts-html"><?php echo esc_textarea( $html ); ?></textarea>
	</p>
	
	<p class="description">
		<?php _e( 'Please use the following code <strong>$CHART$</strong> in place of the chart IDs or variables. It will be replaced with the proper, safe and unique chart ID when generating the page', 'amcharts' ); ?>
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
	if ( amcharts_is_new_post() && ! empty( $_GET['chart_type'] ) ) {
		$javascript = $settings['chart_types'][$_GET['chart_type']]['default_javascript'];
	}
	?>
	
	<p>
		<textarea name="javascript" class="widefat code code-javascript" id="amcharts-javascript"><?php echo esc_textarea( $javascript ); ?></textarea>
	</p>
	
	<p class="description">
		<?php _e( 'Please use the following code <strong>$CHART$</strong> in place of the chart IDs or variables. It will be replaced with the proper, safe and unique chart ID when generating the page', 'amcharts' ); ?>
	</p>
	
	<?php
}

/**
 * Defaults meta box
 */

function amcharts_defaults_box ( $post ) {
	// get available resources and chart types
	$settings = get_option( 'amcharts_options', amcharts_get_defaults() );
	$chart_types = amcharts_get_chart_types();
	?>
	
	<p>
		<select id="amcharts-chart-type-default">
			<option value=""><?php echo esc_attr( __( 'Select a chart type to apply', 'amcharts' ) ); ?></option>
			<?php foreach ( $chart_types as $chart_type => $chart_type_name ) { ?>
			<option value="<?php echo $chart_type; ?>"><?php echo $chart_type_name; ?></option>
			<?php } ?>
		</select>
		<input type="button" class="button" id="amcharts-apply-default" value="<?php echo esc_attr( __( 'Apply', 'amcharts' ) ); ?>" disabled="disabled" />
	</p>
	
	<p class="description">
		<?php _e( 'ATTENTION! When you select a chart type above and click "Apply", the content in Resources, HTML and JavaScript fields will be overwritten with the defaults set by the website administrator.', 'amcharts' ); ?>
	</p>
	
	<script>
		var amcharts_settings = <?php echo json_encode( $settings['chart_types'] ); ?>;
	</script>
	
	<?php
}

/**
 * HTML meta box
 */

function amcharts_misc_box ( $post ) {
	// nonce field
	wp_nonce_field( AMCHARTS_NONCE, 'amcharts_nonce' );
	
	// new?
	if ( amcharts_is_new_post() ) {
		$slug = amcharts_generate_slug( empty( $_GET['chart_type'] ) ? '' : $_GET['chart_type'] );
	}
	else {
		$slug = get_post_meta( $post->ID, '_amcharts_slug', true );
		if ( '' == $slug )
			$slug = amcharts_generate_slug();
	}
	?>
	<div class="misc-pub-section">
		<strong><label for="amcharts-slug"><?php _e( 'Slug', 'amcharts' ); ?></label></strong><br />
		<input name="slug" type="text" class="widefat" id="amcharts-slug" value="<?php echo esc_attr( $slug ); ?>" />
		<p class="description"><?php _e( 'Use this field to enter a user-friendly slug (ID) for your chart that can be used in shortcodes, i.e. [amcharts id="chart-1"]', 'amcharts' ); ?></p>
	</div>
	<div class="misc-pub-section amcharts-center amcharts-edit-section">
		<a class="button" id="amcharts-preview"><?php _e( 'Preview chart or map', 'amcharts' ); ?></a>
	</div>
	<script>
		var amcharts_preview_url = '<?php echo esc_js( home_url( '?amcharts_preview=1' ) ); ?>';
	</script>
	
	<?php
}

/**
 * Save custom fields
 */

add_action( 'save_post', 'amcharts_save_post', 70 );
function amcharts_save_post ( $post_id ) {
	// checks
	if ( !isset( $_POST['post_type'] ) || !in_array( $_POST['post_type'], array( 'amchart' ) ) ) return;
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
	update_post_meta( $post_id, '_amcharts_slug', trim( $_POST['slug'] ) );
}

/**
 * Add custom admin columns
 */

add_filter( 'manage_posts_columns', 'amcharts_manage_posts_columns', 100, 2 );
function amcharts_manage_posts_columns ( $posts_columns, $post_type = 'post' ) {
	if ( 'amchart' == $post_type )
		$posts_columns['amcharts_shortcode'] = __( 'Shortcode', 'amcharts' );
	
	return $posts_columns;
}

add_filter( 'manage_posts_custom_column', 'amcharts_manage_posts_custom_column', 100, 2 );
function amcharts_manage_posts_custom_column ( $column_name, $post_id ) {
	if ( 'amcharts_shortcode' == $column_name ) {
		if ( $slug = get_post_meta( $post_id, '_amcharts_slug', true ) )
			$post_id = $slug;
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
	if ( isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) {
		add_thickbox();
		wp_enqueue_style( 'amcharts-popup', plugins_url( 'lib/amcharts_popup.css', AMCHARTS_BASE ), array(), AMCHARTS_VERSION );
	}
	?>
	<script>
		var amcharts_prompts = {
			'insert_chart': '<?php echo esc_js( __( 'Insert chart or map', 'amcharts' ) ); ?>',
			'select_chart': '<?php echo esc_js( __( 'Select a chart or map to insert', 'amcharts' ) ); ?>',
			'are_you_sure': '<?php echo esc_js( __( 'Are you sure? This operation cannot be undone.', 'amcharts' ) ); ?>'
		};
		
		var amcharts_chart_types = <?php echo json_encode( amcharts_get_chart_types( amcharts_get_lib_version() ) ); ?>
	</script>
	<?php
}

add_action( 'admin_footer', 'amcharts_admin_footer' );
function amcharts_admin_footer () {
	if ( isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) {
		?>
		<div id="amcharts-popup" style="display: none;">
			<div class="amcharts-popup-content">
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
					<p class="description"><?php _e( 'I.e.: https://live.amcharts.com/NmU2Z/', 'amcharts' ); ?></p>
					<p>
						<input type="text" value="" id="live-editor-width" placeholder="<?php _e( 'Width (default: 400px)', 'amcharts' ); ?>" class="fat" />
						<input type="text" value="" id="live-editor-height" placeholder="<?php _e( 'Height (default: 300px)', 'amcharts' ); ?>" class="fat" />
						<input type="button" id="live-editor-ok" value="<?php _e( 'Insert', 'amcharts' ); ?>" class="button button-primary" />
					</p>
					<p>
						<input type="button" value="<?php _e( 'Open Live Editor', 'amcharts' ); ?>" onclick="window.open('https://live.amcharts.com/');" class="button" />
						<span class="description"><?php _e( 'This will open Live Editor in a new window', 'amcharts' ); ?></span>
					</p>

				</div>
				<div id="tabs-local">
					<input type="text" value="" id="post-search" placeholder="<?php _e( 'Start typing to search', 'amcharts' ); ?>" class="widefat" />
					<div id="results"></div>
				</div>
			</div>
		</div>
		<script>
			jQuery( function( $ ) {
				amchartsUpdateSearchResults();
				$( '#results').on( 'click', '.post', function () {
					window.parent.tinyMCE.activeEditor.execCommand( 'mceInsertContent', false, '[amcharts id="' + this.id + '"]' );
					//parent.tinyMCE.activeEditor.windowManager.close( window );
					tb_remove();
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
						$path = home_url( $path );
						?>
						embed += ' src="<?php echo $path; ?>" tkn="replaceDefault"';
						<?php
					}
					?>
					embed += ']' + url + '[/embed]'
					window.parent.tinyMCE.activeEditor.execCommand( 'mceInsertContent', false, embed );
					//parent.tinyMCE.activeEditor.windowManager.close( window );
					tb_remove();
					console.log("closing");
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
				<?php if ( isset( $_GET['l'] ) ) { ?>
					data.language = '<?php echo esc_js( $_GET['l'] ); ?>';
				<?php } ?>
				jQuery.post(ajaxurl, data, function(response) {
					jQuery('#results').html(response);
				});
			}
		</script>
		<?php
	}
}

/**
 * AJAX handler for post search
 */

add_action( 'wp_ajax_amcharts_get_posts', 'amcharts_get_posts' );
function amcharts_get_posts() {
	$query = array(
		'post_type'					=> 'amchart',
		'posts_per_page'		=> 20,
		//'suppress_filters'	=> false
	);
	if ( '' != $_POST['query'] ) {
		$query['s'] = $_POST['query'];
		?><h2><?php _e( 'Search results', 'amcharts' ); ?></h2><?php
	}
	else {
		?><h2><?php _e( 'Recent charts', 'amcharts' ); ?></h2><?php
	}
	?><ul id="results"><?php
	$posts = get_posts( $query );

	/**
	 * Pre-process for WPML
	 * Combine both default language and current language items
	 */
	if ( function_exists( 'icl_object_id' ) ) {
		$newposts = array();
		global $sitepress;
		$default_language = $sitepress->get_current_language();
		$current_language = $_POST['language'];
		foreach( $posts as $post ) {
			$args = array(
				'element_id'		=> $post->ID,
				'element_type'	=> 'amchart'
			);
			$details = apply_filters( 'wpml_element_language_details', null, $args );
			if ( $details->language_code == $current_language ) {
				$newposts[ $details->trid ] = $post;
			}
			elseif ( $details->language_code == $default_language && ! isset( $newposts[ $details->trid ] ) ) {
				$newposts[ $details->trid ] = $post;
			}
		}
		$posts = array_values( $newposts );
	}

	foreach( $posts as $post ) {
		$id = $post->ID;
		$slug = get_post_meta( $id, '_amcharts_slug', true );
		?>
		<li class="post" id="<?php echo '' != $slug ? esc_attr( $slug ) : $id; ?>"><?php echo $post->post_title; ?></li>
		<?php
	}
	?></ul><?php
	die();
}