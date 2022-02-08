<?php

use BaserCore\Utility\BcUtil;
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 2.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] よく使う項目
 * @var \BaserCore\View\BcAdminAppView $this
 */
$this->BcBaser->js('BcFavorite.admin/favorites/main.bundle', true);
$this->BcBaser->css('BcFavorite.admin/favorite');
$user = BcUtil::loginUser();
?>
<nav id="FavoriteMenu" class="bca-nav-favorite">
    <favorite-index user-id="<?php echo $user->id ?>" />
</nav>
