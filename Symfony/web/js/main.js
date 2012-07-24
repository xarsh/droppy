require.config({
  baseUrl: "js",
  
  paths: {
	tpl : 'libs/tpl/tpl',
	order: 'libs/requirejs-plugins/order',
	text: 'libs/requirejs-plugins/text',
    jQuery: 'libs/jquery/jquery',
    Underscore: 'libs/underscore/underscore',
    Backbone: 'libs/backbone/backbone-amd',
    templates: '../templates',
    Sync: 'helpers/sync'
  }
});

require([
  'app',
  'helpers/objects_extension',
  'helpers/date_extension'
], function(App){
	App.initialize();
});
