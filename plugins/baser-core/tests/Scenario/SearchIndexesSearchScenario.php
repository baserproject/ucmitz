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

use BcSearchIndex\Test\Factory\SearchIndexFactory;
use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;

/**
 * SearchIndexesSearchScenario
 *
 * 利用する場合は、テーブルの初期化に次のフィクスチャの定義が必要
 * - plugin.BaserCore.Factory/SearchIndexes
 */
class SearchIndexesSearchScenario implements FixtureScenarioInterface
{

    /**
     * load
     */
    public function load(...$args)
    {
        SearchIndexFactory::make(['id' => 1, 'title' => 'test data 1', 'type' => 'admin', 'site_id' => 1])->persist();
        SearchIndexFactory::make(['id' => 2, 'title' => 'test data 2', 'type' => 'admin', 'site_id' => 1])->persist();
        SearchIndexFactory::make(['id' => 3, 'title' => 'test data 3', 'priority' => '1', 'site_id' => 2])->persist();
        SearchIndexFactory::make(['id' => 4, 'title' => 'test data 4', 'priority' => '2', 'site_id' => 2])->persist();
        SearchIndexFactory::make([
            'id' => 5,
            'title' => 'test data 5',
            'site_id' => 3,
            'modified' => '2022-09-14 21:10:41',
        ])->persist();
        SearchIndexFactory::make([
            'id' => 6,
            'model' => 'Page',
            'title' => 'test data 6',
            'site_id' => 3,
            'modified' => '2022-09-15 21:10:41',
        ])->persist();
    }

}
