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
use BaserCore\Service\PermissionsService;
use BaserCore\Service\PermissionGroupsServiceInterface;
use BaserCore\Service\PermissionsServiceInterface;
use BaserCore\Service\UserGroupsServiceInterface;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\PermissionGroupsScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Test\Factory\PermissionGroupFactory;
use BaserCore\Test\Factory\PermissionFactory;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Utility\Hash;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * PermissionGroupsServiceTest
 *
 * @property PermissionGroupsService $PermissionGroups
 * @property PermissionsService $Permissions
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
        $this->Permissions = $this->getService(PermissionsServiceInterface::class);
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
        $this->markTestIncomplete('このテストは未実装です。');
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

    /**
     * Test buildAll
     *
     * @return void
     */
    public function testBuildAll(): void
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $userGroupsService = $this->getService(UserGroupsServiceInterface::class);
        $userGroups = $userGroupsService->getIndex(['exclude_admin' => true])->all()->toArray();
        $count = 0;
        foreach ($userGroups as $userGroup) {
            $plugins = array_merge([0 => 'BaserCore'], Hash::extract(BcUtil::getEnablePlugins(true), '{n}.name'));
            foreach ($plugins as $plugin) {
                Configure::load($plugin . '.permission', 'baser');
                $settings = Configure::read('permission');
                $count = +count($settings);
                Configure::delete('permission');
            }
        }
        $settings = Configure::read('BcPrefixAuth');
        $count = +count($settings);
        $this->PermissionGroups->buildAll();
        $Pg = $this->PermissionGroups->getList();
        $this->assertCount($count, $Pg);
    }


    /**
     * Test deleteByUserGroup
     *
     * @return void
     */
    public function testDeleteByUserGroup(): void
    {
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        PermissionFactory::make(
            [
                'no' => 1,
                'sort' => 1,
                'permission_group_id' => 1,
                'name' => 'nghiem',
                'url' => 'abc',
                'user_group_id' => 99
            ]
        )->persist();
        PermissionFactory::make(
            [
                'no' => 2,
                'sort' => 2,
                'permission_group_id' => 1,
                'name' => 'nghiem 2',
                'url' => 'abc',
                'user_group_id' => 99
            ]
        )->persist();
        $data1 = $this->PermissionGroups->get(1, 99);
        $this->assertCount(2, $data1->permissions);
        $this->PermissionGroups->deleteByUserGroup(99);
        $data2 = $this->PermissionGroups->get(1, 99);
        $this->assertCount(0, $data2->permissions);
    }
}
