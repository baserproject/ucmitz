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

namespace BaserCore\Controller\Api;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Error\BcException;
use BaserCore\Service\UtilitiesServiceInterface;
use BaserCore\Utility\BcUtil;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * Class UtilitiesController
 */
class UtilitiesController extends BcApiController
{

    /**
     * [API] サーバーキャッシュを削除する
     *
     * @checked
     * @unitTest
     * @noTodo
     */
    public function clear_cache()
    {
        BcUtil::clearAllCache();

        $this->set([
            'message' => __d('baser', 'サーバーキャッシュを削除しました。')
        ]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }


    /**
     * [API] ユーティリティ：ツリー構造リセット
     * @param UtilitiesServiceInterface $service
     * @checked
     * @noTodo
     */
    public function reset_contents_tree(UtilitiesServiceInterface $service)
    {
        $this->request->allowMethod(['post']);
        $errors = null;
        try {
            if ($service->resetContentsTree()) {
                $message = __d('baser', 'コンテンツのツリー構造をリセットしました。');
            } else {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser', 'コンテンツのツリー構造のリセットに失敗しました。');
            }
        } catch (PersistenceFailedException $e) {
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        } catch (\Throwable $e) {
            $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'message' => $message,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'errors']);
    }

    /**
     * [API] ユーティリティ：ツリー構造チェック
     *
     * @param UtilitiesServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function verity_contents_tree(UtilitiesServiceInterface $service)
    {
        $this->request->allowMethod(['post']);
        $errors = null;
        try {
            if ($service->verityContentsTree()) {
                $message = __d('baser', 'コンテンツのツリー構造に問題はありません。');
            } else {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser', 'コンテンツのツリー構造に問題があります。ログを確認してください。');
            }
        } catch (PersistenceFailedException $e) {
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        } catch (\Throwable $e) {
            $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'message' => $message,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'errors']);
    }

    /**
     * [API] ユーティリティ：バックアップダウンロード
     *
     * @param UtilitiesServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function download_backup(UtilitiesServiceInterface $service)
    {
        $this->request->allowMethod(['get']);
        $errors = null;
        try {
            $result = $service->backupDb($this->request->getQuery('backup_encoding'));
            if (!$result) {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser', 'バックアップダウンロードが失敗しました。');
            } else {
                $this->autoRender = false;
                $result->download('baserbackup_' . str_replace(' ', '_', BcUtil::getVersion()) . '_' . date('Ymd_His'));
                $service->resetTmpSchemaFolder();
                return;
            }
        } catch (PersistenceFailedException $e) {
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        } catch (\Throwable $e) {
            $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'message' => $message,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'errors']);
    }


    /**
     * [API] ユーティリティ：バックアップよりレストア
     *
     * @param UtilitiesServiceInterface $service
     * @checked
     * @noTodo
     */
    public function restore_db(UtilitiesServiceInterface $service)
    {
        $this->request->allowMethod(['post']);
        $errors = null;
        try {
            $service->restoreDb($this->getRequest()->getData(), $this->getRequest()->getUploadedFiles());
            $message = __d('baser', 'データの復元が完了しました。');
        } catch (PersistenceFailedException $e) {
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        } catch (\Throwable $e) {
            $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'message' => $message,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'errors']);
    }

    /**
     * [API] ユーティリティ：ログファイルダウンロード
     * @param UtilitiesServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function download_log(UtilitiesServiceInterface $service)
    {
        $this->request->allowMethod(['get']);
        $errors = null;
        try {
            $this->autoRender = false;
            $result = $service->createLogZip();

            if ($result) {
                $result->download('basercms_logs_' . date('Ymd_His'));
                return;
            }

            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', 'エラーログが存在しません。');
        } catch (PersistenceFailedException $e) {
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        } catch (\Throwable $e) {
            $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'message' => $message,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'errors']);
    }

    /**
     * [API] ユーティリティ：ログファイルを削除
     *
     * @param UtilitiesServiceInterface $service
     * @checked
     * @noTodo
     */
    public function delete_log(UtilitiesServiceInterface $service)
    {
        $this->request->allowMethod(['post']);
        $errors = null;
        try {
            $service->deleteLog();
            $message = __d('baser', 'エラーログを削除しました。');
        } catch (PersistenceFailedException $e) {
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        } catch (\Throwable $e) {
            $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'message' => $message,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'errors']);
    }

    /**
     * 検索ボックスの表示状態を保存する
     *
     * @param string $key キー
     * @param mixed $open 1 Or ''
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function save_search_opened($key, $open = '')
    {
        $this->request->allowMethod(['post']);
        $this->request->getSession()->write('BcApp.adminSearchOpened.' . $key, $open);
        $this->set([
            'result' => true
        ]);
        $this->viewBuilder()->setOption('serialize', ['result']);
    }

}
