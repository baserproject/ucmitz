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
use BaserCore\Utility\BcUtil;
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
     * initialize
     * @param  array $config
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        $this->table = $this->table();
        /** @var BaserCore\Model\Table\ContentsTable $Contents  */
        $this->Contents = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        /** @var BaserCore\Model\Table\SearchIndexesTable $SearchIndexes  */
        $this->SearchIndexes = TableRegistry::getTableLocator()->get('BaserCore.SearchIndexes');
        /** @var BaserCore\Model\Table\SiteConfigsTable $SiteConfigs  */
        $this->SiteConfigs = TableRegistry::getTableLocator()->get('BaserCore.SiteConfigs');
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
     * @param array $data
     * @return bool
     */
    public function saveSearchIndex($data)
    {
        if (!$data) {
            return false;
        }

        if (!empty($data['SearchIndex']['content_id'])) {
            $content = $this->Contents->find()->select(['lft', 'rght'])->where(['id' => $data['SearchIndex']['content_id']])->first();
            $data['SearchIndex']['lft'] = $content->lft;
            $data['SearchIndex']['rght'] = $content->rght;
        } else {
            $data['SearchIndex']['lft'] = 0;
            $data['SearchIndex']['rght'] = 0;
        }
        $data['SearchIndex']['model'] = Inflector::classify($this->table->getAlias());
        // タグ、空白を除外
        $data['SearchIndex']['detail'] = str_replace(["\r\n", "\r", "\n", "\t", "\s"], '', trim(strip_tags($data['SearchIndex']['detail'])));

        // 検索用データとして保存
        $before = false;
        if (!empty($data['SearchIndex']['model_id'])) {
            $before = $this->SearchIndexes->find()
                ->select(['id', 'content_id'])
                ->where([
                    'model' => $data['SearchIndex']['model'],
                    'model_id' => $data['SearchIndex']['model_id']
                ])->first();
        }
        if ($before) {
            $data['SearchIndex']['id'] = $before->id;
            $searchIndex = $this->SearchIndexes->patchEntity($before, $data['SearchIndex']);
        } else {
            if (empty($data['SearchIndex']['priority'])) {
                $data['SearchIndex']['priority'] = '0.5';
            }
            $searchIndex = $this->SearchIndexes->newEntity($data['SearchIndex']);
        }
        $result = $this->SearchIndexes->save($searchIndex);

        // カテゴリを site_configsに保存
        if ($result) {
            return $this->updateSearchIndexMeta();
        }

        return $result;
    }

    /**
     * コンテンツデータを削除する
     *
     * @param string $modelName
     * @param string $id
     */
    public function deleteSearchIndex($modelName, $id)
    {
        if ($this->SearchIndexes->deleteAll(['model' => $modelName, 'model_id' => $id])) {
            return $this->updateSearchIndexMeta();
        }
    }

    /**
     * コンテンツメタ情報を更新する
     *
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public function updateSearchIndexMeta()
    {
        $contentTypes = [];
        $searchIndexes = $this->SearchIndexes->find()->select('type')->group('type')->where(['status' => true]);
        foreach($searchIndexes as $searchIndex) {
            if ($searchIndex->type) {
                $contentTypes[$searchIndex->type] = $searchIndex->type;
            }
        }
        return $this->SiteConfigs->saveValue('content_types', BcUtil::serialize($contentTypes));
    }

}
