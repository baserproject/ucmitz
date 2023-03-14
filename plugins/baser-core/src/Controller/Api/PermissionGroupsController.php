<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Controller\Api;

use BaserCore\Error\BcException;
use BaserCore\Service\PermissionGroupsServiceInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * Class PermissionGroupsController
 */
class PermissionGroupsController extends BcApiController
{
    /**
     * [API] 単一アクセスルールグループ取得
     * @param PermissionGroupsServiceInterface $service
     * @param int $id
     */
    public function view(PermissionGroupsServiceInterface $service, int $id)
    {
        //todo 単一アクセスルールグループ取得
    }

    /**
     * [API] アクセスルールグループの一覧
     * @param PermissionGroupsServiceInterface $service
     */
    public function index(PermissionGroupsServiceInterface $service)
    {
        //todo アクセスルールグループの一覧
    }

    /**
     * 登録処理
     *
     * @param PermissionGroupsServiceInterface $service
     */
    public function add(PermissionGroupsServiceInterface $service)
    {
        //todo 登録処理
    }

    /**
     * [API] 削除処理
     *
     * @param PermissionGroupsServiceInterface $service
     * @param int $id
     */
    public function delete(PermissionGroupsServiceInterface $service, int $id)
    {
        //todo 削除処理
    }

    /**
     * [API] 編集処理
     *
     * @param PermissionGroupsServiceInterface $service
     * @param int $id
     */
    public function edit(PermissionGroupsServiceInterface $service, int $id)
    {
        //todo 編集処理
    }


    /**
     * [API] ユーザーグループを指定してアクセスルールを再構築する
     *
     * @param PermissionGroupsServiceInterface $service
     * @param int $userGroupId
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function rebuild_by_user_group(PermissionGroupsServiceInterface $service, int $userGroupId)
    {
        $this->request->allowMethod(['post', 'put']);
        try {
            if ($service->rebuildByUserGroup($userGroupId)) {
                $message = __d('baser_core', 'アクセスルールの再構築に成功しました。');
            } else {
                $message = __d('baser_core', 'アクセスルールの再構築に失敗しました。');
            }
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'message' => $message,
        ]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

}
