define([
  'jQuery',  
  'Underscore',
  'Backbone',
], function($, _, Backbone){
	var TopMessageView = Backbone.View.extend({
		el : $('div#top_message'),
		
		initialize : function() {
			this.className = '';
		},
		
		showTopMessage : function(str, ms, type) {
			ms = ms || 5000;
			var div = this.$el;
			div.attr('class', type);
			this.className = type;
			div.children('p').text(str);
			div.show();
			if(ms >= 0) {
				setTimeout(function() {
						div.fadeOut();
						//adjustWindow();
					}, ms);
			}
		},

		hideTopMessage : function() {
		    this.$el.fadeOut('fast');
		},
		
		hideLoading : function() {
			if(this.className === 'information') {
				this.hideTopMessage();
			}
		},

		showError : function(str, ms) {
			this.hideTopMessage();
			str = str || ExposeTranslation.get('error.general'); 
			this.showTopMessage(str, ms, 'error');
		},

		showInformationMessage : function(str, ms) {
			this.hideTopMessage();
			this.showTopMessage(str, ms, 'information');
		},

		showSuccessMessage : function(str, ms) {
			this.hideTopMessage();
			str = str || ExposeTranslation.get('message.success');
			this.showTopMessage(str, ms, 'success');
		},
	});
  return new TopMessageView;
});

