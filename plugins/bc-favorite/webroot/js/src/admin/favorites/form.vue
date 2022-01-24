<template>
    <form :action="registerUrl" @submit.prevent="checkSubmit" method="POST" id="FavoriteAjaxForm">
        <input type="hidden" name="id" />
        <input type="hidden" name="user_id" :value="userId" />
        <dl>
            <!-- TDDO: ucmitz favorite-nameをnameに変更する? -->
            <dt><label for="favorite-name">{{ i18Title }}</label></dt>

            <dd>
                <input class="required" type="text" v-model="title" placeholder="タイトル" size=30 name="name" />
                <div class="invalid-feedback" v-if="!$v.title.required" style="color:red">必須です</div>
            </dd>
            <dt><label for="favorite-url" />{{ i18Url }}</dt>
            <dd>
                <input class="required" type="text" v-model="url" placeholder="URL" size=30 name="url" />
                <div class="invalid-feedback" v-if="!$v.url.required" style="color:red">必須です</div>
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
    validations: {
        title: { required },
        url: { required }
    },
    methods: {
        checkSubmit: function() {
        // this.$v.$touch();
        console.log('test')
        // エラーの場合は送信しない
        // if (this.$v.$pending || this.$v.$error) return;
        // alert("送信成功");
        }
    },
    props: ['userId'],
}
</script>

