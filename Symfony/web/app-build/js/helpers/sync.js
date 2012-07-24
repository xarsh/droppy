define([
  'jQuery',  
  'Underscore',
], function($, _){
	var Sync = {};
	Sync.makeRequest = function(method, params, options) {
		var type = (method === 'read') ? 'GET' : 'POST';
		options.type = type;
		var datas = { data: params };
		var params = _.extend(datas, options);
		return $.ajax(params);
	};
  return Sync;
});

