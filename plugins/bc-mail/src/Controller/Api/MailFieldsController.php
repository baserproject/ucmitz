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
use BcMail\Service\MailFieldsService;
use BcMail\Service\MailFieldsServiceInterface;

/**
 * メールフィールドコントローラー
 */
class MailFieldsController extends BcApiController
{

    /**
     * メールフィールドのバッチ処理
     *
     * 指定したメールフィールドに対して削除、公開、非公開の処理を一括で行う
     *
     * ### エラー
     * 受け取ったPOSTデータのキー名'batch'が'delete','publish','unpublish'以外の値であれば500エラーを発生させる
     *
     * @param MailFieldsService $service
     * @checked
     * @noTodo
     */
    public function batch(MailFieldsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);
        $allowMethod = [
            'delete' => '削除',
            'publish' => '有効化',
            'unpublish' => '無効化'
        ];
        $method = $this->getRequest()->getData('batch');
        if (!isset($allowMethod[$method])) {
            $this->setResponse($this->response->withStatus(500));
            $this->viewBuilder()->setOption('serialize', []);
            return;
        }
        $targets = $this->getRequest()->getData('batch_targets');
        try {
            $names = $service->getTitlesById($targets);
            $service->batch($method, $targets);
            $this->BcMessage->setSuccess(
                sprintf(__d('baser', 'メールフィールド「%s」を %s しました。'), implode('」、「', $names), $allowMethod[$method]),
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
     * 並び替えを更新する [AJAX]
     *
     * @param MailFieldsService $service
     * @param int $mailContentId
     * @return bool|void
     * @checked
     * @noTodo
     */
    public function update_sort(MailFieldsServiceInterface $service, int $mailContentId)
    {
        $this->request->allowMethod(['post']);
        $conditions = [
            'mail_content_id' => $mailContentId,
        ];
        $entity = $service->get($this->request->getData('id'));
        if (!$service->changeSort($this->request->getData('id'), $this->request->getData('offset'), $conditions)) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '一度リロードしてから再実行してみてください。');
        } else {
            $message = sprintf(__d('baser', 'メールフィールド「%s」の並び替えを更新しました。'), $entity->name);
        }
        $this->set([
            'message' => $message,
            'permission' => $entity
        ]);
        $this->viewBuilder()->setOption('serialize', ['plugin', 'message']);
    }

    /**
     * [API] メールフィールド API 一覧取得
     *
     * @param MailFieldsServiceInterface $service
     * @return void
     */
    public function index(MailFieldsServiceInterface $service)
    {
        //todo メールフィールド API 一覧取得
    }

    /**
     * [API] メールフィールド API 単一データ取得
     *
     * @param MailFieldsServiceInterface $service
     * @return void
     */
    public function view(MailFieldsServiceInterface $service)
    {
        //todo メールフィールド API 単一データ取得
    }

    /**
     * [API] メールフィールド API リスト取得
     *
     * @param MailFieldsServiceInterface $service
     * @return void
     */
    public function list(MailFieldsServiceInterface $service)
    {
        //todo メールフィールド API リスト取得
    }

    /**
     * [API] メールフィールド API 新規追加
     *
     * @param MailFieldsServiceInterface $service
     * @return void
     */
    public function add(MailFieldsServiceInterface $service)
    {
        //todo メールフィールド API 新規追加
    }


    /**
     * [API] メールフィールド API 編集
     *
     * @param MailFieldsServiceInterface $service
     * @return void
     */
    public function edit(MailFieldsServiceInterface $service)
    {
        //todo メールフィールド API 編集
    }

    /**
     * [API] メールフィールド API 削除
     *
     * @param MailFieldsServiceInterface $service
     * @return void
     */
    public function delete(MailFieldsServiceInterface $service)
    {
        //todo メールフィールド API 削除
    }

    /**
     * [API] メールフィールド API コピー
     *
     * @param MailFieldsServiceInterface $service
     * @return void
     */
    public function copy(MailFieldsServiceInterface $service)
    {
        //todo メールフィールド API コピー
    }
}
