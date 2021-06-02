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

namespace BaserCore\Test\TestCase\Service;

use BaserCore\Service\PluginsService;
use BaserCore\TestSuite\BcTestCase;
use Cake\Filesystem\Folder;
use Cake\Core\App;

/**
 * Class PluginsServiceTest
 * @package BaserCore\Test\TestCase\Service
 * @property PluginsService $Plugins
 */
class PluginsServiceTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Plugins',
    ];

    /**
     * @var PluginsService|null
     */
    public $Plugins = null;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Plugins = new PluginsService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Plugins);
        parent::tearDown();
    }

    /**
     * Test getIndex
     * @dataProvider indexDataprovider
     */
    public function testGetIndex($sortMode, $expected)
    {
        $plugins = $this->Plugins->getIndex($sortMode);
        $this->assertEquals(count($plugins), $expected);
    }
    public function indexDataprovider()
    {
        return [
            // 普通の場合
            ["0", "4"],
            // ソートモードの場合
            ["1", "3"],
        ];
    }

    /**
     * testGetAvailable
     * @dataProvider getAvailableDataProvider
     */
    public function testGetAvailable($isRegistered, $expected)
    {
        // テスト用のプラグインフォルダ作成
        $pluginPath = App::path('plugins')[0] . DS . 'BcTest';
        $folder = new Folder($pluginPath);
        $folder->create($pluginPath, 0777);
        $plugins = $this->Plugins->getAvailable($isRegistered);
        $pluginNames = [];
        foreach($plugins as $plugin) {
            $pluginNames[] = $plugin->name;
        }
        $folder->delete($pluginPath);

        if ($isRegistered) {
            $this->assertNotContains('BcTest', $pluginNames);
        }
        $this->assertContains($expected, $pluginNames);
    }
    public function getAvailableDataProvider()
    {
        return [
            // DBに登録されてる場合
            ['1', 'BcBlog'],
            // DBに登録されておらず、フォルダから取得してる場合
            ['0', 'BcTest'],
        ];
    }

    /**
     * testGetPluginConfig
     */
    public function testGetPluginConfig()
    {
        $plugin = $this->Plugins->getPluginConfig('BaserCore');
        $this->assertEquals('BaserCore', $plugin->name);
    }




}
