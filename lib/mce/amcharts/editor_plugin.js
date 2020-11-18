( function() {

	tinymce.create( 'tinymce.plugins.AmChartsPlugin', {

		init: function( ed, url ) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceAmCharts');
			ed.addCommand( 'mceAmCharts', function() {
				w = jQuery( window ).width() * 0.8;
				h = jQuery( window ).height() * 0.8;
				if ( w > 500 ) w = 500;
				if ( h > 400 ) h = 400;
				tb_show( amcharts_prompts.select_chart, "#TB_inline?&width=" + w + "&height=" + h + "&inlineId=amcharts-popup" );
				jQuery( "#TB_ajaxContent" ).css( "width", "" ).css( "height", "" )
			} );

			// Register amcharts button
			ed.addButton( 'amcharts', {
				title: amcharts_prompts.insert_chart,
				cmd: 'mceAmCharts',
				image: url + '/img/charts.png'
			} );

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add( function( ed, cm, n ) {
				cm.setActive( 'amcharts', n.nodeName == 'IMG' );
			} );
		},

		createControl: function( n, cm ) {
			return null;
		},

		getInfo: function() {
			return {
				longname: 'AmCharts plugin',
				author: 'amCharts',
				authorurl: 'http://www.amcharts.com',
				infourl: 'http://www.amcharts.com',
				version: "1.0"
			};
		}
	} );

	// Register plugin
	tinymce.PluginManager.add( 'amcharts', tinymce.plugins.AmChartsPlugin );
} )();