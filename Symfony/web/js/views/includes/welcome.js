define([
  'jQuery',  
  'Underscore',
  'Backbone',
  'event_manager'
], function($, _, Backbone, Dispatcher) {
	var WelcomeView = Backbone.View.extend({
		
		el : 'div#welcome',
		
		events : {
			'click .next' : 'nextPopup',
			'click .finish' : 'finishGuidance'
		},
		
		initialize : function () {
			Dispatcher.on('loaded', this.closeAllPopups(), this);
		    var today = $('#main_calendar td.today');
		    if(today.length === 0) {
		    	this.$('#ewp').remove();
		    	var lastPopup = this.$('.popup').last();
		    	lastPopup.find('.next').remove();
		    	lastPopup.find('.finish').text(ExposeTranslation.get('button.close'));
		    } else {
		    	var offset = today.offset();
		    
			    this.$('#ewp').css({
			    	left:offset.left-90,
			    	top:offset.top-100,
			    	right:'auto'
				});
		    }
		    this.showFirstPopup();
		},
		
		showFirstPopup : function() {
			this.$('.popup').first().fadeIn('fast');
		},
	
		nextPopup : function() {
			var next = this.$('.popup').filter(':visible').next();
			this.closeAllPopups();
			next.fadeIn('fast');
		},
	
		finishGuidance : function() {
			this.closeAllPopups();
			$.ajax({
				type: "POST",
				url: Routing.generate('droppy_user_ajax_set_started')
			});
		},
	
		closeAllPopups : function() {
			this.$('.popup').fadeOut('fast');
		}
	});
	
  return WelcomeView;
});