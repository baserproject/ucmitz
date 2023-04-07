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

namespace BaserCore\Controller;

use Authentication\Controller\Component\AuthenticationComponent;
use BaserCore\Controller\Admin\BcAdminAppController;
use BaserCore\Controller\Component\BcMessageComponent;
use BaserCore\Error\BcException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Service\PasswordRequestsService;
use BaserCore\Service\PasswordRequestsServiceInterface;
use BaserCore\Service\UsersServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class PasswordRequestsController
 * @property AuthenticationComponent $Authentication
 * @property BcMessageComponent $BcMessage
 */
class PasswordRequestsController extends BcAdminAppController
{

    /**
     * initialize
     * ログインページ認証除外
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['entry', 'apply', 'done']);
    }

    /**
     * パスワード変更申請
     *
     * @param PasswordRequestsService $service
     * @return void|ResponseInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function entry(PasswordRequestsServiceInterface $service)
    {
        $passwordRequest = $service->getNew();
        $this->set('passwordRequest', clone $passwordRequest);
        $this->setTitle(__d('baser_core', 'パスワードのリセット'));
        if (!$this->request->is(['patch', 'post', 'put'])) return;

        try {
            $passwordRequest = $service->update($passwordRequest, $this->request->getData());
            if (!$passwordRequest) {
                $this->BcMessage->setError(__d('baser_core', '入力エラーです。内容を修正してください。'));
                return;
            }
        } catch (RecordNotFoundException $e) {
            $this->BcMessage->setError($e->getMessage());
            return $this->redirect(['action' => 'entry']);
        } catch (PersistenceFailedException $e) {
            $this->BcMessage->setError($e->getMessage());
            return $this->redirect(['action' => 'entry']);
        } catch (\Throwable $e) {
            $this->BcMessage->setError(__d('baser_core', '処理中にエラーが発生しました。') . $e->getMessage());
            return $this->redirect(['action' => 'entry']);
        }
        $this->BcMessage->setSuccess(__d('baser_core', 'パスワードのリセットを受付ました。該当メールアドレスが存在した場合、変更URLを送信いたしました。'));
        $this->redirect(['action' => 'entry']);
    }

    /**
     * パスワード変更
     * @checked
     * @noTodo
     * @unitTest
     */
    public function apply(PasswordRequestsServiceInterface $service, UsersServiceInterface $usersService, $key): void
    {
        $this->set('user', $usersService->getNew());
        $this->setTitle(__d('baser_core', 'パスワードのリセット'));
        $passwordRequest = $service->getEnableRequestData($key);

        if (empty($passwordRequest)) {
            $this->response->withStatus(404);
            $this->setTitle(__d('baser_core', 'Not Found'));
            $this->render('expired');
            return;
        }

        if (!$this->request->is(['patch', 'post', 'put'])) return;

        try {
            $service->updatePassword($passwordRequest, $this->getRequest()->getData());
        } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
            $this->set('user', $e->getEntity());
            return;
        } catch (BcException $e) {
            $this->BcMessage->setError(__d('baser_core', 'システムエラーが発生しました。'));
            return;
        }
        $this->redirect(['action' => 'done']);
    }

    /**
     * パスワード変更完了
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function done()
    {
        $this->setTitle(__d('baser_core', 'パスワードのリセット完了'));
    }

}
