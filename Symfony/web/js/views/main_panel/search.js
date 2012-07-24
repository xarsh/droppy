define([
  'jQuery',  
  'Underscore',
  'Backbone',
  'views/main_panel/recommendations',
  'collections/user_list',
  'views/general/user_list',
  'collections/event_list',
  'views/general/event_list',
  'helpers/template_manager',
  'helpers/assets'
], 	function($, _, Backbone, RecommendationsView, 
		UserList, UserListView, EventList, EventListView, templateManager, Assetics) {
	var SearchView = RecommendationsView.extend({
		initialize : function() {
			this.events = _.extend({}, RecommendationsView.prototype.events, this.events);
			this.keyPressCount = 0;
			this.templateText = {
				user : 'user.user',
				event : 'event.event'
			};
		},
		
		events : {
			'keyup input#search_keyword' : 'handleKeyword',
			'keyup input#search_place' : 'handlePlace',
			'click span#search_button' : 'makeSearch',
			'change input#search_start_date' : 'handleDate',
			'change input#search_end_date' : 'handleDate',
			'focus input' : 'resetKeyPressCount'
		},
		
		getEventRouteList : function() {
			return {
				read : 'droppy_event_ajax_search_events'
			}; 
		},
		
		resetKeyPressCount : function() {
			this.keyPressCount = 0;
		},
		
		handleKeyword : function(e) {
			var code = e.which || e.keycode;
			var $target = $(e.target);
			if(code == 13) {
				$target.blur();
				this.makeSearch();
			} else {
				this.userParams.keywords = $target.val();
				this.eventParams.keywords = $target.val();
			}
		},
		
		handlePlace : function(e) {
			var code = e.which || e.keycode;
			var $target = $(e.target);
			if(code == 13) {
				$target.blur();
				this.makeSearch();
			} else {
				this.userParams.places = $target.val();
				this.eventParams.places = $target.val();
			}
		},
		
		handleDate : function(e) {
			var $target = $(e.target);
			var value = $target.val();
			if(!/[0-9]{4}\/[0-9]{2}\/[0-9]{2}/.exec(value)) {
				$target.val('');
				return;
			}
			if($target.attr('id') === 'search_start_date') {
				this.eventParams.start = value;
			} else {
				this.eventParams.end = value;
			}
		},

		makeSearch : function() {
			this.eventList.params = this.eventParams;
			this.userList.params = this.userParams;
			this.eventList.fetch();
			this.userList.fetch();
		},
		
		formTemplate : _.template(templateManager.getSearchFormTemplate()),
		
		getUserRouteList : function() {
			return {
				read : 'droppy_user_ajax_search_users'
			}; 
		},
		
		search : function(keyword) {
			this.userParams = {
				keywords : keyword
			};
			this.eventParams = {
					keywords : keyword
				};
		},
		
		show : function() {
			RecommendationsView.prototype.show.call(this);
            this.$('#search_keyword').val(this.userParams.keywords);
        },

		render : function() {
			this.$el.html(this.template(Assetics.wrapAssetics({ text : this.templateText })));
			this.$('#search_result').before(this.formTemplate());
			this.$('.datepicker').datepicker({ dateFormat: "yy/mm/dd"});
			return this;
		}
	});
	
	return new SearchView;
});
