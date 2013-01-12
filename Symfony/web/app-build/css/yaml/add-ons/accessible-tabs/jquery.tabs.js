/**
 * Accessible Tabs - jQuery plugin for accessible, unobtrusive tabs
 * Build to seemlessly work with the CCS-Framework YAML (yaml.de) not depending on YAML though
 * @requires jQuery - tested with 1.7 and 1.4.2 but might as well work with older versions
 *
 * english article: http://blog.ginader.de/archives/2009/02/07/jQuery-Accessible-Tabs-How-to-make-tabs-REALLY-accessible.php
 * german article: http://blog.ginader.de/archives/2009/02/07/jQuery-Accessible-Tabs-Wie-man-Tabs-WIRKLICH-zugaenglich-macht.php
 *
 * code: http://github.com/ginader/Accessible-Tabs
 * please report issues at: http://github.com/ginader/Accessible-Tabs/issues
 *
 * Copyright (c) 2007 Dirk Ginader (ginader.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 * Version: 1.9.4
 *
 * History:
 * * 1.0 initial release
 * * 1.1 added a lot of Accessibility enhancements
 * * * rewrite to use "fn.extend" structure
 * * * added check for existing ids on the content containers to use to proper anchors in the tabs
 * * 1.1.1 changed the headline markup. thanks to Mike Davies for the hint.
 * * 1.5 thanks to Dirk Jesse, Ansgar Hein, David Maciejewski and Mike West for commiting patches to this release
 * * * new option syncheights that syncs the heights of the tab contents when the SyncHeight plugin
 * *   is available http://blog.ginader.de/dev/jquery/syncheight/index.php
 * * * fixed the hardcoded current class
 * * * new option tabsListClass to be applied to the generated list of tabs above the content so lists
 * *   inside the tabscontent can be styled differently
 * * * added clearfix and tabcounter that adds a class in the schema "tabamount{number amount of tabs}"
 * *   to the ul containg the tabs so one can style the tabs to fit 100% into the width
 * * * new option "syncHeightMethodName" fixed issue: http://github.com/ginader/Accessible-Tabs/issues/2/find
 * * * new Method showAccessibleTab({index number of the tab to show starting with 0})  fixed issue: http://github.com/ginader/Accessible-Tabs/issues/3/find
 * * * added support for the Cursor Keys to come closer to the WAI ARIA Tab Panel Best Practices http://github.com/ginader/Accessible-Tabs/issues/1/find
 * * 1.6
 * * * new option "saveState" to allow tabs remember their selected state using cookies requires the cookie plugin: http://plugins.jquery.com/project/Cookie
 * * * changed supported jquery version to 1.4.2 to make sure it's future compatible
 * * * new option "autoAnchor" which allows to add ID's to headlines in the tabs markup that allow direct linking into a tab i.e.: file.html#headlineID
 * * 1.7
 * * * new option "pagination" that adds links to show the next/previous tab. This adds the following markup to each tab for you to style:
 <ul class="pagination">
     <li class="previous"><a href="#{the-id-of-the-previous-tab}"><span>{the headline of the previous tab}</span></a></li>
     <li class="next"><a href="#{the-id-of-the-next-tab}"><span>{the headline of the previous tab}</span></a></li>
 </ul>
 * * 1.8
 * * * new option "position" can be 'top' or 'bottom'. Defines where the tabs list is inserted.
 * * 1.8.1
 * * * Bugfix for broken pagination in ie6 and 7: Selector and object access modified by Daniel K�nt�s (www.MilkmanMedia.de). Thanks to Carolin Moll for the report.
 * * 1.8.2
 * * * Bugfix for issue described by Sunshine here: http://blog.ginader.de/archives/2009/02/07/jQuery-Accessible-Tabs-How-to-make-tabs-REALLY-accessible.php#c916
 * * 1.8.3
 * * * Bugfix by Michael Schulze: Only change current class in tab navigation and not in all unordered lists inside the tabs.
 * * 1.9
 * * * new method showAccessibleTabSelector({valid jQuery selector of the tab to show}) that allows the opening of tabs \
 * * * by jQuery Selector instead of the index in showAccessibleTab() fixing issue https://github.com/ginader/Accessible-Tabs/issues/15
 * * 1.9.1 by Michael Schulze:
 * * * firstNavItemClass and lastNavItemClass to define a custom classname on the first and last tab
 * * * wrapInnerNavLinks: inner wrap for a-tags in tab navigation.
 * * 1.9.2
 * * * Bugfix by Dirk Jesse: fixing an issue that happened when passing multiple selectors to the init call instead of one
 * * * Bugfix that fixes a reset of the tabs counter when accessibleTabs() was called more than once on a page
 * * 1.9.3
 * * * Bugfix by Norm: before, when cssClassAvailable was true, all of the tabhead elements had to have classes or they wouldn't get pulled out into tabs.
 * * * This commit fixes this assumption, as I only want classes on some elements https://github.com/ginader/Accessible-Tabs/pull/25
 * * 1.9.4 Bugfix by Patrick Bruckner to fix issue with Internet Explorer using jQuery 1.7 https://github.com/ginader/Accessible-Tabs/issues/26
 */

(function(e){function n(e,n){t&&window.console&&window.console.log&&(n?window.console.log(n+": ",e):window.console.log(e))}var t=!0;e.fn.extend({getUniqueId:function(e,t,n){return n===undefined?n="":n="-"+n,e+t+n},accessibleTabs:function(t){var r={wrapperClass:"content",currentClass:"current",tabhead:"h4",tabheadClass:"tabhead",tabbody:".tabbody",fx:"show",fxspeed:"normal",currentInfoText:"current tab: ",currentInfoPosition:"prepend",currentInfoClass:"current-info",tabsListClass:"tabs-list",syncheights:!1,syncHeightMethodName:"syncHeight",cssClassAvailable:!1,saveState:!1,autoAnchor:!1,pagination:!1,position:"top",wrapInnerNavLinks:"",firstNavItemClass:"first",lastNavItemClass:"last"},i={37:-1,38:-1,39:1,40:1},s={top:"prepend",bottom:"append"};this.options=e.extend(r,t);var o=0;e("body").data("accessibleTabsCount")!==undefined&&(o=e("body").data("accessibleTabsCount")),e("body").data("accessibleTabsCount",this.size()+o);var u=this;return this.each(function(t){var r=e(this),a="",f=0,l=[];e(r).wrapInner('<div class="'+u.options.wrapperClass+'"></div>'),e(r).find(u.options.tabhead).each(function(n){var r="";elId=e(this).attr("id");if(elId){if(elId.indexOf("accessibletabscontent")===0)return;r=' id="'+elId+'"'}var i=u.getUniqueId("accessibletabscontent",o+t,n),s=u.getUniqueId("accessibletabsnavigation",o+t,n);l.push(i);if(u.options.cssClassAvailable===!0){var c="";e(this).attr("class")&&(c=e(this).attr("class"),c=' class="'+c+'"'),a+='<li id="'+s+'"><a'+r+""+c+' href="#'+i+'">'+e(this).html()+"</a></li>"}else a+='<li id="'+s+'"><a'+r+' href="#'+i+'">'+e(this).html()+"</a></li>";e(this).attr({id:i,"class":u.options.tabheadClass,tabindex:"-1"}),f++}),u.options.syncheights&&e.fn[u.options.syncHeightMethodName]&&(e(r).find(u.options.tabbody)[u.options.syncHeightMethodName](),e(window).resize(function(){e(r).find(u.options.tabbody)[u.options.syncHeightMethodName]()}));var c="."+u.options.tabsListClass;e(r).find(c).length||e(r)[s[u.options.position]]('<ul class="clearfix '+u.options.tabsListClass+" tabamount"+f+'"></ul>'),e(r).find(c).append(a);var h=e(r).find(u.options.tabbody);h.length>0&&(e(h).hide(),e(h[0]).show()),e(r).find("ul."+u.options.tabsListClass+">li:first").addClass(u.options.currentClass).addClass(u.options.firstNavItemClass).find("a")[u.options.currentInfoPosition]('<span class="'+u.options.currentInfoClass+'">'+u.options.currentInfoText+"</span>").parents("ul."+u.options.tabsListClass).children("li:last").addClass(u.options.lastNavItemClass),u.options.wrapInnerNavLinks&&e(r).find("ul."+u.options.tabsListClass+">li>a").wrapInner(u.options.wrapInnerNavLinks),e(r).find("ul."+u.options.tabsListClass+">li>a").each(function(t){e(this).click(function(n){n.preventDefault(),r.trigger("showTab.accessibleTabs",[e(n.target)]),u.options.saveState&&e.cookie&&e.cookie("accessibletab_"+r.attr("id")+"_active",t),e(r).find("ul."+u.options.tabsListClass+">li."+u.options.currentClass).removeClass(u.options.currentClass).find("span."+u.options.currentInfoClass).remove(),e(this).blur(),e(r).find(u.options.tabbody+":visible").hide(),e(r).find(u.options.tabbody).eq(t)[u.options.fx](u.options.fxspeed),e(this)[u.options.currentInfoPosition]('<span class="'+u.options.currentInfoClass+'">'+u.options.currentInfoText+"</span>").parent().addClass(u.options.currentClass),e(e(this).attr("href"),!0).focus().keyup(function(n){i[n.keyCode]&&(u.showAccessibleTab(t+i[n.keyCode]),e(this).unbind("keyup"))})}),e(this).focus(function(n){e(document).keyup(function(e){i[e.keyCode]&&u.showAccessibleTab(t+i[e.keyCode])})}),e(this).blur(function(t){e(document).unbind("keyup")})});if(u.options.saveState&&e.cookie){var p=e.cookie("accessibletab_"+r.attr("id")+"_active");n(e.cookie("accessibletab_"+r.attr("id")+"_active")),p!==null&&u.showAccessibleTab(p,r.attr("id"))}if(u.options.autoAnchor&&window.location.hash){var d=e("."+u.options.tabsListClass).find(window.location.hash);d.size()&&d.click()}if(u.options.pagination){var v='<ul class="pagination">';v+='    <li class="previous"><a href="#{previousAnchor}"><span>{previousHeadline}</span></a></li>',v+='    <li class="next"><a href="#{nextAnchor}"><span>{nextHeadline}</span></a></li>',v+="</ul>";var m=e(r).find(".tabbody"),g=m.size();m.each(function(t){e(this).append(v);var n=t+1;n>=g&&(n=0);var i=t-1;i<0&&(i=g-1);var s=e(this).find(".pagination"),o=s.find(".previous");o.find("span").text(e("#"+l[i]).text()),o.find("a").attr("href","#"+l[i]).click(function(t){t.preventDefault(),e(r).find(".tabs-list a").eq(i).click()});var u=s.find(".next");u.find("span").text(e("#"+l[n]).text()),u.find("a").attr("href","#"+l[n]).click(function(t){t.preventDefault(),e(r).find(".tabs-list a").eq(n).click()})})}})},showAccessibleTab:function(t,r){n("showAccessibleTab");var i=this;if(!r)return this.each(function(){var n=e(this);n.trigger("showTab.accessibleTabs");var r=n.find("ul."+i.options.tabsListClass+">li>a");n.trigger("showTab.accessibleTabs",[r.eq(t)]),r.eq(t).click()});var s=e("#"+r),o=s.find("ul."+i.options.tabsListClass+">li>a");s.trigger("showTab.accessibleTabs",[o.eq(t)]),o.eq(t).click()},showAccessibleTabSelector:function(t){n("showAccessibleTabSelector");var r=this,i=e(t);i&&(i.get(0).nodeName.toLowerCase()=="a"?i.click():n("the selector of a showAccessibleTabSelector() call needs to point to a tabs headline!"))}})})(jQuery);