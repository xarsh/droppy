define(["jQuery","Underscore","Backbone","views/calendar/popup","model/event","helpers/template_manager","event_manager","helpers/assets"],function(e,t,n,r,i,s,o,u){var a=r.extend({initialize:function(){this.events=t.extend({},r.prototype.events,this.events),r.prototype.initialize(this.$el),this.displayed=!1},template:t.template(s.getEventDetailsPopupTemplate()),setModel:function(e){this.model=e},events:{"click a.setting_details":"showEvent","click span.button_small.edit":"editEvent","click span.button_small.delete":"deleteEvent","click span.button_small.toggle_drop":"toggleDrop","click span.button_small.in":"outOfCalendar"},editEvent:function(e){this.close(),o.trigger("editEvent",this.model)},deleteEvent:function(e){this.close(),this.model.trigger("out"),this.model.destroy()},toggleDrop:function(){if(this.model.get("loading"))return;this.model.get("dropped")?o.trigger("undrop",this.model,this.$("span.button_small.drop")):o.trigger("drop",this.model,this.$("span.button_small.drop")),this.close(),this.model.trigger("out")},outOfCalendar:function(){this.close(),this.model.outOfCalendar(),this.model.trigger("out")},render:function(){return this.$el.html(this.template(u.wrapAssetics(this.model.display()))),this.delegateEvents(this.events),this},showEvent:function(e){e.preventDefault(),this.close(),o.trigger("changePage","event",{id:this.model.get("id")})}});return new a});