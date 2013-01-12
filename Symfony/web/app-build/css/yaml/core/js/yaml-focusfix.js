/**
 * "Yet Another Multicolumn Layout" - YAML CSS Framework
 *
 * (en) Workaround for IE8 und Webkit browsers to fix focus problems when using skiplinks
 * (de) Workaround für IE8 und Webkit browser, um den Focus zu korrigieren, bei Verwendung von Skiplinks
 *
 * @note			inspired by Paul Ratcliffe's article
 *					http://www.communis.co.uk/blog/2009-06-02-skip-links-chrome-safari-and-added-wai-aria
 *                  Many thanks to Mathias Schäfer (http://molily.de/) for his code improvements
 *
 * @copyright       Copyright 2005-2012, Dirk Jesse
 * @license         CC-BY 2.0 (http://creativecommons.org/licenses/by/2.0/),
 *                  YAML-CDL (http://www.yaml.de/license.html)
 * @link            http://www.yaml.de
 * @package         yaml
 * @version         4.0+
 * @revision        $Revision: 617 $
 * @lastmodified    $Date: 2012-01-05 23:56:54 +0100 (Do, 05 Jan 2012) $
 */

(function(){var e={skipClass:"ym-skip",init:function(){var t=navigator.userAgent.toLowerCase(),n=t.indexOf("webkit")>-1,r=t.indexOf("msie")>-1;if(n||r){var i=document.body,s=e.click;i.addEventListener?i.addEventListener("click",s,!1):i.attachEvent&&i.attachEvent("onclick",s)}},trim:function(e){return e.replace(/^\s\s*/,"").replace(/\s\s*$/,"")},click:function(t){t=t||window.event;var n=t.target||t.srcElement,r=n.className.split(" ");for(var i=0;i<r.length;i++){var s=e.trim(r[i]);if(s===e.skipClass){e.focus(n);break}}},focus:function(e){if(e.href){var t=e.href,n=t.substr(t.indexOf("#")+1),r=document.getElementById(n);r&&(r.setAttribute("tabindex","-1"),r.focus())}}};e.init()})();