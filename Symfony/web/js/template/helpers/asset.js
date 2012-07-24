define([
	'Handlebars',
	'helpers/assets'
], function(Handlebars, Assetics) {
	function asset(path) {
		return Assetics.asset(path);
	}
	
	Handlebars.registerHelper('asset', asset);
  
	return asset;
});