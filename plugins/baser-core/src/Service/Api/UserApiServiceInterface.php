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

namespace BaserCore\Service\Api;

use Authentication\Authenticator\ResultInterface;

/**
 * Interface UserApiServiceInterface
 * @package BaserCore\Service
 */
interface UserApiServiceInterface
{
    /**
     * ログイントークンを取得する
     * @param ResultInterface $result
     * @return array
     */
    public function getAccessToken(ResultInterface $result): array;
}
