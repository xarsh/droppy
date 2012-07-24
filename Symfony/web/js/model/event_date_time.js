define([
  'jQuery',  
  'Underscore',
  'Backbone'   
], function($, _, Backbone){
	var EventDateTime = Backbone.RelationalModel.extend({
		defaults: function() {
			return {
				date: Date.getCurrent(),
				allDay: true
			};
		},

		initialize: function() {
			if(!this.get('date')) {
				this.set('date', this.defaults.date);
			}
			if(!this.get('allDay')) {
				this.set('allDay', this.defaults.allDay);
			}
			this.bind('change', this.checkAllDay, this);
		},
		
		checkAllDay : function() {
			if(typeof this.get('allDay') === 'undefined') {
				this.set('allDay', false);
			}
		},
		
		timelineCategory: function() {
			return this.get('date').getCategory();
		},
		
		isSameDate: function(that) {
			return this.get('date').dateEquals(that.get('date'));
		},
		
		compare: function(that) {
			if(!this.isSameDate(that)) {
				return that.get('date') - this.get('date');
			} else if(this.get('allDay') !== that.get('allDay')) {
				return this.get('allDay') ? 1 : -1;
			} else {
				if(this.get('allDay')) {
					return 0;
				} else {
					return that.get('date') - this.get('date');
				}
			}
		},
		
		parse: function(response) {
			return EventDateTime.parse(response);
		},
		
		format : function() {
			var datas = this.toJSON();
			var date = this.get('date');
			datas.date = date.format('Y/m/d');
			datas.time = date.format('H:i');
			return Object.keysToCamelCase(datas);
		},
		
		getString: function(that) {
			that = (typeof that !== 'undefined') ? new Date(that) : new Date;
			that.setFullYear(that.getFullYear() + 1);
			var dateStr = '';
			var date = this.get('date');
			var format = (date < that) ? 'month_day_dow' : 'year_month_day_dow';
			var formatStr = ExposeTranslation.get('time.' + format);
			dateStr += date.format(formatStr);
			if(!this.get('allDay')) {
				dateStr += ' ' + date.format('H:i');
			}
			return dateStr;
		}
	});

	EventDateTime.parse = function(response) {
		var obj = {};
		var date = response.date;
		obj.date = new Date(date.year, date.month - 1, date.day);
		obj.allDay = response.all_day || response.allDay;
		if(!obj.allDay) {
			obj.date.setHours(response.time.hour, response.time.minute);
		}
		return obj;
	};
  return EventDateTime;
});

