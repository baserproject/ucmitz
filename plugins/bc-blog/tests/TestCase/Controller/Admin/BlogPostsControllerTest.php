<?php

/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.Test.Case.Controller
 * @since           baserCMS v 4.0.9
 * @license         https://basercms.net/license/index.html
 */

namespace BcBlog\Test\TestCase\Controller\Admin;

use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcBlog\Controller\Admin\BlogPostsController;
use BcBlog\Service\BlogPostsServiceInterface;
use BcBlog\Test\Factory\BlogContentFactory;
use BcBlog\Test\Factory\BlogPostFactory;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BlogPostsControllerTest
 *
 * @property BlogPostsController $BlogPostsController
 */
class BlogPostsControllerTest extends BcTestCase
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
    public $fixtures = [
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/SiteConfigs',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BaserCore.Factory/UserGroups',
        'plugin.BcBlog.Factory/BlogPosts',
    ];

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->BlogPostsController = new BlogPostsController($this->loginAdmin($this->getRequest()));
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test initialize
     */
    public function testInitialize(): void
    {
        $this->assertNotEmpty($this->BlogPostsController->BcAdminContents);
    }

    /**
     * test beforeFilter
     */
    public function testBeforeFilter()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test index
     * [ADMIN] ブログ記事一覧表示
     */
    public function testIndex()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test add
     * [ADMIN] ブログ記事追加処理
     */
    public function testAdd()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test edit
     * [ADMIN] ブログ記事編集処理
     */
    public function testEdit()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test delete
     * [ADMIN] ブログ記事削除処理
     */
    public function testDelete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test unpublish
     * [ADMIN] ブログ記事を非公開状態にする
     */
    public function testUnpublish()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test publish
     * [ADMIN] ブログ記事を公開状態にする
     */
    public function testPublish()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test copy
     * [ADMIN] コピー
     */
    public function testCopy()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        SiteConfigFactory::make(['name' => 'content_types', 'value' => ''])->persist();
        ContentFactory::make(['plugin' => 'BcBlog', 'type' => 'BlogContent', 'entity_id' => 1])->persist();
        BlogContentFactory::make([
            'id' => 1,
            'use_content' => '1'
        ])->persist();
        BlogPostFactory::make(
            [
                'id' => '1',
                'blog_content_id' => '1',
                'no' => '1',
                'name' => 'ホームページをオープンしました',
                'title' => 'test',
                'content' => 'content test',
                'detail' => 'detail test',
                'blog_category_id' => '1',
                'user_id' => '1',
                'status' => '1',
                'posts_date' => '2015-01-27 12:57:59',
                'content_draft' => '',
                'detail_draft' => '',
                'publish_begin' => null,
                'publish_end' => null,
                'exclude_search' => 0,
                'eye_catch' => 'template1.jpg',
                'created' => '2015-01-27 12:56:53',
                'modified' => '2015-01-27 12:57:59'
            ]
        )->persist();
        BlogPostFactory::make(['id' => 2])->persist();
        $this->post('/baser/admin/bc-blog/blog_posts/copy/1/1');
        $this->assertResponseCode(302);
        $this->assertFlashMessage('ブログ記事「test」をコピーしました。');
        $BlogPostsService = $this->getService(BlogPostsServiceInterface::class);
        $copyBlogPost = $BlogPostsService->getIndex(['title' => 'test_copy'])->first();
        $this->assertEquals($copyBlogPost->content, 'content test');

        $this->post('/baser/admin/bc-blog/blog_posts/copy/1/2');
        $this->assertResponseCode(302);
        $this->assertFlashMessage('入力エラーです。内容を修正してください。');
    }

}
