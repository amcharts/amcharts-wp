var amcharts_code = {};
jQuery(document).ready(function ($) {
  $( '#amcharts-location-remote' ).click( function () {
    $( '#amcharts-path-group,#amcharts-live-editor-group' ).fadeOut();
  } );
  
  $( '#amcharts-location-local' ).click( function () {
    $( '#amcharts-path-group,#amcharts-live-editor-group' ).fadeIn();
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
  
  $( '.amcharts-add-resource' ).click( function () {
    var parent = $(this).parents('.amcharts-resource-group');
    var lib = parent.find( '.amcharts-select-resource' ).val();
    var fld = parent.find( '.amcharts-resources' );
    if ( '' != lib && -1 == fld.val().indexOf( lib ) ) {
      
      if ( fld.val().length )
        lib = "\n" + lib;
      
      fld.val( fld.val() + lib );
    }
  } );
  
  $( '.amcharts-edit-myself' ).click( function () {
    var parent = $(this).parents('.amcharts-tab');
    if ( this.checked ) {
      parent.find( '.amcharts-resource-list' ).hide();
      parent.find( '.amcharts-resource-group' ).show();
    }
    else {
      parent.find( '.amcharts-resource-list' ).show();
      parent.find( '.amcharts-resource-group' ).hide();
    }
  } );
  
  $( 'textarea.code' ).each( function () {
    var cfg = {
      mode: $(this).hasClass( 'code-html' ) ? 'htmlmixed' : 'javascript',
      lineNumbers: true
    };
    amcharts_code[this.name] = CodeMirror.fromTextArea( this, cfg );
  } );
  
  if ( undefined != $().tabs )
    $( '#amcharts-tabs' ).tabs();
  
  $( '#menu-posts-amchart li:last' ).each( function () {
    var elem = $( this );
    var url = elem.find( 'a' ).attr( 'href' );
    var list = $( '<ul>' );
    for ( type in amcharts_chart_types ) {
      list.append( '<li><a href="' + url + '&chart_type=' + type + '">+ ' + amcharts_chart_types[type] + '</a></li>' );
    }
    list.insertAfter( elem );
  });
  
  $( '#amcharts-chart-type-default' ).change( function () {
    if ( '' == $( this ).val() )
      $( '#amcharts-apply-default' ).prop( 'disabled', true );
    else
      $( '#amcharts-apply-default' ).prop( 'disabled', false );
  } );
  
  $( '#amcharts-apply-default' ).click( function () {
    if ( ! confirm( amcharts_prompts.are_you_sure ) )
      return;
    
    var type = $( '#amcharts-chart-type-default' ).val();
    if ( '' == type )
      return;
    
    var defaults = amcharts_settings[type];
    $( '#amcharts-resources' ).html( defaults.default_resources );
    amcharts_code['html'].setValue( defaults.default_html );
    amcharts_code['html'].getInputField().innerHTML = defaults.default_html;
    amcharts_code['javascript'].setValue( defaults.default_javascript );
    amcharts_code['javascript'].getInputField().innerHTML = defaults.default_javascript;
  } );
  
  $( '#amcharts-preview' ).click( function () {
    amcharts_flush_code();
    var form = $( '<form>', {
      action: amcharts_preview_url,
      method: 'post',
      target: 'amcharts_preview'
    }).hide().appendTo('body').append(
      $( '<input>', {
        type: 'hidden',
        name: 'amcharts_resources',
        value: $( '#amcharts-resources' ).val()
      } )
    ).append(
      $( '<input>', {
        type: 'hidden',
        name: 'amcharts_html',
        value: $( '#amcharts-html' ).val()
      } )
    ).append(
      $( '<input>', {
        type: 'hidden',
        name: 'amcharts_javascript',
        value: $( '#amcharts-javascript' ).val()
      } )
    ).submit().remove();
  });
  
  function amcharts_flush_code() {
    for ( var x in amcharts_code ) {
      amcharts_code[x].save();
    }
  }
});