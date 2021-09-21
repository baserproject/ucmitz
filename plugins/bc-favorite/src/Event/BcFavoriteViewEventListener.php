<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BcFavorite\Event;

use Cake\Event\Event;

/**
 * BcFavoriteViewEventListener
 */
class BcFavoriteViewEventListener extends \BaserCore\Event\BcViewEventListener
{

    /**
     * Event
     * @var string[]
     */
    public $events = ['BaserCore.beforeAdminMenu'];

    /**
     * 管理画面メニュー上部
     */
    public function baserCoreBeforeAdminMenu(Event $event) {
        /* @var \BaserCore\View\BcAdminAppView $viewClass */
        $viewClass = $event->getSubject();
        echo $viewClass->element('BcFavorite.favorite_menu');
    }

}
