define([
	'Handlebars'
], function(Handlebars) {
	function translate(word) {
		return ExposeTranslation.get(word);
	}
	
	Handlebars.registerHelper('translate', translate);
  
	return translate;
});