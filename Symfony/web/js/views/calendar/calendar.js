define([
  'jQuery',  
  'Underscore',
  'Backbone',
  'views/calendar/calendar_event',
  'helpers/template_manager',
  'views/calendar/event_creation_popup',
  'collections/calendar_events',
  'event_manager',
  'helpers/assets'
  ], function($, _, Backbone, CalendarEventView, templateManager, 
		eventCreationPopup, calendarEvents, Dispatcher, Assetics){
	var CalendarView = Backbone.View.extend({

		template : _.template(templateManager.getCalendarTemplate()),
		
		initialize : function() {
			this.date = new Date;
			this.calendarEvents = calendarEvents;
			this.startListening();
			this.views = [];
			this.thisMonthEnabled = false;
			this.mouseWheelActive = false;
			
			$('div#body').append(eventCreationPopup.render().el);
		},
		
		startListening : function() {
			this.calendarEvents.bind('add', this.addAll, this);
			this.calendarEvents.bind('reset', this.addAll, this);
			this.calendarEvents.bind('remove', this.addAll, this);
			Dispatcher.on('resize', this.resize, this);
			Dispatcher.on('calendarChange', this.addAll, this);
			Dispatcher.on('calendarUpdate', this.updateDatas, this);
			Dispatcher.on('calendarReset', this.resetCalendar, this);
		},
		
		events : {
			'click span.calendar_button.prev' : 'lastMonth',
			'click span.calendar_button.next' : 'nextMonth',
			'click span.calendar_button.current' : 'thisMonth',
			'mousemove table.calendar tbody' : 'colorCell',
			'mouseleave table.calendar tbody' : 'uncolorCell',
			'click div.calendar td' : 'showEventCreationPopup',
			'mousewheel div.calendar tbody' : 'changeMonth'
		},
		
		changeMonth : function(e, delta) {
			if(this.mouseWheelActive) {
				return;
			}
			if(delta > 0) {
				this.lastMonth();
			} else {
				this.nextMonth();
			}
			this.mouseWheelActive = true;
			var self = this;
			setTimeout(function() {
				self.mouseWheelActive = false;
			}, 200);
		},
		
		resize : function() {
			var windowHeight = $(window).height();
			var headerHeight = $('div#header').height();
			var titleHeight = this.$('div.calendar_tools').height();
			var calendar = this.$('table.calendar');
			var theadHeight = calendar.find('thead tr').height();
			var availableHeight = windowHeight - headerHeight - titleHeight - theadHeight;
			var tbody = calendar.children('tbody');
			tbody.height(availableHeight);
			var trs = tbody.children('tr').filter(':visible');
			trs.height(availableHeight / trs.length);
			this.addAll();
		},
		
		setDate : function(date) {
			this.date = date;
		},
		
		update : function(updateHash) {
			this.updateDatas();
			this.updateView(updateHash);
		},
		
		updateDatas : function() {
			var self = this;
			var calendarEvents = this.calendarEvents;
			calendarEvents.setDate(this.date);
			this.firstDay = this.date.firstDateToDisplay();
			this.lastDay = this.date.lastDateToDisplay();
			Dispatcher.trigger('loading');
			calendarEvents.fetch({ 
				add : true, 
				silent : true, 
				success : function() { 
					self.addAll(); 
					Dispatcher.trigger('endLoading');
				}
			});
		},
		
		lastMonth : function() {
			this.date.setMonth(this.date.getMonth() - 1);
			this.update(true);
		},
		
		nextMonth : function() {
			this.date.setMonth(this.date.getMonth() + 1);
			this.update(true);
		},
		
		thisMonth : function() {
			if(this.thisMonthEnabled) {
				this.date = new Date;
				this.update(true);
			}
		},
		
		updateView : function(updateHash) {
			this.updateButtonsView();
			this.updateCalendarView();
			if(updateHash) {
				if(this.date.sameMonth(new Date)) {
					Dispatcher.trigger('pageChanged', 'top');
				} else {
					Dispatcher.trigger('pageChanged', 'calendar', {
						date : this.date.format('Y-m')
					});
				}
			}
		},
		
		resetCalendar : function() {
			this.calendarEvents.reset([], { silent : true});
			this.updateDatas();
		},
		
		updateButtonsView : function() {
			var tools = this.$('div.calendar_tools tr');
			tools.find('td.date').text(this.date.format(ExposeTranslation.get('time.year_month')));
			var thisMonth = tools.find('td.right span.current'); 
			if(this.date.sameMonth(new Date)) {
				thisMonth.addClass('disabled');
				this.thisMonthEnabled = false;
			} else {
				thisMonth.removeClass('disabled');
				this.thisMonthEnabled = true;
			}
		},
		
		updateCalendarView : function() {
			var current = this.date.firstDateToDisplay();
			var daysToDisplay = this.date.daysToDisplay();
			var trs = this.$('table.calendar tbody tr');
			trs.hide();
			var tr = trs.first();
			for(var i = 0; i < daysToDisplay / 7; i++) {
				var td = tr.children('td').first();
				for(var j = 0; j < 7; j++) {
					td.attr('id', current.format('Y-m-d'));
					td.attr('class', current.getClassName(this.date));
					td.children('p').text(current.getDate());
					td = td.next();
					current.setDate(current.getDate() + 1);
				}
				tr.show();
				tr = tr.next();
			}
		},
		
		render : function() {
			this.$el.html(this.template());
			return this;
		},
		
		show : function() {
			this.loaded = true;
			this.update();
			this.$el.show();
		},
		
		addOne : function(event) {
			var view = new CalendarEventView({
				model : event
			});
			var start = event.get('startDateTime').get('date');
			start = this.firstDay > start ? new Date(this.firstDay) : new Date(start);
			var end = event.get('endDateTime').get('date');
			end = this.lastDay < end ? new Date(this.lastDay) : new Date(end);
			
			var options = {
					eventsAbove : this.calendarEvents.getEventsNumberOnDate(start),
					preceded : !start.dateEquals(event.get('startDateTime').get('date'))
			};
			
			var td = this.$('.calendar tbody td');
			var eventsPerCell = Math.floor((td.height() - 
					td.children('p').outerHeight(true)) / (view.height + 2));
			if(options.eventsAbove >= eventsPerCell) {
				return;
			}
			options.allDay = event.get('startDateTime').get('allDay') || !start.dateEquals(end);

			while(start <= end) {
				var length = Math.min(end.getDaysDifference(start), start.toLastDayOfWeek());
				var column = start.fromFirstDayOfWeek();
				var row = start.rowsFromFirstDisplayedDay(0, this.date);
				start.setDate(start.getDate() + length);
				options.followed = (start <= end);
				var datas = _.extend(this.getCellInfos(length, row, column, 
						event.get('startDateTime').get('allDay')), options);
				view.display(datas);
				options.preceded = true;
			}
			event.printed = true;
			this.$('table.calendar tbody').append(view.render().el);
			this.views.push(view);
		},
		
		getCellInfos : function(length, row, column, allDay) {
			var calendar = this.$('table.calendar tbody');
			var cellRow = calendar.find('tr').eq(row);
			var startCell = cellRow.find('td').eq(column);
			var startCellOffset = startCell.offset();
			var borderWidth = allDay ? 1 : 0;
			var endCell = cellRow.find('td').eq(column + length - 1);
			var endPos = endCell.offset().left + endCell.innerWidth() - 2 * borderWidth;
			return {
				top : startCellOffset.top + startCell.children('p').outerHeight(true),
				left : startCellOffset.left,
				width : endPos - startCellOffset.left			
			};			
		},
		
		addAll : function() {
			for(var i in this.views) {
				this.views[i].remove();
			}
			this.view = [];
			this.calendarEvents.resetPrintedStatus();
			var events = this.calendarEvents.getEventsInMonth();
			for(var i = 0; i < events.length; i++) {
				var event = events[i];
				if(event.get('dropped') && event.get('inCalendar')) {
					this.addOne(event);
				}
			}
		},
		
		colorCell : function(e) {
			this.$('td.hover').removeClass('hover');

			var tableOffset = this.$('table.calendar tbody').offset();
			var mouse = {
				'top' : e.clientY - tableOffset.top + $(window).scrollTop(),
				'left' : e.clientX - tableOffset.left
			};
			var cell = {
				'height' : this.$('table.calendar td').outerHeight(),
				'width' : this.$('table.calendar td').outerWidth()
			};

			numberOfCell = Math.min(Math.floor((1 + mouse.left)/cell.width), 6);
			numberOfCell += 7 * Math.floor(mouse.top/cell.height);
			this.$('table.calendar').find('td').eq(numberOfCell).addClass('hover');
		},
		
		showEventCreationPopup : function(e) {
			e.stopPropagation();
			var src = $(e.target);
			if(src.is('p')) {
				src = src.parent('td');
			}
			eventCreationPopup.setDate(src.attr('id'));
			eventCreationPopup.render();
			eventCreationPopup.open(e.pageX, e.pageY);
			eventCreationPopup.$('input.title').focus();
		},
		
		uncolorCell : function() {
			this.$('td.hover').removeClass('hover');
		}
	});
  return new CalendarView;
});
