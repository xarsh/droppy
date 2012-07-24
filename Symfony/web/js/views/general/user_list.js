define([
  'jQuery',  
  'Underscore',
  'Backbone',
  'views/includes/single_user',
  'event_manager',
  'helpers/assets'
], function($, _, Backbone, SingleUserView, Dispatcher, Assetics){
	var UserListView = Backbone.View.extend({

		initialize : function() {
			var $loading = $('<div />').attr('class', 'loading centering').append($('<img />').attr('src', 
					Assetics.staticAsset('img/loading_big.gif'))); 
			this.$el.append($loading);
			this.userList = this.options.userList;
			this.templateName = this.options.templateName;
			
			this.userList.bind('add', this.addOne, this);
			this.userList.bind('reset', this.addAll, this);
			Dispatcher.trigger('loading');
			this.userList.fetch({ 
				success : function() { 
					Dispatcher.trigger('endLoading'); 
				}
			});
		},

		addOne : function(user) {
			var view = new SingleUserView({
				model : user,
				templateName : this.templateName
			});
			this.$el.append(view.render().el);
		},

		addAll : function() {
			this.$('.loading').remove();
			this.$('div').remove();
			for(var i = 0; i < this.userList.length; i++) {
				this.addOne(this.userList.at(i));
			}
		}	
	});
  return UserListView;
});

