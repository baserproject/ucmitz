!function(t){var e={};function n(a){if(e[a])return e[a].exports;var r=e[a]={i:a,l:!1,exports:{}};return t[a].call(r.exports,r,r.exports,n),r.l=!0,r.exports}n.m=t,n.c=e,n.d=function(t,e,a){n.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:a})},n.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},n.t=function(t,e){if(1&e&&(t=n(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var a=Object.create(null);if(n.r(a),Object.defineProperty(a,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var r in t)n.d(a,r,function(e){return t[e]}.bind(null,r));return a},n.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return n.d(e,"a",e),e},n.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},n.p="",n(n.s=20)}({20:function(t,e){
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */
$((function(){$(".help").bt&&($(".helptext").css("display","none"),$.bt.options.closeWhenOthersOpen=!0,$(".help").bt({trigger:"click",positions:"top",shadow:!0,shadowOffsetX:1,shadowOffsetY:1,shadowBlur:8,shadowColor:"rgba(101,101,101,.6)",shadowOverlap:!1,noShadowOpts:{strokeStyle:"#999",strokeWidth:1},width:"600px",spikeLength:12,spikeGirth:18,padding:20,cornerRadius:0,strokeWidth:1,strokeStyle:"#656565",fill:"rgba(255, 255, 255, 1.00)",cssStyles:{fontSize:"14px"},showTip:function(t){$(t).fadeIn(200)},hideTip:function(t,e){$(t).animate({opacity:0},100,e)},contentSelector:"$(this).next('.helptext').html()"})),$("#BtnMenuHelp").click((function(){"none"===$("#Help").css("display")?$("#Help").fadeIn(300):$("#Help").fadeOut(300)})),$("#CloseHelp").click((function(){$("#Help").fadeOut(300)})),$.bcUtil.init({}),$.bcToken.init(),$("[data-bca-collapse='collapse']").on({click:function(){var t=$(this).attr("data-bca-target");return"open"==$(t).attr("data-bca-state")?($(t).attr("data-bca-state","").slideUp(),$(this).attr("data-bca-state","").attr("aria-expanded","true")):($(t).attr("data-bca-state","open").slideDown(),$(this).attr("data-bca-state","open").attr("aria-expanded","false")),!1}}),$(".error-message:has(ul)").removeClass("error-message").addClass("error-wrap")}))}});
//# sourceMappingURL=startup.bundle.js.map