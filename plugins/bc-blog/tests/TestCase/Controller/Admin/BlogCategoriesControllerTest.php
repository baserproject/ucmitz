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

use BaserCore\TestSuite\BcTestCase;
use BcBlog\Controller\Admin\BlogCategoriesController;
use BcBlog\Test\Factory\BlogCategoryFactory;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * Class BlogCategoriesControllerTest
 *
 * @package Blog.Test.Case.Controller
 * @property  BlogCategoriesController $Controller
 */
class BlogCategoriesControllerTest extends BcTestCase
{

    /**
     * IntegrationTestTrait
     */
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.Plugins',
        'plugin.BaserCore.Permissions',
        'plugin.BaserCore.Sites',
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
        $this->Controller = new BlogCategoriesController($this->loginAdmin($this->getRequest()));
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
     * beforeFilter
     */
    public function testBeforeFilter()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] ブログを一覧表示する
     */
    public function testAdmin_index()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] 登録処理
     */
    public function testAdmin_add()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $blogContentId = 90;
        $data = ['name' => 'testName', 'title' => 'testTitle'];
        $this->post("/baser/admin/bc-blog/blog_categories/add/$blogContentId", $data);
        // ステータスを確認
        $this->assertResponseCode(302);
        // データの登録を確認
        $blogCategory = BlogCategoryFactory::get(1);
        $this->assertEquals($data['name'], $blogCategory['name']);
        // 失敗のメッセージを確認
        $data['name'] = 'test name';
        $this->post("/baser/admin/bc-blog/blog_categories/add/$blogContentId", $data);
        // TODO can not assert the error message
        $this->assertFlashMessage('入力エラーです。内容を修正してください。');
    }

    /**
     * [ADMIN] 編集処理
     */
    public function testAdmin_edit()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] 削除処理
     */
    public function testAdmin_delete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
