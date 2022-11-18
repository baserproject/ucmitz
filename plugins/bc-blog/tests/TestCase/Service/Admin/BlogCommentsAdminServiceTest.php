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

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcBlog\Service\Admin\BlogCommentsAdminService;
use BcBlog\Test\Factory\BlogCommentFactory;
use BcBlog\Test\Factory\BlogContentFactory;
use BcBlog\Test\Factory\BlogPostFactory;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * BlogCommentsAdminServiceTest
 * @property BlogCommentsAdminService $BlogCommentsAdminService
 */
class BlogCommentsAdminServiceTest extends BcTestCase
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
        'plugin.BcBlog.Factory/BlogPosts',
        'plugin.BcBlog.Factory/BlogComments',
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
        $this->BlogCommentsAdminService = new BlogCommentsAdminService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BlogCommentsAdminService);
        parent::tearDown();
    }

    /**
     * test getViewVarsForIndex
     */
    public function test_getViewVarsForIndex()
    {
        //データ生成
        BlogContentFactory::make(['id' => 1, 'description' => 'test view'])->persist();
        BlogPostFactory::make(['id' => 1, 'title' => 'post title'])->persist();
        BlogCommentFactory::make([
            'id' => 1,
            'blog_content_id' => 1,
            'blog_post_id' => 1,
            'name' => 'name comment',
            'email' => 'test@test.com',
            'url' => '/test'
        ])->persist();
        BlogCommentFactory::make([
            'id' => 2,
            'blog_content_id' => 1,
            'blog_post_id' => 1,
            'name' => 'name comment 2',
            'email' => 'test2@test.com',
            'url' => '/test-2'
        ])->persist();

        //メソードをコル
        $rs = $this->BlogCommentsAdminService->getViewVarsForIndex(
            1,
            1,
            $this->BlogCommentsAdminService->getIndex([])->all()
        );

        //戻り値を確認
        $this->assertEquals($rs['blogContent']['description'], 'test view');
        $this->assertEquals($rs['blogPost']['title'], 'post title');

        //blogComment値を確認
        $blogComment = $rs['blogComments']->first();
        $this->assertEquals(count($rs['blogComments']), 2);
        $this->assertEquals($blogComment['name'], 'name comment');
        $this->assertEquals($blogComment['email'], 'test@test.com');
    }
}
