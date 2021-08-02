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

use BaserCore\Model\Entity\Content;
use Cake\Http\ServerRequest;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;

/**
 * Interface ContentsServiceInterface
 * @package BaserCore\Service
 */
interface ContentsServiceInterface
{
    /**
     * コンテンツを取得する
     * @param int $id
     * @return EntityInterface
     */
    public function get($id): EntityInterface;

    // /**
    //  * getIndex
    //  *
    //  * @return array
    //  */
    // public function getIndex(): array;

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
    public function getTableIndex($siteId, $searchData): Query;

    /**
     * getTrashIndex
     *
     * @return Query
     */
    public function getTrashIndex(): Query;



}
