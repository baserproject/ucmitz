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

namespace BcSearchIndex\Controller\Api;

use BaserCore\Controller\Api\BcApiController;
use BaserCore\Error\BcException;
use BcSearchIndex\Service\SearchIndexesServiceInterface;
use Cake\Event\EventInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Http\Exception\ForbiddenException;

/**
 * SearchIndicesController
 */
class SearchIndexesController extends BcApiController
{

    /**
     * initialize
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['index']);
    }

    /**
     * Before filter
     * @param EventInterface $event
     * @return \Cake\Http\Response|void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Security->setConfig('validatePost', false);
    }

    /**
     * [AJAX] 優先順位を変更する
     * @checked
     * @noTodo
     * @unitTest
     */
    public function change_priority(SearchIndexesServiceInterface $service, $id)
    {
        $this->request->allowMethod(['post', 'put']);
        $searchIndex = $service->get($id);
        try {
            $searchIndex = $service->changePriority(
                $searchIndex,
                $this->getRequest()->getData('priority')
            );
            $message = __d('baser', '検索インデックス「{0}」の優先度を変更しました。', $searchIndex->title);
        } catch (BcException $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '検索インデックスの優先度の変更に失敗しました。');
        }
        $this->set([
            'message' => $message,
            'searchIndex' => $searchIndex,
        ]);
        $this->viewBuilder()->setOption('serialize', ['searchIndex', 'message']);
    }

    /**
     * バッチ処理
     *
     * @param SearchIndexesServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function batch(SearchIndexesServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);
        $allowMethod = [
            'delete' => '削除'
        ];
        $method = $this->getRequest()->getData('batch');
        if (!isset($allowMethod[$method])) {
            $this->setResponse($this->response->withStatus(500));
            $this->viewBuilder()->setOption('serialize', []);
            return;
        }
        try {
            $service->batch($method, $this->getRequest()->getData('batch_targets'));
            $this->BcMessage->setSuccess(
                sprintf(__d('baser', '検索インデックスより NO.%s を %s しました。'), implode(', ', $this->getRequest()->getData('batch_targets')), $allowMethod[$method]),
                true,
                false
            );
            $message = __d('baser', '一括処理が完了しました。');
        } catch (BcException $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', $e->getMessage());
        }
        $this->set(['message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

    /***
     * [API] 検索インデックスを再構築する
     * @param SearchIndexesServiceInterface $searchIndexesService
     *
     * @noTodo
     * @checked
     * @unitTest
     */
    public function reconstruct(SearchIndexesServiceInterface $searchIndexesService)
    {
        $this->request->allowMethod(['post']);

        if ($searchIndexesService->reconstruct()) {
            $message = __d('baser', '検索インデックスの再構築に成功しました。');
        } else {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '検索インデックスの再構築に失敗しました。');
        }

        $this->set(['message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }


    /**
     * [API] 検索インデックス情報一覧取得
     * @param SearchIndexesServiceInterface $searchIndexesService
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(SearchIndexesServiceInterface $searchIndexesService)
    {
        $this->request->allowMethod('get');
        $queryParams = $this->getRequest()->getQueryParams();
        if(isset($queryParams['status'])) {
            if(!$this->Authentication->getIdentity()) throw new ForbiddenException();
        }
        $queryParams = array_merge($queryParams, [
            'status' => 'publish'
        ]);
        $this->set([
            'searchIndexes' => $this->paginate($searchIndexesService->getIndex($queryParams))
        ]);
        $this->viewBuilder()->setOption('serialize', ['searchIndexes']);
    }

    /**
     * [API] 検索インデックス削除
     * @param SearchIndexesServiceInterface $searchIndexesService
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(SearchIndexesServiceInterface $searchIndexesService, $id)
    {
        $this->request->allowMethod(['post', 'delete']);

        $searchIndex = null;

        try {
            $searchIndex = $searchIndexesService->get($id);
            if ($searchIndexesService->delete($id)) {
                $message = __d('baser', '検索インデックス: {0} を削除しました。', $searchIndex->title);
            }else{
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser', 'データベース処理中にエラーが発生しました。');
            }
        } catch (\Exception $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
        }

        $this->set([
            'message' => $message,
            'searchIndex' => $searchIndex
        ]);
        $this->viewBuilder()->setOption('serialize', ['searchIndex', 'message']);
    }

}
