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
use BaserCore\Service\UtilitiesServiceInterface;
use BaserCore\Utility\BcUtil;

/**
 * Class UtilitiesController
 *
 * https://localhost/baser/api/baser-core/utilities/action_name.json で呼び出す
 *
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

        if ($service->resetContentsTree()) {
            $message = __d('baser', 'コンテンツのツリー構造をリセットしました。');
        } else {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', 'コンテンツのツリー構造のリセットに失敗しました。');
        }

        $this->set([
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['message']);
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

        if ($service->verityContentsTree()) {
            $message = __d('baser', 'コンテンツのツリー構造に問題はありません。');
        } else {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', 'コンテンツのツリー構造に問題があります。ログを確認してください。');
        }

        $this->set([
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['message']);
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

        try {
            $result = $service->backupDb($this->request->getQuery('backup_encoding'));
            $result->download('baserbackup_' . str_replace(' ', '_', BcUtil::getVersion()) . '_' . date('Ymd_His'));
            $service->resetTmpSchemaFolder();

            return;
        } catch (\Exception $exception) {
            $message = __d('baser', 'バックアップダウンロードが失敗しました。' . $exception->getMessage());
            $this->set([
                'message' => $message
            ]);
            $this->setResponse($this->response->withStatus(400));
            $this->viewBuilder()->setOption('serialize', ['message']);
        }
    }

}
