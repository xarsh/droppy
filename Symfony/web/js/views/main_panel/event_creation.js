define([
  'jQuery',  
  'Underscore',
  'Backbone',
  'model/event',
  'helpers/template_manager',
  'event_manager',
  'app_user',
  'helpers/assets'
], function($, _, Backbone, Event, templateManager, Dispatcher, appUser, Assetics) {
	var EventCreationView = Backbone.View.extend({
		tagName : 'div',
		
		template : _.template(templateManager.getEventCreationTemplate()),
		
		events : {
			'click span#create_button' : 'save',
			'click span#back_button' : 'back',
			'click input#event_allday' : 'setAllDay',
			'click input#event_privacy' : 'handlePrivacy',
			'keyup input#event_title' : 'handleTitle',
			'keyup input#event_place' : 'handlePlace',
			'keyup input#event_address' : 'handleAddress',
			'keyup textarea#event_details' : 'handleDetails',
			'click div#color_sample div' : 'handleColor',
			'change input#event_start_date' : 'handleDate',
			'change input#event_end_date' : 'handleDate',
			'change input#event_start_time' : 'handleTime',
			'change input#event_end_time' : 'handleTime'
		},
		
		initialize : function() {
			this.model = new Event;
			var start = this.model.get('startDateTime').get('date');
			var end = this.model.get('endDateTime').get('date');
			this.timeDifference = new Date(end.getFullYear() - start.getFullYear() + 1970,
					end.getMonth() - start.getMonth(), end.getDate() - start.getDate(),
					end.getHours() - start.getHours(), end.getMinutes() - start.getMinutes());
			this.loading = false;
		},
		
		save : function() {
			if(this.model.isValid() && !this.loading) {
				Dispatcher.trigger('loading');
				var self = this;
				this.model.save({}, {
					success : function(method, model) {
						Dispatcher.trigger('endLoading');
						Dispatcher.trigger('success', ExposeTranslation.get('common.event_update_success'));
						Dispatcher.trigger('back');
						self.loading = false;
					}
				});
			}
		},
		
		handleDate : function(e) {
			var $target = $(e.target);
			$target.blur();
			var value = $target.val();
			var isStart = $target.attr('id') === 'event_start_date'; 
			var data =  isStart ? 'startDateTime' : 'endDateTime';
			var match = /^([0-9]{4})\/([0-9]{2})\/([0-9]{2})$/.exec(value);
			if(!match) {
				$target.val(this.model.get(data).get('date').format('Y/m/d'));
			} else {
				this.model.get(data).get('date').setFullYear(
						parseInt(match[1], 10), parseInt(match[2], 10) - 1, parseInt(match[3], 10));
				var start = this.model.get('startDateTime').get('date');
				var end = this.model.get('endDateTime').get('date');
				
				if(isStart) {
					end.setFullYear(start.getFullYear() + this.timeDifference.getYearFromZero(),
							start.getMonth() + this.timeDifference.getMonth(),
							start.getDate() + this.timeDifference.getDate());
					this.$('input#event_end_date').val(end.format('Y/m/d'));
				} else {
					if(end < start) {
						start.setFullYear(end.getFullYear() - this.timeDifference.getYearFromZero(),
								end.getMonth() - this.timeDifference.getMonth(),
								end.getDate() - this.timeDifference.getDate());
					} else {
						this.timeDifference = new Date(end - start);
					}
					this.$('input#event_start_date').val(start.format('Y/m/d'));
				}
			}
		},
		
		handleTime : function(e) {
			var $target = $(e.target);
			var value = $target.val();
			$target.blur();
			var isStart = $target.attr('id') === 'event_start_time'; 
			var data =  isStart ? 'startDateTime' : 'endDateTime';
			var match = /^([0-9]{2}):([0-9]{2})$/.exec(value);
			if(!match) {
				$target.val(this.model.get(data).get('date').format('H:i'));
			} else {
				var endSave = new Date(this.model.get('endDateTime').get('date'));
				this.model.get(data).get('date').setHours(parseInt(match[1], 10), parseInt(match[2], 10));
				var start = this.model.get('startDateTime').get('date');
				var end = this.model.get('endDateTime').get('date');
				if(isStart) {
					end.setHours(start.getHours() + this.timeDifference.getHours(),
								start.getMinutes() + this.timeDifference.getMinutes());
					this.$('#event_end_time').val(end.format('H:i'));
				} else {
					if(end < start) {
						this.model.get('endDateTime').get('date').setHours(endSave.getHours(), endSave.getMinutes());
						this.$('#event_end_time').val(end.format('H:i'));
					} else {
						this.timeDifference = new Date(end - start);
					}
				}
			}			
		},
		
		handleTitle : function(e) {
			var changed = this.model.set('name', $(e.target).val());
		},
		
		handlePlace : function(e) {
			var changed = this.model.set('location', {
				'place' : $(e.target).val()
			});
		},
		
		handleAddress : function(e) {
			var changed = this.model.set('address', $(e.target).val());
		},
		
		handleDetails : function(e) {
			var changed = this.model.set('details', $(e.target).val());
		},
		
		handleColor : function(e) {
			this.$('div#color_sample div').removeClass('clicked');
			var clicked = $(e.target);
			var changed = this.model.set('color', {
				name : clicked.attr('class')
			});
			clicked.addClass('clicked');
		},
		
		resetModel : function() {
			this.model.clear();
			this.model.initialize();
			this.render();
		},
		
		setModel : function(model) {
			this.model = model;
			this.render();
		},

		render : function() {
			this.$el.html(this.template(Assetics.wrapAssetics(_.extend({}, 
					{ appUser : appUser }, this.model.getObject()))));
			this.$('#event_start_time').timePicker();
			this.endTimePicker = this.$('#event_end_time').timePicker();
			this.$('.datepicker').datepicker({ dateFormat: "yy/mm/dd"});
			this.$('input[placeholder]').placeholder();
			
			return this;
		},
		
		back : function() {
			Dispatcher.trigger('back');
		},
		
		handlePrivacy : function(e) {
			this.model.set('privacySettings', {
				visibility : $(e.target).val()
			});
		},
		
		show : function() {
			this.loaded = true;
			this.$el.show();
		},
		
		setAllDay : function(e) {
			var input = $(e.target);
			var allDay = input.is(':checked');
			this.model.get('startDateTime').set('allDay', allDay);
			this.model.get('endDateTime').set('allDay', allDay);
			input.blur();
			this.$('input.time').toggle(!allDay);
		}
	});
	
  return new EventCreationView;
});