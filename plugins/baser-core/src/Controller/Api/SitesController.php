<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Controller\Api;

use BaserCore\Service\SitesServiceInterface;

/**
 * Class SitesController
 * @package BaserCore\Controller\Api
 */
class SitesController extends BcApiController
{

    /**
     * サイト情報取得
     * @param SitesServiceInterface $sites
     * @param $id
     */
    public function view(SitesServiceInterface $sites, $id)
    {
        $this->set([
            'site' => $sites->get($id)
        ]);
        $this->viewBuilder()->setOption('serialize', ['site']);
    }
}
