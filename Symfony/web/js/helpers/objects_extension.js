if(typeof Array.isArray === 'undefined') {
	Array.isArray = function(object) {
		return object instanceof Array;
	};
}

String.prototype.capitalize = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
};

String.prototype.fromCamelCase = function() {
	var words = [];
	var start = 0;
	for(var i in this) {
		if(!this.hasOwnProperty(i)) {
			continue;
		}
		if(this[i] >= "A" && this[i] <= "Z") {
			words.push(this.substring(start, i).toLowerCase());
			start = parseInt(i);
		}
	}
	words.push(this.substring(start).toLowerCase());
	return words.join("_");
};

String.prototype.toCamelCase = function() {
    var words = this.split('_');
    var capitalize = function(s) { return s.capitalize(); };
    var ret = words[0];
    var rest = words.slice(1);
    for(var i in rest) {
    	ret += rest[i].capitalize();
    }
    return ret;
};

String.prototype.urlify = function() {
    var urlRegex =/(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
    return this.replace(urlRegex, '<a href="$1">$1</a>');
};

String.prototype.nl2br = function() {
	return this.replace(/\n/g, '<br />\n');
};

Object.changeKeys = function(f, toChange) {
	var obj = {};
	for(var n in toChange) {
		if(!toChange.hasOwnProperty(n)) {
			continue;
		}
		if(typeof toChange[n] === "object" && toChange[n] !== null &&
				!Array.isArray(toChange[n])) {
			obj[f(n)] = Object.changeKeys(f, toChange[n]);
		} else {
			obj[f(n)] = toChange[n];
		}
	}
	return obj;
};


Object.keysToCamelCase = function(toChange) {
	return Object.changeKeys(function(s) { return s.toCamelCase(); }, toChange);
};

Object.keysFromCamelCase = function(toChange) {
	return Object.changeKeys(function(s) { return s.fromCamelCase(); }, toChange);
};
