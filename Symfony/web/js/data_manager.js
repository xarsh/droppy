define([
  'jQuery',  
  'Underscore',
  'Backbone',
  'collections/event_list',
  'views/timeline/timeline',
  'collections/user_list',
  'views/general/user_list',
  'event_manager',
  'bootstrap',
  'helpers/assets'
], function($, _, Backbone, EventList, TimelineView,
		RecommendedUsers, UserListView, Dispatcher, bootstrap, Assetics) {
	var DataManager = function() {
		this.initialize();
	};
	DataManager.prototype = {
		initialize : function() {
			this.timeline = new EventList;
			_.extend(this.timeline, {
				routeList : {
					'read' : 'droppy_event_ajax_timeline'
				}
			});
			new TimelineView(this.timeline);
			if(bootstrap.bootstrappedTimeline) {
				this.timeline.setFromJSON(bootstrap.bootstrappedTimeline);
			}
			
			this.initRecommendedUsers();
			this.initEvents();
		},
		
		initEvents : function() {
			Dispatcher.on('drop', this.drop, this);
			Dispatcher.on('undrop', this.undrop, this);
			Dispatcher.on('eventUpdate', this.addToTimeline, this);
			Dispatcher.on('eventRemoved', this.removeFromTimeline, this);
		},
		
		addToTimeline: function(model) {
			this.timeline.add(model);
		},
		
		initRecommendedUsers : function() {
			this.recommendedUsers = new RecommendedUsers;
			_.extend(this.recommendedUsers, {
				routeList : {
					'read' : 'droppy_user_ajax_isobeselected_users'
				},
				comparator : function(user) {
					return user.get('droppersNumber');
				}
			});
			new UserListView({
				userList : this.recommendedUsers,
				templateName : 'single_user_small',
				el : 'div.recommended_users'
			});
		},
		
		drop : function(model, button, user) {
			var self = this;
			button.children('img').attr('src', Assetics.staticAsset('img/loading.gif'));
			model.set('loading', true);
			model.drop({
				success : function(response) {
					button.addClass('dropping');
					button.children('img').attr('src', Assetics.staticAsset('img/check.png'));
					button.children('span').text(ExposeTranslation.get(user ? 'button.dropping' : 'button.delete_from_calendar'));
					model.set('dropped', true);
					model.set('loading', false);
					var events = EventList.fromJSON(response);
					Dispatcher.trigger('timelineAdd', events);
					Dispatcher.trigger('calendarAdd', events);
					/*self.timeline.params = {};
					self.timeline.fetch();
					Dispatcher.trigger('calendarUpdate');*/
				}
			});
		},
		
		undrop : function(model, button, user) {
			var self = this;
			button.children('img').attr('src', Assetics.staticAsset('img/loading_undrop.gif'));
			model.set('loading', true);
			model.undrop({
				success : function(response) {
					button.removeClass('dropping');
					button.children('img').attr('src', Assetics.staticAsset('img/drop.png'));
					button.children('span').text(ExposeTranslation.get(user ? 'button.drop' : 'button.add_into_calendar'));
					model.set('dropped', false);
					model.set('loading', false);
					var events = EventList.fromJSON(response);
					Dispatcher.trigger('outOfCalendar', events);
					Dispatcher.trigger('outOfTimeline', events);
					/*self.timeline.params = {};
					self.timeline.fetch();
					
					console.log(events);
					Dispatcher.trigger('calendarReset');*/
				}
			});
		}
	};
		
	return DataManager;
});
