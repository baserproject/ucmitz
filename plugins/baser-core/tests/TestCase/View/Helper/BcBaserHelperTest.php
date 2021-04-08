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
use Cake\Routing\Router;


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

        // TODO: basercms4系より移植
        // 'baser.Default.Page',    // メソッド内で読み込む
        // 'baser.Default.Content',    // メソッド内で読み込む
        // 'baser.Routing.Route.BcContentsRoute.ContentBcContentsRoute',    // メソッド内で読み込む
        // 'baser.Routing.Route.BcContentsRoute.SiteBcContentsRoute',    // メソッド内で読み込む
        // 'baser.View.Helper.BcBaserHelper.PageBcBaserHelper',
        // 'baser.View.Helper.BcBaserHelper.SiteConfigBcBaserHelper',
        // 'baser.Default.SearchIndex',
        // 'baser.Default.User',
        // 'baser.Default.UserGroup',
        // 'baser.Default.Favorite',
        // 'baser.Default.Permission',
        // 'baser.Default.ThemeConfig',
        // 'baser.Default.WidgetArea',
        // 'baser.Default.Plugin',
        // 'baser.Default.BlogContent',
        // 'baser.Default.BlogPost',
        // 'baser.Default.BlogCategory',
        // 'baser.Default.BlogTag',
        // 'baser.Default.BlogPostsBlogTag',
        // 'baser.Default.Site',
        // 'baser.Default.BlogComment',
        // 'baser.View.Helper.BcContentsHelper.ContentBcContentsHelper',
    ];

    /**
     * View
     *
     * @var View
     */
    protected $_View;

    /**
     * __construct
     * @since basercms4
     * @param string $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }
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

        // TODO: basercms4より移植
        // $this->_View = new BcAppView();
        // $this->_View->request = $this->_getRequest('/');
        // $SiteConfig = ClassRegistry::init('SiteConfig');
        // $siteConfig = $SiteConfig->findExpanded();
        // $this->_View->set('widgetArea', $siteConfig['widget_area']);
        // $this->_View->set('siteConfig', $siteConfig);
        // $this->_View->helpers = ['BcBaser'];
        // $this->_View->loadHelpers();
        // $this->BcBaser = $this->_View->BcBaser;


    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcAdminAppView, $this->BcBaser, $this->Html, $this->Flash, $this->Url);
        Router::reload();
        parent::tearDown();
    }
    /**
     * Test BcBaser->jsが適切な<script>を取得できているかテスト
     * @todo basercms4系を統合する
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
     * JSの読み込みタグを出力する
     * @since basercms4
     * @param string $expected 期待値
     * @param string $url URL
     * @return void
     * @dataProvider jsDataProvider
     */
    public function testJs_Version4($expected, $url)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->expectOutputString($expected);
        $this->BcBaser->js($url);
    }

    public function jsDataProvider()
    {
        return [
            ['<script type="text/javascript" src="/js/admin/startup.js"></script>', 'admin/startup'],
            ['<script type="text/javascript" src="/js/admin/startup.js"></script>', 'admin/startup.js']
        ];
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
     * @todo basercms4系と統合する
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
     * エレメントテンプレートのレンダリング結果を取得する
     *　@since basercms4
     * @return void
     */
    public function testGetElement_Version4()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        // フロント
        $result = $this->BcBaser->getElement('site_search_form');
        $this->assertTextContains('<div class="section search-box">', $result);

        // ### 管理画面
        $View = new BcAppView();
        $View->request = $this->_getRequest('/admin');
        $View->subDir = 'admin';
        // 管理画面用のテンプレートがなくフロントのテンプレートがある場合
        // ※ フロントが存在する場合にはフロントのテンプレートを利用する
        $result = $this->BcBaser->getElement(('site_search_form'));
        $this->assertTextContains('<div class="section search-box">', $result);
        // 強制的にフロントのテンプレートに切り替えた場合
        $result = $this->BcBaser->getElement('crumbs', [], ['subDir' => false]);
        $this->assertTextContains('ホーム', $result);
    }

    /**
     * Test BcBaser->imgが適切な画像を出力できてるかテスト
     * @todo basercms4系統合が必要
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
     * 画像読み込みタグを出力する
     * @since basercms4
     * @return void
     */
    public function testImg_Version4()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->expectOutputString('<img src="/img/baser.power.gif" alt=""/>');
        $this->BcBaser->img('baser.power.gif');
    }

    /**
     * Test BcBaser->getImgが適切な画像を取得できているかテスト
     * @todo basercms4系統合必要
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
     * 画像タグを取得する
     * @since basercms4
     * @param string $path 画像のパス
     * @param array $options オプション
     * @param string $expected 結果
     * @return void
     * @dataProvider getImgDataProvider
     */
    public function testGetImg_Version4($path, $options, $expected)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $result = $this->BcBaser->getImg($path, $options);
        $this->assertEquals($expected, $result);
    }

    public function getImgDataProvider()
    {
        return [
            ['baser.power.gif', ['alt' => "baserCMSロゴ"], '<img src="/img/baser.power.gif" alt="baserCMSロゴ"/>'],
            ['baser.power.gif', ['title' => "baserCMSロゴ"], '<img src="/img/baser.power.gif" title="baserCMSロゴ" alt=""/>']
        ];
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
     * @todo basercms4統合必要
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
     * アンカータグを取得する
     * @since basercms4
     * @param string $title タイトル
     * @param string $url URL
     * @param array $option オプション
     * @param string $expected 結果
     * @return void
     * @dataProvider getLinkDataProvider
     */
    public function testGetLink($title, $url, $option, $expected)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        if (!empty($option['prefix'])) {
            $this->_getRequest('/admin');
        }
        if (!empty($option['forceTitle'])) {
            $this->_View->viewVars['user']['user_group_id'] = 2;
        }
        if (!empty($option['ssl'])) {
            Configure::write('BcEnv.sslUrl', 'https://localhost/');
        }
        $result = $this->BcBaser->getLink($title, $url, $option);
        $this->assertEquals($expected, $result);
        Configure::write('BcEnv.sslUrl', '');
    }

    public function getLinkDataProvider()
    {
        return [
            ['', '/', [], '<a href="/"></a>'],
            ['会社案内', '/about', [], '<a href="/about">会社案内</a>'],
            ['会社案内 & 会社データ', '/about', ['escape' => true], '<a href="/about">会社案内 &amp; 会社データ</a>'],    // エスケープ
            ['固定ページ管理', ['controller' => 'pages', 'action' => 'index'], ['prefix' => true], '<a href="/admin/pages/">固定ページ管理</a>'],    // プレフィックス
            ['システム設定', ['admin' => true, 'controller' => 'site_configs', 'action' => 'form'], ['forceTitle' => true], '<span>システム設定</span>'],    // 強制タイトル
            ['会社案内', '/about', ['ssl' => true], '<a href="https://localhost/about">会社案内</a>'], // SSL
            ['テーマファイル管理', ['controller' => 'themes', 'action' => 'manage', 'jsa'], ['ssl' => true], '<a href="https://localhost/themes/manage/jsa">テーマファイル管理</a>'], // SSL
            ['画像', '/img/test.jpg', ['ssl' => true], '<a href="https://localhost/img/test.jpg">画像</a>'], // SSL
        ];
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
     * @todo basercms4系を統合する
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
     * セッションメッセージを出力する
     * @since basercms4
     * @return void
     */
    public function testFlash_Version4()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        // TODO コンソールからのセッションのテストをどうするか？そもそもするか？ ryuring
        if (isConsole()) {
            return;
        }

        $message = 'エラーが発生しました。';
        $this->expectOutputString('<div id="MessageBox"><div id="flashMessage" class="message">' . $message . '</div></div>');
        App::uses('SessionComponent', 'Controller/Component');
        App::uses('ComponentCollection', 'Controller/Component');
        $Session = new SessionComponent(new ComponentCollection());
        $Session->setFlash($message);
        $this->BcBaser->flash();
    }

    /**
     * コンテンツタイトルを取得する
     * @since basercms4
     * @todo メソッド未実装
     * @return void
     */
    public function testGetContentsTitle()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // 設定なし
        $this->assertEmpty($this->BcBaser->getContentsTitle());

        // 設定あり
        $this->BcBaser->setTitle('会社データ');
        $this->assertEquals('会社データ', $this->BcBaser->getContentsTitle());
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
     * @todo testGetUrl_basercms4を統合する
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

    /**
     * Test BcBaser->getUrlが適切なURLを取得してるかテスト
     * @since basercms4
     * @todo testGetUrlに統合する
     * @return void
     */
    public function testGetUrl_Version4()
    {
        // TODO; basercms4系より移植
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        // ノーマル
        $result = $this->BcBaser->getUrl('/about');
        $this->assertEquals('/about', $result);

        // 省略した場合
        $result = $this->BcBaser->getUrl();
        $this->assertEquals('/', $result);

        // フルURL
        $result = $this->BcBaser->getUrl('/about', true);
        $this->assertEquals(Configure::read('App.fullBaseUrl') . '/about', $result);

        // 配列URL
        $result = $this->BcBaser->getUrl([
            'admin' => true,
            'plugin' => 'blog',
            'controller' => 'blog_posts',
            'action' => 'edit',
            1
        ]);
        $this->assertEquals('/admin/blog/blog_posts/edit/1', $result);

        // セッションIDを付加する場合
        // TODO セッションIDを付加する場合、session.use_trans_sid の値が0である必要が
        // があるが、上記の値はセッションがスタートした後では書込不可の為見送り
        /*Configure::write('BcRequest.agent', 'mobile');
        Configure::write('BcAgent.mobile.sessionId', true);
        ini_set('session.use_trans_sid', 0);*/

        // --- サブフォルダ+スマートURLオフ ---
        Configure::write('App.baseUrl', '/basercms/index.php');
        $this->BcBaser->request = $this->_getRequest('/');

        // ノーマル
        $result = $this->BcBaser->getUrl('/about');
        $this->assertEquals('/basercms/index.php/about', $result);

        // 省略した場合
        $result = $this->BcBaser->getUrl();

        $this->assertEquals('/basercms/index.php/', $result);

        // フルURL
        $result = $this->BcBaser->getUrl('/about', true);
        $this->assertEquals(Configure::read('App.fullBaseUrl') . '/basercms/index.php/about', $result);

        // 配列URL
        $result = $this->BcBaser->getUrl([
            'admin' => true,
            'plugin' => 'blog',
            'controller' => 'blog_posts',
            'action' => 'edit',
            1
        ]);
        $this->assertEquals('/basercms/index.php/admin/blog/blog_posts/edit/1', $result);
    }


    /**************** TODO:下記basercms4系より移行 メソッドが準備され次第　調整して上記のテストコードに追加してください　********************/

    /**
     * ログイン状態にする
     * @since basercms4
     * @return void
     */
    protected function _login()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $User = ClassRegistry::init('User');
        $user = $User->find('first', ['conditions' => ['User.id' => 1]]);
        unset($user['User']['password']);
        $this->BcBaser->set('user', $user['User']);
        $user['User']['UserGroup'] = $user['UserGroup'];
        $sessionKey = BcUtil::authSessionKey('admin');
        $_SESSION['Auth'][$sessionKey] = $user['User'];
    }

    /**
     * ログイン状態を解除する
     * @since basercms4
     * @return void
     */
    protected function _logout()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->BcBaser->set('user', '');
    }
        /**
     * タイトルを設定する
     * @since basercms4
     * @return void
     */
    public function testSetTitle()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $topTitle = '｜baserCMS inc. [デモ]';
        $this->BcBaser->request = $this->_getRequest('/about');
        // カテゴリがない場合
        $this->BcBaser->setTitle('会社案内');
        $this->assertEquals("会社案内{$topTitle}", $this->BcBaser->getTitle());

        // カテゴリがある場合
        $this->BcBaser->request = $this->_getRequest('/service/service2');
        $this->BcBaser->_View->set('crumbs', [
            ['name' => '会社案内', 'url' => '/service/index'],
            ['name' => '会社データ', 'url' => '/service/data']
        ]);
        $this->BcBaser->setTitle('会社沿革');
        $this->assertEquals("会社沿革｜会社データ｜会社案内{$topTitle}", $this->BcBaser->getTitle());

        // カテゴリは存在するが、カテゴリの表示をオフにした場合
        $this->BcBaser->setTitle('会社沿革', false);
        $this->assertEquals("会社沿革{$topTitle}", $this->BcBaser->getTitle());
    }

    /**
     * タイトルをセットする
     * @since basercms4
     */
    public function testSetHomeTitle()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->BcBaser->setHomeTitle();
        $this->assertEquals(null, $this->_View->viewVars['homeTitle'], 'タイトルをセットできません。');

        $this->BcBaser->setHomeTitle('hoge');
        $this->assertEquals('hoge', $this->_View->viewVars['homeTitle'], 'タイトルをセットできません。');
    }

    /**
     * ページにeditLinkを追加する
     * @since basercms4
     */
    public function testSetPageEditLink()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        // 存在しない
        $this->BcBaser->setPageEditLink(1);
        $this->assertEquals(true, empty($this->_View->viewVars['editLink']));
        // 存在する
        $this->_View->viewVars['user'] = ['User' => ['id' => 1]];
        $this->BcBaser->setPageEditLink(1);
        $this->assertEquals(['admin' => true, 'controller' => 'pages', 'action' => 'edit', '0' => '1'], $this->_View->viewVars['editLink']);
    }

    /**
     * meta タグのキーワードを設定する
     * @since basercms4
     * @return void
     */
    public function testSetKeywords()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->BcBaser->setKeywords('baserCMS,国産,オープンソース');
        $this->assertEquals('baserCMS,国産,オープンソース', $this->BcBaser->getKeywords());
    }

        /**
     * meta タグの説明文を設定する
     * @since basercms4
     * @return void
     */
    public function testSetDescription()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->BcBaser->setDescription('国産オープンソースのホームページです');
        $this->assertEquals('国産オープンソースのホームページです', $this->BcBaser->getDescription());
    }

    /**
     * レイアウトで利用する為の変数を設定する
     * @since basercms4
     * @return void
     */
    public function testSet()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->BcBaser->set('keywords', 'baserCMS,国産,オープンソース');
        $this->assertEquals('baserCMS,国産,オープンソース', $this->BcBaser->getKeywords());
    }

    /**
     * タイトルへのカテゴリタイトルの出力有無を設定する
     * @since basercms4
     * @return void
     */
    public function testSetCategoryTitle()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $topTitle = '｜baserCMS inc. [デモ]';
        $this->BcBaser->request = $this->_getRequest('/about');
        $this->BcBaser->_View->set('crumbs', [
            ['name' => '会社案内', 'url' => '/company/index'],
            ['name' => '会社データ', 'url' => '/company/data']
        ]);
        $this->BcBaser->setTitle('会社沿革');

        // カテゴリをオフにした場合
        $this->BcBaser->setCategoryTitle(false);
        $this->assertEquals("会社沿革{$topTitle}", $this->BcBaser->getTitle());

        // カテゴリをオンにした場合
        $this->BcBaser->setCategoryTitle(true);
        $this->assertEquals("会社沿革｜会社データ｜会社案内{$topTitle}", $this->BcBaser->getTitle());

        // カテゴリを指定した場合
        $this->BcBaser->setCategoryTitle('店舗案内');
        $this->assertEquals("会社沿革｜店舗案内{$topTitle}", $this->BcBaser->getTitle());

        // パンくず用にリンクも指定した場合
        $this->BcBaser->setCategoryTitle([
            'name' => '店舗案内',
            'url' => '/shop/index'
        ]);
        $expected = [
            [
                'name' => '店舗案内',
                'url' => '/shop/index'
            ],
            [
                'name' => '会社沿革',
                'url' => ''
            ]
        ];
        $this->assertEquals($expected, $this->BcBaser->getCrumbs());
    }

    /**
     * meta タグ用のキーワードを取得する
     * @since basercms4
     * @param string $expected 期待値
     * @param string|null $keyword 設定されるキーワードの文字列
     * @dataProvider getKeywordsDataProvider
     */
    public function testGetKeywords($expected, $keyword = null)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        if ($keyword !== null) {
            $this->BcBaser->setKeywords($keyword);
        }
        $this->assertEquals($expected, $this->BcBaser->getKeywords());
    }

    public function getKeywordsDataProvider()
    {
        return [
            ['baser,CMS,コンテンツマネジメントシステム,開発支援'],
            ['baser,CMS,コンテンツマネジメントシステム,開発支援', ''],
            ['baserCMS,国産,オープンソース', 'baserCMS,国産,オープンソース'],
        ];
    }

    /**
     * meta タグ用のページ説明文を取得する
     * @since basercms4
     * @param string $expected 期待値
     * @param string|null $description 設定されるキーワードの文字列
     * @return void
     * @dataProvider getDescriptionDataProvider
     */
    public function testGetDescription($expected, $description = null)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        if ($description !== null) {
            $this->BcBaser->setDescription($description);
        }
        $this->assertEquals($expected, $this->BcBaser->getDescription());
    }

    public function getDescriptionDataProvider()
    {
        return [
            ['baserCMS は、CakePHPを利用し、環境準備の素早さに重点を置いた基本開発支援プロジェクトです。Webサイトに最低限必要となるプラグイン、そしてそのプラグインを組み込みやすい管理画面、認証付きのメンバーマイページを最初から装備しています。', ''],
            ['国産オープンソースのホームページです', '国産オープンソースのホームページです']
        ];
    }

        /**
     * タイトルタグを取得する
     * @since basercms4
     * @return void
     */
    public function testGetTitle()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $topTitle = 'baserCMS inc. [デモ]';
        $this->BcBaser->request = $this->_getRequest('/about');
        // 通常
        $this->BcBaser->_View->set('crumbs', [
            ['name' => '会社案内', 'url' => '/company/index'],
            ['name' => '会社データ', 'url' => '/company/data']
        ]);
        $this->BcBaser->setTitle('会社沿革');
        $this->assertEquals("会社沿革｜会社データ｜会社案内｜{$topTitle}", $this->BcBaser->getTitle());

        // 区切り文字を ≫ に変更
        $this->assertEquals("会社沿革≫会社データ≫会社案内≫{$topTitle}", $this->BcBaser->getTitle('≫'));

        // カテゴリタイトルを除外
        $this->assertEquals("会社沿革｜{$topTitle}", $this->BcBaser->getTitle('｜', false));

        // カテゴリが対象ページと同じ場合に省略する
        $this->BcBaser->setTitle('会社データ');
        $this->assertEquals("会社データ｜会社案内｜{$topTitle}", $this->BcBaser->getTitle('｜', true));

        // strip_tagの機能確認 tag付
        $this->BcBaser->setTitle('会社<br>沿革<center>真ん中</center>');
        $this->assertEquals("会社<br>沿革<center>真ん中</center>｜会社データ｜会社案内｜{$topTitle}", $this->BcBaser->getTitle('｜', true));

        // strip_tagの機能確認 tagを削除
        $options = [
            'categoryTitleOn' => true,
            'tag' => false
        ];
        $this->assertEquals("会社沿革真ん中｜会社データ｜会社案内｜{$topTitle}", $this->BcBaser->getTitle('｜', $options));

        // 一部タグだけ削除
        $options = [
            'categoryTitleOn' => true,
            'tag' => false,
            'allowableTags' => '<center>'
        ];
        $this->assertEquals("会社沿革<center>真ん中</center>｜会社データ｜会社案内｜{$topTitle}", $this->BcBaser->getTitle('｜', $options));
    }

    /**
     * パンくずリストの配列を取得する
     * @since basercms4
     * @return void
     */
    public function testGetCrumbs()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        // パンくずが設定されてない場合
        $result = $this->BcBaser->getCrumbs(true);
        $this->assertEmpty($result);

        // パンくずが設定されている場合
        $this->BcBaser->_View->set('crumbs', [
            ['name' => '会社案内', 'url' => '/company/index'],
            ['name' => '会社データ', 'url' => '/company/data']
        ]);
        $this->BcBaser->setTitle('会社沿革');
        $expected = [
            ['name' => '会社案内', 'url' => '/company/index'],
            ['name' => '会社データ', 'url' => '/company/data'],
            ['name' => '会社沿革', 'url' => '']
        ];
        $this->assertEquals($expected, $this->BcBaser->getCrumbs(true));

        // パンくずは設定されているが、オプションでカテゴリをオフにした場合
        $expected = [
            ['name' => '会社沿革', 'url' => '']
        ];
        $this->assertEquals($expected, $this->BcBaser->getCrumbs(false));
    }

    /**
     * コンテンツタイトルを出力する
     * @since basercms4
     * @return void
     */
    public function testContentsTitle()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->expectOutputString('会社データ');
        $this->BcBaser->setTitle('会社データ');
        $this->BcBaser->contentsTitle();
    }

    /**
     * コンテンツメニューを取得する
     * @since basercms4
     */
    public function testGetContentsMenu()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->assertRegExp('/<ul class="menu ul-level-1">/s', $this->BcBaser->getContentsMenu());
        $this->assertRegExp('/<ul class="menu ul-level-1">/s', $this->BcBaser->getContentsMenu(1, 1));
        $this->assertRegExp('/<ul class="menu ul-level-1">/s', $this->BcBaser->getContentsMenu(1, 1, 1));
    }

    /**
     * タイトルタグを出力する
     * @since basercms4
     * @return void
     */
    public function testTitle()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $topTitle = 'baserCMS inc. [デモ]';
        $title = '会社データ';
        $this->BcBaser->request = $this->_getRequest('/about');
        $this->expectOutputString('<title>' . $title . '｜' . $topTitle . '</title>' . PHP_EOL);
        $this->BcBaser->setTitle($title);
        $this->BcBaser->title();
    }

    /**
     * キーワード用のメタタグを出力する
     * @since basercms4
     * @return void
     */
    public function testMetaKeywords()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->BcBaser->setKeywords('baserCMS,国産,オープンソース');
        ob_start();
        $this->BcBaser->metaKeywords();
        $result = ob_get_clean();
        $excepted = [
            'meta' => [
                'name' => 'keywords',
                'content' => 'baserCMS,国産,オープンソース'
            ]
        ];

        $this->assertTags($result, $excepted);
    }

    /**
     * ページ説明文用のメタタグを出力する
     * @since basercms4
     * @return void
     */
    public function testMetaDescription()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->BcBaser->setDescription('国産オープンソースのホームページです');
        ob_start();
        $this->BcBaser->metaDescription();
        $result = ob_get_clean();
        $excepted = [
            'meta' => [
                'name' => 'description',
                'content' => '国産オープンソースのホームページです'
            ]
        ];
        $this->assertTags($result, $excepted);
    }

    /**
     * RSSフィードのリンクタグを出力する
     * @since basercms4
     * @return void
     */
    public function testRss()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        ob_start();
        $this->BcBaser->rss('ブログ', 'http://localhost/blog/');
        $result = ob_get_clean();
        $excepted = [
            'link' => [
                'href' => 'http://localhost/blog/',
                'type' => 'application/rss+xml',
                'rel' => 'alternate',
                'title' => 'ブログ'
            ]
        ];
        $this->assertTags($result, $excepted);
    }

    /**
     * 現在のページがトップページかどうかを判定する
     * @since basercms4
     * @param bool $expected 期待値
     * @param string $url リクエストURL
     * @return void
     * @dataProvider isHomeDataProvider
     */
    public function testIsHome($expected, $url)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->BcBaser->request = $this->_getRequest($url);
        $this->assertEquals($expected, $this->BcBaser->isHome());
    }

    public function isHomeDataProvider()
    {
        return [
            //PC
            [true, '/'],
            [true, '/index'],
            [false, '/news/index'],

            // モバイルページ
            [true, '/m/'],
            [true, '/m/index'],
            [false, '/m/news/index'],

            // スマートフォンページ
            [true, '/s/'],
            [true, '/s/index'],
            [false, '/s/news/index'],
            [false, '/s/news/index']
        ];
    }

    /**
     * baserCMSが設置されているパスを取得する
     * @since basercms4
     * @param string $expected 期待値
     * @param string $baseUrl App.baseUrl
     * @return void
     * @dataProvider getRootDataProvider
     */
    public function testGetRoot($expected, $baseUrl)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        Configure::write('App.baseUrl', $baseUrl);
        $this->BcBaser->request = $this->_getRequest('/');
        $this->assertEquals($expected, $this->BcBaser->getRoot());
    }

    public function getRootDataProvider()
    {
        return [
            ['/', ''],
            ['/index.php/', 'index.php'],
            ['/basercms/index.php/', 'basercms/index.php']
        ];
    }

    /**
     * ヘッダーテンプレートを出力する
     * @since basercms4
     *
     * @return void
     */
    public function testHeader()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->expectOutputRegex('/<div id="Header">.*<a href="\/sitemap">サイトマップ<\/a>.*<\/li>.*<\/ul>.*<\/div>.*<\/div>/s');
        $this->BcBaser->header();
    }

    /**
     * フッターテンプレートを出力する
     * @since basercms4
     * @return void
     */
    public function testFooter()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->expectOutputRegex('/<div id="Footer">.*<img src="\/img\/cake.power.gif".*<\/a>.*<\/p>.*<\/div>/s');
        $this->BcBaser->footer();
    }

    /**
     * ページネーションを出力する
     * @since basercms4
     * @return void
     */
    public function testPagination()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->expectOutputRegex('/<div class="pagination">/');
        $this->BcBaser->request->params['paging']['Model'] = [
            'count' => 100,
            'pageCount' => 3,
            'page' => 2,
            'limit' => 10,
            'current' => null,
            'prevPage' => 1,
            'nextPage' => 3,
            'options' => [],
            'paramType' => 'named'
        ];
        $this->BcBaser->pagination();
    }

    /**
     * コンテンツ本体を出力する
     * @since basercms4
     * @return void
     */
    public function testContent()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->expectOutputString('コンテンツ本体');
        $this->_View->assign('content', 'コンテンツ本体');
        $this->BcBaser->content();
    }

    /**
     * コンテンツ内で設定した CSS や javascript をレイアウトテンプレートに出力する
     * @since basercms4
     * @return void
     */
    public function testScripts()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $themeConfigTag = '<link rel="stylesheet" type="text/css" href="/files/theme_configs/config.css" />';

        // CSS
        $expected = "\n" . '<meta name="generator" content="basercms"/><link rel="stylesheet" type="text/css" href="/css/admin/layout.css"/>';
        $this->BcBaser->css('admin/layout', ['inline' => false]);
        ob_start();
        $this->BcBaser->scripts();
        $result = ob_get_clean();
        $result = str_replace($themeConfigTag, '', $result);
        $this->assertEquals($expected, $result);
        $this->_View->assign('css', '');

        Configure::write('BcApp.outputMetaGenerator', false);

        // Javascript
        $expected = '<script type="text/javascript" src="/js/admin/startup.js"></script>';
        $this->BcBaser->js('admin/startup', false);
        ob_start();
        $this->BcBaser->scripts();
        $result = ob_get_clean();
        $result = str_replace($themeConfigTag, '', $result);
        $this->assertEquals($expected, $result);
        $this->_View->assign('script', '');

        // meta
        $expected = '<meta name="description" content="説明文"/>';
        App::uses('BcHtmlHelper', 'View/Helper');
        $BcHtml = new BcHtmlHelper($this->_View);
        $BcHtml->meta('description', '説明文', ['inline' => false]);
        ob_start();
        $this->BcBaser->scripts();
        $result = ob_get_clean();
        $result = str_replace($themeConfigTag, '', $result);
        $this->assertEquals($expected, $result);
        $this->_View->assign('meta', '');

        // ツールバー
        $expected = '<link rel="stylesheet" type="text/css" href="/css/admin/toolbar.css"/>';
        $this->BcBaser->set('user', ['User']);
        ob_start();
        $this->BcBaser->scripts();
        $result = ob_get_clean();
        $result = str_replace($themeConfigTag, '', $result);
        $this->assertEquals($expected, $result);
    }

    /**
     * ツールバーエレメントや CakePHP のデバッグ出力を表示
     * @since basercms4
     * @return void
     */
    public function testFunc()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        Configure::write('debug', 0);

        // 未ログイン
        ob_start();
        $this->BcBaser->func();
        $result = ob_get_clean();
        $this->assertEquals('', $result);

        // ログイン中
        $expects = '<div id="ToolBar">';
        $this->_login();
        $this->BcBaser->set('currentPrefix', 'admin');
        $this->BcBaser->set('currentUserAuthPrefixes', ['admin']);
        ob_start();
        $this->BcBaser->func();
        $result = ob_get_clean();
        $this->assertTextContains($expects, $result);
        $this->_logout();

        // デバッグモード２
        $expects = '<table class="cake-sql-log"';
        Configure::write('debug', 2);
        ob_start();
        $this->BcBaser->func();
        $result = ob_get_clean();
        $this->assertTextContains($expects, $result);
    }

    /**
     * サブメニューを設定する
     * @since basercms4
     * @param array $elements サブメニューエレメント名を配列で指定
     * @param array $expects 期待するサブメニュータイトル
     * @return void
     * @dataProvider setSubMenusDataProvider
     */
    public function testSetSubMenus($elements, $expects)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->_View->subDir = 'admin';
        $this->BcBaser->setSubMenus($elements);
        ob_start();
        $this->BcBaser->subMenu();
        $result = ob_get_clean();
        foreach($expects as $expect) {
            $this->assertTextContains($expect, $result);
        }
    }

    public function setSubMenusDataProvider()
    {
        return [
            [['contents'], ['<th>コンテンツメニュー</th>']],
            [['editor_templates', 'site_configs'], ['<th>エディタテンプレートメニュー</th>', '<th>システム設定メニュー</th>']],
            [['tools'], ['<th>ユーティリティメニュー</th>']],
            [['plugins', 'themes'], ['<th>プラグイン管理メニュー</th>', '<th>テーマ管理メニュー</th>']],
            [['users'], ['<th>ユーザー管理メニュー</th>']],
            [['widget_areas'], ['<th>ウィジェットエリア管理メニュー</th>']],
        ];
    }

    /**
     * XMLヘッダタグを出力する
     * @since basercms4
     * @param string $expected 期待値
     * @param string $url URL
     * @return void
     * @dataProvider xmlDataProvider
     */
    public function testXmlHeader($expected, $url = null)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->BcBaser->request = $this->_getRequest($url);
        $this->expectOutputString($expected);
        $this->BcBaser->xmlHeader();
    }

    public function xmlDataProvider()
    {
        return [
            ['<?xml version="1.0" encoding="UTF-8" ?>' . "\n", '/'],
            ['<?xml version="1.0" encoding="Shift-JIS" ?>' . "\n", '/m/']
        ];
    }

    /**
     * アイコン（favicon）タグを出力する
     * @since basercms4
     * @return void
     */
    public function testIcon()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->expectOutputString('<link href="/favicon.ico" type="image/x-icon" rel="icon"/><link href="/favicon.ico" type="image/x-icon" rel="shortcut icon"/>' . "\n");
        $this->BcBaser->icon();
    }

    /**
     * ドキュメントタイプを指定するタグを出力する
     * @since basercms4
     * @param string $docType ドキュメントタイプ
     * @param string $expected ドキュメントタイプを指定するタグ
     * @return void
     * @dataProvider docTypeDataProvider
     */
    public function testDocType($docType, $expected)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->expectOutputString($expected . "\n");
        $this->BcBaser->docType($docType);
    }

    public function docTypeDataProvider()
    {
        return [
            ['xhtml-trans', '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'],
            ['html5', '<!DOCTYPE html>']
        ];
    }

    /**
     * CSSの読み込みタグを出力する
     * @since basercms4
     * @return void
     */
    public function testCss()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        // ノーマル
        ob_start();
        $this->BcBaser->css('admin/import');
        $result = ob_get_clean();
        $expected = '<link rel="stylesheet" type="text/css" href="/css/admin/import.css"/>';
        $this->assertEquals($expected, $result);
        // 拡張子あり
        ob_start();
        $this->BcBaser->css('admin/import.css');
        $result = ob_get_clean();
        $expected = '<link rel="stylesheet" type="text/css" href="/css/admin/import.css"/>';
        $this->assertEquals($expected, $result);
        // インラインオフ（array）
        $this->BcBaser->css('admin/import.css', ['inline' => false]);
        $expected = '<link rel="stylesheet" type="text/css" href="/css/admin/import.css"/>';
        $result = $this->_View->Blocks->get('css');
        $this->assertEquals($expected, $result);
        $this->_View->Blocks->end();
        // インラインオフ（boolean）
        $this->BcBaser->css('admin/import.css', false);
        $expected = '<link rel="stylesheet" type="text/css" href="/css/admin/import.css"/>';
        $this->_View->assign('css', '');
        $this->assertEquals($expected, $result);
    }

    /**
     * JSの読み込みタグを出力する（インラインオフ）
     * @since basercms4
     * @return void
     */
    public function testJsNonInline()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        // インラインオフ（boolean）
        $this->BcBaser->js('admin/function', false);
        $expected = '<script type="text/javascript" src="/js/admin/function.js"></script>';
        $result = $this->_View->fetch('script');
        $this->assertEquals($expected, $result);
    }

    /**
     * SSL通信かどうか判定する
     * @since basercms4
     * @return void
     */
    public function testIsSSL()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $_SERVER['HTTPS'] = true;
        $this->BcBaser->request = $this->_getRequest('https://localhost/');
        $this->assertEquals(true, $this->BcBaser->isSSL());
    }

    /**
     * charset メタタグを出力する
     * @since basercms4
     * @param string $expected 期待値
     * @param string $encoding エンコード
     * @param string $url URL
     * @return void
     * @dataProvider charsetDataProvider
     */
    public function testCharset($expected, $encoding, $url = null)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->BcBaser->request = $this->_getRequest($url);
        $this->expectOutputString($expected);
        if ($encoding !== null) {
            $this->BcBaser->charset($encoding);
        } else {
            $this->BcBaser->charset();
        }
    }

    public function charsetDataProvider()
    {
        return [
            ['<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />', 'UTF-8', '/'],
            ['<meta http-equiv="Content-Type" content="text/html; charset=Shift-JIS" />', null, '/m/']
        ];
    }

    /**
     * コピーライト用の年を出力する
     * @since basercms4
     * @param string $expected 期待値
     * @param mixed $begin 開始年
     * @return void
     * @dataProvider copyYearDataProvider
     */
    public function testCopyYear($expected, $begin)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->expectOutputString($expected);
        $this->BcBaser->copyYear($begin);
    }

    public function copyYearDataProvider()
    {
        $year = date('Y');
        return [
            ["2000 - {$year}", 2000],
            [$year, 'はーい']
        ];
    }



}
