<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Controller\Admin\UtilitiesController;
use Cake\Filesystem\Folder;
use Cake\Cache\Cache;

/**
 * class UtilitiesControllerTest
 * @package Cake\TestSuite\BcTestCase;
 * @package BaserCore\Controller\Admin\UtilitiesController;
 */
class UtilitiesControllerTest extends BcTestCase
{
    use IntegrationTestTrait;
    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->UtilitiesController = new UtilitiesController();
    }

    /**
	 * tearDown
	 *
	 * @return void
	 */
	public function tearDown(): void
	{
        Cache::drop('_cake_env_');
        Cache::drop('_cake_core_');
        Cache::drop('_cake_model_');
		parent::tearDown();
	}

    /**
	 * test clear_cache
	 *
	 * @return void
	 */
    public function testClear_cache(): void
    {

        $this->get('/baser/admin/utilities/clear_cache');
        $this->assertResponseCode(302);

        // cacheファイルのバックアップ作成
        $folder = new Folder();
        $origin = CACHE;
        $backup = str_replace('cache', 'cache_backup', CACHE);
        $folder->move($backup, [
            'from' => $origin,
            'mode' => 0777,
            'schema' => Folder::OVERWRITE,
        ]);

        // cache環境準備
        $cacheList = ['enviroment' => '_cake_env_', 'persistent' => '_cake_core_', 'models' => '_cake_model_'];

        foreach ($cacheList as $path => $cacheName) {
            Cache::drop($cacheName);
            Cache::setConfig($cacheName, [
                'className' => "File",
                'prefix' => 'myapp'. $cacheName,
                'path' => CACHE . $path . DS,
                'serialize' => true,
                'duration' => '+999 days',
            ]);
            Cache::write($cacheName . 'test', 'testtest', $cacheName);
        }

        // 削除実行
        $this->UtilitiesController->clear_cache();
        foreach($cacheList as $cacheName) {
            $this->assertNull(Cache::read($cacheName . 'test', $cacheName));
        }

        // cacheファイル復元
        $folder->move($origin, [
            'from' => $backup,
            'mode' => 0777,
            'schema' => Folder::OVERWRITE,
        ]);
        $folder->chmod($origin, 0777);
    }

    /**
	 * test ajax_save_search_box
	 *
	 * @return void
	 */
    public function testAjax_save_search_box(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
