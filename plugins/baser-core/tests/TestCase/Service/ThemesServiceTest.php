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

namespace BaserCore\Test\TestCase\Service;

use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\Service\ThemesService;
use BaserCore\Service\ThemesServiceInterface;
use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Cake\Utility\Inflector;

/**
 * ThemesServiceTest
 * @property ThemesService $ThemesService
 */
class ThemesServiceTest extends \BaserCore\TestSuite\BcTestCase
{

    /**
     * Trait
     */
    use BcContainerTrait;
    use IntegrationTestTrait;
    use ScenarioAwareTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/SiteConfigs',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BaserCore.Factory/UserGroups',
        'plugin.BaserCore.Factory/Contents',
        'plugin.BaserCore.Factory/ContentFolders',
        'plugin.BaserCore.Factory/Permissions',
        'plugin.BaserCore.Factory/Pages',
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
        $this->ThemesService = $this->getService(ThemesServiceInterface::class);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * 初期データのセットを取得する
     */
    public function testGetDefaultDataPatterns()
    {
        $options = ['useTitle' => false];
        $result = $this->ThemesService->getDefaultDataPatterns('BcFront', $options);
        $expected = [
            'BcFront.default' => 'default',
            'BcFront.empty' => 'empty'
        ];
        $this->assertEquals($expected, $result, '初期データのセットのタイトルを外して取得できません');
        $result = $this->ThemesService->getDefaultDataPatterns('BcFront');
        $expected = [
            'BcFront.default' => 'フロントテーマ ( default )',
            'BcFront.empty' => 'フロントテーマ ( empty )'
        ];
        $this->assertEquals($expected, $result);
    }

    /**
     * test copy
     * @return void
     */
    public function testCopy()
    {
        $rs = $this->ThemesService->copy('BcFront');
        $this->assertTrue($rs);
        //コピーを確認
        $this->assertTrue(is_dir(BASER_THEMES . 'BcFrontCopy'), 'テーマのコピーが確認できませんでした。');

        $pluginPath = BcUtil::getPluginPath('BcFrontCopy');
        $file = new File($pluginPath . 'src' . DS . 'Plugin.php');
        $data = $file->read();
        //namespaceの書き換えを確認
        $this->assertTrue(str_contains($data, 'namespace BcFrontCopy;'), 'namespace の書き換えが確認できませんでした。');
        $file->close();

        $this->ThemesService->delete('BcFrontCopy');
    }
    /**
     * test delete
     * @return void
     */
    public function testDelete()
    {
        $this->ThemesService->copy('BcFront');
        $rs = $this->ThemesService->delete('BcFrontCopy');
        $this->assertTrue($rs);
        $this->assertTrue(!is_dir(BASER_THEMES . 'BcFrontCopy'));
    }
    /**
     * test getThemesDefaultDataInfo
     * @return void
     */
    public function testGetThemesDefaultDataInfo()
    {
        $theme = 'BcFront';
        $themePath = BcUtil::getPluginPath($theme);

        mkdir($themePath . 'Plugin', 0777, true);
        mkdir($themePath . 'Plugin/test', 0777, true);

        $file = new File($themePath . 'Plugin/test/test.txt');
        $file->write('test file plugin');
        $file->close();

        $file = new File($themePath . 'Plugin/test2.txt');
        $file->write('test file 2');
        $file->close();

        $info = [
            'このテーマは下記のプラグインを同梱しています。',
            '	・test'
        ];
        $expected = [
            'このテーマは下記のプラグインを同梱しています。',
            '	・test',
            '',
            'このテーマは初期データを保有しています。',
            'Webサイトにテーマに合ったデータを適用するには、初期データ読込を実行してください。'
        ];

        $rs = $this->execPrivateMethod($this->ThemesService, 'getThemesDefaultDataInfo', [$theme, $info]);
        $this->assertEquals($expected, $rs);

        $folder = new Folder();
        $folder->delete($themePath . 'Plugin');
    }

    /**
     * test getMarketThemes
     * @return void
     */
    public function testGetMarketThemes()
    {
        $themes = $this->ThemesService->getMarketThemes();
        $this->assertEquals(true, count($themes) > 0);
        $this->assertEquals('multiverse', $themes[0]['title']);
        $this->assertEquals('1.0.0', $themes[0]['version']);
        $this->assertEquals('テーマ', $themes[0]['category']);
    }

    /**
     * 指定したテーマをダウンロード用のテーマとして一時フォルダに作成する
     * @return void
     */
    public function testCreateDownloadToTmp()
    {
        $tmpDir = TMP . 'theme' . DS;
        $theme = 'BcFront';
        $tmpThemeDir = $tmpDir . $theme;

        $result = $this->ThemesService->createDownloadToTmp($theme);
        $this->assertEquals($tmpDir, $result);
        $this->assertTrue(is_dir($tmpThemeDir));

        $folder = new Folder();
        $folder->delete($tmpThemeDir);
    }

    /**
     * 初期データチェックする
     * @return void
     */
    public function testCheckDefaultDataPattern()
    {
        $theme = Configure::read('BcApp.defaultFrontTheme');
        $configDataPath = BASER_THEMES . Inflector::dasherize($theme) . DS . 'config' . DS . 'data';
        $Folder = new Folder($configDataPath . DS . 'default' . DS . 'BaserCore');
        $files = $Folder->read(true, true);
        $coreTables = $files[1];

        // 一つ目のダミーフォルダを作る
        $pattern = 'dummy1';
        $dummyFolder = new Folder($configDataPath . DS . $pattern, true);
        // BaserCoreフォルダを作る
        new Folder($configDataPath . DS . $pattern . DS . 'BaserCore', true);
        // テーブルファイルを作る
        foreach ($coreTables as $table) {
            new File($configDataPath . DS . $pattern . DS . 'BaserCore' . DS . $table, true);
        }
        $result = $this->ThemesService->checkDefaultDataPattern($theme, $pattern);
        $dummyFolder->delete();
        // 成功を確認
        $this->assertTrue($result);

        // 二つ目のダミーフォルダを作る
        $pattern = 'dummy2';
        $dummyFolder = new Folder($configDataPath . DS . $pattern, true);
        $result = $this->ThemesService->checkDefaultDataPattern($theme, $pattern);
        $dummyFolder->delete();
        // 失敗を確認
        $this->assertFalse($result);
    }

    /**
     * 現在のDB内のデータをダウンロード用のCSVとして一時フォルダに作成する
     * @return void
     */
    public function testCreateDownloadDefaultDataPatternToTmp()
    {
        $this->ThemesService->createDownloadDefaultDataPatternToTmp();
        $tmpDir = TMP . 'csv' . DS;
        // CSVファイルが作成されている事を確認
        $baserCoreFolder = new Folder($tmpDir . 'BaserCore' . DS);
        $csvFiles = $baserCoreFolder->find('.*\.csv');
        $this->assertNotEmpty($csvFiles);
        // 作成されたディレクトリを削除
        $folder = new Folder();
        $folder->delete($tmpDir);
    }

    /**
     * 一覧データ取得
     */
    public function testGetIndex()
    {
        $themes = $this->ThemesService->getIndex();
        $this->assertEquals('BcFront', $themes[array_key_last($themes)]->name);
    }

    /**
     * 指定したテーマが梱包するプラグイン情報を取得
     */
    public function testGetThemesPluginsInfo()
    {
        $theme = 'BcFront';
        $themePath = BcUtil::getPluginPath($theme);
        $pluginName = 'test';
        mkdir($themePath . 'Plugin', 777, true);
        mkdir($themePath . 'Plugin/' . $pluginName, 777, true);

        $pluginsInfo = $this->execPrivateMethod($this->ThemesService, 'getThemesPluginsInfo', [$theme]);
        $this->assertEquals('このテーマは下記のプラグインを同梱しています。', $pluginsInfo[0]);
        $this->assertEquals('	・' . $pluginName, $pluginsInfo[1]);

        $folder = new Folder();
        $folder->delete($themePath . 'Plugin');
    }

    /**
     * site_configs テーブルにて、 CSVに出力しないフィールドを空にする
     */
    public function test_modifySiteConfigsCsv()
    {
        SiteConfigFactory::make(['name' => 'email', 'value' => 'chuongle@mediabridge.asia'])->persist();
        SiteConfigFactory::make(['name' => 'google_analytics_id', 'value' => 'gg123'])->persist();
        SiteConfigFactory::make(['name' => 'version', 'value' => '1.1.1'])->persist();

        $this->ThemesService->createDownloadDefaultDataPatternToTmp();
        $path = TMP . 'csv' . DS . 'BaserCore' . DS . 'site_configs.csv';
        $this->execPrivateMethod($this->ThemesService, '_modifySiteConfigsCsv', [$path]);

        $targets = ['email', 'google_analytics_id', 'version'];
        $fp = fopen($path, 'a+');
        while(($record = BcUtil::fgetcsvReg($fp, 10240)) !== false) {
            if (in_array($record[1], $targets)) {
                $this->assertEmpty($record[2]);
            }
        }
    }

    /**
     * CSVファイルを書きだす
     * @return void
     */
    public function test_writeCsv()
    {
        $plugin = 'BaserCore';
        $dbService = $this->getService(BcDatabaseServiceInterface::class);
        $tableList = $dbService->getAppTableList($plugin);
        $path = TMP . 'testWriteCsv' . DS;
        $csvFolder = new Folder($path, true, 0777);
        BcUtil::emptyFolder($path);
        $this->execPrivateMethod($this->ThemesService, '_writeCsv', [$plugin, $path]);
        $files = $csvFolder->find();
        foreach ($tableList as $table) {
            $this->assertTrue(in_array($table . '.csv', $files));
        }
    }

    /**
     * テーマを適用する
     */
    public function testApply()
    {
        $beforeTheme = 'BcSpaSample';
        $afterTheme = 'BcFront';
        SiteFactory::make(['id' => 1, 'title' => 'Test Title', 'name' => 'Test Site', 'theme'=> $beforeTheme, 'status' => 1])->persist();
        $site = SiteFactory::get(1);
        Router::setRequest($this->getRequest());
        $result = $this->ThemesService->apply($site, $afterTheme);
        $site = SiteFactory::get(1);
        $this->assertNotEquals($beforeTheme, $site->theme);
        $this->assertCount(2, $result);
        $this->assertEquals('このテーマは初期データを保有しています。', $result[0]);
        $this->assertEquals('Webサイトにテーマに合ったデータを適用するには、初期データ読込を実行してください。', $result[1]);
    }

    public function testLoadDefaultDataPattern()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        Router::setRequest($this->loginAdmin($this->getRequest()));
        $theme = 'BcFront';
        $result = $this->ThemesService->loadDefaultDataPattern($theme, $theme . '.default');

        // 戻り値を確認
        $this->assertTrue($result);
    }

}
