define([
  'jQuery',     
  'Underscore', 
  'Backbone',
  'router',
  'model/user',
  'data_manager',
  'views/top_message',
  'event_manager',
  'app_user',
  'views/includes/app_user_profile',
  'views/includes/welcome'
], function($, _, Backbone, Router, User, DataManager, topMessageView, 
		Dispatcher, appUser, appUserProfileView, WelcomeView) {
	var AppView = Backbone.View.extend({
		initialize: function() {
			this.loading = 0;
			this.dataManager = new DataManager;
			this.topMessageView = topMessageView;
			this.initEvents();
			$(window).resize(this.resize);
			this.router = Router.initialize();
			if(!(appUser.get('hasStarted'))) {
				new WelcomeView;
			}
			this.$('div#left').prepend(appUserProfileView.el);
		},
		
		initEvents : function() {
			Dispatcher.on('changePage', this.changePage, this);
			Dispatcher.on('loading', this.setLoading, this);
			Dispatcher.on('endLoading', this.endLoading, this);
			Dispatcher.on('back', this.back, this);
			Dispatcher.on('loaded', this.showPage, this);
			Dispatcher.on('pageChanged', this.pageChanged, this);
			Dispatcher.on('success', this.success, this);
		},
		
		el: $('div#wrapper'),
		
		events: {
			'click div.logo a' : 'toTop',
			'click div.home a' : 'toTop',
			'click div.make_event a' : 'eventCreation',
			'click div.recommend a' : 'recommendations',
			'click div.pulldown' : 'showMenu',
			'keyup input.header_search_txt' : 'makeSearch',
			'click #user_profile' : 'showUser'
		},
		
		isLoading : function() {
			return this.loading;
		},
		
		success : function(message) {
			this.topMessageView.showSuccessMessage(message);
		},
		
		showUser : function(e) {
			e.preventDefault();
			this.changePage('user', {
				username : appUser.get('username')
			});
		},
		
		toTop : function(e) {
			e.preventDefault();
			this.changePage(this.router.generateRoute('top'));
		},
		
		setLoading : function() {
			if(this.loading === 0) {
				var message = ExposeTranslation.get('common.loading');
				this.topMessageView.showInformationMessage(message, -1);
			}
			this.loading++;
		},
		
		endLoading : function() {
			this.loading--;
			if(this.loading < 0) {
				this.loading = 0;
			}
			if(this.loading === 0) {
				this.topMessageView.hideLoading();
			}
		},
		
		makeSearch : function(e) {
			var code = e.which || e.keycode;
			var $target = $(e.target);
			if(code == 13) {
				this.changePage('search', {
					keyword : $target.val()
				});
				$target.val('');
				$target.blur();
			}
		},
		
		changePage : function(page, params) {
			var route = this.router.generateRoute(page, params);
			if(('#' + route) === window.location.hash) {
				return;
			}
			this.router.navigate(route, {trigger : true});
		},
		
		eventCreation : function(e) {
			e.preventDefault();
			this.changePage('event_creation');
		},
		
		recommendations : function(e) {
			e.preventDefault();
			this.changePage('recommendations');
		},
		
		showMenu : function(e) {
			$('#pulldown_items').not(':animated').slideToggle('fast');
		},
		
		back : function() {
			window.history.back();
		},
		
		showPage : function(container) {
			if(!container.loaded) {
				var el = container.render().el;
				this.$('div#main').prepend(el);
			}
			this.$('div#main').children('div').hide();
			container.show();
			this.resize();
		},
		
		pageChanged : function(route, params) {
			this.resize();
			this.router.navigate(this.router.generateRoute(route, params));
		},
		
		resize : function() {
			var windowHeight = document.documentElement.clientHeight || document.body.clientHeight || document.body.scrollHeight;
            //$("table.calendar").height(windowHeight - 35 - 50);
            $("div#timeline").height(windowHeight - 125 - 35);
            $('#col2_content .recommended_users').find('div').each(function(){
                $(this).show();
                if($(this).offset().top + 100 > windowHeight){
                    $(this).nextAll().hide();
                    return false;
                }
            });
            $('#user_details').height(windowHeight - 35);
            $('div.search').height(windowHeight - 35);
            Dispatcher.trigger('resize');
		}
	});
	
	var initialize = function() {
		Backbone.emulateHTTP = true;

		new AppView;
	};
	
	return {
		initialize: initialize
	};
});
