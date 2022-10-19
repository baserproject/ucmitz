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

namespace BaserCore\Test\Scenario;

use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\ContentFolderFactory;
use BaserCore\Test\Factory\PageFactory;
use BcBlog\Test\Factory\BlogContentsFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * SmallSetContents
 *
 * コンテンツ、フォルダ、固定ページの最小限のデータセット
 * - /
 * - /index
 * - /about
 * - /service/
 * - /service/index
 * - /service/about
 *
 * 利用する場合は、テーブルの初期化に次のフィクスチャの定義が必要
 * - plugin.BaserCore.Factory/Contents
 * - plugin.BaserCore.Factory/ContentFolders
 * - plugin.BaserCore.Factory/Pages
 */
class SmallSetContentsScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args)
    {
        ContentFactory::make([
            'id' => 1,
            'url' => '/',
            'name' => '',
            'plugin' =>
            'BaserCore',
            'type' =>
            'ContentFolder',
            'site_id' => 1,
            'parent_id' => null,
            'lft' => 1,
            'rght' => 10,
            'entity_id' => 1,
            'site_root' => true,
            'status' => true
        ])->persist();
        ContentFactory::make([
            'id' => 2,
            'url' =>
            '/index',
            'name' =>
            'index',
            'plugin' =>
            'BaserCore',
            'type' =>
            'Page',
            'site_id' => 1,
            'parent_id' => 1,
            'lft' => 2,
            'rght' => 3,
            'entity_id' => 1,
            'site_root' => false,
            'status' => true
        ])->persist();
        ContentFactory::make([
            'id' => 3,
            'url' => '/service/',
            'name' => 'service',
            'plugin' => 'BaserCore',
            'type' => 'ContentFolder',
            'site_id' => 1,
            'parent_id' => 1,
            'lft' => 4,
            'rght' => 9,
            'entity_id' => 2,
            'site_root' => false,
            'status' => true
        ])->persist();
        ContentFactory::make([
            'id' => 4,
            'url' => '/service/index',
            'name' => 'index',
            'plugin' => 'BaserCore',
            'type' => 'Page',
            'site_id' => 1,
            'parent_id' => 3,
            'lft' => 5,
            'rght' => 6,
            'entity_id' => 2,
            'site_root' => false,
            'status' => true
        ])->persist();
        ContentFactory::make([
            'id' => 5,
            'url' => '/service/about',
            'name' => 'about',
            'plugin' => 'BaserCore',
            'type' => 'Page',
            'site_id' => 1,
            'parent_id' => 3,
            'lft' => 7,
            'rght' => 8,
            'entity_id' => 3,
            'site_root' => false,
            'status' => true
        ])->persist();
        ContentFactory::make([
            'id' => 6,
            'url' => '/blog/',
            'name' => 'blog',
            'plugin' => 'BcBlog',
            'type' => 'BlogContent',
            'site_id' => 1,
            'parent_id' => 3,
            'lft' => 7,
            'rght' => 8,
            'entity_id' => 1,
            'site_root' => false,
            'status' => true
        ])->persist();
        BlogContentsFactory::make([
            'id' => '1',
            'description' => 'baserCMS inc. [デモ] の最新の情報をお届けします。',
            'template' => 'default',
            'list_count' => '10',
            'list_direction' => 'DESC',
            'feed_count' => '10',
            'tag_use' => '1',
            'comment_use' => '1',
            'comment_approve' => '0',
            'auth_captcha' => '1',
            'widget_area' => '2',
            'eye_catch_size' => 'YTo0OntzOjExOiJ0aHVtYl93aWR0aCI7czozOiIzMDAiO3M6MTI6InRodW1iX2hlaWdodCI7czozOiIzMDAiO3M6MTg6Im1vYmlsZV90aHVtYl93aWR0aCI7czozOiIxMDAiO3M6MTk6Im1vYmlsZV90aHVtYl9oZWlnaHQiO3M6MzoiMTAwIjt9',
            'use_content' => '1',
            'created' => '2015-08-10 18:57:47',
            'modified' => NULL,
        ])->persist();
        ContentFolderFactory::make(['id' => 1])->persist();
        ContentFolderFactory::make(['id' => 2])->persist();
        PageFactory::make(['id' => 1])->persist();
        PageFactory::make(['id' => 2])->persist();
        PageFactory::make(['id' => 3])->persist();
    }

}
