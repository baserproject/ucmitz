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
use BaserCore\Service\ContentsService;

/**
 * BaserCore\Model\Table\ContentsTable Test Case
 *
 * @property ContentsService $ContentsService
 */
class ContentsServiceTest extends BcTestCase
{

    /**
     * Test subject
     *
     * @var ContentsService
     */
    public $Contents;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Contents',
    ];

        /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ContentsService = new ContentsService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->ContentsService);
        parent::tearDown();
    }

    public function testGetTreeIndex(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // $site_id = 0;
        // $result = $this->ContentsService->getTreeIndex($site_id);
        // $a = $result->first();
        // $this->assertEquals($result,"");
    }
}
