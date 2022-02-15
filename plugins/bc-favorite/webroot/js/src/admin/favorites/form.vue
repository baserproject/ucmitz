<template>
    <form :action="registerUrl" method="POST" id="FavoriteAjaxForm">
        <input type="hidden" name="id" />
        <input type="hidden" name="user_id" :value="userId" />
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
    },
    validations: {
        title: { required },
        url: { required }
    },
    props: ['userId', 'currentPageName', 'currentPageUrl']
}
</script>

