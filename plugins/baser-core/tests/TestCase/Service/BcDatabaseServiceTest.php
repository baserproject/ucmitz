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
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use Cake\Cache\Cache;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\Filesystem\File;

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
     * @var string[]
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/Contents',
        'plugin.BaserCore.Factory/ContentFolders',
        'plugin.BaserCore.Factory/Pages',
        'plugin.BaserCore.Factory/SiteConfigs',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
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

    public function test_getEncoding()
    {
        $encoding = $this->BcDatabaseService->getEncoding();
        $this->assertEquals('utf8', $encoding);
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
     * test loadCsvToArray
     * @return void
     */
    public function test_loadCsvToArray()
    {
        ContentFactory::make(
            [
                'name' => 'BaserCore',
                'type' => 'ContentFolder',
                'entity_id' => 1,
                'title' => 'メインサイト',
                'lft' => 1,
                'right' => 18,
                'level' => 0
            ]
        )->persist();
        $path = TMP . DS . 'csv' . DS . 'contents.csv';
        $options = [
            'path' => $path,
            'encoding' => 'utf8',
            'init' => false,
        ];
        $this->BcDatabaseService->writeCsv('contents', $options);
        //CSVファイルを指定して実行
        $rs = $this->BcDatabaseService->loadCsvToArray($path);
        //戻り値が配列になっていることを確認
        $this->assertIsArray($rs);
        $this->assertEquals('メインサイト', $rs[0]['title']);

        //SJIS のCSVファイルを作成
        $options = [
            'path' => $path,
            'encoding' => 'sjis',
            'init' => false,
        ];
        $this->BcDatabaseService->writeCsv('contents', $options);
        $rs = $this->BcDatabaseService->loadCsvToArray($path);
        $this->assertIsArray($rs);
        // 戻り値の配列の値のエンコードがUTF-8になっている事を確認
        $this->assertEquals('メインサイト', $rs[0]['title']);
        $this->assertTrue(mb_check_encoding($rs[0]['title'], 'UTF-8'));

        $file = new File($path);
        $file->delete();
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

}
