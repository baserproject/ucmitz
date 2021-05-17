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

use BaserCore\Service\UserGroupsService;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class UserGroupsServiceTest
 * @package BaserCore\Test\TestCase\Service
 * @property UserGroupsService $UserGroups
 */
class UserGroupsServiceTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.LoginStores'
    ];

    /**
     * @var UserGroupsService|null
     */
    public $UserGroups = null;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->UserGroups = new UserGroupsService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->UserGroups);
        parent::tearDown();
    }

    /**
     * Test get
     */
    public function testGet()
    {
        $userGroup = $this->UserGroups->get(1);
        $this->assertEquals('admins', $userGroup->name);
    }

    /**
     * Test create
     */
    public function testCreate()
    {
        $request = $this->getRequest('/');
        $data = [
            'name' => 'ucmitzGroup',
            'title' => 'ucmitzグループ',
            'use_move_contents' => '1',
        ];
        $request = $request->withParsedBody($data);
        $this->UserGroups->create($request);
        $group = $this->UserGroups->UserGroups->find('all')->all();
        $this->assertEquals($group->last()->name, $data['name']);
    }

    /**
     * Test update
     */
    public function testUpdate()
    {
        $request = $this->getRequest('/');
        $data = ['name' => 'ucmitzGroup'];
        $request = $request->withParsedBody($data);
        $userGroup = $this->UserGroups->get(1);
        $this->UserGroups->update($userGroup, $request);
        $group = $this->UserGroups->UserGroups->find('all');
        $this->assertEquals($group->first()->name, $data['name']);
    }

    /**
     * Test delete
     */
    public function testDelete()
    {
        $this->UserGroups->delete(2);
        $group = $this->UserGroups->UserGroups->find('all');
        $this->assertEquals(1, $group->count());
    }
}
