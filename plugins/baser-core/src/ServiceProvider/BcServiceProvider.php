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

namespace BaserCore\ServiceProvider;

use BaserCore\Service\UserManageService;
use BaserCore\Service\UserManageServiceInterface;
use BaserCore\Service\UsersService;
use BaserCore\Service\UsersServiceInterface;
use BaserCore\Service\UserGroupManageService;
use BaserCore\Service\UserGroupManageServiceInterface;
use BaserCore\Service\UserGroupsService;
use BaserCore\Service\UserGroupsServiceInterface;
use BaserCore\Service\PluginsServiceInterface;
use BaserCore\Service\PluginsService;
use BaserCore\Service\PluginManageServiceInterface;
use BaserCore\Service\PluginManageService;
use Cake\Core\ServiceProvider;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class BcServiceProvider
 * @package BaserCore\ServiceProvider
 */
class BcServiceProvider extends ServiceProvider
{

    /**
     * Provides
     * @var string[]
     */
    protected $provides = [
        UsersServiceInterface::class,
        UserManageServiceInterface::class,
        UserGroupsServiceInterface::class,
        UserGroupManageServiceInterface::class,
        PluginsServiceInterface::class,
        PluginManageServiceInterface::class,

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
        // Usersサービス
        $container->add(UsersServiceInterface::class, UsersService::class);
        $container->add(UserManageServiceInterface::class, UserManageService::class);
        // UserGroupsサービス
        $container->add(UserGroupsServiceInterface::class, UserGroupsService::class);
        $container->add(UserGroupManageServiceInterface::class, UserGroupManageService::class);
        // Pluginsサービス
        $container->add(PluginsServiceInterface::class, PluginsService::class);
        $container->add(PluginManageServiceInterface::class, PluginManageService::class);
    }
}
