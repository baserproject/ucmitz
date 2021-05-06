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

namespace BaserCore\Test\TestCase\Controller;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcUtil;
use Cake\Datasource\ConnectionManager;
use Cake\Filesystem\Folder;
use Cake\TestSuite\IntegrationTestTrait;
use BaserCore\Controller\Admin\PluginsController;
use Cake\Event\Event;

/**
 * Class PluginsControllerTest
 * @package BaserCore\Test\TestCase\Controller
 */
class PluginsControllerTest extends BcTestCase
{
    /**
     * IntegrationTestTrait
     */
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.Plugins'
    ];

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loginAdmin();
        $this->PluginsController = new PluginsController($this->getRequest());
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * プラグインの初期化テスト
     */
    public function testInitialize()
    {
        $this->assertNotEmpty($this->PluginsController->RequestHandler);
    }
    /**
     * beforeFilterテスト
     */
    public function testBeforeFilter()
    {
        $event = new Event('Controller.beforeFilter', $this->PluginsController);
        $this->PluginsController->beforeFilter($event);
        $this->assertEquals($this->PluginsController->Security->getConfig('unlockedActions'), ['reset_db', 'update_sort', 'batch']);
    }
    /**
     * プラグインをアップロードしてインストールする
     */
    public function testAdd()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * プラグインの一覧を表示する
     */
    public function testIndex()
    {
        $this->get('/baser/admin/baser-core/plugins/index');
        $this->assertResponseOk();
    }

    /**
     * baserマーケットのプラグインデータを取得する
     */
    public function testAjax_get_market_plugins()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 並び替えを更新する
     */
    public function testAjax_update_sort()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * ファイル削除
     */
    public function testAjax_delete_file()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * プラグインを無効にして有効にして削除する
     *
     * 複数のプラグインのインストールを行うと
     * Migration ファイルの Initial クラスの重複読み込みエラーとなるので
     * 一つのプラグインで行わなければならない
     */
    public function testDetachAndInstallAndUninstall(): void
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->post('/baser/admin/baser-core/plugins/detach/BcSample');
        $this->assertFlashMessage('プラグインの無効化に失敗しました。');
        $this->post('/baser/admin/baser-core/plugins/detach/BcBlog');
        $this->assertFlashMessage('プラグイン「BcBlog」を無効にしました。');

        $this->put('/baser/admin/baser-core/plugins/install/BcBlog', ['connection' => 'test']);
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'plugins',
            'action' => 'index'
        ]);

        $from = BcUtil::getPluginPath('BcBlog');
        $pluginDir = dirname($from);
        $folder = new Folder();
        $to = $pluginDir . DS . 'BcBlogBak';
        $folder->copy($to, [
            'from' => $from,
            'mode' => 0777
        ]);
        $folder->create($from, 0777);
        $this->post('/baser/admin/baser-core/plugins/uninstall/BcBlog', ['connection' => 'test']);
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'plugins',
            'action' => 'index'
        ]);
        $this->assertFlashMessage('プラグイン「BcBlog」を削除しました。');
        $folder->move($from, [
            'from' => $to,
            'mode' => 0777,
            'schema' => Folder::OVERWRITE
        ]);

    }

    /**
     * アクセス制限設定を追加する
     */
    public function test_addPermission()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * データベースをリセットする
     */
    public function testReset_db()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->put('/baser/admin/baser-core/plugins/reset_db/BcBlog', ['connection' => 'test', 'name' => 'BcBlog']);
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'plugins',
            'action' => 'install',
            'BcBlog'
        ]);
        $this->assertFlashMessage('ブログ プラグインのデータを初期化しました。');
    }

}
