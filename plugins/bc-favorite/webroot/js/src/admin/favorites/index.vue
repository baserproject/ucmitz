<template>
    <div>
        <h2 class="bca-nav-favorite-title">
            <button type="button" id="btn-favorite-expand" class="bca-collapse__btn bca-nav-favorite-title-button"
                    data-bca-collapse="favorite-collapse" data-bca-target="#favoriteBody" :aria-expanded="ariaExpanded"
                    aria-controls="favoriteBody" @click="changeOpenFavorite">
            {{ i18Favorite }} <i class="bca-icon--chevron-down bca-nav-favorite-title-icon"></i>
            </button>
        </h2>

        <ul v-if="favorites.length" :style="'display:' + favoriteBoxOpened" class="favorite-menu-list bca-nav-favorite-list bca-collapse" id="favoriteBody">
            <li v-for="(favorite, i) in favorites" :key="i" :id="'FavoriteRow' + favorite.name" class="bca-nav-favorite-list-item">
                <a :href="baseUrl + favorite.url" :title="favorite.url"><span class="bca-nav-favorite-list-item-label">{{favorite.name}}</span></a>
                <input type="hidden" :value="favorite.id" class="favorite-id"  :name="'id' + '.' + favorite.id" />
                <input type="hidden" :value="favorite.name" class="favorite-name"  :name="'name' + '.' + favorite.id" />
                <input type="hidden" :value="favorite.url" class="favorite-url"  :name="'url' + '.' + favorite.id" />
            </li>
        </ul>
        <ul :style="'display:' + favoriteBoxOpened" v-else class="favorite-menu-list bca-nav-favorite-list bca-collapse" id="favoriteBody">
            <li  class="no-data"><small>{{ i18NoData }}</small></li>
        </ul>

        <div id="FavoriteDialog" title="お気に入り登録" style="display:none">
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
        </div>
        <ul id="FavoritesMenu" class="context-menu" style="display:none">
            <li class="edit"><a href="#FavoriteEdit">{{ i18Edit }}</a></li>
            <li class="delete"><a href="#FavoriteDelete">{{ i18Delete }}</a></li>
        </ul>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data:function () {
        return {
            favoriteBoxOpened: "none",
            i18Favorite: 'testest2',
            favorites: [],
            i18NoData: 'nodata',
            registerUrl: $.bcUtil.apiBaseUrl + "bc-favorite/favorites/add.json",
            i18Title: 'title',
            i18Url: 'url',
            i18Edit: 'edit',
            i18Delete: 'delete',
            ariaExpanded: 'true',
            baseUrl: $.bcUtil.baseUrl,
        }
    },
    props: ['userId'],
    /**
     * Methods
     */
    methods: {
        /**
         * initFavorite
         */
        initFavorite: function() {
            // 一覧呼び出し
            this.refresh();
            // 開閉
            var url = $.bcUtil.apiBaseUrl + "bc-favorite/favorites/get_favorite_box_opened.json";
            axios.get(url).then(function (response) {
                if (response.data.result === "1") {
                    this.favoriteBoxOpened = "block";
                    this.ariaExpanded = 'false';
                } else {
                    this.favoriteBoxOpened = 'none';
                    this.ariaExpanded = 'true';
                }
            }.bind(this));
        },
        changeOpenFavorite: function() {
            if (this.favoriteBoxOpened == 'block') {
                // ボタンの制御
                this.favoriteBoxOpened = 'none';
                this.ariaExpanded = 'true';
                var url = $.bcUtil.apiBaseUrl + "bc-favorite/favorites/save_favorite_box.json";
                axios.post(url);
            } else {
                // ボタンの制御
                this.favoriteBoxOpened = 'block';
                this.ariaExpanded = 'false';
                var url = $.bcUtil.apiBaseUrl + "bc-favorite/favorites/save_favorite_box/1.json";
                axios.post(url);
            }
        },
        refresh: function() {
            // 一覧呼び出し
            const indexUrl = $.bcUtil.apiBaseUrl + "bc-favorite/favorites/index.json";
            axios.get(indexUrl).then(function (response) {
                this.favorites = response.data.favorites;
            }.bind(this));
        },

    },
    mounted: function() {
        this.initFavorite();
    }
}
</script>
