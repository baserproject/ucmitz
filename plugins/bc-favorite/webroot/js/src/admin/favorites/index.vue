<template>
    <div>
        <h2 class="bca-nav-favorite-title">
            <button type="button" id="btn-favorite-expand" class="bca-collapse__btn bca-nav-favorite-title-button"
                    data-bca-collapse="favorite-collapse" data-bca-target="#favoriteBody" aria-expanded="false"
                    aria-controls="favoriteBody" :data-bca-state="favoriteBoxOpened">
            {{ i18Favorite }} <i class="bca-icon--chevron-down bca-nav-favorite-title-icon"></i>
            </button>
        </h2>
<!--
        <ul v-if="favorites" class="favorite-menu-list bca-nav-favorite-list bca-collapse" id="favoriteBody">
            <li v-for="(favorite, i) in favorites" :key="i" :id="'FavoriteRow' + name" class="bca-nav-favorite-list-item">
                <a :href="url" :title="fullUrl"><span class="bca-nav-favorite-list-item-label">{{name}}</span></a>
                <hidden :value="id" class="favorite-id"  :name="'id' + '.' + id" />
                <hidden :value="name" class="favorite-name"  :name="'name' + '.' + id" />
                <hidden :value="url" class="favorite-url"  :name="'url' + '.' + id" />
            </li>
        </ul>
        <ul v-else class="favorite-menu-list bca-nav-favorite-list bca-collapse" id="favoriteBody">
            <li  class="no-data"><small>{{ i18NoData }}</small></li>
        </ul>

        <div id="FavoriteDialog" title="お気に入り登録" style="display:none">
            <form :action="registerUrl" method="POST">
                <input type="hidden" name="id" />
                <dl>
                    <dt><label for="favorite-name">{{ i18Title }}</label></dt>
                    <dd><input class="required" type="text" size=30 name="favorite-name" /></dd>
                    <dt><label for="favorite-url" />{{ i18Url }}</dt>
                    <dd><input class="required" type="text" size=30 name="favorite-url" /></dd>
                </dl>
            </form>
        </div>
        <ul id="FavoritesMenu" class="context-menu" style="display:none">
            <li class="edit"><a href="#FavoriteEdit">{{ i18Edit }}</a></li>
            <li class="delete"><a href="#FavoriteDelete">{{ i18Delete }}</a></li>
        </ul> -->
    </div>
</template>

<script>
module.exports = {
    data:function () {
    return {
        favoriteBoxOpened: true,
        i18Favorite: 'testest2'
        }
    }
}
</script>
