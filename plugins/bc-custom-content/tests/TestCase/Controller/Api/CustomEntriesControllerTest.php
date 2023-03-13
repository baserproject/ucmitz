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

use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\Service\CustomTablesServiceInterface;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * Class CustomContentsControllerTest
 */
class CustomEntriesControllerTest extends BcTestCase
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test view
     */
    public function test_view()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test add
     */
    public function test_add()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        //カスタムテーブルとカスタムエントリテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_categories',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);
        //追加データを準備
        $data = [
            'custom_table_id' => 1,
            'name' => 'プログラマー',
            'title' => 'プログラマー',
            'creator_id' => 1,
            'lft' => 1,
            'rght' => 2,
            'level' => 0,
            'status' => 1
        ];
        //APIを呼ぶ
        $this->post('/baser/api/bc-custom-content/custom_entries/add/1.json?token=' . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->entry);
        $this->assertEquals('フィールド「プログラマー」を追加しました。', $result->message);

        //タイトルがない場合、
        //存在しないBlogPostIDを削除場合、
        $this->post('/baser/api/bc-custom-content/custom_entries/add/1.json?token=' . $this->accessToken, ['title' => '']);
        //ステータスを確認
        $this->assertResponseCode(400);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertEquals('タイトルは必須項目です。', $result->errors->title->_empty);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
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
     * test batch
     */
    public function test_list()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
