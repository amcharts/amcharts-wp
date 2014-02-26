jQuery(document).ready(function ($) {
  $( '#amcharts-location-remote' ).click( function () {
    $( '#amcharts-path-group' ).fadeOut();
  } );
  
  $( '#amcharts-location-local' ).click( function () {
    $( '#amcharts-path-group' ).fadeIn();
  } );
  
  $( '#amcharts-find-path' ).click( function () {
    $( this ).prop( 'disabled', 'disabled' );
    $( '#amcharts-path-working' ).fadeIn();
    $( '#amcharts-find-path-error').hide();
    var data = {
      action: 'amcharts_find_me'
    };
    
    $.post( ajaxurl, data, function( response ) {
      if ( '' == response ) {
        $( '#amcharts-find-path-error').fadeIn();
      }
      $( '#amcharts-find-path' ).prop( 'disabled', false );
      $( '#amcharts-path' ).val( response );
      $( '#amcharts-path-working' ).fadeOut();
    });
  } );
  
  $( '#amcharts-rescan-resources' ).click( function () {
    $( '#amcharts-refresh' ).val( '1' );
  } );
  
  $( '#amcharts-add-resource' ).click( function () {
    var lib = $( '#amcharts-select-resource' ).val();
    var fld = $( '#amcharts-resources,#amcharts-default-resources' );
    if ( '' != lib && -1 == fld.val().indexOf( lib ) ) {
      
      if ( fld.val().length )
        lib = "\n" + lib;
      
      fld.val( fld.val() + lib );
    }
  } );
  
  $( 'textarea.code' ).each( function () {
    var cfg = {
      mode: $(this).hasClass( 'code-html' ) ? 'htmlmixed' : 'javascript',
      lineNumbers: true
    };
    CodeMirror.fromTextArea( this, cfg );
  } );
});