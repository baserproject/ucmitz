<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Permission Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Permission Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Service;

use BaserCore\Model\Entity\Permission;
use BaserCore\Model\Table\PermissionsTable;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Http\ServerRequest;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Datasource\EntityInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class PermissionsService
 * @package BaserCore\Service
 * @property PermissionsTable $Permissions
 */
class PermissionsService implements PermissionsServiceInterface
{

    /**
     * Permissions Table
     * @var \Cake\ORM\Table
     */
    public $Permissions;

    /**
     * PermissionsService constructor.
     */
    public function __construct()
    {
        $this->Permissions = TableRegistry::getTableLocator()->get('BaserCore.Permissions');
    }

    /**
     * パーミッションの新規データ用の初期値を含んだエンティティを取得する
     * @return Permission
     * @noTodo
     */
    public function getNew(): EntityInterface
    {
        return $this->Permissions->newEntity([
            'url' => Configure::read('BcApp.baserCorePrefix') . Configure::read('BcApp.adminPrefix') . DS,
            'auth' => 0,
            'status' => 1,
        ]);
    }

    /**
     * パーミッションを取得する
     * @param int $id
     * @return EntityInterface
     * @noTodo
     */
    public function get($id): EntityInterface
    {
        return $this->Permissions->get($id, [
            'contain' => ['PermissionGroups'],
        ]);
    }

    /**
     * パーミッション管理の一覧用のデータを取得
     * @param array $queryParams
     * @param array $paginateParams
     * @return array
     * @noTodo
     */
    public function getIndex(ServerRequest $request, $userGroupId): Query
    {
        $queryParams = $request->getQueryParams();
        if (!empty($queryParams['num'])) {
            $options = ['limit' => $queryParams['num']];
        }
        $query = $this->Permissions->find('all', $options)->order('sort', 'ASC');
        return $query;
    }

    public function set($data, $userGroupId)
    {
        $permission = $this->Permissions->newEmptyEntity([
            'user_group_id' => $userGroupId,
        ]);
        return $this->Permissions->patchEntity($permission, $data, ['validate' => 'default']);
    }

    /**
     * パーミッション登録
     * @param ServerRequest $request
     * @return \Cake\Datasource\EntityInterface|false
     * @noTodo
     */
    public function create(EntityInterface $permission)
    {
        return $this->Permissions->save($permission);
    }

    /**
     * パーミッション情報を更新する
     * @param EntityInterface $target
     * @param ServerRequest $request
     * @return EntityInterface|false
     * @noTodo
     */
    public function update(EntityInterface $target, ServerRequest $request)
    {
        $Permission = $this->Permissions->patchEntity($target, $request->getData());
        return $this->Permissions->save($Permission);
    }

    /**
     * パーミッション情報を削除する
     * 最後のシステム管理者でなければ削除
     * @param int $id
     * @return bool
     * @noTodo
     */
    public function delete($id)
    {
        $Permission = $this->Permissions->get($id, ['contain' => ['PermissionGroups']]);
        if($Permission->isAdmin()) {
            $count = $this->Permissions
                ->find('all', ['conditions' => ['PermissionsPermissionGroups.Permission_group_id' => Configure::read('BcApp.adminGroupId')]])
                ->join(['table' => 'Permissions_Permission_groups',
                    'alias' => 'PermissionsPermissionGroups',
                    'type' => 'inner',
                    'conditions' => 'PermissionsPermissionGroups.Permission_id = Permissions.id'])
                ->count();
            if ($count === 1) {
                throw new Exception(__d('baser', '最後のシステム管理者は削除できません'));
            }
        }
        return $this->Permissions->delete($Permission);
    }

    /**
     * 許可・拒否を指定するメソッドのリストを取得
     *
     * @return array
     * @noTodo
     * @checked
     */
    public function getMethodList() : array
    {
        return $this->Permissions::METHOD_LIST;
    }
}
