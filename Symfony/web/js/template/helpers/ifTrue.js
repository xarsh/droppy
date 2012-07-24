define([
	'Handlebars'
], function(Handlebars) {
	function ifTrue(condition, string) {
		return (typeof condition !== 'undefined' && condition) ? string : '';
	}
	
	Handlebars.registerHelper('ifTrue', ifTrue);
  
	return ifTrue;
});