<?php
// TODO : コード確認要
use BaserCore\Event\BcEventDispatcherTrait;

return;
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View.Helper
 * @since           baserCMS v 4.0.5
 * @license         https://basercms.net/license/index.html
 */

/**
 * 検索ボックスヘルパ
 *
 * @package Baser.View.Helper
 */
class BcSearchBoxHelper extends AppHelper
{
    /**
     * Trait
     */
    use BcEventDispatcherTrait;

    /**
     * 検索フィールド発火
     *
     * @return string
     */
    public function dispatchShowField()
    {
        $request = $this->_View->request;
        $id = Inflector::camelize($request->getParam('controller')) . '.' . Inflector::camelize($request->getParam('action'));
        $event = $this->dispatchLayerEvent('showField', ['id' => $id, 'fields' => []], ['class' => 'BcSearchBox', 'plugin' => '']);
        $output = '';
        if ($event !== false) {
            if (!empty($event->getData('fields'))) {
                foreach($event->getData('fields') as $field) {
                    $output .= "<span class=\"bca-search__input-item\">";
                    if (!empty($field['title'])) {
                        $output .= $field['title'] . "&nbsp;";
                    }
                    if (!empty($field['input'])) {
                        $output .= $field['input'] . "&nbsp;";
                    }
                    $output .= "</span>　\n";
                }
            }
        }
        return $output;
    }

}
