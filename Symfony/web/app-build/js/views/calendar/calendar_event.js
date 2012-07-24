define([
  'jQuery',  
  'Underscore',
  'Backbone',
  'views/calendar/event_details_popup',
  'helpers/template_manager',
  'helpers/assets',
], function($, _, Backbone, eventDetailsPopup, templateManager, Assetics) {
	var CalendarEventView = Backbone.View.extend({
		tagName : 'div',
		
		initialize : function() {
			this.height = 16;
			var self = this;
			this.model.on('out', function() {
				self.remove();
			});
		},

		events : {
			'click' : 'showDetailsPopup'
		},

		template : _.template(templateManager.getCalendarEventTemplate()),

		display : function(options) {
			var toDisplay = this.model.display();
			var cellInfos = _.extend(toDisplay, {
				options : {
					today : Date.getToday(),
					showTime : options.showTime,
					top : options.top + options.eventsAbove * (this.height + 2),
					width : options.width,
					height : this.height,
					left : options.left,
					preceded : options.preceded,
					followed : options.followed,
					allDay : options.allDay
				}
			});
			this.$el.append(this.template(Assetics.wrapAssetics(cellInfos)));
			return this;
		},
		
		showDetailsPopup : function(e) {
			e.stopPropagation();
			eventDetailsPopup.setModel(this.model);
			eventDetailsPopup.render();
			if(!eventDetailsPopup.displayed) {
				$('div#col3').append(eventDetailsPopup.el);
			}
			eventDetailsPopup.open(e.pageX, e.pageY);
		}
	});
	
  return CalendarEventView;
});
