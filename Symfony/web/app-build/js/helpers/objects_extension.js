typeof Array.isArray=="undefined"&&(Array.isArray=function(e){return e instanceof Array}),String.prototype.capitalize=function(){return this.charAt(0).toUpperCase()+this.slice(1)},String.prototype.fromCamelCase=function(){var e=[],t=0;for(var n in this){if(!this.hasOwnProperty(n))continue;this[n]>="A"&&this[n]<="Z"&&(e.push(this.substring(t,n).toLowerCase()),t=parseInt(n))}return e.push(this.substring(t).toLowerCase()),e.join("_")},String.prototype.toCamelCase=function(){var e=this.split("_"),t=function(e){return e.capitalize()},n=e[0],r=e.slice(1);for(var i in r)n+=r[i].capitalize();return n},String.prototype.urlify=function(){var e=/(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;return this.replace(e,'<a href="$1">$1</a>')},String.prototype.nl2br=function(){return this.replace(/\n/g,"<br />\n")},Object.changeKeys=function(e,t){var n={};for(var r in t){if(!t.hasOwnProperty(r))continue;typeof t[r]=="object"&&t[r]!==null&&!Array.isArray(t[r])?n[e(r)]=Object.changeKeys(e,t[r]):n[e(r)]=t[r]}return n},Object.keysToCamelCase=function(e){return Object.changeKeys(function(e){return e.toCamelCase()},e)},Object.keysFromCamelCase=function(e){return Object.changeKeys(function(e){return e.fromCamelCase()},e)};