define([
  'jQuery',  
  'Underscore',
  'Backbone',
  'views/calendar/popup',
  'model/event',
  'helpers/template_manager',
  'event_manager',
  'helpers/assets'
], function($, _, Backbone, Popup, Event, templateManager, Dispatcher, Assetics) {
	var EventDetailsPopupView = Popup.extend({
		
		initialize : function() {
			this.events = _.extend({}, Popup.prototype.events, this.events);
			Popup.prototype.initialize(this.$el);
			this.displayed = false;
		},
		
		template : _.template(templateManager.getEventDetailsPopupTemplate()),
		
		setModel : function(model) {
			this.model = model;
		},
		
		events : {
			'click a.setting_details' : 'showEvent',
			'click span.button_small.edit' : 'editEvent',
			'click span.button_small.delete' : 'deleteEvent',
			'click span.button_small.toggle_drop' : 'toggleDrop',
			'click span.button_small.in' : 'outOfCalendar'
		},
		
		editEvent : function(e) {
			this.close();
			Dispatcher.trigger('editEvent', this.model);
		},
		
		deleteEvent : function(e) {
			this.close();
			this.model.trigger('out');
			this.model.destroy();
		},
		
		toggleDrop : function() {
			if(this.model.get('loading')) {
				return;
			}
			if(this.model.get('dropped')) {
				Dispatcher.trigger('undrop', this.model, this.$('span.button_small.drop'));
			} else {
				Dispatcher.trigger('drop', this.model, this.$('span.button_small.drop'));
			}
			this.close();
			this.model.trigger('out');
		},
		
		outOfCalendar : function() {
			this.close();
			this.model.outOfCalendar();
			this.model.trigger('out');
		},
		
		render : function() {
			this.$el.html(this.template(Assetics.wrapAssetics(this.model.display())));
			this.delegateEvents(this.events);
			return this;
		},
		
		showEvent : function(e) {
			e.preventDefault();
			this.close();
			Dispatcher.trigger('changePage', 'event', {
				id : this.model.get('id')
			});
		}
		
	});
	
  return new EventDetailsPopupView;
});
