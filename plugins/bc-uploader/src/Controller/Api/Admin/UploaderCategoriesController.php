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

namespace BcUploader\Controller\Api\Admin;

use BaserCore\Controller\Api\Admin\BcAdminApiController;
use BcUploader\Service\UploaderCategoriesServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * アップロードカテゴリコントローラー
 */
class UploaderCategoriesController extends BcAdminApiController
{

    /**
     * 一覧取得API
     *
     * @param UploaderCategoriesServiceInterface $service
     *
     * @checked
     * @notodo
     * @unitTest
     */
    public function index(UploaderCategoriesServiceInterface $service)
    {
        $this->request->allowMethod(['get']);

        $queryParams = array_merge([
            'contain' => null,
        ], $this->request->getQueryParams());
        $this->set([
            'uploaderCategories' => $this->paginate(
                $service->getIndex($queryParams)
            )
        ]);
        $this->viewBuilder()->setOption('serialize', ['uploaderCategories']);
    }

    /**
     * 新規追加API
     *
     * @param UploaderCategoriesServiceInterface $service
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(UploaderCategoriesServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'delete']);
        $uploaderCategory = $errors = null;
        try {
            $uploaderCategory = $service->create($this->request->getData());
            $message = __d('baser_core', '新規アップロードカテゴリ「{0}」を追加しました。', $uploaderCategory->name);
        } catch (PersistenceFailedException $e) {
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'message' => $message,
            'uploaderCategory' => $uploaderCategory,
            'errors' => $errors,
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'uploaderCategory', 'errors']);
    }

    /**
     * 編集API
     *
     * @param UploaderCategoriesServiceInterface $service
     * @param int $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(UploaderCategoriesServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $uploaderCategory = $errors = null;
        try {
            $uploaderCategory = $service->update($service->get($id), $this->request->getData());
            $message = __d('baser_core', 'アップロードカテゴリ「{0}」を更新しました。', $uploaderCategory->name);
        } catch (PersistenceFailedException $e) {
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'message' => $message,
            'uploaderCategory' => $uploaderCategory,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['uploaderCategory', 'message', 'errors']);
    }

    /**
     * 削除API
     *
     * @param UploaderCategoriesServiceInterface $service
     * @param int $id
     *
     * @checked
     * @notodo
     * @unitTest
     */
    public function delete(UploaderCategoriesServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['post', 'delete']);
        $uploaderCategory = null;
        try {
            $uploaderCategory = $service->get($id);
            if ($service->delete($id)) {
                $message = __d('baser_core', 'アップロードカテゴリ「{0}」を削除しました。', $uploaderCategory->name);
            } else {
                $this->setResponse($this->response->withStatus(500));
                $message = __d('baser_core', 'データベース処理中にエラーが発生しました。');
            }
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'message' => $message,
            'uploaderCategory' => $uploaderCategory
        ]);
        $this->viewBuilder()->setOption('serialize', ['uploaderCategory', 'message']);
    }

    /**
     * コピーAPI
     *
     * @param UploaderCategoriesServiceInterface $service
     * @param int $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy(UploaderCategoriesServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['patch', 'post', 'put']);
        $uploaderCategory = null;
        try {
            $uploaderCategory = $service->copy($id);
            if ($uploaderCategory) {
                $message = __d('baser_core', 'アップロードカテゴリ「{0}」をコピーしました。', $uploaderCategory->name);
            } else {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser_core', 'データベース処理中にエラーが発生しました。');
            }
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'message' => $message,
            'uploaderCategory' => $uploaderCategory
        ]);

        $this->viewBuilder()->setOption('serialize', ['message', 'uploaderCategory']);
    }

    /**
     * アップロードカテゴリのバッチ処理
     *
     * 指定したアップロードカテゴリに対して削除、公開、非公開の処理を一括で行う
     *
     * ### エラー
     * 受け取ったPOSTデータのキー名 'batch' が 'delete' 以外の値であれば500エラーを発生させる
     *
     * @param UploaderCategoriesServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function batch(UploaderCategoriesServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);
        $allowMethod = [
            'delete' => __d('baser_core', '削除')
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
                sprintf(__d('baser_core', 'アップロードカテゴリ「%s」を %s しました。'), implode('」、「', $names), $allowMethod[$method]),
                true,
                false
            );
            $message = __d('baser_core', '一括処理が完了しました。');
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set(['message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

}
