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

namespace BcSearchIndex\ServiceProvider;


use BcSearchIndex\Service\SearchIndexesAdminService;
use BcSearchIndex\Service\SearchIndexesAdminServiceInterface;
use BcSearchIndex\Service\SearchIndexesService;
use BcSearchIndex\Service\SearchIndexesServiceInterface;
use Cake\Core\ServiceProvider;

/**
 * Class BcSearchIndexServiceProvider
 */
class BcSearchIndexServiceProvider extends ServiceProvider
{

    /**
     * Provides
     * @var string[]
     */
    protected $provides = [
        SearchIndexesServiceInterface::class,
        SearchIndexesAdminServiceInterface::class,
    ];

    /**
     * Services
     * @param \Cake\Core\ContainerInterface $container
     * @checked
     * @noTodo
     * @unitTest
     */
    public function services($container): void
    {
        $container->defaultToShared(true);
        // SearchIndexesサービス
        $container->add(SearchIndexesServiceInterface::class, SearchIndexesService::class);
        $container->add(SearchIndexesAdminServiceInterface::class, SearchIndexesAdminService::class);
    }

}
