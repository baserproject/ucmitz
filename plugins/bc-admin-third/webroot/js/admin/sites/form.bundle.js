!function(e){var n={};function t(i){if(n[i])return n[i].exports;var o=n[i]={i:i,l:!1,exports:{}};return e[i].call(o.exports,o,o.exports,t),o.l=!0,o.exports}t.m=e,t.c=n,t.d=function(e,n,i){t.o(e,n)||Object.defineProperty(e,n,{enumerable:!0,get:i})},t.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},t.t=function(e,n){if(1&n&&(e=t(e)),8&n)return e;if(4&n&&"object"==typeof e&&e&&e.__esModule)return e;var i=Object.create(null);if(t.r(i),Object.defineProperty(i,"default",{enumerable:!0,value:e}),2&n&&"string"!=typeof e)for(var o in e)t.d(i,o,function(n){return e[n]}.bind(null,o));return i},t.n=function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(n,"a",n),n},t.o=function(e,n){return Object.prototype.hasOwnProperty.call(e,n)},t.p="",t(t.s=15)}({15:function(e,n){
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */
$((function(){var e=$("#alias").val();function n(){var e=$.bcUtil.apiBaseUrl+"baser-core/sites/get_selectable_devices_and_lang/"+$("#main-site-id").val()+".json";void 0!==$("#id").val()&&(e+="/"+$("#id").val()),$.bcUtil.ajax(e,(function(e){var n=$("#device"),i=$("#lang"),o=n.val(),r=i.val();n.find("option").remove(),i.find("option").remove(),e=$.parseJSON(e),$.each(e.devices,(function(e,t){n.append($("<option>").val(e).text(t).prop("selected",e===o))})),$.each(e.langs,(function(e,n){i.append($("<option>").val(e).text(n).prop("selected",e===r))})),t()}),{type:"GET",loaderType:"after",loaderSelector:"#main-site-id"})}function t(){var e=$("#auto-redirect"),n=$("#same-main-url"),t=$("#auto-link"),i=$("#SpanSiteAutoRedirect"),o=$("#SpanSiteAutoLink");$("#device").val()||$("#lang").val()?$("#SectionAccessType").show():($("#SectionAccessType").hide(),e.prop("checked",!1),n.prop("checked",!1),t.prop("checked",!1)),n.prop("checked")?(e.prop("checked",!1),i.hide(),t.prop("checked",!1),o.hide()):(i.show(),"mobile"==$("#device").val()||"smartphone"==$("#device").val()?o.show():o.hide())}$("#BtnSave").click((function(){if(e&&e!=$("#alias").val())return $.bcConfirm.show({title:bcI18n.confirmTitle1,message:bcI18n.confirmMessage2,ok:function(){$.bcUtil.showLoader(),$("#SiteAdminEditForm").submit()}}),!1;$.bcUtil.showLoader()})),$("#main-site-id").change(n),$("#device, #lang").change(t),$('input[name="same_main_url"]').click(t),n()}))}});
//# sourceMappingURL=form.bundle.js.map