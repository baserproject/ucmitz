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
     */
    public function testGetAvailable()
    {
        $plugins = $this->Plugins->getAvailable();
        $pluginNames = [];
        foreach($plugins as $plugin) {
            $pluginNames[] = $plugin->name;
        }
        $this->assertContains('BcBlog', $pluginNames);

        $pluginPath = App::path('plugins')[0] . DS . 'BcTest';
        $folder = new Folder($pluginPath);
        $folder->create($pluginPath, 0777);

        $plugins = $this->Plugins->getAvailable();
        $pluginNames = [];
        foreach($plugins as $plugin) {
            $pluginNames[] = $plugin->name;
        }
        $this->assertContains('BcTest', $pluginNames);

        $folder->delete($pluginPath);
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
