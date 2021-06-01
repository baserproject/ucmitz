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

namespace BaserCore\Utility;

use Cake\Core\Container;

/**
 * Class BcContainer
 * @package BaserCore\Utility
 */
class BcContainer
{
    /**
     * Container
     * @var Container $container
     */
    static $container;

    /**
     * Set Container
     * @param $container
     */
    static public function set($container): void
    {
        self::$container = $container;
    }

    /**
     * Get Container
     * @return Container
     */
    static public function get(): Container
    {
        if (!self::$container) {
            self::$container = new Container();
        }
        return self::$container;
    }
}
