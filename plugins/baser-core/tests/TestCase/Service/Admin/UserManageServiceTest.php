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

namespace BaserCore\Test\TestCase\Service\Admin;

use BaserCore\Model\Table\LoginStoresTable;
use BaserCore\Service\Admin\UserManageService;
use Cake\Http\Response;

/**
 * Class UserManageServiceTest
 * @package BaserCore\Test\TestCase\Service
 */
class UserManageServiceTest extends \BaserCore\TestSuite\BcTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.LoginStores'
    ];

    /**
     * @var UserManageService|null
     */
    public $UserManage = null;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->UserManage = new UserManageService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->UserManage);
        parent::tearDown();
    }

    /**
     * test Login
     */
    public function testLoginAndLogout()
    {
        $request = $this->getRequest('/baser/admin/users/index');
        $authentication = $this->BaserCore->getAuthenticationService($request);
        $request = $request->withAttribute('authentication', $authentication);
        $response = new Response();
        $request = $this->UserManage->login($request, $response, 1)['request'];
        $this->assertEquals(1, $request->getAttribute('identity')->id);
        $this->assertEquals(1, $request->getSession()->read('AuthAdmin')->id);
        $this->UserManage->logout($request, $response, 1);
        $this->assertNull($request->getSession()->read('AuthAdmin'));
    }

    /**
     * test getAuthSessionKey
     */
    public function testGetAuthSessionKey()
    {
        $this->assertEquals('AuthAdmin', $this->UserManage->getAuthSessionKey('Admin'));
        $this->assertFalse($this->UserManage->getAuthSessionKey('baser'));
    }

    /**
     * test reLogin
     */
    public function testReLogin()
    {
        $request = $this->loginAdmin($this->getRequest('/baser/admin/baser-core/users/index'));
        $this->UserManage->update($request->getAttribute('identity')->getOriginalData(), ['name' => 'test']);
        $request = $this->UserManage->reLogin($request, new Response())['request'];
        $this->assertEquals('test', $request->getAttribute('identity')->name);
    }

    /**
     * test setCookieAutoLoginKey
     */
    public function testSetCookieAutoLoginKey()
    {
        $response = $this->UserManage->setCookieAutoLoginKey(new Response(), 1);
        $cookie = $response->getCookie(LoginStoresTable::KEY_NAME);
        $this->assertNotEmpty($cookie['value']);
    }

    /**
     * test checkAutoLogin
     */
    public function testCheckAutoLogin()
    {
        $response = $this->UserManage->setCookieAutoLoginKey(new Response(), 1);
        $request = $this->getRequest('/baser/admin/users/');
        $beforeCookie = $response->getCookie(LoginStoresTable::KEY_NAME);
        $request = $request->withCookieParams([LoginStoresTable::KEY_NAME => $beforeCookie['value']]);
        $response = $this->UserManage->checkAutoLogin($request, $response);
        $afterCookie = $response->getCookie(LoginStoresTable::KEY_NAME);
        $this->assertNotEmpty($afterCookie['value']);
        $this->assertNotEquals($beforeCookie['value'], $afterCookie['value']);
    }

    /**
     * test loginToAgent
     */
    public function testLoginToAgentAndReturnLoginUserFromAgent()
    {
        $request = $this->loginAdmin($this->getRequest('/baser/admin/baser-core/users/'));
        $response = new Response();
        $this->UserManage->loginToAgent($request, $response, 2);
        $this->assertSession(1, 'AuthAgent.User.id');
        $this->assertSession(2, 'AuthAdmin.id');
        $this->UserManage->returnLoginUserFromAgent($request, $response);
        $this->assertSession(null, 'AuthAgent.User.id');
        $this->assertSession(1, 'AuthAdmin.id');
    }

    /**
     * test reload
     *
     * @return void
     */
    public function testReload()
    {
        // 未ログイン
        $request = $this->getRequest('/baser/admin/users/index');
        $noLoginUser = $this->UserManage->reload($request);
        $this->assertTrue($noLoginUser);

        $authentication = $this->BaserCore->getAuthenticationService($request);
        $request = $request->withAttribute('authentication', $authentication);
        $response = new Response();
        $request = $this->UserManage->login($request, $response, 1)['request'];

        // 通常読込
        $users = $this->getTableLocator()->get('Users');
        $user = $users->get(1);
        $user->name = 'modified name';
        $users->save($user);
        $this->UserManage->reload($request);
        $this->assertSession('modified name', 'AuthAdmin.name');

        // 削除
        $users->delete($user);
        $deleteRealaodUser = $this->UserManage->reload($request);
        $this->assertFalse($deleteRealaodUser);
    }

}
