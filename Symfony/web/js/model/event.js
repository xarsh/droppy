define([
  'jQuery',  
  'Underscore',
  'Backbone',
  'Sync',
  'model/user',
  'model/event_date_time',
  'event_manager'
], function($, _, Backbone, Sync, User, EventDateTime, Dispatcher) {
	var Event = Backbone.RelationalModel.extend({
		relations: [
			{
				type: Backbone.HasOne,
				key: 'creator',
				relatedModel: User,
				
			},
			{
				type: Backbone.HasOne,
				key: 'startDateTime',
				relatedModel: EventDateTime,
			}, 
			{
				type: Backbone.HasOne,
				key: 'endDateTime',
				relatedModel: EventDateTime,
				
			}
		],		
		
		defaults: function() {
			return {
				name: ExposeTranslation.get('event.default_name'),
				startDateTime : new EventDateTime
			};
		},
		
		initialize: function() {
			if(!this.get('name') || this.get('name') === null) {
				this.set('name', this.defaults().name);
			}
			if(!this.get('startDateTime') || this.get('startDateTime') === null) {
				this.set('startDateTime', new EventDateTime);
			}
			if(!this.get('endDateTime') || this.get('endDateTime') === null) {
				this.set('endDateTime', new EventDateTime);
				var date = this.get('endDateTime').get('date');
				date.setHours(date.getHours() + 1);
			}
		},
		
		validate : function() {
		},
		
		timelineCategory: function() {
			if(this.get('startDateTime').get('date') < new Date) {
				return 'today';
			}
			return this.get('startDateTime').timelineCategory();
		},
		
		setFromJSON : function(json) {
			var event = this.parse(json);
			this.set(event);
		},
		
		compareDates: function(that) {
			if(!this.get('startDateTime').isSameDate(that.get('startDateTime'))) {
				return this.get('startDateTime').compare(that.get('startDateTime'));
			} else if(this.get('endDateTime') && that.get('endDateTime') &&
						!this.get('endDateTime').isSameDate(that.get('endDateTime'))) {
				return that.get('endDateTime').compare(this.get('endDateTime'));
			}
			return this.get('startDateTime').compare(that.get('startDateTime'));
		},
		
		parse: function(response, xhr) {
			var event = Object.keysToCamelCase(response.event);
			event.liked = ('liked' in response) ? response.liked : false;
			event.inCalendar = (response.relation !== null) ? response.relation.in_calendar : false;
			event.isCreator = (response.relation !== null) ? response.relation.user_is_creator : false;
			event.startDateTime = EventDateTime.parse(event.startDateTime);
			event.endDateTime = EventDateTime.parse(event.endDateTime);
			event.dropped = (response.relation !== null);
			return event;
		},
		
		sync : function(method, model, options) {
			var urlList = {
				'create' : Routing.generate('droppy_event_ajax_update_event'),
				'read' : Routing.generate('droppy_event_ajax_event_and_rel', { event_id : this.get('id')}),
				'update' : Routing.generate('droppy_event_ajax_update_event'),
				'delete' : Routing.generate('droppy_event_ajax_remove_event')
			};
			options.url = urlList[method];
			if(method === 'delete') {
				var params = { event_id : this.get('id')};
				Dispatcher.trigger('outOfTimeline', this);
				Dispatcher.trigger('outOfCalendar', this);
				return Sync.makeRequest('delete', params, options);
			} else if(method === 'create') {
				var success = options.success;
				var model = this;
				options.success = function(resp, status, xhr) {
					if(success) {
						success(resp, status, xhr);
					}
					model.notifyAdd(model);
				};
			}
			return Backbone.sync(method, model, options);
		},
		
		notifyAdd : function(model) {
			model = model || this;
			Dispatcher.trigger('calendarAdd', model);
			if(model.get('endDateTime').get('date') >= Date.getToday()) {
				Dispatcher.trigger('timelineAdd', model);
			}
		},
		
		getObject: function() {
			return Backbone.RelationalModel.prototype.toJSON.call(this);
		},
		
		inCalendar : function(options) {
			options = options || {};
			_.extend(options, {'url' : Routing.generate('droppy_event_ajax_event_in')});
			Sync.makeRequest('update', {event_id: this.get('id')}, options);
			this.set('inCalendar', true);
		},
		
		outOfCalendar : function(options) {
			options = options || {};
			_.extend(options, {'url' : Routing.generate('droppy_event_ajax_event_out')});
			Sync.makeRequest('update', {event_id: this.get('id')}, options);
			this.set('inCalendar', false);
		},
		
		toJSON: function(options) {
			var event = this.getObject();
			event.startDateTime = this.get('startDateTime').format();
			event.endDateTime = this.get('endDateTime').format();
			return Object.keysFromCamelCase(event);
		},
		
		dateToString : function() {
			var start = this.get('startDateTime');
			var end = this.get('endDateTime');
			var date = start.getString();
			if(start.compare(end) === 0) {
				return date;
			}
			date += ' 〜 ';
			if(!start.get('date').dateEquals(end.get('date'))) {
				date += end.getString(start.get('date'));
			} else {
				date += end.get('date').format('H:i');
			}
			return date;
		},
		
		display: function() {
			var obj = this.getObject();
			obj.startDateString = this.get('startDateTime').getString(); 
			obj.endDateString = this.get('endDateTime').getString();
			obj.date = this.dateToString();
			return obj;
		},
		
		drop: function(options) {
			options = options || {};
			_.extend(options, {'url' : Routing.generate('droppy_event_ajax_drop')});
			Sync.makeRequest('create', {event_id: this.get('id')}, options);
			this.set('dropped', true);
			this.set('inCalendar', true);
		},
		
		undrop: function(options) {
			options = options || {};
			_.extend(options, {'url' : Routing.generate('droppy_event_ajax_undrop')});
			Sync.makeRequest('delete', {event_id: this.get('id')}, options);
			Dispatcher.trigger('outOfTimeline', this);
			Dispatcher.trigger('outOfCalendar', this);
			this.set('dropped', false);
		}
	});
  return Event;
});

