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

namespace BcMail\Test\TestCase\Controller\Api\Admin;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcMail\Service\MailMessagesServiceInterface;
use BcMail\Service\MailFieldsServiceInterface;
use BcMail\Test\Scenario\MailFieldsScenario;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class MailFieldsControllerTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use IntegrationTestTrait;
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
        'plugin.BaserCore.Factory/Contents',
        'plugin.BcMail.Factory/MailContents',
        'plugin.BcMail.Factory/MailFields',
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
     * [API] メールフィールド API リスト取得
     */
    public function testList()
    {
        //データを生成
        $this->loadFixtureScenario(MailFieldsScenario::class);
        //APIを呼ぶ
        $this->get("/baser/api/admin/bc-mail/mail_fields/list/1.json?token=" . $this->accessToken);
        // レスポンスコードを確認する
        $this->assertResponseOk();
        // 戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->mailFields);
    }

    /**
     * [API] メールフィールド API 新規追加
     */
    public function testAdd()
    {
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        //テストデータベースを生成
        $MailMessagesService->createTable(10);
        //データを生成
        $data = [
            'mail_content_id' => 10,
            'field_name' => 'name_add_1',
            'type' => 'text',
            'name' => '性',
            'head' => 'お名前',
            'valid' => 0,
            'not_empty' => 0,
            'valid_ex' => null,
            'attention'=>null,
            'before_attachment'=>null,
            'after_attachment'=>null,
            'description'=>null,
            'source' => '',
            'size' => null,
            'text_rows' => null,
            'maxlength' => 255,
            'group_field' => null,
            'group_valid' => null,
            'options' => null,
            'class' => null,
            'default_value' => null,
            'auto_convert' => null,
            'use_field' => 1,
            'no_send' => 0,
        ];
        //APIを呼ぶ
        $this->post("/baser/api/admin/bc-mail/mail_fields/add.json?token=" . $this->accessToken, $data);
        // レスポンスコードを確認する
        $this->assertResponseOk();
        // 戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->mailField);
        $this->assertEquals($result->message, '新規メールフィールド「性」を追加しました。');

        //テストデータベースを削除
        $MailMessagesService->dropTable(10);
    }

    /**
     * [API] メールフィールド API 編集
     */
    public function testEdit()
    {
        //データを生成
        $this->loadFixtureScenario(MailFieldsScenario::class);
        $data = ['name' => 'name_edited', 'source' => '', 'valid_ex' => ''];
        //APIを呼ぶ
        $this->post("/baser/api/admin/bc-mail/mail_fields/edit/1.json?token=" . $this->accessToken, $data);
        // レスポンスコードを確認する
        $this->assertResponseOk();
        // 戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals($result->mailField->name, 'name_edited');
        $this->assertEquals($result->message, 'メールフィールド「name_edited」を更新しました。');

        //エラーを発生した場合、
        //データを生成
        $data = ['name' => '', 'source' => '', 'valid_ex' => ''];
        //APIを呼ぶ
        $this->post("/baser/api/admin/bc-mail/mail_fields/edit/1.json?token=" . $this->accessToken, $data);
        // レスポンスコードを確認する
        $this->assertResponseCode(400);
        // 戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        //メッセージを確認、
        $this->assertEquals($result->message, '入力エラーです。内容を修正してください。');
        //エラー内容を確認、
        $this->assertEquals($result->errors->name->_empty, '項目名を入力してください。');
    }

    /**
     * [API] メールフィールド API 削除
     */
    public function testDelete()
    {
        //データを生成
        //メールメッセージサービスをコル
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        //メールメッセージフィルドを追加
        $MailMessagesService->addMessageField(1, 'name_1');
        //メールフィルドのデータを生成
        $this->loadFixtureScenario(MailFieldsScenario::class);
        //APIを呼ぶ
        $this->post("/baser/api/admin/bc-mail/mail_fields/delete/1.json?token=" . $this->accessToken);
        // レスポンスコードを確認する
        $this->assertResponseOk();
        // 戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->mailField);
        $this->assertEquals($result->message, 'メールフィールド「性」を削除しました。');
    }

    /**
     * [API] メールフィールド API 削除
     */
    public function testCopy()
    {
        //データを生成
        //メールメッセージサービスをコル
        $MailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $MailFieldsService = $this->getService(MailFieldsServiceInterface::class);
        //メールメッセージフィルドを追加
        $MailMessagesService->addMessageField(1, 'name_1');
        //メールフィルドのデータを生成
        $this->loadFixtureScenario(MailFieldsScenario::class);
        //APIを呼ぶ
        $this->post("/baser/api/admin/bc-mail/mail_fields/copy/1/1.json?token=" . $this->accessToken);
        // レスポンスコードを確認する
        $this->assertResponseOk();
        // 戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->mailField);
        $this->assertEquals($result->message, 'メールフィールド「性」をコピーしました。');
        //メールフィルドがコピーできるか確認
        $mailField = $MailFieldsService->getIndex(1, ['name' => '性_copy'])->get();
        $this->assertCount(1, $mailField);
    }

    /**
     * [API] メールフィールドのバッチ処理
     */
    public function testBatch()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [API] 並び替えを更新する
     */
    public function testUpdateSort()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
