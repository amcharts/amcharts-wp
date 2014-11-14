<?php
/**
 * The template for reviewing amCharts chart
 */

// enqueue resources
$libs = amcharts_split_libs( amcharts_stripslashes( $_POST['amcharts_resources'] ) );
foreach ( $libs as $lib ) {
	wp_enqueue_script( 'amcharts-external-' . md5( basename( $lib ) ), $lib, array(), AMCHARTS_VERSION, true );
}

// enqueue JavaScript part
amcharts_increment_instance();
amcharts_enqueue_javascript( amcharts_parse_code( amcharts_stripslashes( $_POST['amcharts_javascript'] ) ) );
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