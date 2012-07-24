define([
  'jQuery',  
  'Underscore',
  'Backbone',
  'model/event',
  'event_manager',
  'bootstrap'
], function($, _, Backbone, Event, Dispatcher, bootstrap) {
	var CalendarEvents = Backbone.Collection.extend({
		model : Event,
		
		initialize : function() {
			this.date = new Date;
			this.params = {};
			if(bootstrap.bootstrappedCalendar) {
				this.setFromJSON(bootstrap.bootstrappedCalendar);
			}
			Dispatcher.on('calendarAdd', this.silentAdd, this);
			Dispatcher.on('outOfCalendar', this.silentRemove, this);
		},
		
		setFromJSON : function(json) {
			for(var i in json) {
				var event = new Event;
				event.setFromJSON(json[i]);
				this.add(event, { silent : true });
			}
			this.trigger('reset');
		},
		
		getParams : function() {
			var params = _.extend(this.params, {
				start : this.date.firstDateToDisplay().format('Y-m-d'),
				end: this.date.lastDateToDisplay().format('Y-m-d')
			});
			return params;
		},
		
		silentAdd : function(toAdd) {
			this.add(toAdd, { silent : true });
			this.trigger('add');
		},
		
		silentRemove : function(toRemove) {
			this.remove(toRemove, { silent : true });
			this.trigger('remove');
		},
		
		comparator : function(event, other) {
			return -1 * event.compareDates(other);
		},
		
		routeList : {
			read : 'droppy_event_ajax_interval'
		},
		
		getEventsInMonth : function() {
			var start = this.date.firstDateToDisplay();
			var end = this.date.lastDateToDisplay();
			return this.filter(function(event) {
				return event.get('endDateTime').get('date') >= start && 
						event.get('startDateTime').get('date') <= end;
			});
		},
		
		getEventsNumberOnDate : function(date) {
			return this.filter(function(event) {
				return event.printed && (date.dateEquals(event.get('startDateTime').get('date')) ||  
					(event.get('endDateTime').get('date') >= date  &&
						event.get('startDateTime').get('date') <= date));
			}).length;
		},
		
		resetPrintedStatus : function() {
			this.map(function(event) {
				event.printed = false;
			});
		},
		
		setDate : function(date) {
			this.date = date;
		},
		
		sync : function(method, model, options) {
			var params = this.getParams();
			options.url = Routing.generate(this.routeList[method], params);
			return Backbone.sync(method, model, options);
		}
	});
  return new CalendarEvents;
});
