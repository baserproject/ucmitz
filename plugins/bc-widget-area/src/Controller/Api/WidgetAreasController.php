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

namespace BcWidgetArea\Controller\Api;

use BaserCore\Controller\Api\BcApiController;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BcWidgetArea\Service\WidgetAreasService;
use BcWidgetArea\Service\WidgetAreasServiceInterface;

/**
 * Class WidgetAreasController
 *
 * ウィジェットエリアコントローラー
 */
class WidgetAreasController extends BcApiController
{

    /**
     * 一覧取得
     *
     * @param WidgetAreasService $service
     */
    public function index(WidgetAreasServiceInterface $service)
    {
        $this->set([
            'widgetAreas' => $this->paginate($service->getIndex($this->request->getQueryParams()))
        ]);
        $this->viewBuilder()->setOption('serialize', ['widgetAreas']);
    }

    /**
     * 新規追加
     *
     * @param WidgetAreasService $service
     */
    public function add(WidgetAreasServiceInterface $service)
    {
        // TODO APIを実装してください
    }

    /**
     * 削除
     *
     * @param WidgetAreasService $service
     */
    public function delete(WidgetAreasServiceInterface $service)
    {
        // TODO APIを実装してください
    }

    /**
     * メールフィールドのバッチ処理
     *
     * 指定したメールフィールドに対して削除、公開、非公開の処理を一括で行う
     *
     * ### エラー
     * 受け取ったPOSTデータのキー名'batch'が'delete'以外の値であれば500エラーを発生させる
     *
     * @param WidgetAreasService $service
     * @checked
     * @noTodo
     */
    public function batch(WidgetAreasServiceInterface $service)
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
            $names = $service->getTitlesById($targets);
            $service->batch($method, $targets);
            $this->BcMessage->setSuccess(
                sprintf(__d('baser', 'ウィジェットエリア「%s」を %s しました。'), implode('」、「', $names), $allowMethod[$method]),
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
     * [AJAX] タイトル更新
     *
     * @return void
     * @checked
     * @noTodo
     */
    public function update_title(WidgetAreasServiceInterface $service, int $widgetAreaId)
    {
        $this->getRequest()->allowMethod(['post', 'put']);
        $entity = $service->get($widgetAreaId);
        try {
            $entity = $service->update($entity, $this->getRequest()->getData());
            $message = __d('baser', 'ウィジェットエリア「{0}」を更新しました。', $entity->name);
            $this->BcMessage->setSuccess($message, true, false);
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
        }
        $this->set([
            'message' => $message,
            'widgetArea' => $entity? $entity->toArray() : null,
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'widgetArea']);
    }

    /**
     * [AJAX] ウィジェット更新
     *
     * @param WidgetAreasService $service
     * @param int $widgetAreaId
     * @return void
     * @checked
     * @noTodo
     */
    public function update_widget(WidgetAreasServiceInterface $service, int $widgetAreaId)
    {
        $this->getRequest()->allowMethod(['post', 'put']);
        try {
            $entity = $service->updateWidget($widgetAreaId, $this->getRequest()->getData());
            $message = __d('baser', 'ウィジェットエリア「{0}」を更新しました。', $entity->name);
            $this->BcMessage->setSuccess($message, true, false);
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
        }
        $this->set([
            'message' => $message,
            'widgetArea' => $entity? $entity->toArray() : null,
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'widgetArea']);
    }

    /**
     * 並び順を更新する
     * @param WidgetAreasService $service
     * @param int $widgetAreaId
     * @return void
     * @checked
     * @noTodo
     */
    public function update_sort(WidgetAreasServiceInterface $service, int $widgetAreaId)
    {
        $this->getRequest()->allowMethod(['post', 'put']);
        try {
            $entity = $service->updateSort($widgetAreaId, $this->getRequest()->getData());
            $message = __d('baser', 'ウィジェットエリア「{0}」の並び順を更新しました。', $entity->name);
            $this->BcMessage->setSuccess($message, true, false);
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
        }
        $this->set([
            'message' => $message,
            'widgetArea' => $entity? $entity->toArray() : null,
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'widgetArea']);
    }

    /**
     * [AJAX] ウィジェットを削除
     *
     * @param WidgetAreasService $service
     * @param int $widgetAreaId
     * @param int $id
     * @return void
     * @checked
     * @noTodo
     */
    public function delete_widget(WidgetAreasServiceInterface $service, int $widgetAreaId, int $id)
    {
        $this->getRequest()->allowMethod(['post', 'put']);
        try {
            $entity = $service->deleteWidget($widgetAreaId, $id);
            $message = __d('baser', 'ウィジェットを削除しました。');
            $this->BcMessage->setSuccess($message, true, false);
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
        }
        $this->set([
            'message' => $message,
            'widgetArea' => $entity? $entity->toArray() : null,
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'widgetArea']);
    }

}
