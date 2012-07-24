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
	var EventCreationPopupView = Popup.extend({
		
		initialize : function() {
			this.date = new Date;
			this.events = _.extend({}, Popup.prototype.events, this.events);
			this.keyPressCount = 0;
			Popup.prototype.initialize(this.$el);
		},
		
		template : _.template(templateManager.getEventCreationPopupTemplate()),
		
		events : {
			'keyup input.title' : 'updateModel',
			'keypress input.title' : 'increaseKeyPressCount',
			'click span.button_big' : 'saveAndQuit',
			'click a.setting_details' : 'toDetails'
		},
		
		increaseKeyPressCount : function(e) {
			var code = e.which || e.keycode;
			if(code !== 241 && code !== 242) {
				this.keyPressCount++;
			}
		},

		setDate : function(dateString) {
			var date = Date.parse(dateString);
			date = isNaN(date) ? new Date : new Date(date);
			this.date = date;
		},
		
		updateModel : function(e) {
			var code = e.which || e.keycode;
			e.preventDefault();
			this.model.set('name', $(e.target).val());
			this.keyPressCount--;
			if(this.keyPressCount < 0) {
				this.keyPressCount = 0;
			} else {
				this.model.set('name', $(e.target).val());
				if(code === 13) {
					this.saveAndQuit();
				} 
			}			
		},
		
		saveAndQuit : function() {
			this.close();
			this.model.set('name', this.$('input.title').val());
			this.model.save({}, {
				success : function() {
					Dispatcher.trigger('success', ExposeTranslation.get('common.event_update_success'));
				}
			});
		},
		
		render : function() {
			this.model = new Event;
			var start = this.model.get('startDateTime').get('date');
			var end = this.model.get('endDateTime').get('date');
			start.setFullYear(this.date.getFullYear(), this.date.getMonth(), this.date.getDate());
			end.setFullYear(this.date.getFullYear(), this.date.getMonth(), this.date.getDate());
			this.$el.html(this.template(Assetics.wrapAssetics({ date : this.date })));
			this.delegateEvents(this.events);
			return this;
		}
		
	});
	
  return new EventCreationPopupView;
});
