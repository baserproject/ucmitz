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

use BaserCore\Service\Admin\PluginManageService;
use BaserCore\TestSuite\BcTestCase;
use Cake\Filesystem\Folder;
use Cake\Core\App;

/**
 * Class PluginManageServiceTest
 * @package BaserCore\Test\TestCase\Service
 * @property PluginManageService $PluginManage
 */
class PluginManageServiceTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Plugins',
        'plugin.BaserCore.Permissions',
        'plugin.BaserCore.UserGroups'
    ];

    /**
     * @var PluginManageService|null
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
        $this->PluginManage = new PluginManageService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->PluginManage);
        parent::tearDown();
    }

    /**
     * Test getIndex
     * @dataProvider indexDataprovider
     */
    public function testGetIndex($sortMode, $expected)
    {
        $plugins = $this->PluginManage->getIndex($sortMode);
        $this->assertEquals(count($plugins), $expected);
    }
    public function indexDataprovider()
    {
        return [
            // 普通の場合
            ["0", "4"],
            // ソートモードの場合
            ["1", "2"],
        ];
    }

    /**
     * test install
     */
    public function testInstall()
    {
        $data = [
            'connection' => 'test',
            'name' => 'BcUploader',
            'title' => 'アップローダー',
            'status' => "0",
            'version' => "1.0.0",
            'permission' => "1"
        ];
        // 正常な場合
        $this->assertTrue($this->PluginManage->install('BcUploader', $data));
        // プラグインがない場合
        try {
            $data = [
                'connection' => 'test',
                'name' => 'UnKnown',
                'title' => '未知',
                'status' => "0",
                'version' => "1.0.0",
                'permission' => "1"
            ];
            $this->PluginManage->install('UnKnown', $data);
        } catch (\Exception $e) {
            $this->assertEquals("Plugin UnKnown could not be found.", $e->getMessage());
        }
        // フォルダはあるがインストールできない場合
        $data = [
            'connection' => 'test',
            'name' => 'BcTest',
            'title' => 'テスト',
            'status' => "0",
            'version' => "1.0.0",
            'permission' => "1"
        ];
        $pluginPath = App::path('plugins')[0] . DS . 'BcTest';
        $folder = new Folder($pluginPath);
        $folder->create($pluginPath, 0777);
        try {
            $this->assertNull($this->PluginManage->install('BcTest', $data));
        } catch (\Exception $e) {
            $this->assertEquals("プラグインに Plugin クラスが存在しません。src ディレクトリ配下に作成してください。", $e->getMessage());
        }
        $folder->delete($pluginPath);
    }

    /**
     * test getInstallStatusMessage
     */
    public function testGetInstallStatusMessage()
    {
        $this->assertEquals('既にインストール済のプラグインです。', $this->PluginManage->getInstallStatusMessage('BcBlog'));
        $this->assertEquals('インストールしようとしているプラグインのフォルダが存在しません。', $this->PluginManage->getInstallStatusMessage('BcTest'));
        $pluginPath = App::path('plugins')[0] . DS . 'BcTest';
        $folder = new Folder($pluginPath);
        $folder->create($pluginPath, 0777);
        $this->assertEquals('', $this->PluginManage->getInstallStatusMessage('BcTest'));
        $folder->delete($pluginPath);
    }

    /**
     * test detach
     */
    public function testDetach()
    {
        $plugins = $this->getTableLocator()->get('BaserCore.Plugins');
        $plugins->save($plugins->newEntity([
            'name' => 'あいうえお',
            'status' => true
        ]));
        $this->assertEquals(true, $this->PluginManage->detach(urlencode('あいうえお')));
    }

}
