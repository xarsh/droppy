define([
	'Handlebars'
], function(Handlebars) {
	function formatDate(date, format) {
		return date.format(format);
	}
	
	Handlebars.registerHelper('formatDate', formatDate);
  
	return formatDate;
});