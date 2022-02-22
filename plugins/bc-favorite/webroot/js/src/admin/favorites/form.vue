<template>
    <form :action="registerUrl" method="POST" id="FavoriteAjaxForm">
        <input type="hidden" name="id" :value="id" />
        <input type="hidden" name="user_id" :value="userId" />
        <input type="hidden" name="_csrfToken" />
        <dl>
            <!-- TDDO: ucmitz favorite-nameをnameに変更する? -->
            <dt><label for="favorite-name">{{ i18Title }}</label></dt>
            <dd>
                <span class="bca-textbox">
                    <input class="required" type="text" v-model="title" id="FavoriteName" placeholder="タイトル" size=30 name="name" @input="formUpdated" />
                </span>
                <div class="invalid-feedback" v-if="$v.title.$invalid" style="color:red">必須です</div>
            </dd>
            <dt><label for="favorite-url" />{{ i18Url }}</dt>
            <dd>
                <span class="bca-textbox">
                    <input class="required" type="text" v-model="url" id="FavoriteUrl" placeholder="URL" size=30 name="url" @input="formUpdated" />
                </span>
                <div class="invalid-feedback" v-if="$v.url.$invalid" style="color:red">必須です</div>
            </dd>
        </dl>
    </form>
</template>

<script>
import { required } from "vuelidate/lib/validators";
import axios from "axios";

export default {
    name: "FavoriteForm",
    data () {
        return {
            registerUrl: $.bcUtil.apiBaseUrl + "bc-favorite/favorites/add.json",
            i18Title: 'title',
            i18Url: 'url',
            title: this.title,
            url: this.url,
        }
    },
    mounted() {
        this.title = this.currentPageName;
        this.url = this.currentPageUrl;
    },
    methods: {
        formUpdated: function() {
            this.$emit("formUpdated", this.$v.$invalid);
        },
        formSubmit: function() {
            let registerUrl = this.registerUrl;
            let userId = this.userId;
            let title = this.title;
            let url = this.url;
            $.bcToken.check(function () {
                $('#FavoriteAjaxForm input[name="_csrfToken"]').val($.bcToken.key);
                axios.post(registerUrl, {
                    user_id: userId,
                    name: title,
                    url: url
                }, {
                    headers: {
                        "Authorization": $.bcJwt.accessToken,
                    }
                }).then(function (response) {
                    if (response.data) {
                        $("#Waiting").hide();
                        $.bcToken.key = null;
                        // TODO: モーダルのクローズがうまくいってない
                        this.$emit("formSubmited");
                    }
                }.bind(this))
                .catch(function (error) {
                    // this.isError = true
                    // if(error.response.status === 401) {
                    //     this.message = 'アカウント名、パスワードが間違っています。'
                    // } else {
                    //     this.message = error.response.data.message
                    // }
                }.bind(this));

                //     return $("#FavoriteAjaxForm").ajaxSubmit({
                //         url: submitUrl,
                //         headers: {
                //             "Authorization": $.bcJwt.accessToken,
                //         },
                //         success: function () {
                //             favoriteIndex.refresh();
                //             // TODO ucmitz 未精査
                //             // initFavoriteList();
                //             $("#FavoriteDialog").dialog('close');
                //         },
                //         error: function (XMLHttpRequest, textStatus) {
                //             if (XMLHttpRequest.responseText) {
                //                 alert(bcI18n.favoriteAlertMessage2 + '\n\n' + XMLHttpRequest.responseText);
                //             } else {
                //                 alert(bcI18n.favoriteAlertMessage2 + '\n\n' + XMLHttpRequest.statusText);
                //             }
                //         },
                //         complete: function () {
                //             $("#Waiting").hide();
                //             $.bcToken.key = null;
                //         }
                //     });

            }, {useUpdate: false, hideLoader: false});
        }
    },
    validations: {
        title: { required },
        url: { required }
    },
    props: ['id', 'userId', 'currentPageName', 'currentPageUrl']
}
</script>

