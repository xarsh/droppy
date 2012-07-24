define([
  'jQuery',  
  'Underscore',
  'Backbone',
  'model/user'
], function($, _, Backbone, User){
	var UserList = Backbone.Collection.extend({
		model : User,
		
		sync : function(method, model, options) {
			var params = ('params' in this) ? this.params : {}; 
			options.url = Routing.generate(this.routeList[method], params);
			return Backbone.sync(method, model, options);
		}
	});
  return UserList;
});
