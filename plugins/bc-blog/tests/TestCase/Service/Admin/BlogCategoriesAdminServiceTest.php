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

namespace BcBlog\Test\TestCase\Service\Admin;

use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcBlog\Service\Admin\BlogCategoriesAdminService;
use BcBlog\Service\BlogCategoriesService;
use BcBlog\Test\Factory\BlogCategoryFactory;
use BcBlog\Test\Factory\BlogContentsFactory;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * BlogCategoriesAdminServiceTest
 * @property BlogCategoriesAdminService $BlogCategoriesAdminService
 */
class BlogCategoriesAdminServiceTest extends BcTestCase
{

    /**
     * Trait
     */
    use BcContainerTrait;
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/Contents',
        'plugin.BaserCore.Factory/ContentFolders',
        'plugin.BaserCore.Factory/Pages',
        'plugin.BaserCore.Factory/SiteConfigs',
        'plugin.BaserCore.Factory/SearchIndexes',
        'plugin.BcBlog.Factory/BlogContents',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
        $this->BlogCategoriesAdminService = new BlogCategoriesAdminService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BlogCategoriesAdminService);
        parent::tearDown();
    }

    /**
     * test getViewVarsForIndex
     */
    public function test_getViewVarsForIndex()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getViewVarsForAdd
     */
    public function test_getViewVarsForAdd()
    {
        BlogContentsFactory::make(['id' => 51, 'description' => 'test add'])->persist();
        BlogCategoryFactory::make(['id' => 51, 'title' => 'title add', 'name' => 'name-add', 'rght' => 5, 'lft' => 6])->persist();

        $blogCategoriesService = new BlogCategoriesService();
        $blogCategory = $blogCategoriesService->get(51);

        $rs = $this->BlogCategoriesAdminService->getViewVarsForAdd(51, $blogCategory);

        $this->assertTrue(isset($rs['blogContent']));
        $this->assertTrue(isset($rs['blogCategory']));
        $this->assertTrue(isset($rs['parents']));
        $this->assertEquals($blogCategory, $rs['blogCategory']);
        $this->assertEquals('test add', $rs['blogContent']['description']);
    }

    /**
     * test getViewVarsForEdit
     */
    public function test_getViewVarsForEdit()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
