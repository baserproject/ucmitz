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

namespace BaserCore\Test\TestCase\Model\Table;

use BaserCore\Model\Table\SitesTable;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class SitesTableTest
 * @package BaserCore\Test\TestCase\Model\Table
 * @property SitesTable $Sites
 */
class SitesTableTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Sites',
    ];

    /**
     * Set Up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Sites = $this->getTableLocator()->get('BaserCore.Sites');
    }

    /**
     * Tear Down
     */
    public function tearDown(): void
    {
        unset($this->Sites);
        parent::tearDown();
    }

    /**
     * 公開されている全てのサイトを取得する
     */
    public function testGetPublishedAll()
    {
        $this->assertEquals(1, count($this->Sites->getPublishedAll()));
        $site = $this->Sites->find()->where(['id' => 2])->first();
        $site->status = true;
        $this->Sites->save($site);
        $this->assertEquals(2, count($this->Sites->getPublishedAll()));
    }

    /**
     * サイトリストを取得
     *
     * @param int $mainSiteId メインサイトID
     * @param array $options
     * @param array $expects
     * @param string $message
     * @dataProvider getSiteListDataProvider
     */
    public function testGetSiteList($mainSiteId, $options, $expects, $message)
    {
        $result = $this->Sites->getSiteList($mainSiteId, $options);
        $this->assertEquals($expects, $result, $message);
    }

    public function getSiteListDataProvider()
    {
        return [
            [null, [], [1 => 'メインサイト', 3 => '英語サイト'], '全てのサイトリストの取得ができません。'],
            [1, [], [3 => '英語サイト'], 'メインサイトの指定ができません。'],
            [2, [], [], 'メインサイトの指定ができません。'],
            [null, ['excludeIds' => [1]], [3 => '英語サイト'], '除外指定ができません。'],
            [null, ['excludeIds' => 1], [3 => '英語サイト'], '除外指定ができません。'],
            [null, ['includeIds' => [1, 2], 'status' => null], [1 => 'メインサイト', 2 => 'スマホサイト'], 'ID指定ができません。'],
            [null, ['status' => false], [2 => 'スマホサイト'], 'ステータス指定ができません。'],
        ];
    }

    /**
     * メインサイトのデータを取得する
     */
    public function testGetRootMain()
    {
        $this->assertEquals(1, $this->Sites->getRootMain()['id']);
        $this->assertEquals(2, count($this->Sites->getRootMain(['fields' => ['name', 'display_name']])));
    }

    /**
     * コンテンツに関連したコンテンツをサイト情報と一緒に全て取得する
     */
    public function testGetRelatedContents()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * メインサイトかどうか判定する
     */
    public function testIsMain()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * サブサイトを取得する
     */
    public function testChildren()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * After Save
     */
    public function testAfterSave()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * After Delete
     */
    public function testAfterDelete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * プレフィックスを取得する
     */
    public function testGetPrefix()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * サイトのルートとなるコンテンツIDを取得する
     */
    public function testGetRootContentId()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * URLよりサイトを取得する
     */
    public function testFindByUrl()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * メインサイトを取得する
     */
    public function testGetMain()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * After Find
     */
    public function testAfterFind()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 選択可能なデバイスの一覧を取得する
     */
    public function testGetSelectableDevices()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 選択可能が言語の一覧を取得する
     */
    public function testGetSelectableLangs()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * testResetDevice
     */
    public function testResetDevice()
    {
        $this->Sites->resetDevice();
        $sites = $this->Sites->find('all', ['recursive' => -1]);
        foreach($sites as $site) {
            $this->assertEquals($site['Site']['device'], '');
            $this->assertFalse($site['Site']['same_main_url']);
            $this->assertFalse($site['Site']['auto_redirect']);
            $this->assertFalse($site['Site']['auto_link']);
        }
    }

    /**
     * testResetDevice
     */
    public function testResetLang()
    {
        $this->Sites->resetLang();
        $sites = $this->Sites->find('all', ['recursive' => -1]);
        foreach($sites as $site) {
            $this->assertEquals($site['Site']['lang'], '');
            $this->assertFalse($site['Site']['same_main_url']);
            $this->assertTrue($site['Site']['auto_redirect']);
        }
    }

    /**
     * Before Save
     */
    public function testBeforeSave()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
