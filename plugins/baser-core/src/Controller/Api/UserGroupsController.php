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

namespace BaserCore\Controller\Api;

use BaserCore\Service\UserGroupsServiceInterface;
use Cake\Core\Exception\Exception;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;


/**
 * Class UserGroupsController
 *
 * https://localhost/baser/api/baser-core/user_groups/action_name.json で呼び出す
 *
 * @package BaserCore\Controller\Api
 */
class UserGroupsController extends BcApiController
{
    /**
     * ユーザー情報一覧取得
     * @param UserGroupsServiceInterface $UserGroups
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(UserGroupsServiceInterface $UserGroups)
    {
        $this->set([
            'userGroups' => $this->paginate($UserGroups->getIndex())
        ]);
        $this->viewBuilder()->setOption('serialize', ['userGroups']);
    }

    /**
     * ユーザー情報取得
     * @param UserGroupsServiceInterface $UserGroups
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(UserGroupsServiceInterface $UserGroups, $id)
    {
        $this->set([
            'userGroups' => $UserGroups->get($id)
        ]);
        $this->viewBuilder()->setOption('serialize', ['userGroups']);
    }

    /**
     * ユーザー情報登録
     * @param UserGroupsServiceInterface $UserGroups
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(UserGroupsServiceInterface $UserGroups)
    {
        if ($this->request->is('post')) {
            if ($userGroups = $UserGroups->create($this->request->getData())) {
                $message = __d('baser', 'ユーザーグループ「{0}」を追加しました。', $userGroups->name);
            } else {
                $message = __d('baser', '入力エラーです。内容を修正してください。');
            }
        }
        $this->set([
            'message' => $message,
            'userGroups' => $userGroups
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'userGroups']);
    }

    /**
     * ユーザー情報編集
     * @param UserGroupsServiceInterface $UserGroups
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(UserGroupsServiceInterface $UserGroups, $id)
    {
        $userGroups = $UserGroups->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            if ($userGroups = $UserGroups->update($userGroups, $this->request->getData())) {
                $message = __d('baser', 'ユーザーグループ「{0}」を更新しました。', $userGroups->name);
            } else {
                $message = __d('baser', '入力エラーです。内容を修正してください。');
            }
        }
        $this->set([
            'message' => $message,
            'userGroups' => $userGroups
        ]);
        $this->viewBuilder()->setOption('serialize', ['userGroups', 'message']);
    }

    /**
     * ユーザー情報削除
     * @param UserGroupsServiceInterface $UserGroups
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(UserGroupsServiceInterface $UserGroups, $id)
    {
        $userGroups = $UserGroups->get($id);
        if ($this->request->is(['post', 'delete'])) {
            try {
                if ($UserGroups->delete($id)) {
                    $message = __d('baser', 'ユーザー: {0} を削除しました。', $userGroups->name);
                }
            } catch (Exception $e) {
                $message = __d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
            }
        }
        $this->set([
            'message' => $message,
            'userGroups' => $userGroups
        ]);
        $this->viewBuilder()->setOption('serialize', ['userGroups', 'message']);
    }
}
