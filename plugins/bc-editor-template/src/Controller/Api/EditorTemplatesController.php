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

namespace BcEditorTemplate\Controller\Api;

use BaserCore\Controller\Api\BcApiController;
use BcEditorTemplate\Service\EditorTemplatesServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class EditorTemplatesController
 *
 * エディタテンプレートコントローラー
 *
 * エディタテンプレートのAPI
 */
class EditorTemplatesController extends BcApiController
{

    /**
     * 一覧取得API
     *
     * @param EditorTemplatesServiceInterface $service
     */
    public function index(EditorTemplatesServiceInterface $service)
    {
        //todo 一覧取得API
    }

    /**
     * 単一データAPI
     *
     * @param EditorTemplatesServiceInterface $service
     */
    public function view(EditorTemplatesServiceInterface $service)
    {
        //todo 単一データAPI
    }

    /**
     * 新規追加API
     *
     * @param EditorTemplatesServiceInterface $service
     */
    public function add(EditorTemplatesServiceInterface $service)
    {
        //todo 新規追加API
    }

    /**
     * 編集API
     *
     * @param EditorTemplatesServiceInterface $service
     * @param int $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(EditorTemplatesServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $editorTemplate = $errors = null;
        try {
            $editorTemplate = $service->update($service->get($id), $this->request->getData());
            $message = __d('baser_core', 'エディタテンプレート「{0}」を更新しました。', $editorTemplate->name);
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
            'editorTemplate' => $editorTemplate,
            'errors' => $errors,
        ]);
        $this->viewBuilder()->setOption('serialize', ['editorTemplate', 'message', 'errors']);
    }

    /**
     * 削除API
     *
     * @param EditorTemplatesServiceInterface $service
     */
    public function delete(EditorTemplatesServiceInterface $service)
    {
        //todo 削除API
    }

    /**
     * リストAPI
     *
     * @param EditorTemplatesServiceInterface $service
     */
    public function list(EditorTemplatesServiceInterface $service)
    {
        //todo リストAPI
    }

}
