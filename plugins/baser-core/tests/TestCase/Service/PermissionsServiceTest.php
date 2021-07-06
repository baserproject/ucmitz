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

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Service\PermissionsService;

/**
 * BaserCore\Model\Table\PermissionsTable Test Case
 *
 * @property PermissionsService $PermissionsService
 */
class PermissionsServiceTest extends BcTestCase
{

    /**
     * Test subject
     *
     * @var PermissionsService
     */
    public $Permissions;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Permissions',
        'plugin.BaserCore.UserGroups',
    ];

        /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Permissions = new PermissionsService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Permissions);
        parent::tearDown();
    }

    /**
     * Test getNew
     *
     * @return void
     */
    public function testGetNew()
    {
        $permission = $this->Permissions->getNew(1);
        $this->assertEquals(1, $permission->user_group_id);
        $this->assertFalse($permission->hasErrors());
    }
    /**
     * Test get
     *
     * @return void
     */
    public function testGet()
    {
        $permission = $this->Permissions->get(1);
        $this->assertEquals('システム管理', $permission->name);
        $this->assertEquals(2, $permission->user_group->id);
    }
    /**
     * Test getIndex
     *
     * @return void
     */
    public function testGetIndex()
    {
        $request = $this->getRequest('/');
        $permissions = $this->Permissions->getIndex($request->getQueryParams(), 1);
        $a = $permissions->first()->name;
        $b = $permissions->all()->count();
        // $this->assertEquals($permission);
    }
    /**
     * Test set
     *
     * @return void
     */
    public function testSet()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
    /**
     * Test create
     *
     * @return void
     */
    public function testCeate()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
    /**
     * Test update
     *
     * @return void
     */
    public function testUpdate()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
    /**
     * Test delete
     *
     * @return void
     */
    public function testDelete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Test delete
     *
     * @return void
     */
    public function testGetMethodList()
    {
        $this->assertEquals(
            $this->Permissions->getMethodList(),
            ['*' => 'ALL',
            'GET' => 'GET',
            'POST' => 'POST',]
        );
    }

}
