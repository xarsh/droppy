define([
  'jQuery',  
  'Underscore',
  'Backbone',
  'event_manager'
], function($, _, Backbone, Dispatcher) {
	var PopupView = Backbone.View.extend({
		initialize : function($el) {
			this.$el = $el;
		},
		
		events : { 
			'click a.close' : 'close',
			'click div' : 'stopPropagation'
		},	

		open : function(x, y) {
			$('#event_summary_popup').hide();
			$('#make_event_popup').hide();
			this.setPosition(x, y);
			this.$el.children('div').show();
			var self = this;
			$('body').click(function() {
				self.close.call(self);
			});
		},
		
		close : function(e) {
			if(typeof e !== 'undefined') {
				e.preventDefault();
			}
			this.$el.children('div').hide();
		},
		
		setPosition : function(x, y) {
			var popup = this.$el.children('div');
			var boxWidth = popup.innerWidth();
			var boxHeight = popup.innerHeight();
			var rightMargin = 30;
			var browserWidth = document.documentElement.clientWidth
					|| document.body.clientWidth || document.body.scrollWidth;

			if (y - boxHeight > 0) {
				popup.css("top", y - boxHeight);
			} else {
				popup.css("top", y);
			}

			if (x + boxWidth / 2 + rightMargin <= browserWidth) {
				popup.css("left", x - boxWidth / 2);
			} else {
				popup.css("left", brouserWidth - boxWidth - rightMargin);
			}
		},
		
		toDetails : function() {
			this.close();
			Dispatcher.trigger('editEvent', this.model);
		},
		
		stopPropagation : function(e) {
			e.stopPropagation();
		}
	});
	
  return PopupView;
});
