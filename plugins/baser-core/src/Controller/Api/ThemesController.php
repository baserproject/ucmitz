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
use BaserCore\Service\SitesServiceInterface;
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
     * [API] テーマを適用するAPI
     * @param ThemesServiceInterface $themesService
     * @param SitesServiceInterface $sitesService
     * @param int $siteId
     * @param string $theme
     * @checked
     * @noTodo
     * @unitTest
     */
    public function apply(ThemesServiceInterface $themesService, SitesServiceInterface $sitesService, int $siteId, string $theme)
    {
        $this->request->allowMethod(['post']);

        $errors = null;

        try {
            $info = $themesService->apply($sitesService->get($siteId), $theme);
            $message = [__d('baser', 'テーマ「{0}」を適用しました。', $theme)];
            if ($info) $message = array_merge($message, [''], $info);
            $message = implode("\n", $message);
        } catch (BcException $e) {
            $errors = $e->getMessage();
            $message = __d('baser', 'テーマの適用に失敗しました。', $e->getMessage());
        }

        $this->set([
            'message' => $message,
            'theme' => $theme,
            'errors' => $errors
        ]);

        $this->viewBuilder()->setOption('serialize', ['message', 'theme', 'errors']);
    }

}
