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
     * @param UserGroupsServiceInterface $userGroups

     */
    public function index(UserGroupsServiceInterface $userGroups)
    {
        $this->set([
            'userGroups' => $this->paginate($userGroups->getIndex($this->request))
        ]);
        $this->viewBuilder()->setOption('serialize', ['users']);
    }

    /**
     * ユーザー情報取得
     * @param UserGroupsServiceInterface $userGroups
     * @param $id

     */
    public function view(UserGroupsServiceInterface $userGroups, $id)
    {
        $this->set([
            'user' => $userGroups->get($id)
        ]);
        $this->viewBuilder()->setOption('serialize', ['user']);
    }

    /**
     * ユーザー情報登録
     * @param UserGroupsServiceInterface $userGroups

     */
    public function add(UserGroupsServiceInterface $userGroups)
    {
        if ($user = $userGroups->create($this->request)) {
            $message = __d('baser', 'ユーザー「{0}」を追加しました。', $user->name);
        } else {
            $message = __d('baser', '入力エラーです。内容を修正してください。');
        }
        $this->set([
            'message' => $message,
            'user' => $user
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'user']);
    }

    /**
     * ユーザー情報編集
     * @param UserGroupsServiceInterface $userGroups
     * @param $id

     */
    public function edit(UserGroupsServiceInterface $userGroups, $id)
    {
        $user = $userGroups->get($id);
        if ($this->request->is(['post', 'put'])) {
            if ($user = $userGroups->update($user, $this->request)) {
                $message = __d('baser', 'ユーザー「{0}」を更新しました。', $user->name);
            } else {
                $message = __d('baser', '入力エラーです。内容を修正してください。');
            }
        }
        $this->set([
            'message' => $message,
            'user' => $user
        ]);
        $this->viewBuilder()->setOption('serialize', ['user', 'message']);
    }

    /**
     * ユーザー情報削除
     * @param UserGroupsServiceInterface $userGroups
     * @param $id
     */
    public function delete(UserGroupsServiceInterface $userGroups, $id)
    {
        $user = $userGroups->get($id);
        try {
            if ($userGroups->delete($id)) {
                $message = __d('baser', 'ユーザー: {0} を削除しました。', $user->name);
            }
        } catch (Exception $e) {
            $message = __d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
        }
        $this->set([
            'message' => $message,
            'user' => $user
        ]);
        $this->viewBuilder()->setOption('serialize', ['user', 'message']);
    }
}
