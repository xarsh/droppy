define([
  'jQuery',  
  'Underscore',
  'Backbone',
  'helpers/template_manager',
  'event_manager',
  'helpers/assets'
], function($, _, Backbone, templateManager, Dispatcher, Assetics){
	var SingleEventView = Backbone.View.extend({
		tagName : 'div',
		
		initialize : function() {
			this.showCreator = (typeof this.options.showCreator !== 'undefined') ?
					this.options.showCreator : true;
			this.template = _.template(templateManager.getTemplate(this.options.templateName));
		},
		
		events : {
			'click div.title' : 'showEvent',
			'click span.edit' : 'editEvent',
			'click span.delete' : 'deleteEvent',
			'click div.button_small' : 'toggleDrop',
			'click p.user' : 'showUser'
		},

		render : function() {
			var modelToRender = this.model.display();
			modelToRender.options = {
				showCreator : this.showCreator
			};
			this.$el.html(this.template(Assetics.wrapAssetics(modelToRender)));
			return this;
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
				Dispatcher.trigger('undrop', this.model, $target, false);
			} else {
				Dispatcher.trigger('drop', this.model, $target, false);
			}
		},
		
		showEvent : function(e) {
			e.preventDefault();
			Dispatcher.trigger('changePage', 'event', {
				id : this.model.get('id')
			});
		},
		
		editEvent : function(e) {
			Dispatcher.trigger('editEvent', this.model);
		},
		
		deleteEvent : function(e) {
			this.model.destroy();
			this.$el.remove();
		},
		
		showUser : function(e) {
			e.preventDefault();
			Dispatcher.trigger('changePage', 'user', {
				username : this.model.get('creator').get('username')
			});
		}
	});
	
  return SingleEventView;
});
