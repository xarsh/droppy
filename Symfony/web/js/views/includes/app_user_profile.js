define([
  'jQuery',  
  'Underscore',
  'Backbone',
  'helpers/template_manager',
  'event_manager',
  'app_user',
  'helpers/assets'
], function($, _, Backbone, templateManager, Dispatcher, appUser, Assetics) {
	var AppUserProfileView = Backbone.View.extend({
		
		initialize : function() {
			this.render();
			this.$el.attr('id', 'account_info');
			this.$el.attr('class', 'clearfix');
		},
		
		template : _.template(templateManager.getAppUserProfileTemplate()),
		
		events : {
			'click p.user' : 'showUser',
			'click p.user_name' : 'showUser'
		},
		
		render : function() {
			this.$el.html(this.template(Assetics.wrapAssetics({ appUser : appUser.toJSON() })));
			return this;
		},
		
		showUser : function(e) {
			e.preventDefault();
			Dispatcher.trigger('changePage', 'user', {
				username : appUser.get('username')
			});
		}

	});
	
  return new AppUserProfileView;
});