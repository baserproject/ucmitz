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

namespace BaserCore\Model\Table;

use ArrayObject;
use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Model\AppTable;
use BaserCore\Model\Entity\Site;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\Event\Event;
use Cake\Validation\Validator;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class Site
 *
 * サイトモデル
 * @method Site newEntity($data = null, array $options = [])
 * @package Baser.Model
 */
class SitesTable extends AppTable
{

    /**
     * Trait
     */
    use BcEventDispatcherTrait;

    /**
     * 保存時にエイリアスが変更されたかどうか
     *
     * @var bool
     */
    private $__changedAlias = false;

    /**
     * Validation Default
     *
     * @param Validator $validator
     * @return Validator
     * @checked
     * @noTodo
     * @unitTest
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator->setProvider('site', 'BaserCore\Model\Validation\SiteValidation');
        $validator->requirePresence(['name', 'display_name', 'alias', 'title'], 'create');

        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');
        $validator
            ->scalar('name')
            ->maxLength('name', 50, __d('baser', '識別名称は50文字以内で入力してください。'))
            ->notEmptyString('name', __d('baser', '識別名称を入力してください。'))
            ->add('name', [
                'nameUnique' => [
                    'rule' => 'validateUnique',
                    'provider' => 'table',
                    'message' => __d('baser', '既に利用されている識別名称です。別の名称に変更してください。')
                ]])
            ->add('name', [
                'nameAlphaNumericPlus' => [
                    'rule' => ['alphaNumericPlus'],
                    'provider' => 'bc',
                    'message' => __d('baser', '識別名称は、半角英数・ハイフン（-）・アンダースコア（_）で入力してください。')
                ]]);
        $validator
            ->scalar('display_name')
            ->maxLength('display_name', 50, __d('baser', 'サブサイト名は50文字以内で入力してください。'))
            ->notEmptyString('display_name', __d('baser', 'サイト名を入力してください。'));
        $validator
            ->scalar('alias')
            ->maxLength('alias', 50, __d('baser', 'エイリアスは50文字以内で入力してください。'))
            ->notEmptyString('alias', __d('baser', 'サイト名を入力してください。'))
            ->add('alias', [
                'aliasUnique' => [
                    'rule' => 'validateUnique',
                    'provider' => 'table',
                    'message' => __d('baser', '既に利用されているエイリアス名です。別の名称に変更してください。')
                ]])
            ->add('alias', [
                'aliasSlashChecks' => [
                    'rule' => 'aliasSlashChecks',
                    'provider' => 'site',
                    'message' => __d('baser', 'エイリアスには先頭と末尾にスラッシュ（/）は入力できず、また、連続して入力する事もできません。')
                ]]);
        $validator
            ->scalar('title')
            ->maxLength('title', 255, __d('baser', 'サイトタイトルは255文字以内で入力してください。'))
            ->notEmptyString('title', __d('baser', 'サイトタイトルを入力してください。'));
        return $validator;
    }

    /**
     * 公開されている全てのサイトを取得する
     *
     * @return ResultSetInterface
     * @noTodo
     * @checked
     * @unitTest
     */
    public function getPublishedAll(): ResultSetInterface
    {
        return $this->find()->where(['status' => true])->all();
    }

    /**
     * サイトリストを取得
     *
     * @param bool $mainSiteId メインサイトID
     * @param array $options
     *  - `excludeIds` : 除外するID（初期値：なし）
     *  - `status` : 有効かどうか（初期値：true）
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getSiteList($mainSiteId = null, $options = [])
    {
        $options = array_merge([
            'excludeIds' => [],
            'status' => true
        ], $options);

        // EVENT Site.beforeGetSiteList
        $event = $this->dispatchLayerEvent('beforeGetSiteList', [
            'options' => $options
        ]);
        if ($event !== false) {
            $options = $event->getResult() === true? $event->getData('options') : $event->getResult();
        }

        if(!is_null($options['status'])) {
            $conditions = ['status' => $options['status']];
        }

        if (!is_null($mainSiteId)) {
            $conditions['main_site_id'] = $mainSiteId;
        }

        if (isset($options['excludeIds'])) {
            if (!is_array($options['excludeIds'])) {
                $options['excludeIds'] = [$options['excludeIds']];
            }
            $excludeKey = array_search(0, $options['excludeIds']);
            if ($excludeKey !== false) {
                unset($options['excludeIds'][$excludeKey]);
            }
            if ($options['excludeIds']) {
                $conditions[]['id NOT IN'] = $options['excludeIds'];
            }
        }

        if (isset($options['includeIds'])) {
            if (!is_array($options['includeIds'])) {
                $options['includeIds'] = [$options['includeIds']];
            }
            $includeKey = array_search(0, $options['includeIds']);
            if ($includeKey !== false) {
                unset($options['includeIds'][$includeKey]);
            }
            if ($options['includeIds']) {
                $conditions[]['id IN'] = $options['includeIds'];
            }
        }

        $this->setDisplayField('display_name');
        return $this->find('list')->where($conditions)->toArray();
    }

    /**
     * メインサイトのデータを取得する
     *
     * @param mixed $options
     *  - `fields` : 取得するフィールド
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getRootMain($options = [])
    {
        $options += [
            'fields' => []
        ];
        $site = $this->find()->where(['main_site_id IS' => null])->first()->toArray();
        if ($options['fields']) {
            if (!is_array($options['fields'])) {
                $options['fields'] = [$options['fields']];
            }
            $siteTmp = [];
            foreach($options['fields'] as $field) {
                $siteTmp[$field] = $site[$field];
            }
            $site = $siteTmp;
        }
        return $site;
    }

    /**
     * コンテンツに関連したコンテンツをサイト情報と一緒に全て取得する
     *
     * @param $contentId
     * @return array|null
     */
    public function getRelatedContents($contentId)
    {
        $Content = ClassRegistry::init('Content');
        $data = $Content->find('first', ['conditions' => ['Content.id' => $contentId]]);
        $isMainSite = $this->isMain($data['Site']['id']);

        $conditions = ['Site.status' => true];
        if (is_null($data['Site']['main_site_id'])) {
            $conditions['Site.main_site_id'] = 0;
            $mainSiteContentId = $data['Content']['id'];
        } else {
            $conditions['or'] = [
                ['Site.main_site_id' => $data['Site']['main_site_id']],
                ['Site.id' => $data['Site']['main_site_id']]
            ];
            if ($isMainSite) {
                $conditions['or'][] = ['Site.main_site_id' => $data['Site']['id']];
            }
            if ($data['Content']['main_site_content_id']) {
                $mainSiteContentId = $data['Content']['main_site_content_id'];
            } else {
                $mainSiteContentId = $data['Content']['id'];
            }
        }
        $fields = ['id', 'name', 'alias', 'display_name', 'main_site_id'];
        $sites = $this->find('all', ['fields' => $fields, 'conditions' => $conditions, 'order' => 'main_site_id']);
        if ($data['Site']['main_site_id'] == 0) {
            $sites = array_merge([$this->getRootMain(['fields' => $fields])], $sites);
        }
        $conditions = [
            'or' => [
                ['Content.id' => $mainSiteContentId],
                ['Content.main_site_content_id' => $mainSiteContentId]
            ]
        ];
        if ($isMainSite) {
            $conditions['or'][] = ['Content.main_site_content_id' => $data['Content']['id']];
        }
        $relatedContents = $Content->find('all', ['conditions' => $conditions, 'recursive' => -1]);
        foreach($relatedContents as $relatedContent) {
            foreach($sites as $key => $site) {
                if ($relatedContent['Content']['site_id'] == $site['Site']['id']) {
                    $sites[$key]['Content'] = $relatedContent['Content'];
                    break;
                }
            }
        }
        return $sites;
    }

    /**
     * メインサイトかどうか判定する
     *
     * @param $id
     * @return bool
     */
    public function isMain($id)
    {
        return is_null($this->find()->where(['id' => $id])->first()->main_site_id);
    }

    /**
     * サブサイトを取得する
     *
     * @param $id
     * @param array $options
     * @return ResultSetInterface
     */
    public function children($id, $options = [])
    {
        $options = array_merge_recursive([
            'conditions' => [
                'main_site_id' => $id
            ]
        ], $options);
        return $this->find()->where($options['conditions'])->all();
    }

    /**
     * After Save
     *
     * @param Event $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     */
    public function afterSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        // TODO 未確認のため暫定措置
        // >>>
        return;
        // <<<
        App::uses('AuthComponent', 'Controller/Component');
        $user = AuthComponent::user();
        $ContentFolder = ClassRegistry::init('ContentFolder');
        if ($created) {
            $ContentFolder->saveSiteRoot(null, [
                'site_id' => $this->id,
                'name' => ($this->data['Site']['alias'])? $this->data['Site']['alias'] : $this->data['Site']['name'],
                'parent_id' => 1,
                'title' => $this->data['Site']['title'],
                'self_status' => $this->data['Site']['status'],
                'author_id' => $user['id'],
                'site_root' => true,
                'layout_template' => 'default'
            ]);
        } else {
            $ContentFolder->saveSiteRoot($this->id, [
                'name' => ($this->data['Site']['alias'])? $this->data['Site']['alias'] : $this->data['Site']['name'],
                'title' => $this->data['Site']['title'],
                'self_status' => $this->data['Site']['status'],
            ], $this->__changedAlias);
        }
        if (!empty($this->data['Site']['main'])) {
            $data = $this->find('first', ['conditions' => ['Site.main' => true, 'Site.id <>' => $this->id], 'recursive' => -1]);
            if ($data) {
                $data['Site']['main'] = false;
                $this->save($data, ['validate' => false, 'callbacks' => false]);
            }
        }
        $this->__changedAlias = false;
    }

    /**
     * After Delete
     *
     * @param Event $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     */
    public function afterDelete(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        // TODO 未確認のため暫定措置
        // >>>
        return;
        // <<<
        $Content = ClassRegistry::init('Content');
        $id = $Content->field('id', [
            'Content.site_id' => $this->id,
            'Content.site_root' => true
        ]);

        $children = $Content->children($id, false);
        foreach($children as $child) {
            $child['Content']['site_id'] = 0;
            // バリデートすると name が変換されてしまう
            $Content->save($child, false);
        }

        $children = $Content->children($id, true);
        foreach($children as $child) {
            $Content->softDeleteFromTree($child['Content']['id']);
        }

        $softDelete = $Content->softDelete(null);
        $Content->softDelete(false);
        $Content->removeFromTree($id, true);
        $Content->softDelete($softDelete);
    }

    /**
     * プレフィックスを取得する
     *
     * @param mixed $id | $data
     * @return mixed
     */
    public function getPrefix($id)
    {
        if (!is_array($id)) {
            $data = $this->find('first', ['fields' => ['name', 'alias'], 'conditions' => ['Site.id' => $id], 'recursive' => -1]);
        } else {
            $data = $id;
        }
        if (!$data) {
            return '';
        }
        if (isset($data['Site'])) {
            $data = $data['Site'];
        }
        if (empty($data['name'])) {
            return '';
        }
        $prefix = $data['name'];
        if ($data['alias']) {
            $prefix = $data['alias'];
        }
        return $prefix;
    }

    /**
     * サイトのルートとなるコンテンツIDを取得する
     *
     * @param $id
     * @return mixed
     */
    public function getRootContentId($id)
    {
        if ($id == 0) {
            return 1;
        }
        $Content = ClassRegistry::init('Content');
        return $Content->field('id', ['Content.site_root' => true, 'Content.site_id' => $id]);
    }

    /**
     * URLよりサイトを取得する
     *
     * @param string $url
     * @return array|bool|null
     */
    public function findByUrl($url)
    {
        if ($url === false || $url === "") {
            return $this->getRootMain();
        }
        $params = explode('/', $url);
        if (empty($params[0])) {
            return false;
        }
        $site = $this->find('first', ['conditions' => [
            'or' => [
                'Site.name' => $params[0],
                'Site.alias' => $params[0]
            ]
        ], 'recursive' => -1]);
        if (!$site) {
            $site = $this->getRootMain();
        }
        return $site;
    }

    /**
     * メインサイトを取得する
     *
     * @param int $id
     * @return array|null
     */
    public function getMain($id)
    {
        $mainSiteId = $this->field('main_site_id', [
            'Site.id' => $id
        ]);
        if ($mainSiteId == 0) {
            return $this->getRootMain();
        }
        return $this->find('first', ['conditions' => [
            'Site.main_site_id' => $mainSiteId
        ], 'recursive' => -1]);
    }

    /**
     * After Find
     *
     * @param mixed $results
     * @param bool $primary
     * @return mixed
     */
    public function afterFind($results, $primary = false)
    {
        $results = parent::afterFind($results, $primary = false);
        $this->dataIter($results, function(&$entity, &$model) {
            if (isset($entity['Site']['alias']) && $entity['Site']['alias'] === '' && !empty($entity['Site']['name'])) {
                $entity['Site']['alias'] = $entity['Site']['name'];
            }
        });
        return $results;
    }

    /**
     * 選択可能なデバイスの一覧を取得する
     *
     * @param int $mainSiteId メインサイトID
     * @param int $currentSiteId 現在のサイトID
     * @return array
     */
    public function getSelectableDevices($mainSiteId, $currentSiteId)
    {
        $agents = Configure::read('BcAgent');
        $devices = ['' => __d('baser', '指定しない')];
        $selected = $this->find('list', [
            'fields' => ['id', 'device'],
            'conditions' => [
                'Site.main_site_id' => $mainSiteId,
                'Site.id <>' => $currentSiteId
            ]
        ]);
        foreach($agents as $key => $agent) {
            if (in_array($key, $selected)) {
                continue;
            }
            $devices[$key] = $agent['name'];
        }
        return $devices;
    }

    /**
     * 選択可能が言語の一覧を取得する
     *
     * @param int $mainSiteId メインサイトID
     * @param int $currentSiteId 現在のサイトID
     * @return array
     */
    public function getSelectableLangs($mainSiteId, $currentSiteId)
    {
        $langs = Configure::read('BcLang');
        $devices = ['' => __d('baser', '指定しない')];
        $selected = $this->find('list', [
            'fields' => ['id', 'lang'],
            'conditions' => [
                'Site.main_site_id' => $mainSiteId,
                'Site.id <>' => $currentSiteId
            ]
        ]);
        foreach($langs as $key => $lang) {
            if (in_array($key, $selected)) {
                continue;
            }
            $devices[$key] = $lang['name'];
        }
        return $devices;
    }

    /**
     * デバイス設定をリセットする
     *
     * @return bool
     */
    public function resetDevice()
    {
        $sites = $this->find('all', ['recursive' => -1]);
        $result = true;
        if ($sites) {
            $this->getDataSource()->begin();
            foreach($sites as $site) {
                $site['Site']['device'] = '';
                $site['Site']['auto_link'] = false;
                if (!$site['Site']['lang']) {
                    $site['Site']['same_main_url'] = false;
                    $site['Site']['auto_redirect'] = false;
                }
                $this->set($site);
                if (!$this->save()) {
                    $result = false;
                }
            }
        }
        if (!$result) {
            $this->getDataSource()->rollback();
        } else {
            $this->getDataSource()->commit();
        }
        return $result;
    }

    /**
     * 言語設定をリセットする
     *
     * @return bool
     */
    public function resetLang()
    {
        $sites = $this->find('all', ['recursive' => -1]);
        $result = true;
        if ($sites) {
            $this->getDataSource()->begin();
            foreach($sites as $site) {
                $site['Site']['lang'] = '';
                if (!$site['Site']['device']) {
                    $site['Site']['same_main_url'] = false;
                    $site['Site']['auto_redirect'] = false;
                }
                $this->set($site);
                if (!$this->save()) {
                    $result = false;
                }
            }
        }
        if (!$result) {
            $this->getDataSource()->rollback();
        } else {
            $this->getDataSource()->commit();
        }
        return $result;
    }

    /**
     * Before Save
     *
     * @param Event $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @return bool
     */
    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        // エイリアスに変更があったかチェックする
        if ($entity->id && $entity->alias) {
            $oldSite = $this->find()->where(['id' => $entity->id])->first();
            if ($oldSite && $oldSite->alias !== $entity->alias) {
                $this->__changedAlias = true;
            }
        }
        return true;
    }

}
