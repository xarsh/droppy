define(["jQuery","Underscore","Backbone","model/event","helpers/template_manager","event_manager","app_user","helpers/assets"],function(e,t,n,r,i,s,o,u){var a=n.View.extend({tagName:"div",template:t.template(i.getEventCreationTemplate()),events:{"click span#create_button":"save","click span#back_button":"back","click input#event_allday":"setAllDay","click input#event_privacy":"handlePrivacy","keyup input#event_title":"handleTitle","keyup input#event_place":"handlePlace","keyup input#event_address":"handleAddress","keyup textarea#event_details":"handleDetails","click div#color_sample div":"handleColor","change input#event_start_date":"handleDate","change input#event_end_date":"handleDate","change input#event_start_time":"handleTime","change input#event_end_time":"handleTime"},initialize:function(){this.model=new r;var e=this.model.get("startDateTime").get("date"),t=this.model.get("endDateTime").get("date");this.timeDifference=new Date(t.getFullYear()-e.getFullYear()+1970,t.getMonth()-e.getMonth(),t.getDate()-e.getDate(),t.getHours()-e.getHours(),t.getMinutes()-e.getMinutes()),this.loading=!1},save:function(){if(this.model.isValid()&&!this.loading){s.trigger("loading");var e=this;this.model.save({},{success:function(t,n){s.trigger("endLoading"),s.trigger("success",ExposeTranslation.get("common.event_update_success")),s.trigger("back"),e.loading=!1}})}},handleDate:function(t){var n=e(t.target);n.blur();var r=n.val(),i=n.attr("id")==="event_start_date",s=i?"startDateTime":"endDateTime",o=/^([0-9]{4})\/([0-9]{2})\/([0-9]{2})$/.exec(r);if(!o)n.val(this.model.get(s).get("date").format("Y/m/d"));else{this.model.get(s).get("date").setFullYear(parseInt(o[1],10),parseInt(o[2],10)-1,parseInt(o[3],10));var u=this.model.get("startDateTime").get("date"),a=this.model.get("endDateTime").get("date");i?(a.setFullYear(u.getFullYear()+this.timeDifference.getYearFromZero(),u.getMonth()+this.timeDifference.getMonth(),u.getDate()+this.timeDifference.getDate()),this.$("input#event_end_date").val(a.format("Y/m/d"))):(a<u?u.setFullYear(a.getFullYear()-this.timeDifference.getYearFromZero(),a.getMonth()-this.timeDifference.getMonth(),a.getDate()-this.timeDifference.getDate()):this.timeDifference=new Date(a-u),this.$("input#event_start_date").val(u.format("Y/m/d")))}},handleTime:function(t){var n=e(t.target),r=n.val();n.blur();var i=n.attr("id")==="event_start_time",s=i?"startDateTime":"endDateTime",o=/^([0-9]{2}):([0-9]{2})$/.exec(r);if(!o)n.val(this.model.get(s).get("date").format("H:i"));else{var u=new Date(this.model.get("endDateTime").get("date"));this.model.get(s).get("date").setHours(parseInt(o[1],10),parseInt(o[2],10));var a=this.model.get("startDateTime").get("date"),f=this.model.get("endDateTime").get("date");i?(f.setHours(a.getHours()+this.timeDifference.getHours(),a.getMinutes()+this.timeDifference.getMinutes()),this.$("#event_end_time").val(f.format("H:i"))):f<a?(this.model.get("endDateTime").get("date").setHours(u.getHours(),u.getMinutes()),this.$("#event_end_time").val(f.format("H:i"))):this.timeDifference=new Date(f-a)}},handleTitle:function(t){var n=this.model.set("name",e(t.target).val())},handlePlace:function(t){var n=this.model.set("location",{place:e(t.target).val()})},handleAddress:function(t){var n=this.model.set("address",e(t.target).val())},handleDetails:function(t){var n=this.model.set("details",e(t.target).val())},handleColor:function(t){this.$("div#color_sample div").removeClass("clicked");var n=e(t.target),r=this.model.set("color",{name:n.attr("class")});n.addClass("clicked")},resetModel:function(){this.model.clear(),this.model.initialize(),this.render()},setModel:function(e){this.model=e,this.render()},render:function(){return this.$el.html(this.template(u.wrapAssetics(t.extend({},{appUser:o},this.model.getObject())))),this.$("#event_start_time").timePicker(),this.endTimePicker=this.$("#event_end_time").timePicker(),this.$(".datepicker").datepicker({dateFormat:"yy/mm/dd"}),this.$("input[placeholder]").placeholder(),this},back:function(){s.trigger("back")},handlePrivacy:function(t){this.model.set("privacySettings",{visibility:e(t.target).val()})},show:function(){this.loaded=!0,this.$el.show()},setAllDay:function(t){var n=e(t.target),r=n.is(":checked");this.model.get("startDateTime").set("allDay",r),this.model.get("endDateTime").set("allDay",r),n.blur(),this.$("input.time").toggle(!r)}});return new a});