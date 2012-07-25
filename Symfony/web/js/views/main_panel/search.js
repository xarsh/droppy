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
	var SearchView = Backbone.View.extend({
		
		initialize : function() {
			this.loaded = false;
			this.userParams = {};
		},
		
		el : 'div#search',
		
		makeSearch : function() {
			this.userList.params = this.userParams;
			this.userList.fetch();
		},
		
		getUserRouteList : function() {
			return {
				read : 'droppy_user_ajax_search_users'
			};
		},
		
		search : function(keyword) {
			this.userParams = {
				keywords : keyword
			};
		},
		
		initUserList : function() {
			this.userList = new UserList;
			_.extend(this.userList, {
				routeList : this.getUserRouteList(),
				comparator : function(user) {
					return user.get('username');
				},
				params : this.userParams
			});
			new UserListView({
				userList : this.userList,
				templateName : 'single_user',
				el : 'div.userlist'
			});
			this.userList.on('reset', this.showResults, this); 
		},
		
		showResults : function() {
			this.$('div.userlist').show();
			this.$('span.result.before').hide();
			this.$('span.result.after').text(ExposeTranslation.get('search.user_result', { number : this.userList.length}));
		},
		
		initElements : function() {
			this.$el.jScrollPane({
				verticalGutter:0, 
				autoReinitialise:true
			});
		},
		
		show : function() {
			this.$el.show();
			this.$('div#search').height($(window).height() - $('div#header').height());
			this.initElements();
			this.initUserList();
			this.loaded = true;
		}
	});
	
	return new SearchView;
});
