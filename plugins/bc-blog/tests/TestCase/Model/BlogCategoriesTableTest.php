<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.Test.Case.Model
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

namespace BcBlog\Test\TestCase\Model;

use BaserCore\TestSuite\BcTestCase;
use BcBlog\Model\Table\BlogCategoriesTable;
use BcBlog\Test\Factory\BlogCategoryFactory;

/**
 * Class BlogCategoryTest
 * @property BlogCategoriesTable $BlogCategoriesTable
 */
class BlogCategoriesTableTest extends BcTestCase
{

    public $fixtures = [
        'plugin.BcBlog.Factory/BlogCategories',
    ];

    /**
     * Setup
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
        $this->BlogCategoriesTable = new BlogCategoriesTable();
    }

    /**
     * Tear down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test initialize
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $this->BlogCategoriesTable->initialize([]);
        $this->assertEquals('blog_categories', $this->BlogCategoriesTable->getTable());
        $this->assertEquals('id', $this->BlogCategoriesTable->getPrimaryKey());
        $this->assertTrue($this->BlogCategoriesTable->hasBehavior('Timestamp'));
        $this->assertTrue($this->BlogCategoriesTable->hasBehavior('Tree'));
        $this->assertEquals('BlogPosts', $this->BlogCategoriesTable->getAssociation('BlogPosts')->getName());
    }

    /**
     * Test validationDefault
     *
     * @return void
     */
    public function testValidationDefault(): void
    {
        // id integer　テスト
        $blogCategory = $this->BlogCategoriesTable->newEntity(['id' => 'test']);
        $this->assertNotNull($blogCategory->getErrors()['id']);
        // id allowEmptyString　テスト
        $blogCategory = $this->BlogCategoriesTable->newEntity(['id' => '']);
        $this->assertNull($blogCategory->getErrors()['id']);
        // name maxLength　テスト
        $blogCategory = $this->BlogCategoriesTable->newEntity([
            'name' => '_test_blog_category_test_blog_category_test_blog_category_test_blog_category_test_blog_category'
                .'_test_blog_category_test_blog_category_test_blog_category_test_blog_category_test_blog_category'
                .'_test_blog_category_test_blog_category_test_blog_category_test_blog_category',
            'blog_content_id' => 1,
            'title' => 'test'
        ]);
        $this->assertSame([
            'name' => ['maxLength' => 'カテゴリ名は255文字以内で入力してください。'],
        ], $blogCategory->getErrors());
        // name requirePresence　テスト
        $blogCategory = $this->BlogCategoriesTable->newEntity([
            'blog_content_id' => 1,
            'title' => 'test'
        ]);
        $this->assertSame([
            'name' => ['_required' => 'カテゴリ名を入力してください。'],
        ], $blogCategory->getErrors());
        // name notEmptyString　テスト
        $blogCategory = $this->BlogCategoriesTable->newEntity([
            'name' => '',
            'blog_content_id' => 1,
            'title' => 'test'
        ]);
        $this->assertSame([
            'name' => ['_empty' => 'カテゴリ名を入力してください。'],
        ], $blogCategory->getErrors());
        // name alphaNumericDashUnderscore　テスト
        $blogCategory = $this->BlogCategoriesTable->newEntity([
            'name' => 'test 123',
            'blog_content_id' => 1,
            'title' => 'test'
        ]);
        $this->assertSame([
            'name' => ['alphaNumericDashUnderscore' => 'カテゴリ名はは半角英数字とハイフン、アンダースコアのみが利用可能です。'],
        ], $blogCategory->getErrors());
        // name duplicateBlogCategory　テスト
        BlogCategoryFactory::make([
            'name' => 'test',
            'blog_content_id' => 1,
            'title' => 'test'
        ])->persist();
        $blogCategory = $this->BlogCategoriesTable->newEntity([
            'name' => 'test',
            'blog_content_id' => 1,
            'title' => 'test'
        ]);
        $this->assertSame([
            'name' => ['duplicateBlogCategory' => '入力されたカテゴリ名は既に登録されています。'],
        ], $blogCategory->getErrors());
        // title maxLength　テスト
        $blogCategory = $this->BlogCategoriesTable->newEntity([
            'title' => '_test_blog_category_test_blog_category_test_blog_category_test_blog_category_test_blog_category'
                .'_test_blog_category_test_blog_category_test_blog_category_test_blog_category_test_blog_category'
                .'_test_blog_category_test_blog_category_test_blog_category_test_blog_category',
            'blog_content_id' => 1,
            'name' => 'test2'
        ]);
        $this->assertSame([
            'title' => ['maxLength' => 'カテゴリタイトルは255文字以内で入力してください。'],
        ], $blogCategory->getErrors());
        // title requirePresence　テスト
        $blogCategory = $this->BlogCategoriesTable->newEntity([
            'blog_content_id' => 1,
            'name' => 'test2'
        ]);
        $this->assertSame([
            'title' => ['_required' => 'カテゴリタイトルを入力してください。'],
        ], $blogCategory->getErrors());
        // title notEmptyString　テスト
        $blogCategory = $this->BlogCategoriesTable->newEntity([
            'name' => 'test2',
            'blog_content_id' => 1,
            'title' => ''
        ]);
        $this->assertSame([
            'title' => ['_empty' => 'カテゴリタイトルを入力してください。'],
        ], $blogCategory->getErrors());
    }

    /*
	 * validate
	 */
    public function test必須チェック()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        // blog_content_idを設定
        $this->BlogCategory->validationParams = [
            'blogContentId' => 1
        ];

        $this->BlogCategory->create([
            'BlogCategory' => [
                'blog_content_id' => 1
            ]
        ]);

        $this->assertFalse($this->BlogCategory->validates());

        $this->assertArrayHasKey('name', $this->BlogCategory->validationErrors);
        $this->assertEquals('カテゴリ名を入力してください。', current($this->BlogCategory->validationErrors['name']));

        $this->assertArrayHasKey('title', $this->BlogCategory->validationErrors);
        $this->assertEquals('カテゴリタイトルを入力してください。', current($this->BlogCategory->validationErrors['title']));
    }

    public function test桁数チェック異常系()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        // blog_content_idを設定
        $this->BlogCategory->validationParams = [
            'blogContentId' => 1
        ];

        $this->BlogCategory->create([
            'BlogCategory' => [
                'name' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
                'title' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
            ]
        ]);
        $this->assertFalse($this->BlogCategory->validates());

        $this->assertArrayHasKey('name', $this->BlogCategory->validationErrors);
        $this->assertEquals('カテゴリ名は255文字以内で入力してください。', current($this->BlogCategory->validationErrors['name']));

        $this->assertArrayHasKey('title', $this->BlogCategory->validationErrors);
        $this->assertEquals('カテゴリタイトルは255文字以内で入力してください。', current($this->BlogCategory->validationErrors['title']));
    }

    public function test桁数チェック正常系()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        // blog_content_idを設定
        $this->BlogCategory->validationParams = [
            'blogContentId' => 1
        ];

        $this->BlogCategory->create([
            'BlogCategory' => [
                'name' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
                'title' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
            ]
        ]);

        $this->assertTrue($this->BlogCategory->validates());
    }

    public function testその他異常系()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        // blog_content_idを設定
        $this->BlogCategory->validationParams = [
            'blogContentId' => 1
        ];

        // 半角チェック
        $this->BlogCategory->create([
            'BlogCategory' => [
                'name' => 'テスト',
            ]
        ]);

        $this->assertFalse($this->BlogCategory->validates());

        $this->assertArrayHasKey('name', $this->BlogCategory->validationErrors);
        $this->assertEquals('カテゴリ名は半角のみで入力してください。', current($this->BlogCategory->validationErrors['name']));

        // 重複チェック
        $this->BlogCategory->create([
            'BlogCategory' => [
                'name' => 'release',
            ]
        ]);

        $this->assertFalse($this->BlogCategory->validates());

        $this->assertArrayHasKey('name', $this->BlogCategory->validationErrors);
        $this->assertEquals('入力されたカテゴリ名は既に登録されています。', current($this->BlogCategory->validationErrors['name']));
    }

    /**
     * 同じニックネームのカテゴリがないかチェックする
     * 同じブログコンテンツが条件
     *
     * @dataProvider duplicateBlogCategoryDataProvider
     */
    public function testDuplicateBlogCategory($check, $expected)
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->BlogCategory->validationParams['blogContentId'] = 1;
        $result = $this->BlogCategory->duplicateBlogCategory($check);
        $this->assertEquals($result, $expected);
    }

    public function duplicateBlogCategoryDataProvider()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        return [
            [['id' => 0], true],
            [['id' => 1], false],
            [['name' => 'release'], false],
            [['title' => 'プレスリリース'], false],
            [['title' => '親子関係なしカテゴリ'], false],
            [['title' => 'hoge'], true],
        ];
    }

    /**
     * 関連する記事データをカテゴリ無所属に変更し保存する
     */
    public function testBeforeDelete()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->BlogCategory->data = ['BlogCategory' => [
            'id' => '1'
        ]];
        $this->BlogCategory->delete();

        $BlogPost = ClassRegistry::init('BcBlog.BlogPost');
        $result = $BlogPost->find('first', [
            'conditions' => ['blog_category_id' => 1]
        ]);
        $this->assertEmpty($result);
    }

    /**
     * カテゴリリストを取得する
     */
    public function testGetCategoryList()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $message = '正しくカテゴリリストを取得できません';
        // 正常
        $result = $this->BlogCategory->getCategoryList(1, []);
        $this->assertNotEmpty($result, $message);
        $this->assertEquals($result[0]['BlogCategory']['id'], 1, $message);

        // 存在しないID
        $result = $this->BlogCategory->getCategoryList(0, []);
        $this->assertEmpty($result, $message);

        // option depth 2
        $result = $this->BlogCategory->getCategoryList(1, ['depth' => 2]);
        $this->assertNotEmpty($result[0]['BlogCategory']['children'], $message);

        // option type year
        $result = $this->BlogCategory->getCategoryList(1, ['type' => 'year']);
        $this->assertNotEmpty($result, $message);
        $this->assertEquals($result['2015'][0]['BlogCategory']['id'], 1, $message);

        // option viewCount true
        $result = $this->BlogCategory->getCategoryList(1, ['viewCount' => true]);
        $this->assertEquals($result[0]['BlogCategory']['count'], 2, $message);

        // option limit true
        $result = $this->BlogCategory->getCategoryList(1, ['type' => 'year', 'limit' => 1, 'viewCount' => true]);
        $this->assertEquals($result['2015'][0]['BlogCategory']['count'], 1, $message);
    }

    /**
     * アクセス制限としてカテゴリの新規追加ができるか確認する
     */
    public function testHasNewCategoryAddablePermission()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        //		$result = $this->BlogCategory->hasNewCategoryAddablePermission(2, 99);
    }

    /**
     * 子カテゴリを持っているかどうか
     */
    public function testHasChild()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->assertFalse($this->BlogCategory->hasChild(2));
        $this->assertTrue($this->BlogCategory->hasChild(1));
    }

    /**
     * カテゴリ名よりカテゴリを取得する
     * @dataProvider getByNameDataProvider
     * @param int $blogCategoryId
     * @param string $name
     * @param bool $expects
     */
    public function testGetByName($blogCategoryId, $name, $expects)
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $result = $this->BlogCategory->getByName($blogCategoryId, $name);
        $this->assertEquals($expects, (bool)$result);
    }

    public function getByNameDataProvider()
    {
        return [
            [1, 'child', true],
            [1, 'hoge', false],
            [2, 'child', false]
        ];
    }
}
