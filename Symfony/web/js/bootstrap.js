define([
  'jQuery',
  'Underscore',
  'config'
], function($, _, config) {

  var bootstrap = {
  };
  for(var i in config) {
	  config[i] = JSON.parse(config[i]); 
  }
  _.extend(bootstrap, config);
  return bootstrap;
});
