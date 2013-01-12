/**
 * syncHeight - jQuery plugin to automagically Snyc the heights of columns
 * Made to seemlessly work with the CCS-Framework YAML (yaml.de)
 * @requires jQuery v1.0.3
 *
 * http://blog.ginader.de/dev/syncheight/
 *
 * Copyright (c) 2007-2009
 * Dirk Ginader (ginader.de)
 * Dirk Jesse (yaml.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 * Version: 1.2
 *
 * Usage:
	$(window).load(function(){
		$('p').syncHeight();
	});
 */

(function(e){var t=function(){var t=0,n=[["min-height","0px"],["height","1%"]];return e.browser.msie&&e.browser.version<7&&(t=1),{name:n[t][0],autoheightVal:n[t][1]}};e.getSyncedHeight=function(n){var r=0,i=t();return e(n).each(function(){e(this).css(i.name,i.autoheightVal);var t=e(this).height();t>r&&(r=t)}),r},e.fn.syncHeight=function(n){var r={updateOnResize:!1,height:!1},i=e.extend(r,n),s=this,o=0,u=t().name;return typeof i.height=="number"?o=i.height:o=e.getSyncedHeight(this),e(this).each(function(){e(this).css(u,o+"px")}),i.updateOnResize===!0&&e(window).resize(function(){e(s).syncHeight()}),this}})(jQuery);