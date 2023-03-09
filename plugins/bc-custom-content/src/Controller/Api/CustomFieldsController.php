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

namespace BcCustomContent\Controller\Api;

use BaserCore\Controller\Api\BcApiController;
use BcCustomContent\Service\CustomFieldsServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * CustomFieldsController
 */
class CustomFieldsController extends BcApiController
{
    /**
     * 一覧取得API
     *
     * @param CustomFieldsServiceInterface $service
     */
    public function index(CustomFieldsServiceInterface $service)
    {
        //todo 一覧取得API
    }

    /**
     * 単一データAPI
     *
     * @param CustomFieldsServiceInterface $service
     */
    public function view(CustomFieldsServiceInterface $service)
    {
        //todo 単一データAPI
    }

    /**
     * 新規追加API
     *
     * @param CustomFieldsServiceInterface $service
     *
     * @checked
     * @unitTest
     * @unitTest
     */
    public function add(CustomFieldsServiceInterface $service)
    {
        $this->request->allowMethod(['post']);
        $customField = $errors = null;
        try {
            $customField = $service->create($this->request->getData());
            $message = __d('baser_core', 'フィールド「{0}」を追加しました。', $customField->title);
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
            'customField' => $customField,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'customField', 'errors']);
    }

    /**
     * 編集API
     *
     * @param CustomFieldsServiceInterface $service
     */
    public function edit(CustomFieldsServiceInterface $service)
    {
        //todo 編集API
    }

    /**
     * 削除API
     *
     * @param CustomFieldsServiceInterface $service
     */
    public function delete(CustomFieldsServiceInterface $service)
    {
        //todo 削除API
    }

    /**
     * リストAPI
     *
     * @param CustomFieldsServiceInterface $service
     */
    public function list(CustomFieldsServiceInterface $service)
    {
        //todo リストAPI
    }
}
