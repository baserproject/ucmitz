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
use Cake\Routing\Router;

/**
 * Class BcAdminHelperTest
 * @package BaserCore\Test\TestCase\View\Helper
 * @property BcAdminHelper $BcAdmin
 */
class BcAdminHelperTest extends BcTestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $BcAdminAppView = new BcAdminAppView($this->getRequest()->withParam('controller','users'));
        $BcAdminAppView->setTheme('BcAdminThird');
        $this->BcAdmin = new BcAdminHelper($BcAdminAppView);
    }
    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        Router::reload();
        unset($BcAdminAppView, $this->BcAdmin);
    }

    public function testIsAvailableSideBar()
    {
        // 未ログイン
        $results = $this->BcAdmin->isAvailableSideBar();
        $this->assertEquals(false, $results);
        // ログイン済
        $session = $this->BcAdmin->getView()->getRequest()->getSession();
        $session->write('AuthAdmin', true);
        $results = $this->BcAdmin->isAvailableSideBar();
        $this->assertEquals(true, $results);
        // ログイン画面
        $this->BcAdmin->getView()->setRequest($this->getRequest('/baser/admin/users/login'));
        $results = $this->BcAdmin->isAvailableSideBar();
        $this->assertEquals(false, $results);
        $results = $this->BcAdmin->isAvailableSideBar();
        $this->assertEquals(false, $results);
    }

    /**
     * Test setTitle
     *
     * @return void
     */
    public function testSetTitle()
    {
        $title = 'test';
        $this->BcAdmin->setTitle($title);
        $this->assertEquals($title, $this->BcAdmin->getView()->get('title'));
    }

    /**
     * Test setHelp
     *
     * @return void
     */
    public function testSetHelp()
    {
        $help = 'test';
        $this->BcAdmin->setHelp($help);
        $this->assertEquals($help, $this->BcAdmin->getView()->get('help'));
    }

    /**
     * Test setSearch
     *
     * @return void
     */
    public function testSetSearch()
    {
        $search = 'test';
        $this->BcAdmin->setSearch($search);
        $this->assertEquals($search, $this->BcAdmin->getView()->get('search'));
    }

    /**
     * Test title
     *
     * @return void
     */
    public function testTitle()
    {
        ob_start();
        $this->BcAdmin->title();
        $actualEmpty = ob_get_clean();
        $this->assertEmpty($actualEmpty);

        $title = 'test';
        $this->BcAdmin->getView()->assign('title', $title);
        ob_start();
        $this->BcAdmin->title();
        $actual = ob_get_clean();
        $this->assertEquals($title, $actual);
    }

    /**
     * Test help
     *
     * @return void
     */
    public function testHelp()
    {
        ob_start();
        $this->BcAdmin->help();
        $actualEmpty = ob_get_clean();
        $this->assertEmpty($actualEmpty);

        $help = 'users_index';
        $expected = $this->BcAdmin->getView()->element('Admin/help', ['help' => $help]);

        $this->BcAdmin->getView()->set('help', $help);
        ob_start();
        $this->BcAdmin->help();
        $actual = ob_get_clean();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test search
     * @dataProvider searchDataProvider
     * @return void
     */
    public function testSearch($template,$mode) {
        if(!$template) {
            $this->loadRoutes();
            ob_start();
            $this->BcAdmin->search();
            $actual = ob_get_clean();
            $this->assertEmpty($actual);
        } else {
            $SearchOpenedSaveUrl = '/baser/admin/Utilities/ajax_save_search_box';
            Router::connect($SearchOpenedSaveUrl. '/*', ['controller' => 'Utilities', 'action' => 'ajax_save_search_box']);
            Router::connect('/baser/admin/users/index/*', ['controller' => 'users', 'action' => 'index']);

            $this->BcAdmin->getView()->set('search', $template);
            $session = $this->BcAdmin->getView()->getRequest()->getSession();
            $session->write('BcApp.adminSearchOpened.Admin', $mode);
            $expected = $this->BcAdmin->getView()->element('Admin/search', [
                'search' => $template,
                'adminSearchOpened' => $mode,
                'adminSearchOpenedSaveUrl' => $SearchOpenedSaveUrl . "/Admin",
                ]);
            ob_start();
            $this->BcAdmin->search();
            $actual = ob_get_clean();
            // templateが同じ内容か
            $this->assertStringContainsString($actual, $expected);
            // search_boxが閉じてるか('')開いてるか('1')
            $this->assertStringContainsString("data-adminSearchOpened=\"{$mode}\"", $expected);
        }
    }
    function searchDataProvider()
    {
        return [
            // templateがない場合
            ['',null],
            // templateがある場合 && 検索ボックスclose
            ['users_index',''],
            // templateがある場合 && 検索ボックスopen
            ['users_index','1'],
        ];
    }

    /**
     * Test contentsMenu
     *
     * @return void
     */
    public function testContentsMenu()
    {
        // ヘルプなし 未ログイン
        $expected = $this->BcAdmin->getView()->element('Admin/contents_menu', [
            'isHelp' => false,
            'isLogin' => false
        ]);
        ob_start();
        $this->BcAdmin->contentsMenu();
        $actual = ob_get_clean();
        $this->assertEquals($expected, $actual);

        // ヘルプあり 未ログイン
        $expectedIsHelp = $this->BcAdmin->getView()->element('Admin/contents_menu', [
            'isHelp' => true,
            'isLogin' => false
        ]);
        $this->BcAdmin->getView()->set('help', 'test');
        ob_start();
        $this->BcAdmin->contentsMenu();
        $actualIsHelp = ob_get_clean();
        $this->assertEquals($expectedIsHelp, $actualIsHelp);

        $session = $this->BcAdmin->getView()->getRequest()->getSession();
        $session->write('AuthAdmin', true);

        // ヘルプなし ログイン済
        $expectedIsLogin = $this->BcAdmin->getView()->element('Admin/contents_menu', [
            'isHelp' => false,
            'isLogin' => true
        ]);
        $this->BcAdmin->getView()->set('help', null);
        ob_start();
        $this->BcAdmin->contentsMenu();
        $actualIsLogin = ob_get_clean();
        $this->assertEquals($expectedIsLogin, $actualIsLogin);

        // ヘルプあり ログイン済
        $expectedIsHelpIsLogin = $this->BcAdmin->getView()->element('Admin/contents_menu', [
            'isHelp' => true,
            'isLogin' => true
        ]);
        $this->BcAdmin->setHelp('test');
        ob_start();
        $this->BcAdmin->contentsMenu();
        $actualIsHelpIsLogin = ob_get_clean();
        $this->assertEquals($expectedIsHelpIsLogin, $actualIsHelpIsLogin);
    }
}
