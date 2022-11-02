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

namespace BaserCore\Service;

use BaserCore\Error\BcException;
use BaserCore\Model\Entity\Plugin;
use BaserCore\Model\Table\PluginsTable;
use BaserCore\Utility\BcZip;
use Cake\Cache\Cache;
use Cake\Http\Client;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\Core\Configure;
use BaserCore\Utility\BcUtil;
use Cake\Core\App;
use Cake\Filesystem\Folder;
use Cake\Core\Plugin as CakePlugin;
use Cake\Datasource\EntityInterface;
use Cake\Utility\Xml;
use Exception;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;

/**
 * Class PluginsService
 * @property PluginsTable $Plugins
 */
class PluginsService implements PluginsServiceInterface
{

    /**
     * Plugins Table
     * @var \Cake\ORM\Table
     */
    public $Plugins;

    /**
     * PluginsService constructor.
     * 
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct()
    {
        $this->Plugins = TableRegistry::getTableLocator()->get('BaserCore.Plugins');
    }

    /**
     * プラグインを取得する
     * 
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get($id): EntityInterface
    {
        return $this->Plugins->get($id);
    }

    /**
     * プラグイン一覧を取得
     * 
     * @param string $sortMode
     * @return array $plugins
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getIndex(string $sortMode): array
    {
        $plugins = $this->Plugins->find()
            ->order(['priority'])
            ->all()
            ->toArray();
        if ($sortMode) {
            return $plugins;
        } else {
            $registeredName = Hash::extract($plugins, '{n}.name');
            // DBに登録されてないもの含めて、プラグインフォルダから取得
            if (!$plugins) {
                $plugins = [];
            }
            $paths = App::path('plugins');
            foreach($paths as $path) {
                $Folder = new Folder($path);
                $files = $Folder->read(true, true, true);
                foreach($files[0] as $file) {
                    $name = Inflector::camelize(Inflector::underscore(basename($file)));
                    if (in_array(Inflector::camelize(basename($file), '-'), Configure::read('BcApp.core'))) continue;
                    if(in_array($name, $registeredName)) {
                        $plugins[array_search($name, $registeredName)] = $this->Plugins->getPluginConfig($name);
                    } else {
                        $plugin = $this->Plugins->getPluginConfig($name);
                        if(in_array($plugin->type, ['CorePlugin', 'Plugin'])) {
                            $plugins[] = $plugin;
                        }
                    }
                }
            }
            return $plugins;
        }
    }

    /**
     * プラグインをインストールする
     * 
     * @param string $name プラグイン名
     * @param string $connection test connection指定用
     * @return bool|null
     * @throws Exception
     * @checked
     * @noTodo
     * @unitTest
     */
    public function install($name, $connection = 'default'): ?bool
    {
        if($connection) {
            $options = ['connection' => $connection];
        } else {
            $options = [];
        }
        BcUtil::includePluginClass($name);
        $plugins = CakePlugin::getCollection();
        $plugin = $plugins->create($name);
        if (!method_exists($plugin, 'install')) {
            throw new Exception(__d('baser', 'プラグインに Plugin クラスが存在しません。src ディレクトリ配下に作成してください。'));
        } else {
            return $plugin->install($options);
        }
    }

    /**
     * プラグインをアップデートする
     * 
     * @param string $name プラグイン名
     * @param string $connection コネクション名
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update($name, $connection = 'default'): ?bool
    {
        $options = ['connection' => $connection];
        BcUtil::includePluginClass($name);

        if (function_exists('ini_set')) {
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', '512M');
        }
        if(file_exists(LOGS . 'update.log')) {
            unlink(LOGS . 'update.log');
        }

        if ($name === 'BaserCore') {
            $names = array_merge(['BaserCore'], Configure::read('BcApp.corePlugins'));
            $ids = $this->detachAll();
        } else {
            $names = [$name];
        }

        $result = true;
        $pluginCollection = CakePlugin::getCollection();
        foreach($names as $name) {
            if($name !== 'BaserCore') {
                $entity = $this->Plugins->getPluginConfig($name);
                if(!$entity->registered) continue;
            }
            $plugin = $pluginCollection->create($name);
            if (!method_exists($plugin, 'update')) {
                throw new Exception(__d('baser', 'プラグインに Plugin クラスが存在しません。src ディレクトリ配下に作成してください。'));
            } else {
                if(!$plugin->update($options)) {
                    $result = false;
                }
            }
        }

        if ($name === 'BaserCore') {
            $this->attachAllFromIds($ids);
        }

        return $result;
    }

    /**
     * プラグインを全て無効化する
     * 
     * @return array 無効化したIDのリスト
     * @checked
     * @noTodo
     * @unitTest
     */
    public function detachAll()
    {
        $plugins = $this->Plugins->find()->where(['status' => true])->all();
        $ids = [];
        if ($plugins) {
            foreach($plugins as $plugin) {
                $ids[] = $plugin->id;
                $plugin->status = false;
                $this->Plugins->save($plugin);
            }
        }
        return $ids;
    }

    /**
     * 複数のIDからプラグインを有効化する
     * 
     * @param $ids
     * @checked
     * @noTodo
     * @unitTest
     */
    public function attachAllFromIds($ids)
    {
        if (!$ids) {
            return;
        }
        foreach($ids as $id) {
            $this->Plugins->save(new Plugin(['id' => $id, 'status' => true]));
        }
    }

    /**
     * バージョンを取得する
     * 
     * @param $name
     * @return mixed|string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getVersion($name)
    {
        $plugin = $this->Plugins->find()->where(['name' => $name])->first();
        if ($plugin) {
            return $plugin->version;
        } else {
            return '';
        }
    }

    /**
     * プラグインを無効にする
     * 
     * @param string $name
     * @checked
     * @noTodo
     * @unitTest
     */
    public function detach(string $name): bool
    {
        return $this->Plugins->detach($name);
    }

    /**
     * プラグインを有効にする
     * 
     * @param string $name
     * @checked
     * @noTodo
     * @unitTest PluginsTable::attach() のテストに委ねる
     */
    public function attach(string $name): bool
    {
        return $this->Plugins->attach($name);
    }

    /**
     * プラグイン名からプラグインエンティティを取得
     * 
     * @param string $name
     * @return array|EntityInterface|null
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getByName(string $name)
    {
        return $this->Plugins->find()->where(['name' => $name])->first();
    }

    /**
     * データベースをリセットする
     *
     * @param string $name
     * @param string $connection
     * @throws Exception
     * @checked
     * @noTodo
     * @unitTest
     */
    public function resetDb(string $name, $connection = 'default'): void
    {
        $options = ['connection' => $connection];
        unset($options['name']);
        $plugin = $this->Plugins->find()
            ->where(['name' => $name])
            ->first();

        BcUtil::includePluginClass($plugin->name);
        $plugins = CakePlugin::getCollection();
        $pluginClass = $plugins->create($plugin->name);
        if (!method_exists($pluginClass, 'rollbackDb')) {
            throw new Exception(__d('baser', 'プラグインに Plugin クラスが存在しません。手動で削除してください。'));
        }

        $plugin->db_init = false;
        if (!$pluginClass->rollbackDb($options) || !$this->Plugins->save($plugin)) {
            throw new Exception(__d('baser', '処理中にエラーが発生しました。プラグインの開発者に確認してください。'));
        }
        BcUtil::clearAllCache();
    }

    /**
     * プラグインを削除する
     * 
     * @param string $name
     * @param array $connection
     * @checked
     * @noTodo
     * @unitTest
     */
    public function uninstall(string $name, $connection = 'default'): void
    {
        $options = ['connection' => $connection];
        $name = rawurldecode($name);
        BcUtil::includePluginClass($name);
        $plugins = CakePlugin::getCollection();
        $plugin = $plugins->create($name);
        if (!$plugin->uninstall($options)) {
            throw new Exception(__d('baser', 'プラグインの削除に失敗しました。'));
        }
        if (!method_exists($plugin, 'uninstall')) {
            throw new Exception(__d('baser', 'プラグインに Plugin クラスが存在しません。手動で削除してください。'));
        }
    }

    /**
     * 優先度を変更する
     * 
     * @param int $id
     * @param int $offset
     * @param array $conditions
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function changePriority(int $id, int $offset, array $conditions = []): bool
    {
        $result = $this->Plugins->changeSort($id, $offset, [
            'conditions' => $conditions,
            'sortFieldName' => 'priority',
        ]);
        return $result;
    }

    /**
     * baserマーケットのプラグイン一覧を取得する
     * 
     * @return array|mixed
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getMarketPlugins(): array
    {
        if (Configure::read('debug') > 0) {
            Cache::delete('baserMarketPlugins');
        }
        if (!($baserPlugins = Cache::read('baserMarketPlugins', '_bc_env_'))) {
            $Xml = new Xml();
            try {
                $client = new Client([
                    'host' => '',
                    'redirect' => true,
                ]);
                $response = $client->get(Configure::read('BcLinks.marketPluginRss'));
                $baserPlugins = $Xml->build($response->getBody()->getContents());
                $baserPlugins = $Xml->toArray($baserPlugins->channel);
                $baserPlugins = $baserPlugins['channel']['item'];
            } catch (Exception $e) {
                return [];
            }
            Cache::write('baserMarketPlugins', $baserPlugins, '_bc_env_');
        }
        if ($baserPlugins) {
            return $baserPlugins;
        }
        return [];
    }

    /**
     * ユーザーグループにアクセス許可設定を追加する
     *
     * @param array $data リクエストデータ
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function allow($data): void
    {
        $permissions = TableRegistry::getTableLocator()->get('BaserCore.Permissions');
        $userGroups = $permissions->UserGroups->find('all')->where(['UserGroups.id <>' => Configure::read('BcApp.adminGroupId')]);
        if (!$userGroups) {
            return;
        }

        foreach($userGroups as $userGroup) {

            $permissionAuthPrefix = $permissions->UserGroups->getAuthPrefix($userGroup->id);
            $url = '/baser/' . $permissionAuthPrefix . '/' . Inflector::underscore($data['name']) . '/*';

            $prePermissions = $permissions->find()->where(['url' => $url])->first();
            switch($data['permission']) {
                case 1:
                    if (!$prePermissions) {
                        $permission = $permissions->newEmptyEntity();
                        $permission->name = $data['title'] . ' ' . __d('baser', '管理');
                        $permission->user_group_id = $userGroup->id;
                        $permission->auth = 1;
                        $permission->status = 1;
                        $permission->url = $url;
                        $permission->no = $permissions->getMax('no', ['user_group_id' => $userGroup->id]) + 1;
                        $permission->sort = $permissions->getMax('sort', ['user_group_id' => $userGroup->id]) + 1;
                        $permissions->save($permission);
                    }
                    break;
                case 2:
                    if ($prePermissions) {
                        $permissions->delete($prePermissions->id);
                    }
                    break;
            }
        }
    }

    /**
     * インストールに関するメッセージを取得する
     *
     * @param $pluginName
     * @return string
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getInstallStatusMessage($pluginName): string
    {
        $pluginName = rawurldecode($pluginName);
        $installedPlugin = $this->Plugins->find()->where([
            'name' => $pluginName,
            'status' => true,
        ])->first();

        // 既にプラグインがインストール済み
        if ($installedPlugin) {
            return '既にインストール済のプラグインです。';
        }

        $paths = App::path('plugins');
        $existsPluginFolder = false;
        $folder = $pluginName;
        foreach($paths as $path) {
            if (!is_dir($path . $folder)) {
                $dasherize = Inflector::dasherize($folder);
                if (!is_dir($path . $dasherize)) {
                    continue;
                }
                $folder = $dasherize;
            }
            $existsPluginFolder = true;
            $configPath = $path . $folder . DS . 'config.php';
            if (file_exists($configPath)) {
                $config = include $configPath;
            }
            break;
        }

        // プラグインのフォルダが存在しない
        if (!$existsPluginFolder) {
            return 'インストールしようとしているプラグインのフォルダが存在しません。';
        }

        // インストールしようとしているプラグイン名と、設定ファイル内のプラグイン名が違う
        if (!empty($config['name']) && $pluginName !== $config['name']) {
            return 'このプラグイン名のフォルダ名を' . $config['name'] . 'にしてください。';
        }
        return '';
    }

    /**
     * 一括処理
     * 
     * @param array $ids
     * @return bool
     * @checked
     * @unitTest
     * @noTodo
     */
    public function batch(string $method, array $ids): bool
    {
        if (!$ids) return true;
        $db = $this->Plugins->getConnection();
        $db->begin();
        foreach($ids as $id) {
            $plugin = $this->Plugins->get($id);
            if (!$this->$method($plugin->name)) {
                $db->rollback();
                throw new BcException(__d('baser', 'データベース処理中にエラーが発生しました。'));
            }
        }
        $db->commit();
        return true;
    }

    /**
     * IDを指定して名前リストを取得する
     * 
     * @param $ids
     * @return array
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getNamesById($ids): array
    {
        return $this->Plugins->find('list')->where(['id IN' => $ids])->toArray();
    }

    /**
     * プラグインをアップロードする
     *
     * POSTデータにて キー`file` で Zipファイルをアップロードとすると、
     * /plugins/ 内に、Zipファイルを展開して配置する。
     *
     * ### エラー
     * post_max_size　を超えた場合、サーバーに設定されているサイズ制限を超えた場合、
     * Zipファイルの展開に失敗した場合は、Exception を発生。
     *
     * ### リネーム処理
     * 展開後のフォルダー名はアッパーキャメルケースにリネームする。
     * 既に /plugins/ 内に同名のプラグインが存在する場合には、数字付きのディレクトリ名（PluginName2）にリネームする。
     * 数字付きのディレクトリ名にリネームする際、プラグイン内の Plugin クラスの namespace もリネームする。
     *
     * @param array $postData
     * @return string Zip を展開したフォルダ名
     * @checked
     * @noTodo
     * @unitTest
     * @throws BcException
     */
    public function add(array $postData)
    {
        if (BcUtil::isOverPostSize()) {
            throw new BcException(__d(
                'baser',
                '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。',
                ini_get('post_max_size')
            ));
        }
        if (empty($_FILES['file']['tmp_name'])) {
            $message = '';
            if ($postData['file']->getError() === 1) {
                $message = __d('baser', 'サーバに設定されているサイズ制限を超えています。');
            }
            throw new BcException($message);
        }
        $name = $postData['file']->getClientFileName();
        $postData['file']->moveTo(TMP . $name);
        $srcName = basename($name, '.zip');
        $zip = new BcZip();
        if (!$zip->extract(TMP . $name, TMP)) {
            throw new BcException(__d('baser', 'アップロードしたZIPファイルの展開に失敗しました。'));
        }

        $dstName = Inflector::camelize($srcName);
        if(preg_match('/^(.+?)([0-9]+)$/', $dstName, $matches)) {
            $baseName = $matches[1];
            $num = $matches[2];
        } else {
            $baseName = $dstName;
            $num = null;
        }
        while(is_dir(BASER_PLUGINS . $dstName) || is_dir(BASER_THEMES . Inflector::dasherize($dstName))) {
            if(is_null($num)) {
                $num = 1;
            }
            $num++;
            $dstName = Inflector::camelize($baseName) . $num;
        }
        $folder = new Folder(TMP . $srcName);
        $folder->move(BASER_PLUGINS . $dstName, ['mode' => 0777]);
        unlink(TMP . $name);
        BcUtil::changePluginNameSpace($dstName);
        return $dstName;
    }

}
