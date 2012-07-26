define([
  'jQuery',  
  'Underscore',
  'Backbone',
  'helpers/template_manager',
  'event_manager',
  'app_user',
  'helpers/assets'
], function($, _, Backbone, templateManager, Dispatcher, appUser, Assetics) {
	var SingleUserView = Backbone.View.extend({
		tagName : 'div',
		
		initialize : function() {
			this.template = _.template(templateManager.getTemplate(this.options.templateName));
		},
		
		events : {
			'click div.names' : 'showUser',
			'click div.button_small' : 'toggleDrop'
		},

		render : function() {
			this.$el.html(this.template(Assetics.wrapAssetics(_.extend({}, 
					{ appUser : appUser.toJSON() }, this.model.toJSON()))));
			return this;
		},
		
		showUser : function(e) {
			e.preventDefault();
			Dispatcher.trigger('changePage', 'user', {
				username : this.model.get('username')
			});
		},
		
		toggleDrop : function(e) {
			$target = $(e.target);
			if(!$target.is('div')) {
				$target = $target.parent();
			}
			if(this.model.get('loading')) {
				return;
			}
			if(this.model.get('dropped')) {
				Dispatcher.trigger('undrop', this.model, $target, true);
			} else {
				Dispatcher.trigger('drop', this.model, $target, true);
			}
		}
	});
	
  return SingleUserView;
});
