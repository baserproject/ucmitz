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


}
