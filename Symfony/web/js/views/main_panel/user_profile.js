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
			
			el : 'div#user',
			
			template : _.template(templateManager.getUserProfileTemplate()),
			
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
					el : 'div#created div.eventlist',
					showCreator : false
				});
			},
			
			initDroppings : function() {
				this.droppings = new UserList;
				_.extend(this.droppings, {
					routeList : {
						'read' : 'droppy_user_ajax_dropping_users'
					},
					params : {
						user_id : this.model.get('id')
					},
					comparator : function(user) {
						return user.get('droppingUsersNumber');
					}
				});
				new UserListView({
					userList : this.droppings,
					templateName : 'single_user',
					el : 'div#dropping  div.userlist'			
				});
			},
			
			render : function() {
				this.$('div.summary').html(this.template(Assetics.wrapAssetics(
						_.extend({ appUser : appUser.toJSON() }, this.model.toJSON()))
				));
				return this;
			},
			
			initElements : function() {
				this.$tabs = this.$('div#tabs').tabs(); 
				this.$el.jScrollPane({
					verticalGutter:0, 
					autoReinitialise:true,
					contentWidth: '0px'
				});
			},

			events : {
				'click .button_big' : 'back',
				'focus div#user_details' : 'blur',
				'click div.title_names div.button.drop' : 'toggleDrop',
				'click div.title_names div.introduce' : 'openLink',
				'jsp-scroll-y' : 'loadMore'
			},
			
			openLink : function(e) {
				e.preventDefault();
				var $link = $(e.target);
				window.open($link.attr('href'));
			},
			
			loadMore : function(e, scrollPositionY, isAtTop, isAtBottom) {
				if(isAtBottom) {
					var selected = this.$tabs.tabs('option', 'selected');
					var collections = [this.droppings, this.eventList];
					collections[selected].loadMore();
				}
			},
			
			blur : function() {
				this.$('div#user_details').blur();
			},
			
			toggleDrop : function(e) {
				$target = $(e.target);
				if(!$target.is('div')) {
					$target = $target.parent();
				}
				if(this.model.get('loading')) {
					return;
				}
				if(this.model.get('dropped')) {
					Dispatcher.trigger('undrop', this.model, $target, true);
				} else {
					Dispatcher.trigger('drop', this.model, $target, true);
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

			back : function() {
				Dispatcher.trigger('back');
			}			
		});
		
	  return new UserProfileView;
});