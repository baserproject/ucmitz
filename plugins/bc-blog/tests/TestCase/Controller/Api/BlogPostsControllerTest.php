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

namespace BcBlog\Test\TestCase\Controller\Api;

use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcBlog\Controller\Api\BlogPostsController;
use BcBlog\Test\Factory\BlogPostFactory;
use Cake\Core\Configure;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BlogPostsControllerTest
 * @property BlogPostsController $BlogPostsController
 */
class BlogPostsControllerTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
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
     * Access Token
     * @var string
     */
    public $accessToken = null;

    /**
     * Refresh Token
     * @var null
     */
    public $refreshToken = null;

    /**
     * set up
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $token = $this->apiLoginAdmin(1);
        $this->accessToken = $token['access_token'];
        $this->refreshToken = $token['refresh_token'];
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        Configure::clear();
        parent::tearDown();
    }

    /**
     * test initialize
     */
    public function test_initialize()
    {
        $controller = new BlogPostsController($this->getRequest());
        $this->assertEquals($controller->Authentication->unauthenticatedActions, ['view']);
    }

    /**
     * test index
     */
    public function test_index()
    {
        //データを生成
        BlogPostFactory::make(['blog_content_id' => 1])->persist();
        BlogPostFactory::make(['blog_content_id' => 2])->persist();

        //APIを呼ぶ
        $this->get('/baser/api/bc-blog/blog_posts/index/1.json?token=' . $this->accessToken);
        //responseを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertCount(2, $result->blogPosts);
    }

    /**
     * test view
     */
    public function test_view()
    {
        // データを生成
        BlogPostFactory::make(['id' => 1, 'blog_content_id' => 1, 'status' => true])->persist();
        // APIを呼ぶ
        $this->get('/baser/api/bc-blog/blog_posts/view/1.json');
        // レスポンスを確認
        $this->assertResponseOk();
        // 戻り値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(1, $result->blogPost->id);
        $this->assertEquals(1, $result->blogPost->blog_content_id);

        //存在しないBlogPostIDをテスト場合、
        // APIを呼ぶ
        $this->get('/baser/api/bc-blog/blog_posts/view/100.json?token=' . $this->accessToken);
        // レスポンスを確認
        $this->assertResponseCode(500);
        // 戻り値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データベース処理中にエラーが発生しました。Record not found in table "blog_posts"', $result->message);

        //ログインしていない状態では status パラメーターへへのアクセスを禁止するか確認
        $this->get('/baser/api/bc-blog/blog_posts/view/1.json?status=publish');
        // レスポンスを確認
        $this->assertResponseCode(403);

        //ログインしている状態では status パラメーターへへのアクセできるか確認
        $this->get('/baser/api/bc-blog/blog_posts/view/1.json?status=publish&token=' . $this->accessToken);
        // レスポンスを確認
        $this->assertResponseOk();
    }

    /**
     * test add
     */
    public function test_add()
    {
        // postデータを生成
        $postData = [
            'blog_content_id' => 1,
            'title' => 'baserCMS inc. [デモ] の新しい記事',
            'content' => '記事の概要',
            'detail' => '記事の詳細',
        ];
        // APIを呼ぶ
        $this->post('/baser/api/bc-blog/blog_posts/add.json?token=' . $this->accessToken, $postData);
        // レスポンスの確認
        $this->assertResponseOk();
        // 戻り値を確認
        $result = json_decode((string)$this->_response->getBody());
        //メッセージを確認
        $this->assertEquals('記事「baserCMS inc. [デモ] の新しい記事」を追加しました。', $result->message);
        //作成したBlogPostを確認
        $this->assertEquals('baserCMS inc. [デモ] の新しい記事', $result->blogPost->title);
        $this->assertEquals('記事の概要', $result->blogPost->content);
        $this->assertEquals('記事の詳細', $result->blogPost->detail);

        // 入力エラー
        // titleが空のpostデータを生成
        $postData = [
            'blog_content_id' => 1,
            'title' => '',
            'content' => '',
            'detail' => '',
        ];
        // APIを呼ぶ
        $this->post('/baser/api/bc-blog/blog_posts/add.json?token=' . $this->accessToken, $postData);
        // レスポンスの確認
        $this->assertResponseCode(400);
        // 戻り値を確認
        $result = json_decode((string)$this->_response->getBody());
        //メッセージを確認
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        //エラーメッセージを確認
        $this->assertEquals('タイトルを入力してください。', $result->errors->title->_empty);
    }

    /**
     * test edit
     */
    public function test_edit()
    {
        //データを生成
        BlogPostFactory::make(['id' => 1])->persist();

        //正常の時を確認
        //編集データーを生成
        $data = ['title' => 'blog post edit'];
        //APIをコル
        $this->post('/baser/api/bc-blog/blog_posts/edit/1.json?token=' . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('blog post edit', $result->blogPost->title);
        $this->assertEquals('記事「blog post edit」を更新しました。', $result->message);

        //dataは空にする場合を確認
        //APIをコル
        $this->post('/baser/api/bc-blog/blog_posts/edit/1.json?token=' . $this->accessToken, []);
        //ステータスを確認
        $this->assertResponseCode(400);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertEquals('タイトルを入力してください。', $result->errors->title->_required);
    }

    /**
     * test edit
     */
    public function test_copy()
    {
        //データを生成
        BlogPostFactory::make(['id' => 1, 'title' => 'test'])->persist();

        //正常の時を確認
        //APIをコル
        $this->post('/baser/api/bc-blog/blog_posts/copy/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('ブログ記事「test」をコピーしました。', $result->message);
        $this->assertEquals('test_copy', $result->blogPost->title);

        //存在しないBlogPostIDをコビー場合、
        $this->post('/baser/api/bc-blog/blog_posts/copy/100000.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(500);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データベース処理中にエラーが発生しました。Record not found in table "blog_posts"', $result->message);
    }

    /**
     * test publish
     */
    public function test_publish()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test unpublish
     */
    public function test_unpublish()
    {
        //データーを生成
        BlogPostFactory::make(['id' => '1'])->persist();

        //正常の時を確認
        //APIをコル
        $this->post('/baser/api/bc-blog/blog_posts/unpublish/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertMatchesRegularExpression('/ブログ記事「.+」を非公開状態にしました。/', $result->message);
        // 非公開状態を確認
        $blogPost = BlogPostFactory::get(1);
        $this->assertFalse($blogPost->status);
        $this->assertNull($blogPost->publish_begin);
        $this->assertNull($blogPost->publish_end);

        //存在しないBlogPostIDを削除場合、
        //APIをコル
        $this->post('/baser/api/bc-blog/blog_posts/unpublish/2.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(500);
        // 戻り値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データベース処理中にエラーが発生しました。Record not found in table "blog_posts"', $result->message);
    }

    /**
     * test batch
     */
    public function test_batch()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        //データを生成
        SiteConfigFactory::make(['name' => 'content_types', 'value' => ''])->persist();
        BlogPostFactory::make(['id' => 1])->persist();

        //正常の時を確認
        //APIをコル
        $this->post('/baser/api/bc-blog/blog_posts/delete/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertMatchesRegularExpression('/ブログ記事「.+」を削除しました。/', $result->message);
        $this->assertNotNull($result->blogPost->title);

        //存在しないBlogPostIDを削除場合、
        $this->post('/baser/api/bc-blog/blog_posts/delete/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(500);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データベース処理中にエラーが発生しました。Record not found in table "blog_posts"', $result->message);
    }
}
