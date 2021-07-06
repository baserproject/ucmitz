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

namespace BcFavorite\Model\Table;

use BaserCore\Model\AppTable;

/**
 * Class Favorite
 */
class Favorite extends AppTable
{

    /**
     * belongsTo
     *
     * @var array
     */
    public $belongsTo = [
        'User' => [
            'className' => 'User',
            'foreignKey' => 'user_id'
        ]];

    /**
     * Favorite constructor.
     *
     * @param bool $id
     * @param null $table
     * @param null $ds
     */
    public function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->validate = [
            'url' => [
                ['rule' => ['isPermitted'], 'message' => __d('baser', 'このURLの登録は許可されていません。')]]
        ];
    }

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
