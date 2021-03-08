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
use BaserCore\View\Helper\BcAuthHelper;
use Cake\Core\Configure;

/**
 * Class BcAuthHelperTest
 * @package BaserCore\Test\TestCase\View\Helper
 * @property BcAuthHelper $BcAuth
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
    ];
    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        // adminの場合
        $this->BcAdminAppView = new BcAdminAppView();
        $this->BcAdminAppView->setRequest($this->getRequest()->withParam('prefix','Admin'));
        $this->BcAuth = new BcAuthHelper($this->BcAdminAppView);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcAdminAppView);
        unset($this->BcAuth);
        parent::tearDown();
    }


    /**
     * Test getCurrentPrefix
     *@return void
     */
    public function testGetCurrentPrefix()
    {
        // adminの場合
        $result = $this->BcAuth->getCurrentPrefix();
        $this->assertEquals('Admin',$result);
        // その他の場合
        $this->BcAdminAppView->setRequest($this->getRequest()->withParam('prefix',null));
        $BcAuth = new BcAuthHelper($this->BcAdminAppView);
        $result = $BcAuth->getCurrentPrefix();
        $this->assertEquals('front',$result);
    }

    /**
     * Test getCurrentPrefixSetting
     *@return void
     */
    public function testGetCurrentPrefixSetting()
    {
        // 管理画面の場合
        $config = Configure::read('BcPrefixAuth.Admin');
        $this->assertNotEmpty($config);
        // その他の場合
        $config = Configure::read('BcPrefixAuth.front');
        $this->assertEmpty($config);
    }

    /**
     * Test getCurrentLoginUrl
     * HACK:"/baser/admin/users/login"を環境変数から取得したい
     *@return void
     */
    public function testGetCurrentLoginUrl()
    {
        $expected = "/baser/admin/users/login";
        $result = $this->BcAuth->getCurrentLoginUrl();
        $this->assertEquals($expected,$result);
    }
    /**
     * FIXME:getCurrentUserPrefixSettings改修後にテストも改修必
     * Test getCurrentUserPrefixSettings
     *@return void
     */
    public function testGetCurrentUserPrefixSettings()
    {
        $result = $this->BcAuth->getCurrentUserPrefixSettings();
        $this->assertEquals(['admin'],$result);
    }
    /**
     * Test isCurrentUserAdminAvailable
     *@return void
     */
    public function testIsCurrentUserAdminAvailable()
    {
        $result = $this->BcAuth->isCurrentUserAdminAvailable();
        $this->assertTrue($result);
    }
    /**
     * Test getCurrentLoginAction
     *@return void
     */
    public function testGetCurrentLoginAction()
    {
        $this->markTestIncomplete('テスト対象のメソッドが未実装です');
    }
    /**
     * Test getCurrentName
     *@return void
     */
    public function testGetCurrentName()
    {
        // prefix(admin)の場合
        $expected = Configure::read('BcPrefixAuth.Admin')['name'];
        $result = $this->BcAuth->getCurrentName();
        $this->assertEquals($expected, $result);
    }
    /**
     * Test isAdminLogin
     * @return void
     */
    public function testIsAdminLogin()
    {
        // ログインしない場合;
        $result = $this->BcAuth->isAdminLogin();
        $this->assertFalse($result);
        // ログインした場合
        $this->loginAdmin();
        $result = $this->BcAuth->isAdminLogin();
        $this->assertTrue($result);
    }
    /**
     * Test getCurrentLogoutUrl
     *@return void
     */
    public function testGetCurrentLogoutUrl()
    {
        $expected = "/baser/admin/users/logout";
        $result = $this->BcAuth->getCurrentLogoutUrl();
        $this->assertEquals($expected,$result);
    }
    /**
     * Test getCurrentLoginRedirectUrl
     *@return void
     */
    public function testGetCurrentLoginRedirectUrl()
    {
        $expected = "/baser/admin";
        $result = $this->BcAuth->getCurrentLogoutUrl();
        $this->assertEquals($expected,$result);
    }
    /**
     * Test getCurrentLoginUser
     *@return void
     */
    public function testGetCurrentLoginUser()
    {
        // ログインしない場合;
        $result = $this->BcAuth->getCurrentLoginUser();
        $this->assertNull($result);
        // ログインした場合
        $this->Users = $this->getTableLocator()->get('BaserCore.Users');
        $expected = $this->Users->find()
                    ->where(['Users.id' => 1])
                        ->contain(['UserGroups'])
                        ->first();
        $this->loginAdmin();
        $result = $this->BcAuth->getCurrentLoginUser();
        $this->assertEquals($result,$expected);
    }
    /**
     * Test isSuperUser
     *@return void
     */
    public function testIsSuperUser()
    {
        // ログインしない場合
        $result = $this->BcAuth->isSuperUser();
        $this->assertFalse($result);
        // システム管理者の場合 
        $this->loginAdmin(1);
        $result = $this->BcAuth->isSuperUser();
        $this->assertTrue($result);
        // サイト運営者などそれ以外の場合
        $this->loginAdmin(2);
        $result = $this->BcAuth->isSuperUser();
        $this->assertFalse($result);
    }
    /**
     * Test isAgentUser
     * @dataProvider isAgentUserDataProvider
     *@return void
     */
    public function testIsAgentUser($id,$expected)
    {
        if($id) {
            $user = $this->loginAdmin($id);
            $request = $this->BcAdminAppView->getRequest();
            $session = $request->getSession();
            $session->write('AuthAgent.User',$user);
        }
        $result = $this->BcAuth->isAgentUser();
        $this->assertEquals($result,$expected);
    }
    public function isAgentUserDataProvider() {
        return [
            // ログインしてない場合
            [null,false],
            // システム管理者などAuthAgentが与えられた場合
            [1,true],
        ];
    }
}
