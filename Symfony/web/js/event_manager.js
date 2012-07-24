define([
  'jQuery',  
  'Underscore',
  'Backbone'   
], function($, _, Backbone){
	var Dispatcher = _.clone(Backbone.Events);
	return Dispatcher;
});