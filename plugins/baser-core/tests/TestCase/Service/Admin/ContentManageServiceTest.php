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

namespace BaserCore\Test\TestCase\Service\Admin;

use BaserCore\Service\Admin\ContentManageService;
use BaserCore\TestSuite\BcTestCase;

/**
 * ContentManageServiceTest
 * @property ContentManageService $ContentManage
 */
class ContentManageServiceTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Contents'
    ];


    /**
     * setUp
     *
     * @return void
     */
    public function setUp():void
    {
        parent::setUp();
        $this->ContentManage = new ContentManageService();
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->ContentManage);
    }

    public function testGetTableIndex()
    {
        $searchData = [
            'open' => '1',
            'folder_id' => '6',
            'name' => 'サービス',
            'type' => 'Page',
            'self_status' => '',
            'author_id' => '',
        ];
        $site_id = 0;
        $result = $this->ContentManage->getTableIndex($site_id, $this->ContentManage->getAdminTableConditions($searchData));
        $this->assertEquals(3, $result->count());
    }

    /**
     * testGetAdminTableConditions
     *
     * @return void
     */
    public function testGetAdminTableConditions()
    {
        $searchData = [
            'open' => '1',
            'folder_id' => '6',
            'name' => 'テスト',
            'type' => 'ContentFolder',
            'self_status' => '1',
            'author_id' => '',
        ];
        $result = $this->ContentManage->getAdminTableConditions($searchData);
        $this->assertEquals([
            'OR' => [
            'name LIKE' => '%テスト%',
            'title LIKE' => '%テスト%',
            ],
            'rght <' => (int) 15,
            'lft >' => (int) 8,
            'self_status' => '1',
            'type' => 'ContentFolder',
            ], $result);
    }
    /**
     * testGetContentsInfo
     *
     * @return void
     */
    public function testGetContentsInfo()
    {
        $result = $this->ContentManage->getContensInfo();
        $this->assertTrue(isset($result[0]['unpublished']));
        $this->assertTrue(isset($result[0]['published']));
        $this->assertTrue(isset($result[0]['total']));
        $this->assertTrue(isset($result[0]['display_name']));
    }

}
