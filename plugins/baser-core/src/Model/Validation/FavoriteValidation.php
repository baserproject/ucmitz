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

namespace BaserCore\Model\Validation;

use Cake\Core\Configure;
use Cake\Routing\Router;
use BaserCore\Utility\BcUtil;
use Cake\Validation\Validation;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class BcValidation
 * @package BaserCore\Model\Validation
 */
class FavoriteValidation extends Validation
{
    /**
     * アクセス権があるかチェックする
     *
     * @param array $check
     */
    public function isPermitted($check)
    {
        if (!$this->_Session) {
            return true;
        }
        $url = $check[key($check)];
        $prefix = BcUtil::authSessionKey('admin');
        $userGroupId = $this->_Session->read('Auth.' . $prefix . '.user_group_id');
        if ($userGroupId == Configure::read('BcApp.adminGroupId')) {
            return true;
        }
        $Permission = ClassRegistry::init('Permission');
        return $Permission->check($url, $userGroupId);
    }
}
