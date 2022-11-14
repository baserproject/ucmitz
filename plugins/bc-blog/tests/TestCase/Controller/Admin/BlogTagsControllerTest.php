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

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcBlog\Controller\Admin\BlogTagsController;
use BcBlog\Test\Factory\BlogTagFactory;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BlogTagsControllerTest
 *
 * @property BlogTagsController $BlogTagsController
 */
class BlogTagsControllerTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;

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
        'plugin.BcBlog.Factory/BlogTags',
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
        $this->BlogTagsController = new BlogTagsController($this->loginAdmin($this->getRequest()));
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
     * test index
     */
    public function test_initialize()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test add
     */
    public function test_add(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test edit
     */
    public function test_edit()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        BlogTagFactory::make(['id' => 1, 'name' => 'test'])->persist();

        $this->get('/baser/admin/bc-blog/blog_tags/edit/1', ['name' => null]);
        $this->assertResponseCode(200);
        $vars = $this->_controller->viewBuilder()->getVars();
        $this->assertEquals('test', $vars['blogTag']['name']);

        $this->post('/baser/admin/bc-blog/blog_tags/edit/1', ['name' => 'updated']);
        $this->assertResponseCode(302);
        $this->assertRedirect(['action' => 'index']);
        $this->assertFlashMessage('タグ「updated」を更新しました。');

        $this->post('/baser/admin/bc-blog/blog_tags/edit/1', ['name' => null]);
        $vars = $this->_controller->viewBuilder()->getVars();
        $this->assertEquals(['name' => ['_empty' => "ブログタグを入力してください。"]], $vars['blogTag']->getErrors());
        $this->assertResponseCode(200);

        $this->post('/baser/admin/bc-blog/blog_tags/edit/11', ['name' => null]);
        $this->assertResponseCode(404);
    }

    /**
     * test index
     */
    public function test_index()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
