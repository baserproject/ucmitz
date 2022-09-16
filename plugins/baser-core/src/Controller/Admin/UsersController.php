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

namespace BaserCore\Controller\Admin;

use BaserCore\Service\UsersAdminServiceInterface;
use Cake\Http\Response;
use Cake\Core\Configure;
use Cake\Routing\Router;
use BaserCore\Utility\BcUtil;
use BaserCore\Annotation\NoTodo;
use BaserCore\Model\Entity\User;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Core\Exception\Exception;
use BaserCore\Model\Table\UsersTable;
use BaserCore\Service\UsersServiceInterface;
use Cake\Http\Exception\ForbiddenException;
use BaserCore\Service\SiteConfigsServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;

/**
 * Class UsersController
 * @package BaserCore\Controller\Admin
 * @property UsersTable $Users
 */
class UsersController extends BcAdminAppController
{

    /**
     * initialize
     * ログインページ認証除外
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['login']);
    }

    /**
     * 管理画面へログインする
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function login(UsersAdminServiceInterface $service)
    {
        $this->set($service->getViewVarsForLogin($this->getRequest()));
        if ($this->Authentication->getResult()->isValid()) {
            $this->redirect(Router::url(Configure::read('BcPrefixAuth.Admin.loginRedirect')));
        }
    }

    /**
     * 代理ログイン
     * 別のユーザにログインできる
     * @param string|null $id User id.
     * @return Response|void Redirects
     * @throws RecordNotFoundException When record not found.
     * @checked
     * @unitTest
     * @noTodo
     */
    public function login_agent(UsersServiceInterface $userService, $id): ?Response
    {
        // 特権確認
        if (BcUtil::isSuperUser() === false) {
            throw new ForbiddenException();
        }
        // 既に代理ログイン済み
        if (BcUtil::isAgentUser()) {
            $this->BcMessage->setError(__d('baser', '既に代理ログイン中のため失敗しました。'));
            return $this->redirect(['action' => 'index']);
        }
        $userService->loginToAgent($this->request, $this->response, $id, $this->referer());
        return $this->redirect($this->Authentication->getLoginRedirect() ?? Router::url(Configure::read('BcPrefixAuth.Admin.loginRedirect')));
    }

    /**
     * 代理ログイン解除
     * @return Response
     * @unitTest
     * @noTodo
     * @checked
     */
    public function back_agent(UsersServiceInterface $userService)
    {
        try {
            $redirectUrl = $userService->returnLoginUserFromAgent($this->request, $this->response);
            $this->BcMessage->setInfo(__d('baser', '元のユーザーに戻りました。'));
            return $this->redirect($redirectUrl);
        } catch (\Exception $e) {
            $this->BcMessage->setError($e->getMessage());
            return $this->redirect($this->referer());
        }
    }

    /**
     * ログイン状態のセッションを破棄する
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function logout(UsersServiceInterface $userService)
    {
        /* @var User $user */
        $user = $this->Authentication->getIdentity();
        $userService->logout($this->request, $this->response, $user->id);
        $this->BcMessage->setInfo(__d('baser', 'ログアウトしました'));
        $this->redirect($this->Authentication->logout());
    }

    /**
     * ログインユーザーリスト
     * 管理画面にログインすることができるユーザーの一覧を表示する
     * @param UsersServiceInterface $userService
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(UsersServiceInterface $userService, SiteConfigsServiceInterface $siteConfigService): void
    {
        $this->setViewConditions('User', ['default' => ['query' => [
            'limit' => $siteConfigService->getValue('admin_list_num'),
            'sort' => 'id',
            'direction' => 'asc',
        ]]]);

        // EVENT Users.searchIndex
        $event = $this->dispatchLayerEvent('searchIndex', [
            'request' => $this->request
        ]);
        if ($event !== false) {
            $this->request = ($event->getResult() === null || $event->getResult() === true)? $event->getData('request') : $event->getResult();
        }

        $this->set('users', $this->paginate($userService->getIndex($this->request->getQueryParams())));
        $this->request = $this->request->withParsedBody($this->request->getQuery());
    }

    /**
     * ログインユーザー新規追加
     * 管理画面にログインすることができるユーザーの各種情報を新規追加する
     * @param UsersServiceInterface $userService
     * @return Response|null|void Redirects on successful add, renders view otherwise.
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(UsersAdminServiceInterface $userService)
    {
        if ($this->request->is('post')) {
            try {
                $user = $userService->create($this->request->getData());
                // EVENT Users.afterAdd
                $this->dispatchLayerEvent('afterAdd', [
                    'user' => $user
                ]);
                $this->BcMessage->setSuccess(__d('baser', 'ユーザー「{0}」を追加しました。', $user->name));
                return $this->redirect(['action' => 'edit', $user->id]);
            } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
                $user = $e->getEntity();
                $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            }
        }
        $this->set($userService->getViewVarsForAdd($user ?? $userService->getNew()));
    }

    /**
     * ログインユーザー編集
     * 管理画面にログインすることができるユーザーの各種情報を編集する
     * @param UsersServiceInterface $userService
     * @param string|null $id User id.
     * @return Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws RecordNotFoundException When record not found.
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(UsersAdminServiceInterface $userService, $id = null)
    {
        if (!$id && empty($this->request->getData())) {
            $this->BcMessage->setError(__d('baser', '無効なIDです。'));
            $this->redirect(['action' => 'index']);
        }
        $user = $userService->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            try {
                $user = $userService->update($user, $this->request->getData());
                $this->dispatchLayerEvent('afterEdit', [
                    'user' => $user
                ]);
                if($userService->isSelf($id)) {
                    $userService->reLogin($this->request, $this->response);
                }
                $this->BcMessage->setSuccess(__d('baser', 'ユーザー「{0}」を更新しました。', $user->name));
                return $this->redirect(['action' => 'edit', $user->id]);
            } catch (\Exception $e) {
                $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            }
        }
        $this->set($userService->getViewVarsForEdit($user));
    }

    /**
     * ログインユーザー削除
     * 管理画面にログインすることができるユーザーを削除する
     * @param UsersServiceInterface $userService
     * @param string|null $id User id.
     * @return Response|null|void Redirects to index.
     * @throws RecordNotFoundException When record not found.
     * @checked
     * @unitTest
     * @noTodo
     */
    public function delete(UsersServiceInterface $userService, $id = null)
    {
        if (!$id) {
            $this->BcMessage->setError(__d('baser', '無効なIDです。'));
            $this->redirect(['action' => 'index']);
        }
        $this->request->allowMethod(['post', 'delete']);
        $user = $userService->get($id);
        try {
            if ($userService->delete($id)) {
                $this->BcMessage->setSuccess(__d('baser', 'ユーザー: {0} を削除しました。', $user->name));
            }
        } catch (Exception $e) {
            $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
        }
        return $this->redirect(['action' => 'index']);
    }

}
