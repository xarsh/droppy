define([
  'jQuery',  
  'Underscore',
  'Backbone',
  'helpers/template_manager',
  'event_manager',
  'helpers/assets'
], function($, _, Backbone, templateManager, Dispatcher, Assetics){
	var TimelineEventView = Backbone.View.extend({
		tagName : 'div',
		
		initialize : function() {
			this.model.on('remove', this.remove, this);
		},
		
		template : _.template(templateManager.getTimelineEventTemplate()),

		events : {
			'click div.event' : 'showDetails',
			'click a.close' : 'closeEvent',
			'click div.info div.display_name' : 'showProfile',
			'mouseenter div.event' : 'showClose',
			'mouseleave div.event' : 'hideClose'
		},

		render : function() {
			this.$el.html(this.template(Assetics.wrapAssetics(this.model.display())));
			return this;
		},
		
		showClose : function() {
			this.$('a.close').show();
		},
		
		hideClose : function() {
			this.$('a.close').hide();
		},
		
		showDetails : function(e) {
			e.stopPropagation();
			Dispatcher.trigger('changePage', 'event', {
				id : this.model.get('id')
			});
		},
		
		showProfile : function(e) {
			e.preventDefault();
			e.stopPropagation();
			Dispatcher.trigger('changePage', 'user', {
				username : this.model.get('creator').get('username')
			});
		},

		closeEvent : function(e) {
			e.preventDefault();
			e.stopPropagation();
			this.model.undrop();
			this.remove();
		},
		
		remove : function() {
			var parent = this.$el.parent('div');
			this.$el.remove();
			if(parent.find('div.timeline_container').length === 0) {
				parent.hide();
			}
		}
	});
	
  return TimelineEventView;
});

