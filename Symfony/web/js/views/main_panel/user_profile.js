define([
  'jQuery',  
  'Underscore',
  'Backbone',
  'model/user',
  'collections/user_list',
  'views/general/user_list',
  'collections/event_list',
  'views/general/event_list',
  'helpers/template_manager',
  'event_manager',
  'app_user',
  'helpers/assets'
], 	function($, _, Backbone, User, UserList, UserListView, EventList, EventListView, 
		templateManager, Dispatcher, appUser, Assetics) {
		var UserProfileView = Backbone.View.extend({

			initialize : function() {
				this.model = new User;
				this.loaded = false;
			},
			
			initCreatedEvents : function() {
				this.eventList = new EventList;
				_.extend(this.eventList, {
					routeList : {
						read : 'droppy_event_ajax_created_events'
					},
					params : {
						user_id : this.model.get('id')
					}
				});
				new EventListView({
					eventList : this.eventList,
					templateName : 'single_event', 
					el : 'div#event_tab',
					showCreator : false
				});
			},
			
			initUserList : function(list, readRoute, comparatorParam, el) {
				_.extend(list, {
					routeList : {
						'read' : readRoute
					},
					params : {
						user_id : this.model.get('id')
					},
					comparator : function(user) {
						return user.get(comparatorParam);
					}
				});
				new UserListView({
					userList : list,
					templateName : 'single_user',
					el : el 					
				});
			},
			
			initDroppers : function() {
				this.droppers = new UserList;
				this.initUserList(this.droppers, 'droppy_user_ajax_droppers', 
						'droppingUsersNumber', 'div#droppers_tab');
			},
			
			initDroppings : function() {
				this.droppings = new UserList;
				this.initUserList(this.droppings, 'droppy_user_ajax_dropping_users', 
						'droppingUsersNumber', 'div#droppings_tab');
			},
			
			initElements : function() {
				this.$tabs = this.$('#user_relations_list').tabs({ 
					fx: { 
							opacity: 'toggle', 
							duration: 'fast'  
					}
				});
				this.$('.scrollable').jScrollPane({
					verticalGutter:0, 
					autoReinitialise:true,
					contentWidth: '0px'
				});
			},

			events : {
				'click button.button_big' : 'back',
				'focus div#user_details' : 'blur',
				'click p.drop_button > span' : 'toggleDrop',
				'click div.user_profile p.description a' : 'openLink',
				'jsp-scroll-y .scrollable' : 'loadMore'
			},
			
			openLink : function(e) {
				e.preventDefault();
				var $link = $(e.target);
				window.open($link.attr('href'));
			},
			
			loadMore : function(e, scrollPositionY, isAtTop, isAtBottom) {
				if(isAtBottom) {
					var selected = this.$tabs.tabs('option', 'selected');
					var collections = [this.eventList, this.droppings, this.droppers];
					collections[selected].loadMore();
				}
			},
			
			blur : function() {
				this.$('div#user_details').blur();
			},
			
			toggleDrop : function() {
				if(this.model.get('loading')) {
					return;
				}
				if(this.model.get('dropped')) {
					Dispatcher.trigger('undrop', this.model, this.$('span.button_small'));
				} else {
					Dispatcher.trigger('drop', this.model, this.$('span.button_small'));
				}
			},
			
			update : function(username) {
				Dispatcher.trigger('loading');
				this.model.clear();
				this.model.set('username', username);
				var container = this;
				this.model.fetch({
					success : function(model, response) {
						Dispatcher.trigger('endLoading');
						Dispatcher.trigger('loaded', container);
						container.initDroppers();
						container.initDroppings();
						container.initCreatedEvents();
					}
				});
			},
			
			show : function() {
				this.render();
				this.$el.show();
				this.initElements();
				this.loaded = true;
			},

			template : _.template(templateManager.getUserProfileTemplate()),

			render : function() {
				this.$el.html(this.template(Assetics.wrapAssetics(_.extend({}, 
						{ appUser : appUser }, this.model.toJSON()))));
				return this;
			},

			back : function() {
				Dispatcher.trigger('back');
			}			
		});
		
	  return new UserProfileView;
});