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

namespace BaserCore\Test\TestCase;

use BaserCore\BcPlugin;
use BaserCore\Model\Entity\Site;
use BaserCore\Service\SitesService;
use BaserCore\Test\Factory\PluginFactory;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Test\Factory\UserFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcUtil;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\ORM\TableRegistry;
use Cake\Routing\RouteBuilder;
use Cake\Routing\RouteCollection;

/**
 * Class BcPluginTest
 * @package BaserCore\Test\TestCase
 */
class BcPluginTest extends BcTestCase
{

    /**
     * @var BcPlugin
     */
    public $BcPlugin;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Plugins',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.SiteConfigs',
        'plugin.BaserCore.Sites',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
        $this->BcPlugin = new BcPlugin(['name' => 'BcBlog']);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcPlugin);
        parent::tearDown();
    }

    /**
     * testInitialize
     */
    public function testInitialize()
    {
        $this->assertNotEmpty($this->BcPlugin->migrations);
    }

    /**
     * testInstall
     */
    public function testInstallAndUninstall()
    {
        // インストール
        $this->BcPlugin->install(['connection' => 'test']);
        $plugins = $this->getTableLocator()->get('Plugins')->find()->where(['name' => 'BcBlog'])->first();
        $this->assertEquals(1, $plugins->priority);

        // アンインストール
        $from = BcUtil::getPluginPath('BcBlog');
        $pluginDir = dirname($from);
        $folder = new Folder();
        $to = $pluginDir . DS . 'BcBlogBak';
        $folder->copy($to, [
            'from' => $from,
            'mode' => 0777
        ]);
        $folder->create($from, 0777);
        $this->BcPlugin->uninstall(['connection' => 'test']);
        $this->assertFalse(is_dir($from));
        $plugins = $this->getTableLocator()->get('Plugins')->find()->where(['name' => 'BcBlog'])->first();
        $this->assertNull($plugins);
        $folder->move($from, [
            'from' => $to,
            'mode' => 0777,
            'schema' => Folder::OVERWRITE
        ]);
        $this->BcPlugin->install(['connection' => 'test']);

    }

    /**
     * testRollback
     */
    public function testRollback()
    {
        $this->BcPlugin->install(['connection' => 'test']);
        $this->BcPlugin->rollbackDb(['connection' => 'test']);
        $collection = ConnectionManager::get('default')->getSchemaCollection();
        $tables = $collection->listTables();
        $this->assertNotContains('blog_posts', $tables);
        $plugins = $this->getTableLocator()->get('BaserCore.Plugins');
        $plugins->deleteAll(['name' => 'BcBlog']);
        $this->BcPlugin->install(['connection' => 'test']);
    }

    /**
     * testRoutes
     */
    public function testRoutes()
    {
        $collection = new RouteCollection();
        $routes = new RouteBuilder($collection, '/');
        $this->BcPlugin->routes($routes);
        $all = $collection->routes();
        // connect・fallbacksにより3つコネクションあり|拡張子jsonあり
        $this->assertEquals($all[0]->defaults, ['plugin' => 'BcBlog', 'action' => 'index']);
        $this->assertEquals($all[0]->getExtensions()[0], "json");
        // connect・fallbacksにより3つコネクションあり|拡張子jsonあり
        $this->assertEquals($all[3]->defaults, ['plugin' => 'BcBlog', 'action' => 'index', 'prefix' => 'Api']);
        $this->assertEquals($all[3]->getExtensions()[0], "json");
        // connect・fallbacksにより3つコネクションあり|拡張子jsonなし
        $this->assertEquals($all[6]->defaults, ['plugin' => 'BcBlog', 'action' => 'index', 'prefix' => 'Admin']);
        $this->assertEmpty($all[6]->getExtensions());
        // connectにより1つコネクションあり|拡張子jsonなし
        $this->assertEquals($all[9]->defaults, ['plugin' => 'BcBlog', 'action' => 'index']);
        $this->assertEmpty($all[9]->getExtensions());
    }

    /**
     * test getUpdateScriptMessages And getUpdaters
     */
    public function test_getUpdateScriptMessagesAndGetUpdaters()
    {
        $name = 'Sample';
        $pluginPath = ROOT . DS . 'plugins' . DS . $name . DS;
        $updatePath = $pluginPath . 'config' . DS . 'update' . DS;
        PluginFactory::make(['name' => $name, 'title' => 'サンプル', 'version' => '1.0.0'])->persist();
        $folder = new Folder();

        // 新バージョン
        $folder->create($pluginPath);
        $file = new File($pluginPath . 'VERSION.txt');
        $file->write('1.0.3');
        $file->close();
        // アップデートスクリプト 0.0.1
        $folder->create($updatePath . '0.0.1');
        $file = new File($updatePath . '0.0.1' . DS . 'config.php');
        $file->write('<?php return [\'updateMessage\' => \'test0\'];');
        $file->close();
        // アップデートスクリプト 1.0.1
        $folder->create($updatePath . '1.0.1');
        $file = new File($updatePath . '1.0.1' . DS . 'config.php');
        $file->write('<?php return [\'updateMessage\' => \'test1\'];');
        $file->close();
        $file = new File($updatePath . '1.0.1' . DS . 'updater.php');
        $file->create();
        $file->close();
        // アップデートスクリプト 1.0.2
        $folder->create($updatePath . '1.0.2');
        $file = new File($updatePath . '1.0.2' . DS . 'config.php');
        $file->write('<?php return [\'updateMessage\' => \'test2\'];');
        $file->close();
        $file = new File($updatePath . '1.0.2' . DS . 'updater.php');
        $file->create();
        $file->close();
        // アップデートスクリプト 1.0.4
        $folder->create($updatePath . '1.0.4');
        $file = new File($updatePath . '1.0.4' . DS . 'config.php');
        $file->write('<?php return [\'updateMessage\' => \'test3\'];');
        $file->close();

        $this->assertEquals(
            ['Sample-1.0.1' => 'test1', 'Sample-1.0.2' => 'test2'],
            $this->BcPlugin->getUpdateScriptMessages($name)
        );
        $this->assertEquals(
            ['Sample-1.0.1' => 1000001000, 'Sample-1.0.2' => 1000002000],
            $this->BcPlugin->getUpdaters($name)
        );
        $folder->delete($pluginPath);
    }

    /**
     * test execScript
     */
    public function test_execScript()
    {
        $this->fixtureStrategy->teardownTest();
        $version = '1.0.0';
        $updatePath = Plugin::path('BcBlog') . 'config' . DS . 'update';
        $versionPath = $updatePath . DS . $version;
        // スクリプトなし
        if(file_exists($versionPath . DS . 'updater.php')) {
            unlink($versionPath . DS . 'updater.php');
        }
        $this->assertTrue($this->BcPlugin->execScript($version));
        // 有効スクリプトあり
        UserFactory::make(['id' => 1, 'name' => 'test'])->persist();
        $folder = new Folder();
        $folder->create($versionPath);
        $file = new File($versionPath . DS . 'updater.php');
        $file->write('<?php
use Cake\ORM\TableRegistry;
$users = TableRegistry::getTableLocator()->get(\'BaserCore.Users\');
$user = $users->find()->where([\'id\' => 1])->first();
$user->name = \'hoge\';
$users->save($user);');
        $file->close();
        $this->assertTrue($this->BcPlugin->execScript($version));
        $users = $this->getTableLocator()->get('BaserCore.Users');
        $user = $users->find()->where(['id' => 1])->first();
        $this->assertEquals('hoge', $user->name);
        // 無効スクリプトあり
        $file = new File($versionPath . DS . 'updater.php');
        $file->write('<?php
use BaserCore\Error\BcException;
throw new BcException(\'test\');');
        $file->close();
        $this->assertFalse($this->BcPlugin->execScript($version));
        $file = new File(LOGS . 'cli-error.log');
        $log = $file->read();
        $this->assertStringContainsString('test', $log);
        // 初期化
        $folder->delete($updatePath);
    }

    /**
     * test createAssetsSymlink
     */
    public function test_createAssetsSymlink()
    {
        unlink(WWW_ROOT . 'baser_core');
        $this->BcPlugin->createAssetsSymlink();
        $this->assertTrue(file_exists(WWW_ROOT . 'baser_core'));
    }

    /**
     * test update
     */
    public function test_update()
    {
        $pluginPath = ROOT . DS . 'plugins' . DS . 'BcTest' . DS;
        $folder = new Folder();

        // プラグインフォルダを初期化
        $folder->delete($pluginPath);
        $configPath = $pluginPath . 'config' . DS;
        $updaterPath = $configPath . 'update' . DS . '0.0.2' . DS;
        $migrationPath = $configPath . 'Migrations' . DS;
        $srcPath = $pluginPath . 'src' . DS;
        $folder->create($updaterPath);
        $folder->create($srcPath);
        $folder->create($migrationPath);

        // VERSION.txt
        $file = new File($pluginPath . 'VERSION.txt');
        $file->write('0.0.1');

        // src/Plugin.php
        $file = new File($srcPath . 'Plugin.php');
        $file->write('<?php
namespace BcTest;
use BaserCore\BcPlugin;
class Plugin extends BcPlugin {}');

        // config/Migrations/20220626000000_InitialBcTest.php
        $file = new File($migrationPath . '20220626000000_InitialBcTest.php', 'w');
        $file->write('<?php
use Migrations\AbstractMigration;
class InitialBcTest extends AbstractMigration
{
    public function up()
    {
        $this->table(\'bc_test\')
            ->addColumn(\'name\', \'string\', [
                \'default\' => null,
                \'limit\' => 255,
                \'null\' => true,
            ])
            ->create();
    }
    public function down()
    {
        $this->table(\'bc_test\')->drop()->save();
    }
}');

        // インストール実行
        $plugin = new BcPlugin(['name' => 'BcTest']);
        $plugin->install(['connection' => 'test']);
        $db = ConnectionManager::get('test');
        $collection = $db->getSchemaCollection();
        $tableSchema = $collection->describe('bc_test');
        $this->assertEquals('string', $tableSchema->getColumnType('name'));

        // VERSION.txt
        $file = new File($pluginPath . 'VERSION.txt');
        $file->write('0.0.2');

        // config/Migrations/20220627000000_AlterBcTest.php
        $file = new File($migrationPath . '20220627000000_AlterBcTest.php', 'w');
        $file->write('<?php
use Migrations\AbstractMigration;
class AlterBcTest extends AbstractMigration
{
    public function change()
    {
        $table = $this->table(\'bc_test\');
        $table->changeColumn(\'name\', \'datetime\');
        $table->update();
    }
}');

        // config/update/0.0.2/updater.php
        $file = new File($updaterPath . 'updater.php', 'w');
        $file->write('<?php
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
$table = TableRegistry::getTableLocator()->get(\'BcTest.BcTest\');
$table->save(new Entity([\'name\' => \'2022-06-26\']));');

        // アップデート実行
        // インストールで利用した BcPluginを使い回すと、マイグレーションのキャッシュが残っていて、
        // 新しいマイグレーションファイルを認識しないので初期化しなおす
        $plugin = new BcPlugin(['name' => 'BcTest']);
        $plugin->update(['connection' => 'test']);
        $tableSchema = $collection->describe('bc_test');
        $this->assertEquals('datetime', $tableSchema->getColumnType('name'));
        $table = $this->getTableLocator()->get('BcTest.BcTest');
        $entity = $table->find()->first();
        $this->assertEquals('2022/06/26 00:00:00', (string) $entity->name);

        // 初期化
        $folder->delete($pluginPath);
    }


    /**
     * test update core
     */
    public function test_updateCore()
    {
        $folder = new Folder();

        // アップデートフォルダを初期化
        $configPath = BASER . 'config' . DS;
        $updaterPath = $configPath . 'update' . DS . '5.0.1000' . DS;
        $folder->delete($updaterPath);

        $migrationPath = $configPath . 'Migrations' . DS;
        $folder->create($updaterPath);
        $folder->create($migrationPath);

        // VERSION.txt
        rename(BASER . 'VERSION.txt', BASER . 'VERSION.bak.txt');
        $file = new File(BASER . 'VERSION.txt');
        $file->write('5.0.1000');

        // config/Migrations/30000000000000_AlterUsers.php
        $migrationFile = $migrationPath . '30000000000000_AlterUsers.php';
        $file = new File($migrationFile, 'w');
        $file->write('<?php
use Migrations\AbstractMigration;
class AlterUsers extends AbstractMigration
{
    public function change()
    {
        $table = $this->table(\'users\');
        $table->changeColumn(\'name\', \'datetime\');
        $table->update();
    }
}');

        // config/update/5.0.1000/updater.php
        $file = new File($updaterPath . 'updater.php', 'w');
        $file->write('<?php
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
$table = TableRegistry::getTableLocator()->get(\'BaserCore.Users\');
$table->updateAll([\'name\' => \'2022-06-26\'], []);');

        // アップデート実行
        // インストールで利用した BcPluginを使い回すと、マイグレーションのキャッシュが残っていて、
        // 新しいマイグレーションファイルを認識しないので初期化しなおす
        $plugin = new BcPlugin(['name' => 'BaserCore']);
        $plugin->update(['connection' => 'test']);
        $db = ConnectionManager::get('test');
        $collection = $db->getSchemaCollection();
        $tableSchema = $collection->describe('users');
        $this->assertEquals('datetime', $tableSchema->getColumnType('name'));
        $table = $this->getTableLocator()->get('BaserCore.Users');
        $entity = $table->find()->first();
        $this->assertEquals('2022/06/26 00:00:00', (string) $entity->name);

        // 初期化
        $folder->delete($updaterPath);
        rename(BASER . 'VERSION.bak.txt', BASER . 'VERSION.txt');
        unlink($migrationFile);
    }

    /**
     * テーマを適用する
     */
    public function test_applyAsTheme()
    {
        $targetId = 1;
        $currentTheme = 'BcFront';
        $SiteService = new SitesService();
        $site = $SiteService->get($targetId);
        $this->assertEquals($currentTheme, $site->theme);

        $updateTheme = 'BcSpaSample';
        $this->BcPlugin->applyAsTheme($site, $updateTheme);
        $site = $SiteService->get($targetId);
        $this->assertEquals($updateTheme, $site->theme);
    }

}
