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

namespace BaserCore\Test\TestCase\View\Helper;

use Cake\View\Helper\HtmlHelper;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\BcAdminAppView;
use BaserCore\View\Helper\BcBaserHelper;
use Cake\View\Helper\FlashHelper;
use Cake\View\Helper\UrlHelper;


// use BaserCore\View\BcAdminAppView;
// use Cake\Core\Configure;

/**
 * Class BcBaserHelperTest
 * @package BaserCore\Test\TestCase\View\Helper
 * @property HtmlHelper $Html
 * @property BcBaserHelper $BcBaser
 * @property FlashHelper $Flash
 * @property UrlHelper $Url
 */
class BcBaserHelperTest extends BcTestCase {

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
    ];
    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcAdminAppView = new BcAdminAppView($this->getRequest());
        $this->BcBaser = new BcBaserHelper($this->BcAdminAppView);
        $this->Html = new HtmlHelper($this->BcAdminAppView);
        $this->Flash = new FlashHelper($this->BcAdminAppView);
        $this->Url = new UrlHelper($this->BcAdminAppView);


    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcAdminAppView, $this->BcBaser, $this->Html, $this->Flash, $this->Url);
        parent::tearDown();
    }

    /**
     * Test BcBaser->jsが適切な<script>を取得できているかテスト
     *
     * @return void
     */
    public function testJs()
    {
        // $inlineがfalseの場合
        $options = ['block' => false];
        $result = $this->BcBaser->js("sampletest", $options['block'], $options);
        $this->assertNull($result);
        // $inlineがtrueの場合
        $options = ['block' => true];
        $result = $this->BcBaser->js("sampletest", $options['block'], $options);
        ob_start();
        $this->Html->script("sampletest", $options);
        $expected = ob_get_clean();
        $this->assertEquals($expected, $result);
    }

    /**
     * Test BcBaser->elementが機能してるかテスト
     *
     * @return void
     */
    public function testElement()
    {
        $element = 'flash/default';

        ob_start();
        $this->BcBaser->element($element, ['message' => 'sampletest']);
        $result = ob_get_clean();

        $expected = $this->BcAdminAppView->element($element, ['message' => 'sampletest']);

        $this->assertEquals($expected, $result);
    }

    /**
     * Test BcBaser->getElementが適切なelementを取得できているかテスト
     *
     * @return void
     */
    public function testGetElement()
    {
        $element = 'flash/default';
        $result = $this->BcBaser->getElement($element, ['message' => 'sampletest']);
        $expected = $this->BcAdminAppView->element($element, ['message' => 'sampletest']);
        $this->assertEquals($expected, $result);
    }

    /**
     * Test BcBaser->imgが適切な画像を出力できてるかテスト
     *
     * @return void
     */
    public function testImg()
    {
        $img = 'sampletest.png';
        ob_start();
        $this->BcBaser->img($img);
        $result = ob_get_clean();
        $expected = $this->Html->image($img);
        $this->assertEquals($expected, $result);
    }

    /**
     * Test BcBaser->getImgが適切な画像を取得できているかテスト
     *
     * @return void
     */
    public function testgetImg()
    {
        $img = 'sampletest.png';
        $result = $this->BcBaser->getImg($img);
        $expected = $this->Html->image($img);
        $this->assertEquals($expected, $result);

    }

    /**
     * Test BcBaser->linkが適切なリンクを出力できてるかテスト
     *
     * @return void
     */
    public function testLink()
    {
        $link = 'sampletest';
        ob_start();
        $this->BcBaser->link($link);
        $result = ob_get_clean();
        $expected = $this->Html->link($link);
        $this->assertEquals($expected, $result);
    }
    /**
     * Test BcBaser->getLinkが適切なリンクを取得できているかテスト
     *
     * @return void
     */
    public function testGeLink()
    {
        $options = ['confirm' => true];
        $title = 'sampletest';
        $link = 'sampletest/';
        $result = $this->BcBaser->getLink($title, $link, $options, $options['confirm']);
        $expected = $this->Html->link($title, $link, $options);
        $this->assertEquals($expected, $result);
    }
    /**
     * Test isAdminUser
     *
     * @return void
     * @todo メソッド未実装
     */
    public function testIsAdminUser()
    {
        $this->markTestIncomplete('テストが未実装です');
    }

    /**
     * Test existsEditLink
     *
     * @return void
     * @todo メソッド未実装
     */
    public function testExistsEditLink()
    {
        $this->markTestIncomplete('テストが未実装です');
    }

    /**
     * Test existsPublishLink
     *
     * @return void
     * @todo メソッド未実装
     */
    public function testExistsPublishLink()
    {
        $this->markTestIncomplete('テストが未実装です');
    }

    /**
     * Test url
     *
     * @return void
     * @todo メソッド未実装
     */
    public function testUrl()
    {
        $this->markTestIncomplete('テストが未実装です');
    }

    /**
     * Test BcBaser->getUserNameで適切な名前が取得できてるかテスト
     *
     * @return void
     */
    public function testGetUserName()
    {
        $user = $this->getUser(1);
        // ニックネームの場合
        $expected = $user->get('nickname');
        $result = $this->BcBaser->getUserName($user);
        $this->assertEquals($expected, $result);
        // ニックネームがない場合
        $user->unset('nickname');
        $expected = $user->get('real_name_1') .' ' . $user->get('real_name_2');
        $result = $this->BcBaser->getUserName($user);
        $this->assertEquals($expected, $result);
    }

    /**
     * Test i18nScript
     *
     * @return void
     * @todo メソッド未実装
     */
    public function testi18nScript()
    {
        $this->markTestIncomplete('テストが未実装です');
    }

    /**
     * Test BcBaser->flashが適切なflashメッセージを出力してるかテスト
     *
     * @return void
     */
    public function testFlash()
    {
        // sessionにメッセージがない場合
        $result = $this->BcBaser->flash();
        $this->assertNull($result);
        // sessionにメッセージがある場合
        $session = $this->BcBaser->getView()->getRequest()->getSession();
        $flash = [
            'Flash' => [
                'flash' => [
                    [
                        'message' => "sampletest",
                        'key' => 'flash',
                        'element' => 'flash/default',
                        'params' => ['class' => 'sampletest-message']
                    ]
                ]
            ]];
        // BcBaser->flash
        $session->write($flash);
        ob_start();
        $this->BcBaser->flash();
        $result = ob_get_clean();
        // Flash->render
        $session->write($flash);
        $expected = $this->Flash->render();

        $this->assertStringContainsString($expected, $result);
    }

    /**
     * Test getContentsTitle
     *
     * @return void
     * @todo メソッド未実装
     */
    public function testGetContentsTitle()
    {
        $this->markTestIncomplete('テストが未実装です');
    }

    /**
     * Test BcBaser->contentsNameが適切なコンテンツ名を出力してるかテスト
     *
     * @return void
     */
    public function testContentsName()
    {
        ob_start();
        $this->BcBaser->contentsName();
        $result = ob_get_clean();
        $this->assertEquals('Admin', $result);
    }

    /**
     * Test BcBaser->getContentsNameが適切なコンテンツ名を取得してるかテスト
     *
     * @return void
     */
    public function testGetContentsName()
    {
        // アクションがログインでない場合
        $result = $this->BcBaser->getContentsName();
        $this->assertEquals('Admin', $result);
        // アクションがログインの場合
        $this->BcBaser->getView()->setRequest($this->getRequest()->withParam('action', 'login'));
        $result = $this->BcBaser->getContentsName();
        $this->assertEquals('AdminUsersLogin', $result);
    }

    /**
     * Test editLink
     *
     * @return void
     * @todo メソッド未実装
     */
    public function testEditLink()
    {
        $this->markTestIncomplete('テストが未実装です');
    }

    /**
     * Test publishLink
     *
     * @return void
     * @todo メソッド未実装
     *
     */
    public function testPublishLink()
    {
        $this->markTestIncomplete('テストが未実装です');
    }

    /**
     * Test BcBaser->getUrlが適切なURLを取得してるかテスト
     *
     * @return void
     */
    public function testGetUrl()
    {
        $url = '/sampletest';
        // フルパスかどうか
        $isFull = [false,true];
        foreach($isFull as $full) {
            $result = $this->BcBaser->getUrl($url, $full);
            $expected = $this->Url->build($url, ['fullBase' => $full]);
            $this->assertEquals($expected, $result);
        }
    }

}
