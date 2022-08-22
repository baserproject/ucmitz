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

use BaserCore\Service\PermissionsServiceInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class PermissionsController
 * @uses PermissionsController
 */
class PermissionsController extends BcApiController
{

	/**
	 * 登録処理
     * @checked
     * @noTodo
     * @unitTest
	 */
	public function add(PermissionsServiceInterface $permissionService)
	{
        $this->request->allowMethod(['post', 'delete']);
        try {
            $permission = $permissionService->create($this->request->getData());
            $message = __d('baser', '新規アクセス制限設定「{0}」を追加しました。', $permission->name);
        } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
            $permission = $e->getEntity();
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '入力エラーです。内容を修正してください。');
        }
        $this->set([
            'message' => $message,
            'permission' => $permission,
            'errors' => $permission->getErrors(),
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'permission', 'errors']);
    }

    /**
     * [API] 削除処理
     *
     * @param int $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(PermissionsServiceInterface $permissionService, $permissionId)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);

        $error = null;
        $permission = null;
        try {
            $permission = $permissionService->get($permissionId);
            $permissionName = $permission->name;
            $permissionService->delete($permissionId);
            $message = __d('baser', 'アクセス制限設定「{0}」を削除しました。', $permissionName);
        } catch (\Exception $e) {
            $this->setResponse($this->response->withStatus(400));
            $error = $e->getMessage();
            $message = __d('baser', '入力エラーです。内容を修正してください。');
        }

        $this->set([
            'message' => $message,
            'permission' => $permission,
            'errors' => $error,
        ]);
        $this->viewBuilder()->setOption('serialize', ['permission', 'message', 'errors']);
    }

}
