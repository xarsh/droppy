define([
'text!templates/calendar.html',
'text!templates/event_creation_popup.html',
'text!templates/event_creation.html',
'text!templates/event_details.html',
'text!templates/timeline_event.html',
'text!templates/timeline.html',
'text!templates/single_user.html',
'text!templates/single_user_small.html',
'text!templates/user_profile.html',
'text!templates/single_event.html',
'text!templates/user_event_tabs.html',
'text!templates/search_form.html',
'text!templates/calendar_event.html',
'text!templates/event_details_popup.html',
'text!templates/app_user_profile.html'
], function(calendar, eventCreationPopup, 
			eventCreation, eventDetails, timelineEvent, 
			timeline, singleUser, singleUserSmall, userProfile, 
			singleEvent, userEventTabs, searchForm, calendarEvent,
			eventDetailsPopup, appUserProfile) {
	var TemplateManager = {
	  getTemplate : function(templateName) {
		switch(templateName) {
		case 'calendar' : return this.getCalendarTemplate();
		case 'event_creation_popup' : return this.getEventCreationPopupTemplate();
		case 'event_details_popup' : return this.getEventDetailsPopupTemplate();
		case 'event_creation' : return this.getEventCreationTemplate();
		case 'event_details' : return this.getEventDetailsTemplate();
		case 'single_user' : return this.getSingleUserTemplate();
		case 'timeline_event' : return this.getTimelineEventTemplate();
		case 'timeline' : return this.getTimelineTemplate();
		case 'single_user_small' : return this.getSingleUserSmallTemplate();
		case 'user_profile' : return this.getUserProfileTemplate();
		case 'single_event' : return this.getSingleEventTemplate();
		case 'user_event_tabs' : return this.getUserEventTabsTemplate();
		case 'search_form' : return this.getSearchFormTemplate();
		case 'calendar_event' : return this.getCalendarEventTemplate();
		case 'app_user_profile' : return this.getAppUserProfileTemplate();
		default : throw new TemplateNotFoundException('Could not find template \'' + templateName + '\'.');
		}  
	  },
	  
	  getCalendarTemplate : function() {
		  return calendar;
	  },
	  getEventCreationPopupTemplate : function() {
		  return eventCreationPopup;
	  },
	  getEventCreationTemplate : function() {
		  return eventCreation;
	  },
	  getEventDetailsTemplate : function() {
		  return eventDetails;
	  },
	  getSingleUserTemplate : function() {
		  return singleUser;
	  },
	  getTimelineEventTemplate : function() {
		  return timelineEvent;
	  },
	  getTimelineTemplate : function() {
		  return timeline;
	  },
	  getSingleUserSmallTemplate : function() {
		  return singleUserSmall;
	  } ,
	  getUserProfileTemplate : function() {
		  return userProfile;
	  },
	  getSingleEventTemplate : function() {
		  return singleEvent;
	  },
	  getUserEventTabsTemplate: function() {
		  return userEventTabs;
	  },
	  getSearchFormTemplate: function() {
		  return searchForm;
	  },
	  getCalendarEventTemplate: function() {
		  return calendarEvent;
	  },
	  getEventDetailsPopupTemplate : function() {
		  return eventDetailsPopup;
	  },
	  getAppUserProfileTemplate : function() {
		  return appUserProfile;
	  }
  };
	return TemplateManager;
});