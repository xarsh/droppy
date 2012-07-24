define([
	'Handlebars',
	'helpers/assets'
], function(Handlebars, Assetics) {
	function staticAsset(path) {
		return Assetics.staticAsset(path);
	}
	
	Handlebars.registerHelper('staticAsset', staticAsset);
  
	return staticAsset;
});