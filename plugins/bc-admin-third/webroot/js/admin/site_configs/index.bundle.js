!function(e){var n={};function t(r){if(n[r])return n[r].exports;var o=n[r]={i:r,l:!1,exports:{}};return e[r].call(o.exports,o,o.exports,t),o.l=!0,o.exports}t.m=e,t.c=n,t.d=function(e,n,r){t.o(e,n)||Object.defineProperty(e,n,{enumerable:!0,get:r})},t.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},t.t=function(e,n){if(1&n&&(e=t(e)),8&n)return e;if(4&n&&"object"==typeof e&&e&&e.__esModule)return e;var r=Object.create(null);if(t.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:e}),2&n&&"string"!=typeof e)for(var o in e)t.d(r,o,function(n){return e[n]}.bind(null,o));return r},t.n=function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(n,"a",n),n},t.o=function(e,n){return Object.prototype.hasOwnProperty.call(e,n)},t.p="",t(t.s=15)}({15:function(e,n){
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */
$((function(){var e=$("#AdminSiteConfigsFormScript").attr("data-isAdminSsl");function n(){"BcCkeditor"===$('input[name="editor"]:checked').val()?$(".ckeditor-option").show():$(".ckeditor-option").hide()}$("#BtnSave").click((function(){if(!function(e){if("0"===e&&"1"===$("input[name='admin_ssl']:checked").val())return $("#SiteConfigSslUrl").val()?($.bcConfirm.show({title:bcI18n.confirmTitle1,message:bcI18n.confirmMessage1,defaultCancel:!0,ok:function(){$.bcUtil.showLoader(),$("#SiteConfigFormForm").submit()}}),!1):(alert(bcI18n.alertMessage1),window.location.hash="ssl-url",!1);return!0}(e))return!1;$.bcUtil.showLoader()})),$('input[name="editor"]').click(n),n(),$("#BtnCheckSendmail").click((function(){return!!confirm(bcI18n.confirmMessage2)&&($.bcToken.check((function(){return $.ajax({type:"POST",url:$.bcUtil.apiBaseUrl+"baser-core/site_configs/check_sendmail.json",data:$("#SiteConfigFormForm").serialize(),beforeSend:function(){$("#ResultCheckSendmail").hide(),$("#AjaxLoaderCheckSendmail").show()},success:function(e){$("#ResultCheckSendmail").html(bcI18n.infoMessage1)},error:function(e,n,t){var r="";r=e.responseText?e.responseText:t,$("#ResultCheckSendmail").html(bcI18n.alertMessage2+r)},complete:function(){$("#ResultCheckSendmail").show(),$("#AjaxLoaderCheckSendmail").hide()}})}),{loaderType:"none"}),!1)}))}))}});
//# sourceMappingURL=index.bundle.js.map