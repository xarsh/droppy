define([
  'jQuery',  
  'Underscore',
  'Backbone',
  'views/timeline/timeline_event',
  'event_manager',
  'helpers/assets'
], function($, _, Backbone, TimelineEventView, Dispatcher, Assetics) {
	var TimelineView = Backbone.View.extend({

		el: 'div#timeline',
		
		events : {
			'focus div.event_lists' : 'blur',
			'jsp-scroll-y .scrollable' : 'loadMore'
		},
		
		initialize : function(timeline) {
			this.views = [];
			
			this.$el.jScrollPane({
				verticalGutter : 0,
				autoReinitialise : true,
				scrollbarMargin: 0
			});
			
			this.timeline = timeline;
			Dispatcher.on('timelineAdd', this.timeline.add, this.timeline);
			Dispatcher.on('outOfTimeline', this.timeline.remove, this.timeline);
			
			this.timeline.bind('add', this.addOne, this);
			this.timeline.bind('reset', this.addAll, this);
			//this.timeline.fetch();
		},
		
		loadMore : function(event, scrollPositionY, isAtTop, isAtBottom) {
			if(isAtBottom) {
				this.timeline.loadMore();
			}
		},
		
		blur : function(e) {
			$(e.target).blur();
		},
		
		addOne : function(event, collection, options) {
			var view = new TimelineEventView({
				model : event
			});
			container = this.$el;
			api = container.data('jsp');
			contentPane = api.getContentPane();
			var category = event.timelineCategory();
			var div = contentPane.find('div.' + category);
			this.views.push(view);
			var html = view.render().el;
			if(options.append || typeof options.index === 'undefined') {
				div.append(html);
			} else {
				if(options.index === 0) {
					div.children().after(html);
				} else {
					var prevId = this.timeline.at(options.index - 1).get('id');
					var prev = div.find('div[event-id="' + prevId + '"]').parent();
					if(prev.length === 0) {
						div.children().after(html);
					} else {
						prev.after(html);
					}
				}
			}
			div.show();
		},

		addAll : function() {
			this.$el.find('.loading').remove();
			this.$el.find('div.timeline_container div').remove();
			this.$el.find('div.timeline_container').hide();
			for(var i = 0; i < this.timeline.length; i++) {
				this.addOne(this.timeline.at(i), {}, { append : true });
			}
		}	
	});
  return TimelineView;
});


