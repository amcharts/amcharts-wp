<?php
/**
 * The template for reviewing amCharts chart
 */

// enqueue resources
$libs = amcharts_split_libs( amcharts_stripslashes( $_POST['amcharts_resources'] ) );
foreach ( $libs as $lib ) {
	if ( preg_match( '/\\.css/i', $lib ) )
		wp_enqueue_style( 'amcharts-external-' . md5( basename( $lib ) ), $lib, array(), AMCHARTS_VERSION );
	else
		wp_enqueue_script( 'amcharts-external-' . md5( basename( $lib ) ), $lib, array(), AMCHARTS_VERSION, true );
}

// enqueue JavaScript part
amcharts_increment_instance();
$javascript = amcharts_parse_code( amcharts_stripslashes( $_POST['amcharts_javascript'] ) );
$settings = get_option( 'amcharts_options' );
if ( isset( $settings['wrap'] ) && '1' == $settings['wrap'] )
	$javascript = "try {\n" . $javascript . "\n}\ncatch( err ) { console.log( err ); }";
amcharts_enqueue_javascript( $javascript );
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<title><?php _e( 'amCharts Preview', 'amcharts' ); ?></title>
		<?php wp_head(); ?>
	</head>
	<body>
		
		<!-- HTML -->
		<?php	echo amcharts_parse_code( amcharts_stripslashes( $_POST['amcharts_html'] ) ); ?>
		
		<?php wp_footer(); ?>
	</body>
</html>