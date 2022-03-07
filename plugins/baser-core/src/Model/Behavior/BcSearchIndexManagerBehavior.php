<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */
namespace BaserCore\Model\Behavior;

use ArrayObject;
use Cake\ORM\Behavior;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Cake\Event\EventInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Datasource\EntityInterface;

/**
 * Class BcSearchIndexManagerBehavior
 *
 * 検索インデックス管理ビヘイビア
 *
 * @package Baser.Model.Behavior
 */
class BcSearchIndexManagerBehavior extends Behavior
{

    /**
     * SearchIndex Model
     *
     * @var SearchIndex
     */
    public $SearchIndex = null;

    /**
     * initialize
     * @param  array $config
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        $this->Contents = TableRegistry::getTableLocator()->get('BaserCore.Contents');
    }

    /**
     * 検索インデクスデータを登録する
     *
     * 検索インデクスデータを次のように作成して引き渡す
     *
     * ['SearchIndex' => [
     *        'type' => 'コンテンツのタイプ',
     *        'model_id' => 'モデルでのID',
     *        'content_id' => 'コンテンツID',
     *        'site_id' => 'サブサイトID',
     *        'content_filter_id' => 'フィルターID' // カテゴリIDなど
     *        'category' => 'カテゴリ名',
     *        'title' => 'コンテンツタイトル', // 検索対象
     *        'detail' => 'コンテンツ内容', // 検索対象
     *        'url' => 'URL',
     *        'status' => '公開ステータス',
     *        'publish_begin' => '公開開始日',
     *        'publish_end' => '公開終了日'
     * ]]
     *
     * @param Model $model
     * @param array $data
     * @return bool
     */
    public function saveSearchIndex(Model $model, $data)
    {
        if (!$data) {
            return false;
        }

        if (!empty($data['SearchIndex']['content_id'])) {
            $content = $this->Contents->find('first', ['fields' => ['lft', 'rght'], 'conditions' => ['Content.id' => $data['SearchIndex']['content_id']], 'recursive' => 1]);
            $data['SearchIndex']['lft'] = $content['Content']['lft'];
            $data['SearchIndex']['rght'] = $content['Content']['rght'];
        } else {
            $data['SearchIndex']['lft'] = 0;
            $data['SearchIndex']['rght'] = 0;
        }
        $data['SearchIndex']['model'] = $model->alias;
        // タグ、空白を除外
        $data['SearchIndex']['detail'] = str_replace(["\r\n", "\r", "\n", "\t", "\s"], '', trim(strip_tags($data['SearchIndex']['detail'])));

        // 検索用データとして保存
        $this->SearchIndex = ClassRegistry::init('SearchIndex');
        $before = false;
        if (!empty($data['SearchIndex']['model_id'])) {
            $before = $this->SearchIndex->find('first', [
                'fields' => ['SearchIndex.id', 'SearchIndex.content_id'],
                'conditions' => [
                    'SearchIndex.model' => $data['SearchIndex']['model'],
                    'SearchIndex.model_id' => $data['SearchIndex']['model_id']
                ]]);
        }
        if ($before) {
            $data['SearchIndex']['id'] = $before['SearchIndex']['id'];
            $this->SearchIndex->set($data);
        } else {
            if (empty($data['SearchIndex']['priority'])) {
                $data['SearchIndex']['priority'] = '0.5';
            }
            $this->SearchIndex->create($data);
        }
        $result = $this->SearchIndex->save();

        // カテゴリを site_configsに保存
        if ($result) {
            return $this->updateSearchIndexMeta($model);
        }

        return $result;
    }

    /**
     * コンテンツデータを削除する
     *
     * @param Model $model
     * @param string $id
     */
    public function deleteSearchIndex(Model $model, $id)
    {
        $this->SearchIndex = ClassRegistry::init('SearchIndex');
        if ($this->SearchIndex->deleteAll(['SearchIndex.model' => $model->alias, 'SearchIndex.model_id' => $id])) {
            return $this->updateSearchIndexMeta($model);
        }
    }

    /**
     * コンテンツメタ情報を更新する
     *
     * @param Model $model
     * @return boolean
     */
    public function updateSearchIndexMeta(Model $model)
    {
        $db = ConnectionManager::getDataSource('default');
        $contentTypes = [];
        $searchIndexes = $this->SearchIndex->find('all', ['fields' => ['SearchIndex.type'], 'group' => ['SearchIndex.type'], 'conditions' => ['SearchIndex.status' => true]]);
        foreach($searchIndexes as $searchIndex) {
            if ($searchIndex['SearchIndex']['type']) {
                $contentTypes[$searchIndex['SearchIndex']['type']] = $searchIndex['SearchIndex']['type'];
            }
        }
        $siteConfigs['SiteConfig']['content_types'] = BcUtil::serialize($contentTypes);
        $SiteConfig = ClassRegistry::init('SiteConfig');
        return $SiteConfig->saveKeyValue($siteConfigs);
    }

}
