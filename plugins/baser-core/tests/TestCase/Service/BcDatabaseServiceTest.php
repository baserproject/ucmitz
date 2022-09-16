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
namespace BaserCore\Test\TestCase\Service;

use BaserCore\Service\BcDatabaseService;
use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\Test\Factory\PageFactory;
use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use Cake\Cache\Cache;
use Cake\Filesystem\Folder;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * BcDatabaseServiceTest
 * @property BcDatabaseService $BcDatabaseService
 */
class BcDatabaseServiceTest extends BcTestCase
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
        'plugin.BaserCore.Factory/SiteConfigs',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcDatabaseService = $this->getService(BcDatabaseServiceInterface::class);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Gets the database encoding
     * @return void
     */
    public function test_getEncoding()
    {
        $encoding = $this->BcDatabaseService->getEncoding();
        $this->assertEquals('utf8', $encoding);
    }

    /**
     * Gets the database encoding
     * @return void
     */
    public function test_truncate()
    {
        SiteConfigFactory::make(['name' => 'company', 'value' => 'Company A'])->persist();
        SiteConfigFactory::make(['name' => 'address', 'value' => 'Tokyo'])->persist();
        $this->assertEquals(2, SiteConfigFactory::count());
        $this->BcDatabaseService->truncate('site_configs');
        $this->assertEquals(0, SiteConfigFactory::count());
    }

    /**
     * test resetTables
     */
    public function test_resetTables()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $result = $this->BcManager->resetTables('test');
        $this->assertTrue($result, 'テーブルをリセットできません');

        $this->User = ClassRegistry::init('User');
        $User = $this->User->find('all', [
                'recursive' => -1,
            ]
        );
        $this->assertEmpty($User, 'テーブルをリセットできません');

        $this->FeedDetail = ClassRegistry::init('FeedDetail');
        $FeedDetail = $this->FeedDetail->find('all', [
                'recursive' => -1,
            ]
        );
        $this->assertEmpty($FeedDetail, 'プラグインのテーブルをリセットできません');
    }

    /**
     * test loadCsv
     */
    public function test_loadCsv()
    {
        // csvフォルダーを作成する
        $csvFolder = TMP . 'csv' . DS;
        if (!is_dir($csvFolder)) {
            new Folder($csvFolder, true, 0777);
        }
        // csvファイルを作成する
        $table = 'pages';
        $csvFilePath = $csvFolder . $table . '.csv';
        $csvContents = [
            'head' => ['id', 'contents', 'draft', 'page_template', 'modified', 'created'],
            'row1' => ['id' => 1, 'contents' => 'content 1', 'draft' => 'draft 1', 'page_template' => 'temp 1', '', 'created' => '2022-09-15 18:00:00'],
            'row2' => ['id' => 2, 'contents' => 'content 2', 'draft' => 'draft 2', 'page_template' => 'temp 2', '', 'created' => ''],
        ];
        $fp = fopen($csvFilePath, 'w');
        ftruncate($fp, 0);
        foreach ($csvContents as $row) {
            $csvRecord = implode(',', $row) . "\n";
            fwrite($fp, $csvRecord);
        }
        fclose($fp);
        // CSVファイルをDBに読み込む
        $this->BcDatabaseService->loadCsv(['path' => $csvFilePath, 'encoding' => 'UTF-8']);
        // 複数のレコードが読み込まれいている事を確認
        $this->assertEquals(2, PageFactory::count());
        // 反映したデータが正しい事を確認
        $row1 = PageFactory::get(1);
        $this->assertEquals($row1->contents, $csvContents['row1']['contents']);
        $this->assertEquals($row1->created->format('Y-m-d H:i:s'), $csvContents['row1']['created']);
        // createdが空の時に本日の日付が入っている事を確認
        $row2 = PageFactory::get(2);
        $this->assertEquals($row2->created->format('Y-m-d H:i'), date('Y-m-d H:i'));
    }

    /**
     * test getAppTableList
     */
    public function test_getAppTableList()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        Cache::delete('appTableList', '_bc_env_');
        $result = $this->BcDatabase->getAppTableList();
        $this->assertTrue(in_array('plugins', $result['BaserCore']));
        $this->assertTrue(in_array('plugins', Cache::read('appTableList', '_bc_env_')['BaserCore']));
    }

    /**
     * test clearAppTableList
     * @return void
     */
    public function test_clearAppTableList()
    {
        $this->BcDatabaseService->getAppTableList();
        $this->assertTrue(in_array('plugins', Cache::read('appTableList', '_bc_env_')['BaserCore']));
        $this->BcDatabaseService->clearAppTableList();
        $this->assertEquals(0, count(Cache::read('appTableList', '_bc_env_')));
    }

}
