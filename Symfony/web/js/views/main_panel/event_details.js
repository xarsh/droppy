define([
  'jQuery',  
  'Underscore',
  'Backbone',
  'model/event',
  'helpers/template_manager',
  'event_manager',
  'helpers/assets'
], 	function($, _, Backbone, Event, templateManager, Dispatcher, Assetics) {
		var EventDetailsView = Backbone.View.extend({

			initialize : function() {
				this.model = new Event;
				this.loaded = false;
			},
			
			el : 'div#event',

			events : {
				'click div.event_username' : 'showUserDetails',
				'click .button_big' : 'back',
				'click span.button_small.edit' : 'editEvent',
				'click span.button_small.delete' : 'deleteEvent',
				'click span.button_small.drop' : 'toggleDrop',
				'click div.event_description a' : 'openLink'
			},
			
			update : function(id) {
				Dispatcher.trigger('loading');
				this.model.clear();
				this.model.set('id', id);
				var container = this;
				this.model.fetch({
					success : function(model, response) {
						Dispatcher.trigger('endLoading');
						Dispatcher.trigger('loaded', container);
					}
				});
			},
			
			openLink : function(e) {
				e.preventDefault();
				var $link = $(e.target);
				window.open($link.attr('href'));
			},
			
			editEvent : function(e) {
				Dispatcher.trigger('editEvent', this.model);
			},
			
			deleteEvent : function(e) {
				this.model.destroy();
				Dispatcher.trigger('changePage', 'top');
			},
			
			toggleDrop : function() {
				if(this.model.get('loading')) {
					return;
				}
				if(this.model.get('dropped')) {
					Dispatcher.trigger('undrop', this.model, this.$('span.button_small'));
				} else {
					Dispatcher.trigger('drop', this.model, this.$('span.button_small'));
				}
			},

			template : _.template(templateManager.getEventDetailsTemplate()),

			render : function() {
				this.$('div.event_details').html(this.template(Assetics.wrapAssetics(this.model.display())));
				return this;
			},
			
			showUserDetails : function(e) {
				e.stopPropagation();
				e.preventDefault();
				Dispatcher.trigger('changePage', 'user', {
						username : this.model.get('creator').get('username')
				});
			},
			
			show : function() {
				this.render();
				this.loaded = true;
				this.$el.show();
			},

			closeEvent : function(e) {
				this.model.undrop();
			},
			
			back : function() {
				Dispatcher.trigger('back');
			}			
		});
		
	  return new EventDetailsView;
});