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

namespace BaserCore\Service;

use Cake\Datasource\EntityInterface;
use Exception;

/**
 * Interface PluginsServiceInterface
 * @package BaserCore\Service
 */
interface PluginsServiceInterface
{

    /**
     * プラグインを取得する
     * @param int $id
     * @return EntityInterface
     */
    public function get($id): EntityInterface;

    /**
     * プラグイン一覧を取得
     * @param string $sortMode
     * @return array $plugins
     */
    public function getIndex(string $sortMode): array;

    /**
     * プラグインをインストールする
     * @param string $name プラグイン名
     * @param string $connection test connection指定用
     * @return bool|null
     */
    public function install($name, $connection = 'default'): ?bool;

    /**
     * プラグインを無効にする
     * @param string $name
     */
    public function detach(string $name): bool;

    /**
     * プラグイン名からプラグインエンティティを取得
     * @param string $name
     * @return array|EntityInterface|null
     */
    public function getByName(string $name);

    /**
     * データベースをリセットする
     * @param string $name
     * @param array $connection
     * @throws Exception
     */
    public function resetDb(string $name, $connection = 'default'): void;

    /**
     * プラグインを削除する
     * @param string $name
     * @param string $connection
     */
    public function uninstall(string $name, $connection = 'default'): void;

    /**
     * 優先度を変更する
     * @param int $id
     * @param int $offset
     * @param array $conditions
     * @return bool
     */
    public function changePriority(int $id, int $offset, array $conditions = []): bool;

    /**
     * baserマーケットのプラグイン一覧を取得する
     * @return array|mixed
     */
    public function getMarketPlugins(): array;

    /**
     * ユーザーグループにアクセス許可設定を追加する
     *
     * @param array $data リクエストデータ
     * @return void
     */
    public function allow($data): void;
}
