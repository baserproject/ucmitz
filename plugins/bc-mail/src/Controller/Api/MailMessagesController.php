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

namespace BcMail\Controller\Api;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Controller\Api\BcApiController;
use BcMail\Service\MailContentsServiceInterface;
use BcMail\Service\MailMessagesService;
use BcMail\Service\MailMessagesServiceInterface;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * メールフィールドコントローラー
 */
class MailMessagesController extends BcApiController
{

    /**
     * [API] 受信メール一覧
     *
     * @param MailMessagesService $service
     * @param int $mailContentId
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(MailMessagesServiceInterface $service, int $mailContentId)
    {
        $service->setup($mailContentId);
        $this->set([
            'mailMessages' => $this->paginate($service->getIndex($this->request->getQueryParams()))
        ]);
        $this->viewBuilder()->setOption('serialize', ['mailMessages']);
    }

    /**
     * [API] 受信メール詳細
     *
     * @param MailMessagesService $service
     * @param int $mailContentId
     * @param int $messageId
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(MailMessagesServiceInterface $service, int $mailContentId, int $messageId)
    {
        $service->setup($mailContentId);
        $this->set([
            'mailMessage' => $service->get($messageId)
        ]);
        $this->viewBuilder()->setOption('serialize', ['mailMessage']);
    }

    /**
     * [API] 受信メール追加
     *
     * @param MailMessagesService $service
     * @param MailContentsServiceInterface $mailContentsService
     * @param int $mailContentId
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(
        MailMessagesServiceInterface $service,
        MailContentsServiceInterface $mailContentsService,
        int $mailContentId
    )
    {
        $this->request->allowMethod(['post']);
        try {
            $service->setup($mailContentId);
            $mailContent = $mailContentsService->get($mailContentId);
            $mailMessage = $service->create($mailContent, $this->request->getData());
            $message = __d(
                'baser',
                '{0} への受信データ NO「{1}」を追加しました。',
                $mailContent->content->title,
                $mailMessage->id
            );
        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $mailMessage = $e->getEntity();
            $message = __d('baser', '入力エラーです。内容を修正してください。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $mailMessage = null;
        }
        $this->set([
            'message' => $message,
            'mailMessage' => $mailMessage,
            'errors' => !!$mailMessage ? $mailMessage->getErrors() : null
        ]);
        $this->viewBuilder()->setOption('serialize', ['mailMessage', 'message', 'errors']);
    }

    /**
     * [API] 受信メール編集
     *
     */
    public function edit()
    {
        // TODO 受信メール管理：編集APIを実装
    }

    /**
     * [API] 受信メール削除
     *
     */
    public function delete()
    {
        // TODO 受信メール管理：削除APIを実装
    }

    /**
     * メールメッセージのバッチ処理
     *
     * 指定したメールフィールドに対して削除、公開、非公開の処理を一括で行う
     *
     * ### エラー
     * 受け取ったPOSTデータのキー名'batch'が'delete'以外の値であれば500エラーを発生させる
     *
     * @param MailMessagesService $service
     * @checked
     * @noTodo
     */
    public function batch(MailMessagesServiceInterface $service, $mailContentId)
    {
        $this->request->allowMethod(['post', 'put']);
        $allowMethod = [
            'delete' => '削除',
        ];
        $method = $this->getRequest()->getData('batch');
        if (!isset($allowMethod[$method])) {
            $this->setResponse($this->response->withStatus(500));
            $this->viewBuilder()->setOption('serialize', []);
            return;
        }
        $targets = $this->getRequest()->getData('batch_targets');
        try {
            $service->setup($mailContentId);
            $service->batch($method, $targets);
            $this->BcMessage->setSuccess(
                sprintf(__d('baser', 'メールメッセージ No %s を %s しました。'), implode(', ', $targets), $allowMethod[$method]),
                true,
                false
            );
            $message = __d('baser', '一括処理が完了しました。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', $e->getMessage());
        }
        $this->set(['message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

    /**
     * [API] CSVダウンロード
     *
     */
    public function download()
    {
        // TODO 受信メール管理：受信メールCSVダウンロードAPIを実装
    }

}
