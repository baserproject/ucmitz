<template>
    <div>
        <h2 class="bca-nav-favorite-title">
            <button type="button" id="btn-favorite-expand" class="bca-collapse__btn bca-nav-favorite-title-button"
                    data-bca-collapse="favorite-collapse" data-bca-target="#favoriteBody" aria-expanded="false"
                    aria-controls="favoriteBody" :data-bca-state="favoriteBoxOpened">
            {{ favoriteTitle }} <i class="bca-icon--chevron-down bca-nav-favorite-title-icon"></i>
            </button>
        </h2>

        <ul v-if="favorites" class="favorite-menu-list bca-nav-favorite-list bca-collapse" id="favoriteBody">
            <li v-for="(favorite, i) in favorites" :key="i" :id="'FavoriteRow' + name" class="bca-nav-favorite-list-item">
                <a :href="url" :title="fullUrl"><span class="bca-nav-favorite-list-item-label">{{name}}</span></a>
                <hidden :value="id" class="favorite-id"  :name="'id' + '.' + id" />
                <hidden :value="name" class="favorite-name"  :name="'name' + '.' + id" />
                <hidden :value="url" class="favorite-url"  :name="'url' + '.' + id" />
            </li>
        </ul>
        <ul v-else class="favorite-menu-list bca-nav-favorite-list bca-collapse" id="favoriteBody">
            <li  class="no-data"><small>{{ noDataMessage }}</small></li>
        </ul>

        <div id="FavoriteDialog" title="お気に入り登録" style="display:none">
        <?php echo $this->BcAdminForm->create('Favorite', ['url' => ['plugin' => null, 'action' => 'ajax']]) ?>
        <?php echo $this->BcAdminForm->control('Favorite.id', ['type' => 'hidden']) ?>
        <dl>
            <dt><?php echo $this->BcForm->label('Favorite.name', __d('baser', 'タイトル')) ?></dt>
            <dd><?php echo $this->BcAdminForm->control('Favorite.name', ['type' => 'text', 'size' => 30, 'class' => 'required']) ?></dd>
            <dt><?php echo $this->BcForm->label('Favorite.url', __d('baser', 'URL')) ?></dt>
            <dd><?php echo $this->BcAdminForm->control('Favorite.url', ['type' => 'text', 'size' => 30, 'class' => 'required']) ?></dd>
        </dl>
        <?php echo $this->BcAdminForm->end() ?>
        </div>


        <ul id="FavoritesMenu" class="context-menu" style="display:none">
            <li class="edit"><?php $this->BcBaser->link(__d('baser', '編集'), '#FavoriteEdit') ?></li>
            <li class="delete"><?php $this->BcBaser->link(__d('baser', '削除'), '#FavoriteDelete') ?></li>
        </ul>
    </div>
</template>
