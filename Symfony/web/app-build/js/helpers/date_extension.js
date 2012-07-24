Date.prototype.dateEquals = function(that) {
	return this.getFullYear() === that.getFullYear() && 
			this.getMonth() === that.getMonth() && this.getDate() === that.getDate(); 
};

Date.prototype.sameMonth = function(that) {
	return this.getFullYear() === that.getFullYear() && this.getMonth() === that.getMonth();
};

Date.prototype.getCategory = function() {
	var firstDayOfWeek = window.firstDayOfWeek || 0;
	var that = new Date();
	if(this.dateEquals(that)) {
		return 'today';
	} 
	that.setDate(that.getDate() + 1);
	if(this.dateEquals(that)) {
		return 'tomorrow';
	}
	do {
		if(this.dateEquals(that)) {
			return 'this_week';
		}
		that.setDate(that.getDate() + 1);
	} while(that.getDay() != firstDayOfWeek);
	if(this.sameMonth(that)) {
		return 'this_month';
	}
	return 'after_this_month';
};

Date.getToday = function() {
	var today = new Date;
	today.setHours(0, 0, 0, 0);
	return today;
};

Date.preZero = function(value) {
	return (parseInt(value) < 10) ? "0" + value : value;
};

Date.prototype.format = function(formatString) {
	var formattedDate = '';
	
	for(var i = 0 ; i < formatString.length ; i++){
		var c = formatString.charAt(i);
		switch(c) {
			case "Y": formattedDate += this.getFullYear(); break;
			case "y": formattedDate += this.getFullYear().toString().substr(2,2); break;
			case "m": formattedDate += Date.preZero(this.getMonth() + 1); break;
			case "n": formattedDate += this.getMonth() + 1; break;
			case "F": formattedDate += this.getMonthName(); break;
			case "M": formattedDate += this.getMonthName().substr(0,3); break;
			case "d": formattedDate += Date.preZero(this.getDate()); break;
			case "j": formattedDate += this.getDate(); break;
			case "l": formattedDate += this.getDayName(); break;
			case "D": formattedDate += this.getDayName().substr(0,3); break;
			case "H": formattedDate += Date.preZero(this.getHours()); break;
			case "i": formattedDate += Date.preZero(this.getMinutes()); break;
			default : formattedDate += c; break;
		}
	}
	return formattedDate;
};

Date.prototype.firstDateToDisplay = function(firstDayOfWeek) {
	var newDate = new Date(this);
	firstDayOfWeek = firstDayOfWeek || 0;
	newDate.setDate(1);
	while(newDate.getDay() != firstDayOfWeek) {
		newDate.setDate(newDate.getDate() - 1);
	}
	newDate.setHours(0, 0, 0, 0);
	return newDate;
};

Date.prototype.lastDateToDisplay = function(firstDayOfWeek) {
	var newDate = new Date(this);
	firstDayOfWeek = firstDayOfWeek || 0;
	newDate.setMonth(newDate.getMonth() + 1, 0);
	while(newDate.getDay() != firstDayOfWeek) {
		newDate.setDate(newDate.getDate() + 1);
	}
	newDate.setDate(newDate.getDate() - 1);
	newDate.setHours(23, 59, 59, 999);
	return newDate;
};

Date.prototype.getDaysDifference = function(that) {
	var thisTmp = new Date(this).setHours(0, 0, 0, 0);
	var thatTmp = new Date(that).setHours(0, 0, 0, 0);
	var diff = Math.abs(thisTmp - thatTmp);
	var millisecondsInDay = 1000 * 60 * 60 * 24;
	return diff / millisecondsInDay + 1;
};

Date.prototype.getClassName = function(month) {
	var today = Date.getToday();
	var className = ''; 
	if(!this.sameMonth(month)) {
		className += 'gray ';
	}
	if(this < today) {
		className += 'passed ';
	}
	if(this.dateEquals(today)) {
		className += 'today';
	}
	return className;
};

Date.prototype.daysToDisplay = function(firstDayOfWeek) {
	firstDayOfWeek = firstDayOfWeek || 0;
	var start = this.firstDateToDisplay(firstDayOfWeek);
	var end = this.lastDateToDisplay(firstDayOfWeek);
	return end.getDaysDifference(start);
};

Date.prototype.getDayName = function() {
	var days = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
	return ExposeTranslation.get('time.day_of_week.' + days[this.getDay()]);
};

Date.prototype.getMonthName = function() {
	var months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul',
	              'aug', 'sep', 'oct', 'nov', 'dec'];
	return ExposeTranslation.get('time.months.' + months[this.getMonth()]);
};

Date.prototype.fromFirstDayOfWeek = function(firstDayOfWeek) {
	firstDayOfWeek = firstDayOfWeek || 0;
	return (this.getDay() - firstDayOfWeek + 7) % 7;
};

Date.prototype.toLastDayOfWeek = function(firstDayOfWeek) {
	firstDayOfWeek = firstDayOfWeek || 0;
	return 7 - this.fromFirstDayOfWeek();
};

Date.prototype.rowsFromFirstDisplayedDay = function(firstDayOfWeek, date) {
	firstDayOfWeek = firstDayOfWeek || 0;
	date = date || this;
	var tmp = new Date(date.firstDateToDisplay(firstDayOfWeek));
	for(var i = 0; i < 6; i++) {
		tmp.setDate(tmp.getDate() + 7);
		if(this < tmp) {
			return i;
		}
	}
};

Date.getCurrent = function() {
	var date = new Date;
	if(date.getMinutes() <= 30) {
		date.setMinutes(30, 0, 0);
	} else {
		date.setHours(date.getHours() + 1, 0, 0, 0);
	}
	return date;
};

Date.getNextHour = function() {
	var date = Date.getCurrent();
	date.setHours(date.getHours() + 1);
	return date;
};

Date.prototype.getYearFromZero = function() {
	return this.getFullYear() - 1970;
};
