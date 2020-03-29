( function( blocks, element ) {
  var el = element.createElement;

  var blockStyle = {
    // backgroundColor: '#900',
    // color: '#fff',
    // padding: '20px',
  };

  blocks.registerBlockType( 'gutenberg-examples/example-01-basic', {
    title: 'amCharts Chart',
    icon: 'universal-access-alt',
    category: 'layout',
    example: {},
    edit: function() {
      return el(
        'p', {
          style: blockStyle
        },
        '[amcharts id="xy-1"]'
      );
    },
    save: function() {
      return el(
        'p', {
          style: blockStyle
        },
        '[amcharts id="xy-1"]'
      );
    },
  } );
}(
  window.wp.blocks,
  window.wp.element
) );