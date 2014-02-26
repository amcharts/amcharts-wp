(function() {
        // Load plugin specific language pack
        //tinymce.PluginManager.requireLangPack('amcharts');

        tinymce.create('tinymce.plugins.AmChartsPlugin', {
                /**
                 * Initializes the plugin, this will be executed after the plugin has been created.
                 * This call is done before the editor instance has finished it's initialization so use the onInit event
                 * of the editor instance to intercept that event.
                 *
                 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
                 * @param {string} url Absolute URL to where the plugin is located.
                 */
                init : function(ed, url) {
                        // Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceAmCharts');
                        ed.addCommand('mceAmCharts', function() {
                          w = jQuery(window).width() * 0.8;
                          h = jQuery(window).height() * 0.8;
                          if ( w > 500 ) w = 500;
                          if ( h > 400 ) h = 400;
                            ed.windowManager.open({
                              file : url + '/charts.php',
                              title: amcharts_prompts.select_chart,
                              width : w + ed.getLang('amcharts.delta_width', 0),
                              height : h + ed.getLang('amcharts.delta_height', 0),
                              scrollbars: true,
                              inline : 1
                            }, {
                              plugin_url : url, // Plugin absolute URL
                              some_custom_arg : 'custom arg' // Custom argument
                            });
                        });

                        // Register amcharts button
                        ed.addButton('amcharts', {
                          title : amcharts_prompts.insert_chart,
                          cmd : 'mceAmCharts',
                          image : url + '/img/charts.png'
                        });

                        // Add a node change handler, selects the button in the UI when a image is selected
                        ed.onNodeChange.add(function(ed, cm, n) {
                          cm.setActive('amcharts', n.nodeName == 'IMG');
                        });
                },

                /**
                 * Creates control instances based in the incomming name. This method is normally not
                 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
                 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
                 * method can be used to create those.
                 *
                 * @param {String} n Name of the control to create.
                 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
                 * @return {tinymce.ui.Control} New control instance or null if no control was created.
                 */
                createControl : function(n, cm) {
                        return null;
                },

                /**
                 * Returns information about the plugin as a name/value array.
                 * The current keys are longname, author, authorurl, infourl and version.
                 *
                 * @return {Object} Name/value array containing information about the plugin.
                 */
                getInfo : function() {
                        return {
                                longname : 'AmCharts plugin',
                                author : 'amCharts',
                                authorurl : 'http://www.amcharts.com',
                                infourl : 'http://www.amcharts.com',
                                version : "1.0"
                        };
                }
        });

        // Register plugin
        tinymce.PluginManager.add('amcharts', tinymce.plugins.AmChartsPlugin);
})();