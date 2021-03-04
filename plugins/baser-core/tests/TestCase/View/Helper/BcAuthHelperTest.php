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

namespace BaserCore\Test\TestCase\View\Helper;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\BcAdminAppView;
use BaserCore\View\Helper\BcAdminHelper;

/**
 * Class BcAuthHelperTest
 * @package BaserCore\Test\TestCase\View\Helper
 * @property BcAuthHelper $BcAuthHelper
 */
class BcAuthHelperTest extends BcTestCase {
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.Plugins',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test getCurrentPrefix
     *@return void
     */
    public function testGetCurrentPrefix()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test getCurrentPrefixSetting
     *@return void
     */
    public function getCurrentPrefixSetting()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test getCurrentLoginUrl
     *@return void
     */
    public function getCurrentLoginUrl()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
    /**
     * Test getCurrentUserPrefixSettings
     *@return void
     */
    public function getCurrentUserPrefixSettings()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
    /**
     * Test isCurrentUserAdminAvailable
     *@return void
     */
    public function isCurrentUserAdminAvailable()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
    /**
     * Test getCurrentLoginAction
     *@return void
     */
    public function getCurrentLoginAction()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
    /**
     * Test getCurrentName
     *@return void
     */
    public function getCurrentName()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
    /**
     * Test isAdminLogin
     *@return void
     */
    public function isAdminLogin()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
    /**
     * Test getCurrentLogoutUrl
     *@return void
     */
    public function getCurrentLogoutUrl()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
    /**
     * Test getCurrentLoginRedirectUrl
     *@return void
     */
    public function getCurrentLoginRedirectUrl()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
    /**
     * Test getCurrentLoginUser
     *@return void
     */
    public function getCurrentLoginUser()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
    /**
     * Test isSuperUser
     *@return void
     */
    public function isSuperUser()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
    /**
     * Test isAgentUser
     *@return void
     */
    public function isAgentUser()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
