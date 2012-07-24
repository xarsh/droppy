define([
  'Underscore'
], function(_){
	var Assetics = {};
	Assetics.st = {};

	//Assetics.st.relativePath = 'Sites/droppy/Symfony/web';
	Assetics.protocol = 'https';
	Assetics.hostname = 's3-ap-northeast-1.amazonaws.com';

	Assetics.staticAsset = function(path) {
		var protocol = Assetics.st.protocol || 'http';
		var hostname = Assetics.st.hostname || document.location.hostname;
		var relativePath = Assetics.st.relativePath || '';
		var port = Assetics.st.port || 80;
		if(relativePath !== '') {
			relativePath += '/';
		}
		var fullPath = protocol.concat('://', hostname, ':' + port, '/', relativePath, path);
		return fullPath;
	};

	Assetics.asset = function(path) {
		var protocol = Assetics.protocol || 'http';
		var hostname = Assetics.hostname || document.location.hostname;
		var relativePath = Assetics.relativePath || '';
		var port = Assetics.port || (protocol === 'http' ? 80 : 443);
		if(relativePath !== '') {
			relativePath += '/';
		}
		var fullPath = protocol.concat('://', hostname, ':' + port, '/', relativePath, path);
		return fullPath;
	};
	
	Assetics.wrapAssetics = function(object) {
		return _.extend({}, object, { Assetics : Assetics });
	};
	
	return Assetics;
});




