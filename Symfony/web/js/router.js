define(['jQuery', 
        'Underscore', 
        'Backbone',
        'views/main_panel/event_details',
        'views/main_panel/event_creation',
        'views/calendar/calendar',
        'views/main_panel/user_profile',
        'views/main_panel/recommendations',
        'views/main_panel/search',
        'event_manager'
], function($, _, Backbone, eventDetailsView, eventCreationView, calendarView, 
		userProfileView, RecommendationsView, searchView, Dispatcher) {
	var AppRouter = Backbone.Router.extend({
		
		initialize : function() {
			this.recommendationsView = new RecommendationsView;
			this.route(/^!\/([0-9]{4}-[0-9]{2})$/, 'showCalendar');
			this.calendar = calendarView;
			Dispatcher.on('editEvent', this.createEvent, this);
		},
		
		routes : {
			'!/create-event' : 'createEvent',
			'!/recommendations' : 'showRecommendations',
			'!/event/:id' : 'showEvent',
			'!/:username' : 'showUser',
			'!/search/:keyword' : 'search',
			'*path' : 'showCalendar'
		},
		
		routesInfos: {
			event_creation : {
				route : 'create-event',
			},
			calendar : {
				route : ':date',
				parameters : {
					date : /[0-9]{4}-[0-9]{2}/
				}
			},
			event : {
				route : 'event/:id',
				parameters : {
					id : /[0-9]+/
				}
			},
			user : {
				route : ':username',
				parameters : {
					username : /[a-zA-Z0-9_]+/
				}
			},
			recommendations : {
				route : 'recommendations'
			},
			search : {
				route : 'search/:keyword',
				parameters : {
					keyword : /.*/
				}
			},
			top : {
				route : ''
			}
		},
		
		getDefaultRoute : function() {
			return this.routesInfos.top;
		},
		
		showEvent : function(id) {
			eventDetailsView.update(id);
		},

		showUser : function(username) {
			userProfileView.update(username);
		},
		
		search : function(keyword) {
			searchView.search(keyword);
			Dispatcher.trigger('loaded', searchView);
		},
		
		generateRoute : function(routeName, params) {
			params = params || {};
			var routeInfos = this.routesInfos[routeName] || this.getDefaultRoute();
			var neededParams = routeInfos.parameters;
			var route = routeInfos.route;
			for(var key in neededParams) {
				if(!(key in params) || !neededParams[key].exec(params[key])) {
					return false;
				}
				route = route.replace(':' + key, params[key]);
			}
			return '!/' + route;
		},
		
		showRecommendations : function() {
			Dispatcher.trigger('loaded', this.recommendationsView);
		},
		
		createEvent : function(model) {
			if(typeof model === 'undefined') {
				eventCreationView.resetModel();
			} else {
				eventCreationView.setModel(model);
			}
			this.navigate(this.generateRoute('event_creation'));
			Dispatcher.trigger('loaded', eventCreationView);
		},

		showCalendar : function(date) {
			date = date || '';
			var dateObj = new Date(Date.parse(date));
			if(isNaN(dateObj.getTime())) {
				dateObj = new Date();
			}
			calendarView.setDate(dateObj);
			Dispatcher.trigger('loaded', calendarView);
		}
	});

	var initialize = function() {
		var appRouter = new AppRouter;
		Backbone.history.start();
		return appRouter;
	};
	
	return {
		initialize : initialize
	};
});
