define([
	'Handlebars',
	'helpers/assets'
], function(Handlebars, Assetics) {
	var helpers = {
			translate : function(word) {
				return ExposeTranslation.get(word);
			},
	
			asset : function(path) {
				return Assetics.asset(path);
			},
			
			staticAsset : function(path) {
				return Assetics.staticAsset(path);
			},
			
			ifTrue : function(condition, string) {
				return (typeof condition !== 'undefined' && condition) ? string : '';
			},
			
			dropText : function(dropped) {
				return this.translate((typeof dropped !== 'undefined' && dropped) ? 'dropping' : 'drop'); 
			},
			
			dropImage : function(dropped) {
				var icon = (typeof dropped !== 'undefined' && dropped) ? 'checked' : 'drop';
				return this.staticAsset('img/' + icon + '.png');
			},
			
			formatDate : function(date, format) {
				return date.format(format);
			}
	};
  
	for(var i in helpers) {
		Handlebars.registerHelper(i, helpers[i]);
	}
  
	return helpers;
});