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
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Test\Factory\UserFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use Cake\Cache\Cache;
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
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/Pages',
        'plugin.BaserCore.Factory/Users',
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
        $plugin = 'BaserCore';
        $excludes = ['site_configs', 'sites'];
        SiteConfigFactory::make(['name' => 'test', 'value' => 'test value'])->persist();
        SiteFactory::make(['name' => 'home page', 'title' => 'welcome'])->persist();
        PageFactory::make(['contents' => 'this is the contents', 'draft' => 'trash'])->persist();
        UserFactory::make(['name' => 'Chuong Le', 'email' => 'chuong.le@mediabridge.asia'])->persist();
        $this->BcDatabaseService->resetTables($plugin, $excludes);
        $this->assertEquals(1, SiteConfigFactory::count());
        $this->assertEquals(1, SiteFactory::count());
        $this->assertEquals(0, PageFactory::count());
        $this->assertEquals(0, UserFactory::count());
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
