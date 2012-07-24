define([
  'jQuery',  
  'Underscore',
  'Backbone',
  'views/includes/single_event',
  'event_manager',
  'helpers/assets'
], function($, _, Backbone, SingleEventView, Dispatcher, Assetics) {
	var EventListView = Backbone.View.extend({

		initialize : function() {
			var $loading = $('<div />').attr('class', 'loading centering').append($('<img />').attr('src', 
					Assetics.staticAsset('img/loading_big.gif'))); 
			this.$el.append($loading);
			this.eventList = this.options.eventList;
			this.templateName = this.options.templateName;
			this.showCreator = (typeof this.options.showCreator !== 'undefined') ?
				this.options.showCreator : true;

			this.eventList.bind('add', this.addOne, this);
			this.eventList.bind('reset', this.addAll, this);
			Dispatcher.trigger('loading');
			this.eventList.fetch({ 
				success : function() { 
					Dispatcher.trigger('endLoading'); 
				}
			});
		},

		addOne : function(event) {
			var view = new SingleEventView({
				model : event,
				templateName : this.templateName,
				showCreator : this.showCreator
			});
			this.$el.append(view.render().el);
		},

		addAll : function() {
			this.$el.empty();
			for(var i = 0; i < this.eventList.length; i++) {
				this.addOne(this.eventList.at(i));
			}
		}	
	});
  return EventListView;
});

