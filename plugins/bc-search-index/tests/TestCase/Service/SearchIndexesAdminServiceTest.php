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

namespace BcSearchIndex\Test\TestCase\Service;

use BaserCore\Model\Table\ContentsTable;
use BaserCore\Service\SitesAdminService;
use BaserCore\Service\SitesService;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcSearchIndex\Service\SearchIndexesAdminService;
use Cake\ORM\ResultSet;

/**
 * Class SearchIndexesAdminServiceTest
 * @property SearchIndexesAdminService $SearchIndexesAdminService
 * @property ContentsTable $Contents
 */
class SearchIndexesAdminServiceTest extends BcTestCase
{
    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.ContentFolders',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->SearchIndexesAdminService = new SearchIndexesAdminService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->SearchIndexesAdminService);
        parent::tearDown();
    }

    /**
     * Test getViewVarsForIndex
     *
     * @return void
     */
    public function testGetViewVarsForIndex()
    {
        $sitesService = new SitesService();
        $sites = $sitesService->getIndex([])->all();
        $rs = $this->SearchIndexesAdminService->getViewVarsForIndex($sites, 1);

        $this->assertTrue(isset($rs['searchIndexes']));
        $this->assertTrue(isset($rs['folders']));
        $this->assertTrue(isset($rs['sites']));

        $expected = $sitesService->getList();
        $this->assertEquals(count($expected), count($rs['sites']));
    }

}
