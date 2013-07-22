/**
 * @version    1.7.0.1 May 8, 2012
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright   Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license    http://www.rockettheme.com/legal/license.php RocketTheme Proprietary Use License
 */

(function($){$.fn.extend({magemenus:function(options){var defaults={mm_trigger:"ul.menu",mm_slidedownspeed:250,mm_fadeoutspeed:150,mm_css_pre:{left:100,opacity:0},mm_animatein:{left:165,opacity:1},mm_animateout:{opacity:0,left:180},mm_animate_speed:200,mm_pause:0,mm_offsetfix:0};var options=$.extend(defaults,options);return this.each(function(){var o=options;$.fn.pause=function(duration){$(this).animate({dummy:1},duration);return this};$(o.mm_trigger+' > li').hover(function(){$('ul',this).prev(".bg-top-curves").stop(true,true).show();$(this).addClass("hover").children('ul').stop(true,true).slideDown(o.mm_slidedownspeed)},function(){$('ul',this).prev(".bg-top-curves").stop(true,true).pause(o.mm_pause).slideUp(o.mm_fadeoutspeed);$(this).removeClass("hover").children('ul').stop(true,true).pause(o.mm_pause).fadeOut(o.mm_fadeoutspeed)});$(o.mm_trigger+' li.level1').hover(function(){$(this).addClass("hover");if(jQuery.browser.msie){$('ul.level1',this).prev(".bg-top").css(o.mm_css_pre).show().stop(true,true).animate(o.mm_animatein,o.mm_animate_speed,function(){this.style.removeAttribute("filter")});$('ul.level1',this).css(o.mm_css_pre).show().stop(true,true).animate(o.mm_animatein,o.mm_animate_speed,function(){this.style.removeAttribute("filter")})}else{$('ul.level1',this).prev(".bg-top").css(o.mm_css_pre).show().stop(true,true).animate(o.mm_animatein,o.mm_animate_speed);$('ul.level1',this).css(o.mm_css_pre).show().stop(true,true).animate(o.mm_animatein,o.mm_animate_speed)}},function(){$(this).removeClass("hover");$('ul.level1',this).prev(".bg-top").stop(true,true).animate(o.mm_animateout,o.mm_animate_speed).hide(0.1);$('ul.level1',this).stop(true,true).animate(o.mm_animateout,o.mm_animate_speed).hide(0.1)});$(o.mm_trigger+' li.level2').hover(function(){$(this).addClass("hover");if(jQuery.browser.msie){$('ul.level2',this).prev(".bg-top").css(o.mm_css_pre).show().stop(true,true).animate(o.mm_animatein,o.mm_animate_speed,function(){this.style.removeAttribute("filter")});$('ul.level2',this).css(o.mm_css_pre).show().stop(true,true).animate(o.mm_animatein,o.mm_animate_speed,function(){this.style.removeAttribute("filter")})}else{$('ul.level2',this).prev(".bg-top").css(o.mm_css_pre).show().stop(true,true).animate(o.mm_animatein,o.mm_animate_speed);$('ul.level2',this).css(o.mm_css_pre).show().stop(true,true).animate(o.mm_animatein,o.mm_animate_speed)}},function(){$(this).removeClass("hover");$('ul.level2',this).prev(".bg-top").stop(true,true).animate(o.mm_animateout,o.mm_animate_speed).hide(0.1);$('ul.level2',this).stop(true,true).animate(o.mm_animateout,o.mm_animate_speed).hide(0.1)});$(o.mm_trigger+' li.level2',this).addClass("hover2");$(o.mm_trigger+' li.level3',this).hover(function(){$(this).addClass("hover")},function(){$(this).removeClass("hover")});if(o.mm_offsetfix==1){$(o.mm_trigger+' > li.parent').one("mouseenter",function(){var container=$(this).parent().offset().left+$(this).parent().outerWidth();var dropdown=$(this).children('ul').offset().left+$(this).children('ul').outerWidth();var offset=dropdown-container;if(dropdown>container){$(this).children('ul').css({'margin-left':-offset}).pause(o.mm_pause)}})}})}})})(jQuery);(function($){$(document).ready(function(){if($('ul#magemenu-top').hasClass('menu')){$("ul.menu li ul.level0, ul.menu li ul.level1, ul.menu li ul.level2").each(function(){var elem=$(this);if(elem.children("li").length%2!=0){elem.append('<li class="li-spacer">&nbsp;</li>')}})}$('ul#magemenu-top li ul.level0').before('<div class="bg-top-curves">&nbsp;</div>');$('ul#magemenu-top li ul li ul.level1, ul#magemenu-top li ul li ul.level2').before('<div class="bg-top">&nbsp;</div>');$('ul#magemenu-top li ul.popup > li').before('<div class="bg-top">&nbsp;</div>');$(".level1.parent  > a span, .level2.parent  > a span, .level3.parent  > a span").addClass("arrow");$.fn.pause=function(duration){$(this).animate({dummy:1},duration);return this};if(jQuery.browser.msie){if(parseInt(jQuery.browser.version)==6){$('ul#magemenu-top').hover(function(){$(':input').hide()},function(){$(':input').show()})}};var title=$.trim(document.title);var title2=title.replace(/\s+/g,'').replace('"','');var cmsalt="li[id='"+title2+"']";$(cmsalt).addClass("active");$(cmsalt).parents("ul").parents("li").addClass("active");$('.cms-index-index li#Home, .contacts-index-index li#Contact').addClass("active")})})(jQuery);