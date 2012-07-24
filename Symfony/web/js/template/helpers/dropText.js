define([
	'Handlebars'
], function(Handlebars) {
	function dropText(dropped) {
		return this.translate((typeof dropped !== 'undefined' && dropped) ? 'dropping' : 'drop'); 
	}
	
	Handlebars.registerHelper('dropText', dropText);
  
	return dropText;
});