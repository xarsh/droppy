define([
	'Handlebars'
], function(Handlebars) {
	function dropImage(dropped) {
		var icon = (typeof dropped !== 'undefined' && dropped) ? 'checked' : 'drop';
		return this.staticAsset('img/' + icon + '.png');
	}
	
	Handlebars.registerHelper('dropImage', dropImage);
  
	return dropImage;
});