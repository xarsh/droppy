define([
  'jQuery',  
  'Underscore',
  'Backbone',
  'model/user'
], function($, _, Backbone, User){
	var UserList = Backbone.Collection.extend({
		model : User,
		
		initialize : function() {
			this.allLoaded = false;
			this.loading = false;
			if(typeof this.params === 'undefined') {
				this.params = {};
			}
		},
		
		loadMore : function() {
			if(this.loading || this.allLoaded) {
				return;
			}
			_.extend(this.params, { offset : this.length });
			this.loading = true;
			var self = this;
			this.fetch({
				add : true,
				success : function(method, model) {
					self.loading = false;
					if(model.length === 0) {
						self.allLoaded = true;
					}
				}
			});
		},
		
		sync : function(method, model, options) {
			var params = ('params' in this) ? this.params : {}; 
			options.url = Routing.generate(this.routeList[method], params);
			return Backbone.sync(method, model, options);
		}
	});
  return UserList;
});
