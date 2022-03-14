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

namespace BaserCore\Test\TestCase\Model\Table;

use BaserCore\Model\Table\PluginsTable;
use BaserCore\TestSuite\BcTestCase;
use Cake\Core\App;
use Cake\Filesystem\Folder;
use Cake\Validation\Validator;


/**
 * Class PluginsTableTest
 * @package BaserCore\Test\TestCase\Model\Table
 * @property PluginsTable $Plugins
 */
class PluginsTableTest extends BcTestCase
{

    /**
     * @var PluginsTable
     */
    public $Plugins;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Plugins',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Plugins = $this->getTableLocator()->get('BaserCore.Plugins');
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
     * Test initialize
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertIsBool($this->Plugins->hasBehavior('Timestamp'));
    }

    /**
     * testInstall and testUninstall
     */
    public function testInstallAndUninstall()
    {
        // test Install
        $this->Plugins->install('BcTest');
        $plugin = $this->Plugins->find()->where(['name' => 'BcTest'])->first();
        $this->assertEquals(4, $plugin->priority);
        // test Uninstall
        $this->Plugins->uninstall('BcTest');
        $this->assertEquals(3, $this->Plugins->find()->count());
    }

    /**
     * testGetPluginConfig
     */
    public function testGetPluginConfig()
    {
        $plugin = $this->Plugins->getPluginConfig('BaserCore');
        $this->assertEquals('BaserCore', $plugin->name);
    }

    /**
     * testDetach
     */
    public function testDetach()
    {
        $plugin = 'BcBlog';
        $this->assertFalse($this->Plugins->detach(''));
        $this->Plugins->detach($plugin);
        $this->assertFalse($this->Plugins->find()->where(['name' => $plugin])->first()->status);
    }

    /**
     * testChangePriority
     */
    public function testChangePriority()
    {
        $this->Plugins->changePriority(1, 2);
        $this->assertEquals(3, $this->Plugins->get(1)->priority);
        $this->Plugins->changePriority(2, -1);
        $this->assertEquals(1, $this->Plugins->get(2)->priority);
    }

    /**
     * Test validationDefault
     *
     * @return void
     * @dataProvider validationDefaultDataProvider
     */
    public function testValidationDefault($isValid, $data)
    {
        $validator = $this->Plugins->validationDefault(new Validator());
        $validator->setProvider('table', $this->Plugins);
        if ($isValid) {
            $this->assertEmpty($validator->validate($data));
        } else {
            $this->assertNotEmpty($validator->validate($data));
        }
    }

    public function validationDefaultDataProvider()
    {
        $exceedMax = "123456789012345678901234567890123456789012345678901234567890"; // 60文字
        return [
            // 妥当な例
            [true, ['name' => 'aA-_1', 'title' => 'testtest']],
            // nameがnull
            [false, ['name' => '', 'title' => 'testtest']],
            // nameに許可されない文字がある
            [false, ['name' => '@@@@@', 'title' => 'testtest']],
            // nameの文字数が長い
            [false, ['name' => $exceedMax, 'title' => 'testtest']],
            // titleの文字数が長い
            [false, ['name' => 'aA-_1', 'title' => $exceedMax]],
        ];
    }



    /**
     * Test buildRules
     */
    public function testBuildRules()
    {

        $plugin = $this->Plugins->newEntity([
            'name' => 'BcBlog',
        ]);
        $this->Plugins->save($plugin);

        $this->assertSame([
            'name' => ['_isUnique' => '指定のプラグインは既に使用されています。'],
        ], $plugin->getErrors());
    }


}
