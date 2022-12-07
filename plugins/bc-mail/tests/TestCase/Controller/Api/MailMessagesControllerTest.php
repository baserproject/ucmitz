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

namespace BcMail\Test\TestCase\Controller\Api;

use BaserCore\Service\DblogsServiceInterface;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcMail\Test\Factory\MailContentFactory;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class MailMessagesControllerTest extends BcTestCase
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
        'plugin.BaserCore.Factory/Contents',
        'plugin.BcMail.Factory/MailContents',
    ];

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $token = $this->apiLoginAdmin(1);
        $this->accessToken = $token['access_token'];
        $this->refreshToken = $token['refresh_token'];
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
     * [API] 受信メール一覧
     */
    public function testIndex()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [API] 受信メール詳細
     */
    public function testView()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [API] 受信メール追加
     */
    public function testAdd()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [API] 受信メール編集
     */
    public function testEdit()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [API] 受信メール削除
     */
    public function testDelete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [API] 受信メール一括削除
     */
    public function testBatch()
    {
        // テストデータを作成する
        ContentFactory::make([
            'id' => 9,
            'name' => 'contact',
            'plugin' => 'BcMail',
            'type' => 'MailContent',
            'entity_id' => 1,
            'url' => '/contact/',
            'site_id' => 1,
            'title' => 'お問い合わせ(※関連Fixture未完了)',
            'status' => true,
        ])->persist();
        MailContentFactory::make(['id' => 1, 'save_info' => 1])->persist();
        $mailMessageTable = TableRegistry::getTableLocator()->get('BcMail.MailMessages');
        $mailContentId = 1;
        $mailMessageTable->setup($mailContentId);
        // mail_message_1テーブルに２件のレコードを追加する
        $mailMessageTable->save(new Entity(['id' => 1]));
        $mailMessageTable->save(new Entity(['id' => 2]));

        // 受信メール一括削除のAPIを叩く
        $data = ['batch_targets' => [1, 2], 'batch' => 'delete'];
        $this->post("/baser/api/bc-mail/mail_messages/batch/$mailContentId/1.json?token=$this->accessToken", $data);
        $result = json_decode((string)$this->_response->getBody());
        // レスポンスのコードを確認する
        $this->assertResponseOk();
        // レスポンスのメッセージ内容を確認する
        $this->assertEquals('一括処理が完了しました。', $result->message);

        // DBログに保存したかどうか確認する
        $dbLogService = $this->getService(DblogsServiceInterface::class);
        $dbLog = $dbLogService->getDblogs(1)->toArray()[0];
        $this->assertEquals('メールメッセージ No 1, 2 を 削除 しました。', $dbLog->message);
        $this->assertEquals(1, $dbLog->id);
        $this->assertEquals('MailMessages', $dbLog->controller);
        $this->assertEquals('batch', $dbLog->action);

        // 一括削除が失敗の場合のテスト
        $data = ['batch_targets' => ['invalid id'], 'batch' => 'delete'];
        // 受信メール一括削除のAPIを叩く
        $this->post("/baser/api/bc-mail/mail_messages/batch/$mailContentId/1.json?token=$this->accessToken", $data);
        // レスポンスのコードを確認する
        $this->assertResponseCode(400);
        // レスポンスのメッセージ内容を確認する
        $result = json_decode((string)$this->_response->getBody());
        $this->assertStringContainsString('($id) must be of type int, string given', $result->message);
    }

    /**
     * [API] CSVダウンロード
     */
    public function testDownload()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
