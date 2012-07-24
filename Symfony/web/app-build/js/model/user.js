define([
  'jQuery',  
  'Underscore',
  'Backbone',
  'Sync'
], function($, _, Backbone, Sync){
	var User = Backbone.RelationalModel.extend({
		parse: function(response) {
			var user = Object.keysToCamelCase(response.user);
			user.dropped = response.dropped;
			return user;
		},
		
		setFromJSON : function(json) {
			var user = this.parse(json);
			this.set(user);
		},
		
		drop: function(options) {
			_.extend(options, {
				url : Routing.generate('droppy_user_ajax_drop'),
				dataType : 'json'
			});
			Sync.makeRequest('create', {user_id: this.get('id')}, options);
		},		
		
		undrop: function(options) {
			_.extend(options, {
				url : Routing.generate('droppy_user_ajax_undrop'),
				dataType : 'json'
			});
			Sync.makeRequest('delete', {user_id: this.get('id')}, options);
		},
		
		sync : function(method, model, options) {
			var urlList = {
				'read' : Routing.generate('droppy_user_ajax_get_by_username', { username : this.get('username')}),
			};
			options.url = urlList[method];
			return Backbone.sync(method, model, options);
		}
	});
  return User;
});

