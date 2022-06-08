<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Model\Table;

use BaserCore\Model\Table\SearchIndexesTable;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class SearchIndexesTableTest
 * @package BaserCore\Test\TestCase\Model\Table
 * @property SearchIndexesTable $SearchIndexes
 */
class SearchIndexesTableTest extends BcTestCase
{

    /**
     * @var SearchIndexesTable
     */
    public $SearchIndexes;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.SearchIndexes',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->SearchIndexes = $this->getTableLocator()->get('BaserCore.SearchIndexes');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->SearchIndexes);
        parent::tearDown();
    }

    /**
     * Test initialize
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertTrue($this->SearchIndexes->hasBehavior('Timestamp'));
    }

	/**
	 *
	 * @testAllowPublish
     * 
     * @return false or integer
	 */
	public function testAllowPublish()
	{
        $expected = '555';
        // 無効な期限
        $data1['status'] = $expected;
        $data1['publish_begin'] = date('Y-m-d', strtotime('1 day'));
        $data1['publish_end'] = date('Y-m-d', strtotime('-1 day'));
        $this->assertFalse($this->SearchIndexes->allowPublish($data1));

        // 有効な期限
        $data2['status'] = $expected;
        $data2['publish_begin'] = date('Y-m-d', strtotime('-1 day'));
        $data2['publish_end'] = date('Y-m-d', strtotime('1 day'));
        $this->assertEquals($this->SearchIndexes->allowPublish($data2), (int)$expected);
	}
}
