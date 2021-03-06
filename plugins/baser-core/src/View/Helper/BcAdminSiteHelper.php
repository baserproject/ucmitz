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

namespace BaserCore\View\Helper;

use BaserCore\Service\Admin\SiteManageServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use Cake\View\Helper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class BcAdminSiteHelper
 * @package BaserCore\View\Helper
 */
class BcAdminSiteHelper extends Helper
{

    use BcContainerTrait;

    /**
     * User Manage Service
     * @var SiteManageServiceInterface
     */
    public $SiteManage;

    /**
     * initialize
     * @param array $config
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->SiteManage = $this->getService(SiteManageServiceInterface::class);
    }

    /**
     * デバイスリストを取得
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getDevices(): array
    {
        return $this->SiteManage->getDevices();
    }

    /**
     * デバイスリストを取得
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getlangs(): array
    {
        return $this->SiteManage->getlangs();
    }

    /**
     * サイトのリストを取得
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSiteList(): array
    {
        return $this->SiteManage->getSiteList();
    }

}
