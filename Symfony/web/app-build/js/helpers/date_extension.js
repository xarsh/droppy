Date.prototype.dateEquals=function(e){return this.getFullYear()===e.getFullYear()&&this.getMonth()===e.getMonth()&&this.getDate()===e.getDate()},Date.prototype.sameMonth=function(e){return this.getFullYear()===e.getFullYear()&&this.getMonth()===e.getMonth()},Date.prototype.getCategory=function(){var e=window.firstDayOfWeek||0,t=new Date;if(this.dateEquals(t))return"today";t.setDate(t.getDate()+1);if(this.dateEquals(t))return"tomorrow";do{if(this.dateEquals(t))return"this_week";t.setDate(t.getDate()+1)}while(t.getDay()!=e);return this.sameMonth(t)?"this_month":"after_this_month"},Date.getToday=function(){var e=new Date;return e.setHours(0,0,0,0),e},Date.preZero=function(e){return parseInt(e)<10?"0"+e:e},Date.prototype.format=function(e){var t="";for(var n=0;n<e.length;n++){var r=e.charAt(n);switch(r){case"Y":t+=this.getFullYear();break;case"y":t+=this.getFullYear().toString().substr(2,2);break;case"m":t+=Date.preZero(this.getMonth()+1);break;case"n":t+=this.getMonth()+1;break;case"F":t+=this.getMonthName();break;case"M":t+=this.getMonthName().substr(0,3);break;case"d":t+=Date.preZero(this.getDate());break;case"j":t+=this.getDate();break;case"l":t+=this.getDayName();break;case"D":t+=this.getDayName().substr(0,3);break;case"H":t+=Date.preZero(this.getHours());break;case"i":t+=Date.preZero(this.getMinutes());break;default:t+=r}}return t},Date.prototype.firstDateToDisplay=function(e){var t=new Date(this);e=e||0,t.setDate(1);while(t.getDay()!=e)t.setDate(t.getDate()-1);return t.setHours(0,0,0,0),t},Date.prototype.lastDateToDisplay=function(e){var t=new Date(this);e=e||0,t.setMonth(t.getMonth()+1,0);while(t.getDay()!=e)t.setDate(t.getDate()+1);return t.setDate(t.getDate()-1),t.setHours(23,59,59,999),t},Date.prototype.getDaysDifference=function(e){var t=(new Date(this)).setHours(0,0,0,0),n=(new Date(e)).setHours(0,0,0,0),r=Math.abs(t-n),i=864e5;return r/i+1},Date.prototype.getClassName=function(e){var t=Date.getToday(),n="";return this.sameMonth(e)||(n+="gray "),this<t&&(n+="passed "),this.dateEquals(t)&&(n+="today"),n},Date.prototype.daysToDisplay=function(e){e=e||0;var t=this.firstDateToDisplay(e),n=this.lastDateToDisplay(e);return n.getDaysDifference(t)},Date.prototype.getDayName=function(){var e=["sun","mon","tue","wed","thu","fri","sat"];return ExposeTranslation.get("time.day_of_week."+e[this.getDay()])},Date.prototype.getMonthName=function(){var e=["jan","feb","mar","apr","may","jun","jul","aug","sep","oct","nov","dec"];return ExposeTranslation.get("time.months."+e[this.getMonth()])},Date.prototype.fromFirstDayOfWeek=function(e){return e=e||0,(this.getDay()-e+7)%7},Date.prototype.toLastDayOfWeek=function(e){return e=e||0,7-this.fromFirstDayOfWeek()},Date.prototype.rowsFromFirstDisplayedDay=function(e,t){e=e||0,t=t||this;var n=new Date(t.firstDateToDisplay(e));for(var r=0;r<6;r++){n.setDate(n.getDate()+7);if(this<n)return r}},Date.getCurrent=function(){var e=new Date;return e.getMinutes()<=30?e.setMinutes(30,0,0):e.setHours(e.getHours()+1,0,0,0),e},Date.getNextHour=function(){var e=Date.getCurrent();return e.setHours(e.getHours()+1),e},Date.prototype.getYearFromZero=function(){return this.getFullYear()-1970};