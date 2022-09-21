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

/**
 * Class UtilitiesController
 *
 * https://localhost/baser/api/baser-core/utilities/action_name.json で呼び出す
 *
 * @package BaserCore\Controller\Api
 */
class UtilitiesController extends BcApiController
{
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
            $message = 'コンテンツのツリー構造に問題はありません。';
        } else {
            $this->setResponse($this->response->withStatus(400));
            $message = 'コンテンツのツリー構造に問題があります。ログを確認してください。';
        }

        $this->set([
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }
}
