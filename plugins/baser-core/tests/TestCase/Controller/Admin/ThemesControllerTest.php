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

namespace BaserCore\Test\TestCase\Controller\Admin;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Controller\Admin\ThemesController;

/**
 * Class ThemesControllerTest
 * @property  ThemesController $ThemesController
 */
class ThemesControllerTest extends BcTestCase
{

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ThemesController = new ThemesController($this->getRequest());
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
     * テーマをアップロードして適用する
     */
    public function test_add()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * テーマ一覧
     */
    public function test_index()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 初期データセットを読み込む
     */
    public function test_load_default_data_pattern()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * コアの初期データを読み込む
     */
    public function test_reset_data()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * テーマをコピーする
     */
    public function test_copy()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * テーマを削除する
     */
    public function test_delete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * テーマを適用する
     */
    public function test_apply()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 初期データセットをダウンロードする
     */
    public function test_download_default_data_pattern()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * ダウンロード
     */
    public function test_download()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * スクリーンショットを表示
     */
    public function test_screenshot()
    {
        // デフォルトのフロントテーマのスクリーンショットを取得する
        $response = $this->ThemesController->screenshot('BcFront');
        $fileName = $response->getFile()->getFileName();
        $this->assertEquals('screenshot.png', $fileName);

        try {
            // 存在しないテーマのスクリーンショットを取得する
            $this->ThemesController->screenshot('NotExistsTheme');
            $this->fail();
        } catch (\Exception) {
            $this->assertTrue(true);
        }
    }

}
