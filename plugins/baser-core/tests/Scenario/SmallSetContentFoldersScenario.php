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
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * SmallSetContentFolders
 *
 * 利用する場合は、テーブルの初期化に次のフィクスチャの定義が必要
 * - plugin.BaserCore.Factory/Contents
 * - plugin.BaserCore.Factory/ContentFolders
 */
class SmallSetContentFoldersScenario implements FixtureScenarioInterface
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
            'plugin' => 'BaserCore',
            'type' => 'ContentFolder',
            'site_id' => 1,
            'parent_id' => null,
            'lft' => 1,
            'rght' => 4,
            'entity_id' => 1,
            'site_root' => true,
            'status' => true
        ])->persist();
        ContentFactory::make([
            'id' => 2,
            'url' => '/service/',
            'name' => 'service',
            'plugin' => 'BaserCore',
            'type' => 'ContentFolder',
            'site_id' => 1,
            'parent_id' => 1,
            'lft' => 2,
            'rght' => 3,
            'entity_id' => 2,
            'site_root' => false,
            'status' => true
        ])->persist();
        ContentFactory::make([
            'id' => 3,
            'url' => '/',
            'name' => 'service',
            'plugin' => 'BaserCore',
            'type' => 'ContentFolder',
            'site_id' => 1,
            'parent_id' => null,
            'lft' => 4,
            'rght' => 5,
            'entity_id' => 3,
            'site_root' => false,
            'status' => true
        ])->persist();
        ContentFolderFactory::make(['id' => 1, 'folder_template' => 'folder template 1', 'page_template' => 'page template 1'])->persist();
        ContentFolderFactory::make(['id' => 2])->persist();
        ContentFolderFactory::make(['id' => 3])->persist();
    }

}
