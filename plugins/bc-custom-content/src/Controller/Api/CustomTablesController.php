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
use BcCustomContent\Service\CustomTablesServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * CustomTablesController
 */
class CustomTablesController extends BcApiController
{
    /**
     * 一覧取得API
     *
     * @param CustomTablesServiceInterface $service
     */
    public function index(CustomTablesServiceInterface $service)
    {
        //todo 一覧取得API
    }

    /**
     * 単一データAPI
     *
     * @param CustomTablesServiceInterface $service
     * @param int $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(CustomTablesServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['get']);
        $customTable = $message = null;
        try {
            $customTable = $service->get($id, $this->request->getQueryParams());
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'customTable' => $customTable,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'customTable']);
    }

    /**
     * 新規追加API
     *
     * @param CustomTablesServiceInterface $service
     */
    public function add(CustomTablesServiceInterface $service)
    {
        //todo 新規追加API
    }

    /**
     * 編集API
     *
     * @param CustomTablesServiceInterface $service
     */
    public function edit(CustomTablesServiceInterface $service)
    {
        //todo 編集API
    }

    /**
     * 削除API
     *
     * @param CustomTablesServiceInterface $service
     */
    public function delete(CustomTablesServiceInterface $service)
    {
        //todo 削除API
    }

    /**
     * リストAPI
     *
     * @param CustomTablesServiceInterface $service
     */
    public function list(CustomTablesServiceInterface $service)
    {
        //todo リストAPI
    }
}
