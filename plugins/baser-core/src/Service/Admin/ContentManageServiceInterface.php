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

use BaserCore\Service\ContentsService;
/**
 * Interface PluginManageServiceInterface
 * @package BaserCore\Service
 */
interface ContentManageServiceInterface
{
    /**
     * getTreeIndex
     *
     * @param  int $siteId
     * @return Query
     */
    public function getTreeIndex($siteId): Query;

    /**
     * getTableIndex
     *
     * @param  int $siteId
     * @param  array $searchData
     * @return Query
     */
    public function getTableIndex($siteId, $conditions): Query;

    /**
     * getTrashIndex
     *
     * @return Query
     */
    public function getTrashIndex(): Query;

    /**
      * コンテンツ情報を取得する
      * @return array
      */
    public function getContensInfo ();

    /**
     * getAdminTableConditions
     *
     * @param  array $searchData
     * @return array
     */
    public function getAdminTableConditions($searchData): array;
}
