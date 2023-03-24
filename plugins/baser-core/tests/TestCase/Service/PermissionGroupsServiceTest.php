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

use BaserCore\Service\PermissionGroupsService;
use BaserCore\Service\PermissionGroupsServiceInterface;
use BaserCore\Test\Scenario\PermissionGroupsScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Test\Factory\PermissionGroupFactory;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * PermissionGroupsServiceTest
 *
 * @property PermissionGroupsService $PermissionGroups
 */
class PermissionGroupsServiceTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Factory/Permissions',
        'plugin.BaserCore.Factory/PermissionGroups',
        'plugin.BaserCore.Factory/UserGroups',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/UsersUserGroups',
    ];

    /**
     * Set Up
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
        $this->PermissionGroups = $this->getService(PermissionGroupsServiceInterface::class);
    }

    /**
     * Tear Down
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->PermissionGroups);
    }

    /**
     * test build
     */
    public function testBuild()
    {
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        $plugin = 'BaserCore';
        $result = $this->PermissionGroups->build(0, $plugin);
        $this->assertCount(0, $result);

        $this->PermissionGroups->build(1, $plugin);
        $data = $this->PermissionGroups->getIndex(1, [])->where(['plugin' => $plugin])->all()->toArray();
        Configure::load($plugin . '.permission', 'baser');
        $settings = Configure::read('permission');
        Configure::delete('permission');
        $this->assertCount(count($settings), $data);

        $plugin = 'BcBlog';
        $this->PermissionGroups->build(1, $plugin);
        $data = $this->PermissionGroups->getIndex(1, [])->where(['plugin' => $plugin])->all()->toArray();
        Configure::load($plugin . '.permission', 'baser');
        $settings = Configure::read('permission');
        Configure::delete('permission');
        $this->assertCount(count($settings), $data);

        $result = $this->PermissionGroups->build(1, 'Nghiem');
        $this->assertFalse($result);
    }

    /**
     * Test getList
     *
     * @return void
     */
    public function testGetList(): void
    {
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        $result = $this->PermissionGroups->getList();
        $this->assertCount(3, $result);
        PermissionGroupFactory::make([
            'name' => 'group 1',
            'type' => 'Supper',
            'plugin' => 'BaserCore',
            'status' => 1
        ])->persist();
        PermissionGroupFactory::make([
            'name' => 'group 2',
            'type' => 'Supper',
            'plugin' => 'BaserCore',
            'status' => 1
        ])->persist();
        $result = $this->PermissionGroups->getList();
        $this->assertCount(5, $result);
        $this->assertContains('group 1', $result);
        $option = ['type' => 'Supper'];
        $result = $this->PermissionGroups->getList($option);
        $this->assertCount(2, $result);
        $this->assertContains('group 2', $result);
    }

    /**
     * Test get
     *
     * @return void
     */
    public function testGet(): void
    {
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        $data1 = $this->PermissionGroups->get(1);
        $this->assertNotEmpty($data1);
        $data2 = $this->PermissionGroups->get(1, 1);
        $this->assertNotEmpty($data2);
        $this->expectException(RecordNotFoundException::class);
        $this->PermissionGroups->get(-1);
    }
}
