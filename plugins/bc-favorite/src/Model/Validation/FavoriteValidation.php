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

use BaserCore\Service\PermissionServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\Validation\Validation;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class BcValidation
 * @package BcFavorite\Model\Validation
 */
class FavoriteValidation extends Validation
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * アクセス権があるかチェックする
     *
     * 管理者グループは全て true を返却
     *
     * @param array $check
     * @param PermissionServiceInterface $permissionService
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function isPermitted($url, $permissionService)
    {
        if (BcUtil::isAdminUser()) {
            return true;
        }
        $userGroups = BcUtil::loginUserGroup();

        if (!$userGroups) {
            return false;
        }
        foreach($userGroups as $userGroup) {
            if ($permissionService->check($url, $userGroup->id)) {
                return true;
            }
        }
        return false;
    }
}
