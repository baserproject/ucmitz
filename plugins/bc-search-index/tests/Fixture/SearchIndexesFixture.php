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
namespace BcSearchIndex\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SearchIndexFixture
 */
class SearchIndexesFixture extends TestFixture
{

    /**
     * Import
     *
     * @var array
     */
    public $import = ['table' => 'search_indexes'];
    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'type' => 'ページ',
            'model' => 'Page',
            'model_id' => 16,
            'site_id' => null,
            'content_id' => null,
            'content_filter_id' => null,
            'lft' => null,
            'rght' => null,
            'title' => '会社案内',
            'detail' => 'baserCMS inc.の会社案内ページ 会社案内会社データ会社名baserCMS inc.  [デモ]設立2009年11月所在地福岡県福岡市博多区博多駅前（ダミー）事業内容インターネットサービス業（ダミー）Webサイト制作事業（ダミー）WEBシステム開発事業（ダミー）アクセスマップ※ JavaScript を有効にしてください。var latlng = new google.maps.LatLng(33.6065756,130.4182970);var options = {zoom: 16,center: latlng,mapTypeId: google.maps.MapTypeId.ROADMAP,navigationControl: true,mapTypeControl: true,scaleControl: true,scrollwheel: false,};var map = new google.maps.Map(document.getElementById("map"), options);var marker = new google.maps.Marker({position: latlng,map: map,title:"baserCMS inc. [デモ]"});var infowindow = new google.maps.InfoWindow({content: "baserCMS inc. [デモ]福岡県""});infowindow.open(map,marker);google.maps.event.addListener(marker, "click", function() {infowindow.open(map,marker);});',
            'url' => '/about',
            'status' => 1,
            'priority' => 0.5,
            'created' => '2016-07-21 11:49:19',
            'modified' => NULL,
        ],
        [
            'id' => 2,
            'type' => 'ページ',
            'model' => 'Page',
            'model_id' => 3,
            'site_id' => null,
            'content_id' => null,
            'content_filter_id' => null,
            'lft' => null,
            'rght' => null,
            'title' => 'サービス',
            'detail' => 'baserCMS inc.のサービス紹介ページ。 サービスサービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。サービスの案内文がはいります。',
            'url' => '/service',
            'status' => 1,
            'priority' => 0.5,
            'created' => '2016-07-21 11:49:19',
            'modified' => NULL,
        ],
        [
            'id' => 3,
            'type' => 'ページ',
            'model' => 'Page',
            'model_id' => 4,
            'site_id' => null,
            'content_id' => null,
            'content_filter_id' => null,
            'lft' => null,
            'rght' => null,
            'title' => 'アイコンの使い方',
            'detail' => '50種類のアイコンを自由にカスタマイズしよう。 アイコンの使い方50種類のアイコンを自由にカスタマイズしよう。&nbsp;&nbsp;まずは、nada-works-png.zip をダウンロードして解凍します。&nbsp;&nbsp;icons_ico_.pngをFireworksで開くと下記の50種類のアイコンがレイヤー分けされています。&nbsp;&nbsp;&nbsp;&nbsp;カスタマイズ1：ベースの形を変える。&nbsp;&nbsp;&nbsp;カスタマイズ2：色を変える。&nbsp;&nbsp;&nbsp;カスタマイズ3：パスを使って変形させる。（上級者向け）パスで作成しています。自由に変形させることが可能です。&nbsp;&nbsp;&nbsp;&nbsp;パターン例：各コンテンツで色を変える。同じアイコンを使用する、など&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;文字と写真を変えるだけで完成！かんたんバナーを作ろう。&nbsp;&nbsp;icons_banner_00.png、icons_banner_l_00.pngをFireworksで開くと各要素をレイヤー分けされています。言葉、フォント、色、画像を変更してオリジナルのバナーを作成することができます。画像は「シンボル」にて配置しています。差し替えたい画像をシンボル化し、「シンボルを入れ替え」にて差し替えてご使用ください。&nbsp;&nbsp;例：言葉、フォントの変更、画像差し替え&nbsp;',
            'url' => '/icons',
            'status' => 1,
            'priority' => 0.5,
            'created' => '2016-07-21 11:49:19',
            'modified' => NULL,
        ],
        [
            'id' => 4,
            'type' => 'ブログ',
            'model' => 'BlogContent',
            'model_id' => 1,
            'site_id' => null,
            'content_id' => null,
            'content_filter_id' => null,
            'lft' => null,
            'rght' => null,
            'title' => 'ニュースリリース',
            'detail' => 'Baser CMS inc. [デモ] の最新のニュースリリースをお届けします。',
            'url' => '/news/index',
            'status' => 1,
            'priority' => 0.5,
            'created' => '2016-07-21 11:49:19',
            'modified' => NULL,
        ],
        [
            'id' => 5,
            'type' => 'メール',
            'model' => 'MailContent',
            'model_id' => 1,
            'site_id' => null,
            'content_id' => null,
            'content_filter_id' => null,
            'lft' => null,
            'rght' => null,
            'title' => 'お問い合わせ',
            'detail' => '* 印の項目は必須となりますので、必ず入力してください。',
            'url' => '/contact/index',
            'status' => 1,
            'priority' => 0.5,
            'created' => '2016-07-21 11:49:19',
            'modified' => NULL,
        ],
        [
            'id' => 6,
            'type' => 'ページ',
            'model' => 'Page',
            'model_id' => 1,
            'site_id' => null,
            'content_id' => null,
            'content_filter_id' => null,
            'lft' => null,
            'rght' => null,
            'title' => 'Index',
            'detail' => 'NEWS RELEASEbaserCMS NEWS',
            'url' => '/index',
            'status' => 1,
            'priority' => 0.5,
            'created' => '2016-07-21 11:49:19',
            'modified' => NULL,
        ],
        [
            'id' => 7,
            'type' => 'ページ',
            'model' => 'Page',
            'model_id' => 5,
            'site_id' => null,
            'content_id' => null,
            'content_filter_id' => null,
            'lft' => null,
            'rght' => null,
            'title' => 'サイトマップ',
            'detail' => 'baserCMS inc.のサイトマップページ サイトマップ会社案内サービスアイコンの使い方サイトマップ新着情報お問い合わせ',
            'url' => '/sitemap',
            'status' => 1,
            'priority' => 0.5,
            'created' => '2016-07-21 11:49:19',
            'modified' => NULL,
        ],
        [
            'id' => 8,
            'type' => 'ページ',
            'model' => 'Page',
            'model_id' => 5,
            'site_id' => null,
            'content_id' => null,
            'content_filter_id' => null,
            'lft' => null,
            'rght' => null,
            'title' => 'サービス１',
            'detail' => 'baserCMS inc.のサイトマップページ サイトマップ会社案内サービスアイコンの使い方サイトマップ新着情報お問い合わせ',
            'url' => '/service/service1',
            'status' => 1,
            'priority' => 0.5,
            'created' => '2016-07-21 11:49:19',
            'modified' => NULL,
        ],
        [
            'id' => 9,
            'type' => 'ページ',
            'model' => 'Page',
            'model_id' => 6,
            'site_id' => null,
            'content_id' => null,
            'content_filter_id' => null,
            'lft' => null,
            'rght' => null,
            'title' => 'サービス２',
            'detail' => 'baserCMS inc.のサイトマップページ サイトマップ会社案内サービスアイコンの使い方サイトマップ新着情報お問い合わせ',
            'url' => '/service/service2',
            'status' => 1,
            'priority' => 0.5,
            'created' => '2016-07-21 11:49:19',
            'modified' => NULL,
        ],
        [
            'id' => 10,
            'type' => 'ページ',
            'model' => 'Page',
            'model_id' => 7,
            'site_id' => null,
            'content_id' => null,
            'content_filter_id' => null,
            'lft' => null,
            'rght' => null,
            'title' => 'サービス３',
            'detail' => 'baserCMS inc.のサイトマップページ サイトマップ会社案内サービスアイコンの使い方サイトマップ新着情報お問い合わせ',
            'url' => '/service/service3',
            'status' => 1,
            'priority' => 0.5,
            'created' => '2016-07-21 11:49:19',
            'modified' => NULL,
        ],
    ];

}
