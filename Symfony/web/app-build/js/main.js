require.config({
  baseUrl: "js",
  paths: {
	order: 'libs/requirejs-plugins/order',
	text: 'libs/requirejs-plugins/text',
    jQuery: 'libs/jquery/jquery',
    Underscore: 'libs/underscore/underscore',
    Backbone: 'libs/backbone/backbone',
    templates: '../templates',
    Sync: 'helpers/sync'
  }
});

require([
  'app',
  'event_manager',
  'order!https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js',
  'order!libs/underscore/underscore-min',
  'order!libs/backbone/backbone-min',
  'helpers/objects_extension',
  'helpers/date_extension',
  'helpers/assets'
], function(App){
	App.initialize();
});
