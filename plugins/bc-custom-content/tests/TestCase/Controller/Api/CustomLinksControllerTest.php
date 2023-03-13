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

namespace BcCustomContent\Test\TestCase\Controller\Api;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\Test\Scenario\CustomFieldsScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * Class CustomLinksControllerTest
 */
class CustomLinksControllerTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use IntegrationTestTrait;

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
        'plugin.BcCustomContent.Factory/CustomFields',
        'plugin.BcCustomContent.Factory/CustomLinks',
        'plugin.BcCustomContent.Factory/CustomTables',
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
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test index
     */
    public function test_index()
    {
        //データを生成
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //APIを呼ぶ
        $this->get('/baser/api/bc-custom-content/custom_links/index/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertCount(2, $result->customLinks);
    }

    /**
     * test view
     */
    public function test_view()
    {
        //データを生成
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //APIを呼ぶ
        $this->get('/baser/api/bc-custom-content/custom_links/view/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->customLink);
        $this->assertEquals('求人分類', $result->customLink->custom_field->title);
    }

    /**
     * test add
     */
    public function test_add()
    {
        $data = [
            'custom_table_id' => 1,
            'custom_field_id' => 1,
            'lft' => 1,
            'rght' => 2,
            'name' => 'recruit_category_add',
            'title' => '求人分類',
        ];
        //APIを呼ぶ
        $this->post('/baser/api/bc-custom-content/custom_links/add.json?token=' . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->customLink);
        $this->assertEquals('カスタムリンク「求人分類」を追加しました。', $result->message);

        //タイトルがない場合、
        //存在しないBlogPostIDを削除場合、
        $this->post('/baser/api/bc-custom-content/custom_links/add.json?token=' . $this->accessToken, ['title' => '']);
        //ステータスを確認
        $this->assertResponseCode(400);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertEquals('タイトルを入力してください。', $result->errors->title->_empty);
    }

    /**
     * test edit
     */
    public function test_edit()
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
     * test list
     */
    public function test_list()
    {
        //データを生成
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //APIを呼ぶ
        $this->get('/baser/api/bc-custom-content/custom_links/list/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('求人分類', $result->customLinks->{1});
        $this->assertEquals('この仕事の特徴', $result->customLinks->{2});
    }
}
