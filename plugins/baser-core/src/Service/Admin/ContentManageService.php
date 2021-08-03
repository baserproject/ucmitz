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

namespace BaserCore\Service\Admin;

use Cake\ORM\Query;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Service\ContentsService;
use BaserCore\Service\Admin\ContentManageServiceInterface;


/**
 * ContentManageService
 */
class ContentManageService extends ContentsService implements ContentManageServiceInterface
{

    /**
      * コンテンツ情報を取得する
      * @return array
      */
    public function getContensInfo ()
    {
        $sites = $this->Sites->getPublishedAll();
        $contentsInfo = [];
        foreach($sites as $key => $site) {
            $contentsInfo[$key]['published'] = $this->Contents->find()
                    ->where(['site_id' => $site->id, 'status' => true])
                    ->count();
            $contentsInfo[$key]['unpublished'] = $this->Contents->find()
                    ->where(['site_id' => $site->id, 'status' => false])
                    ->count();
            $contentsInfo[$key]['total'] = $contentsInfo[$key]['published'] + $contentsInfo[$key]['unpublished'];
            $contentsInfo[$key]['display_name'] = $site->display_name;
        }
        return $contentsInfo;
    }

    /**
     * getTableIndex
     *
     * @param  int $siteId
     * @param  array $searchData
     * @return Query
     */
    public function getTableIndex($siteId, $conditions): Query
    {
        return parent::getTableIndex($siteId, $conditions);
    }

    /**
     * getAdminTableConditions
     *
     * @param  array $searchData
     * @return array
     */
    public function getAdminTableConditions($searchData): array
    {
        $conditions = [];
        if ($searchData['name']) {
            $conditions['OR'] = [
                'name LIKE' => '%' . $searchData['name'] . '%',
                'title LIKE' => '%' . $searchData['name'] . '%'
            ];
        }
        if ($searchData['folder_id']) {
            $Contents = $this->Contents->find('all')->select(['lft', 'rght'])->where(['id' => $searchData['folder_id']]);
            $conditions['rght <'] = $Contents->first()->rght;
            $conditions['lft >'] = $Contents->first()->lft;
        }
        if ($searchData['author_id']) {
            $conditions['author_id'] = $searchData['author_id'];
        }
        if ($searchData['self_status'] !== '') {
            $conditions['self_status'] = $searchData['self_status'];
        }
        if ($searchData['type']) {
            $conditions['type'] = $searchData['type'];
        }

        return $conditions;
    }
}

