<template>
    <form :action="registerUrl" method="POST" id="FavoriteAjaxForm">
        <input type="hidden" name="id" />
        <input type="hidden" name="user_id" :value="userId" />
        <dl>
            <!-- TDDO: ucmitz favorite-nameをnameに変更する? -->
            <dt><label for="favorite-name">{{ i18Title }}</label></dt>
            <dd><input class="required" type="text" size=30 name="name" /></dd>
            <dt><label for="favorite-url" />{{ i18Url }}</dt>
            <dd><input class="required" type="text" size=30 name="url" /></dd>
        </dl>
    </form>
</template>

<script>
export default {
    data:function () {
        return {
            registerUrl: $.bcUtil.apiBaseUrl + "bc-favorite/favorites/add.json",
            i18Title: 'title',
            i18Url: 'url',
        }
    },
    props: ['userId'],
}
</script>

