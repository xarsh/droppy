define([
    'order!libs/underscore/underscore-min',
  'order!libs/backbone/backbone-min',
  'order!libs/backbone/backbone-relational'
], function(){
	_.noConflict();
	$.noConflict();
  return Backbone.noConflict();
});
