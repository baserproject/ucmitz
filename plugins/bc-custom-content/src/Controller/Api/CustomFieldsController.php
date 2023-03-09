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
     */
    public function add(CustomFieldsServiceInterface $service)
    {
        //todo 新規追加API
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
     * @param int $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(CustomFieldsServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['post', 'delete']);
        $customField = null;
        try {
            $customField = $service->get($id);
            $service->delete($id);
            $message = __d('baser_core', 'フィールド「{0}」を削除しました。', $customField->title);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'message' => $message,
            'customField' => $customField
        ]);
        $this->viewBuilder()->setOption('serialize', ['customField', 'message']);
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
