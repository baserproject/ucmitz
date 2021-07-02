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

use BaserCore\Model\Entity\Permission;
use Cake\Http\ServerRequest;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;
/**
 * Interface PermissionsServiceInterface
 * @package BaserCore\Service
 */
interface PermissionsServiceInterface
{

    /**
     * サイトを取得する
     * @param int $id
     * @return EntityInterface
     */
    public function get($id): EntityInterface;

    /**
     * サイト一覧を取得
     * @param array $queryParams
     * @return Query
     */
    public function getIndex(ServerRequest $request, $userGroupId): Query;

    /**
     * 新しいデータの初期値を取得する
     * @return EntityInterface
     */
    public function getNew(): EntityInterface;

    /**
     * 新規登録する
     * @param EntityInterface $permission
     * @return EntityInterface|false
     */
    public function create(EntityInterface $permission);

    /**
     * 編集する
     * @param EntityInterface $target
     * @param array $postData
     * @return mixed
     */
    public function update(EntityInterface $target, array $postData);

    /**
     * 削除する
     * @param int $id
     * @return mixed
     */
    public function delete(int $id);

    // /**
    //  * 許可・拒否を指定するメソッドのリストを取得
    //  * @return array
    //  */
    // public function getMethodList(): array;

    // /**
    //  * URLの権限チェックを行う
    //  * @param string $url
    //  * @param array $userGroupId
    //  * @return bool
    //  */
    // public function check($url, $userGroupId): bool;
}
