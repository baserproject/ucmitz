!function(t){var e={};function o(r){if(e[r])return e[r].exports;var n=e[r]={i:r,l:!1,exports:{}};return t[r].call(n.exports,n,n.exports,o),n.l=!0,n.exports}o.m=t,o.c=e,o.d=function(t,e,r){o.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:r})},o.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},o.t=function(t,e){if(1&e&&(t=o(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var r=Object.create(null);if(o.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var n in t)o.d(r,n,function(e){return t[e]}.bind(null,n));return r},o.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return o.d(e,"a",e),e},o.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},o.p="",o(o.s=29)}({29:function(t,e){
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */
$((function(){$(".help").bt&&($(".helptext").css("display","none"),$.bt.options.closeWhenOthersOpen=!0,$(".help").bt({trigger:"click",positions:"top",shadow:!0,shadowOffsetX:1,shadowOffsetY:1,shadowBlur:8,shadowColor:"rgba(101,101,101,.6)",shadowOverlap:!1,noShadowOpts:{strokeStyle:"#999",strokeWidth:1},width:"600px",spikeLength:12,spikeGirth:18,padding:20,cornerRadius:0,strokeWidth:1,strokeStyle:"#656565",fill:"rgba(255, 255, 255, 1.00)",cssStyles:{fontSize:"14px"},showTip:function(t){$(t).fadeIn(200)},hideTip:function(t,e){$(t).animate({opacity:0},100,e)},contentSelector:"$(this).next('.helptext').html()"})),$("a[rel='colorbox']").colorbox&&$("a[rel='colorbox']").colorbox({maxWidth:"60%"}),$("#BtnMenuHelp").click((function(){"none"===$("#Help").css("display")?$("#Help").fadeIn(300):$("#Help").fadeOut(300)})),$("#CloseHelp").click((function(){$("#Help").fadeOut(300)})),$.bcUtil.init({}),$.bcToken.init(),$.bcJwt.init(),$("[data-bca-collapse='collapse']").on({click:function(){var t=$(this).attr("data-bca-target");return"open"==$(t).attr("data-bca-state")?($(t).attr("data-bca-state","").slideUp(),$(this).attr("data-bca-state","").attr("aria-expanded","true")):($(t).attr("data-bca-state","open").slideDown(),$(this).attr("data-bca-state","open").attr("aria-expanded","false")),!1}}),$(".error-message:has(ul)").removeClass("error-message").addClass("error-wrap");var t=$.bcUtil.frontFullUrl;document.queryCommandSupported("copy")?t&&$("#BtnCopyUrl").on({click:function(){var e=$('<textarea style=" opacity:0; width:1px; height:1px; margin:0; padding:0; border-style: none;"/>');return e.text(t),$(this).after(e),e.select(),document.execCommand("copy"),e.remove(),$("#BtnCopyUrl").tooltip("dispose"),$("#BtnCopyUrl").tooltip({title:"コピーしました"}),$("#BtnCopyUrl").tooltip("show"),!1},mouseenter:function(){$("#BtnCopyUrl").tooltip("dispose"),$("#BtnCopyUrl").tooltip({title:"公開URLをコピー"}),$("#BtnCopyUrl").tooltip("show")},mouseleave:function(){$("#BtnCopyUrl").tooltip("hide")}}):$("#BtnCopyUrl").hide()}))}});
//# sourceMappingURL=startup.bundle.js.map