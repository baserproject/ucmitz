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

use BaserCore\Error\BcException;
use BaserCore\Service\ThemesServiceInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class ThemesController
 *
 * https://localhost/baser/api/baser-core/themes/action_name.json で呼び出す
 *
 * @package BaserCore\Controller\Api
 */
class ThemesController extends BcApiController
{

    /**
     * [API] テーマ一覧を取得する
     * @param ThemesServiceInterface $themes
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(ThemesServiceInterface $themes)
    {
        $this->set([
            'themes' => $themes->getIndex()
        ]);
        $this->viewBuilder()->setOption('serialize', ['themes']);
    }

    /**
     * [API] テーマを削除する
     *
     * @param ThemesServiceInterface $service
     * @param string $theme
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(ThemesServiceInterface $service, string $theme)
    {
        $this->request->allowMethod(['post']);

        $error = null;
        try {
            $service->delete($theme);
            $message = __d('baser', 'テーマ「{0}」を削除しました。', $theme);
        } catch (BcException $e) {
            $this->setResponse($this->response->withStatus(400));
            $error = $e->getMessage();
            $message = __d('baser', 'テーマフォルダのアクセス権限を見直してください。' . $e->getMessage());
        }

        $this->set([
            'theme' => $theme,
            'message' => $message,
            'error' => $error
        ]);

        $this->viewBuilder()->setOption('serialize', ['theme', 'message', 'error']);
    }

}
