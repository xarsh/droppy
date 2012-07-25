define([
  'jQuery',  
  'Underscore',
  'Backbone',
  'collections/user_list',
  'views/general/user_list',
  'collections/event_list',
  'views/general/event_list',
  'helpers/template_manager',
  'event_manager',
  'helpers/assets'
], 	function($, _, Backbone, UserList, UserListView, EventList, EventListView, 
		templateManager, Dispatcher, Assetics) {
		var RecommendationsView = Backbone.View.extend({

			initialize : function() {
				this.loaded = false;
				this.eventParams = {};
				this.userParams = {};
				this.templateText = {
					user : 'user.recommended_users',
					event : 'event.recommended_events'
				};
			},
			
			getEventRouteList : function() {
				return {
					read : 'droppy_event_ajax_latest_events'
				}; 
			},
			
			getUserRouteList : function() {
				return {
					read : 'droppy_user_ajax_aetelrecommended_users'
				}; 
			},
			
			initEventList : function() {
				this.eventList = new EventList;
				_.extend(this.eventList, {
					routeList : this.getEventRouteList(),
					params : this.eventParams
				});
				new EventListView({
					eventList : this.eventList,
					templateName : 'single_event', 
					el : 'div#event_tab'
				});
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
					templateName : 'single_user_new',
					el : 'div.userlist'
				});
			},
			
			initElements : function() {
				this.$('#search_result').tabs({ 
					fx: { 
							opacity: 'toggle', 
							duration: 'fast'  
					}
				});
				this.$('.scrollable').jScrollPane({
					verticalGutter:0, 
					autoReinitialise:true
				});
				
			},

			events : {
				'click .button_big' : 'back',
				'focus div.search' : 'blur'
			},
			
			blur : function() {
				this.$('div.search').blur();
			},

			template : _.template(templateManager.getUserEventTabsTemplate()),

			render : function() {
				this.$el.html(this.template(Assetics.wrapAssetics({ text : this.templateText })));
				return this;
			},
			
			show : function() {
				this.render();
				this.$el.show();
				this.$('div.search').height($(window).height() - $('div#header').height());
				this.initElements();
				this.initEventList();
				this.initUserList();
				this.loaded = true;
			},

			back : function() {
				Dispatcher.trigger('back');
			}
		});
		
	  return RecommendationsView;
});
