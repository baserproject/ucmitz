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

namespace BcFavorite\Model\Validation;

use Cake\ORM\TableRegistry;
use BaserCore\Utility\BcUtil;
use Cake\Validation\Validation;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Model\Table\PermissionsTable;

/**
 * Class BcValidation
 * @package BcFavorite\Model\Validation
 */
class FavoriteValidation extends Validation
{
    /**
     * アクセス権があるかチェックする
     *
     * 管理者グループは全て true を返却
     *
     * @param array $check
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isPermitted($url)
    {
        if (BcUtil::isAdminUser()) {
            return true;
        }
        $userGroups = BcUtil::loginUserGroup();

        if (!$userGroups) {
            return false;
        }
        /** @var PermissionsTable $permissions */
        $permissions = TableRegistry::getTableLocator()->get('BaserCore.Permissions');
        foreach($userGroups as $userGroup) {
            if ($permissions->check($url, $userGroup->id)) {
                return true;
            }
        }
        return false;
    }
}
