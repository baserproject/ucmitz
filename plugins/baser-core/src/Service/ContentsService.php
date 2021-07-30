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

use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Datasource\EntityInterface;
use BaserCore\Model\Table\SitesTable;
use BaserCore\Model\Table\ContentsTable;

class ContentsService implements ContentsServiceInterface
{

    /**
     * Contents
     *
     * @var ContentsTable
     */
    public $Contents;

    /**
     * Sites
     *
     * @var SitesTable
     */
    public $Sites;

    public function __construct()
    {
        $this->Contents = TableRegistry::getTableLocator()->get("BaserCore.Contents");
        $this->Sites = TableRegistry::getTableLocator()->get("BaserCore.Sites");
    }

    /**
     * コンテンツを取得する
     * @param int $id
     * @return EntityInterface
     */
    public function get($id): EntityInterface
    {
        return $this->Contents->get($id, [
            'contain' => ['Sites'],
        ]);
    }

    /**
     * コンテンツ管理一覧用のデータを取得
     * @param array $queryParams
     * @return Query
     */
    public function getIndex(array $queryParams): Query
    {
        switch($this->request->getParam('action')) {
            case 'index':
                switch($currentListType) {
                    case 1:
                        //
                        $conditions = $this->_createAdminIndexConditionsByTree($currentSiteId);
                        $datas = $this->Contents->find('threaded')->where([$conditions])->order(['lft'])->contain(['Sites']);
                        // 並び替え最終更新時刻をリセット
                        // $this->SiteConfigs->resetContentsSortLastModified();
                        $template = 'ajax_index_tree';
                        break;
                    case 2:
                        $conditions = $this->_createAdminIndexConditionsByTable($currentSiteId, $this->request->data);
                        $options = [
                            'order' => 'Content.' . $this->request->getParam('pass')['sort'] . ' ' . $this->request->getParam('pass')['direction'],
                            'conditions' => $conditions,
                            'limit' => $this->request->getParam('pass')['num'],
                            'recursive' => 2
                        ];

                        // EVENT Contents.searchIndex
                        $event = $this->dispatchLayerEvent('searchIndex', [
                            'options' => $options
                        ]);
                        if ($event !== false) {
                            $options = ($event->getResult() === null || $event->getResult() === true)? $event->getData('options') : $event->getResult();
                        }

                        $this->paginate = $options;
                        $datas = $this->paginate('Content');
                        $this->set('authors', $this->Users->getUserList());
                        $template = 'ajax_index_table';
                        break;
                }
                break;
            case 'trash_index':
                $this->Content->Behaviors->unload('SoftDelete');
                $conditions = $this->_createAdminIndexConditionsByTrash();
                $datas = $this->Content->find('threaded', ['order' => ['Content.site_id', 'Content.lft'], 'conditions' => $conditions, 'recursive' => 0]);
                $template = 'ajax_index_trash';
                break;
        }
    }

    /**
     * ツリー表示用の検索条件を生成する
     * @todo Testable humuhimi
     * @return array
     */
    protected function _createAdminIndexConditionsByTree($currentSiteId)
    {
        if ($currentSiteId === 'all') {
            $conditions = ['or' => [
                ['Site.use_subdomain' => false],
                ['Content.site_id' => 0]
            ]];
        } else {
            $conditions = ['Contents.site_id' => $currentSiteId];
        }
        return $conditions;
    }

    /**
     * テーブル表示用の検索条件を生成する
     *
     * @return array
     */
    protected function _createAdminIndexConditionsByTable($currentSiteId, $data)
    {
        $data['Content'] = array_merge([
            'name' => '',
            'folder_id' => '',
            'author_id' => '',
            'self_status' => '',
            'type' => ''
        ], $data['Content']);

        $conditions = ['Content.site_id' => $currentSiteId];
        if ($data['Content']['name']) {
            $conditions['or'] = [
                'Content.name LIKE' => '%' . $data['Content']['name'] . '%',
                'Content.title LIKE' => '%' . $data['Content']['name'] . '%'
            ];
        }
        if ($data['Content']['folder_id']) {
            $content = $this->Content->find('first', ['fields' => ['lft', 'rght'], 'conditions' => ['Content.id' => $data['Content']['folder_id']], 'recursive' => -1]);
            $conditions['Content.rght <'] = $content['Content']['rght'];
            $conditions['Content.lft >'] = $content['Content']['lft'];
        }
        if ($data['Content']['author_id']) {
            $conditions['Content.author_id'] = $data['Content']['author_id'];
        }
        if ($data['Content']['self_status'] !== '') {
            $conditions['Content.self_status'] = $data['Content']['self_status'];
        }
        if ($data['Content']['type']) {
            $conditions['Content.type'] = $data['Content']['type'];
        }
        return $conditions;
    }

    /**
     * ゴミ箱用の検索条件を生成する
     *
     * @return array
     */
    protected function _createAdminIndexConditionsByTrash()
    {
        return [
            'Content.deleted' => true
        ];
    }
}

