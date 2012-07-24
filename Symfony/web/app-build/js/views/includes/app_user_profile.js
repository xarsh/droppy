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
		
		el : 'div#col1_content',
		
		initialize : function() {
			this.$el.append(this.template(Assetics.wrapAssetics({ appUser : appUser })));
		},
		
		template : _.template(templateManager.getAppUserProfileTemplate()),
		
		events : {
			'click p.user' : 'showUser',
			'click p.user_name' : 'showUser'
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