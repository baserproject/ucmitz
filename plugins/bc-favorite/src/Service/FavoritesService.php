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

namespace BcFavorite\Service;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * FavoriteService
 */
class FavoriteService implements FavoriteServiceInterface
{

    /**
     * Favorites Table
     * @var \Cake\ORM\Table
     */
    public $Favorites;

    /**
     * FavoriteService constructor.
     */
    public function __construct()
    {
        $this->Favorites = TableRegistry::getTableLocator()->get('BcFavorite.Favorites');
    }

    /**
     * お気に入りを取得する
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get($id): EntityInterface
    {
        return $this->Favorites->get($id);
    }

    /**
     * お気に入り一覧を取得
     * @param array $queryParams
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $queryParams): Query
    {
        $options = [];
        if (!empty($queryParams['num'])) {
            $options = ['limit' => $queryParams['num']];
        }
        $query = $this->Favorites->find('all', $options);
        return $query;
    }

    /**
     * 新しいデータの初期値を取得する
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew(): EntityInterface
    {
        return $this->Favorites->newEntity([]);
    }

    /**
     * お気に入りを新規登録する
     * @param array $postData
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $postData)
    {
        $favorite = $this->Favorites->newEmptyEntity();
        $favorite->sort = $this->Favorites->getMax('sort', ['user_id' => $postData['user_id']]) + 1;
        $favorite = $this->Favorites->patchEntity($favorite, $postData);
        return ($result = $this->Favorites->save($favorite))? $result : $favorite;
    }

    /**
     * 編集する
     * @param EntityInterface $target
     * @param array $postData
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(EntityInterface $target, array $postData)
    {
        $favorite = $this->Favorites->patchEntity($target, $postData);
        return ($result = $this->Favorites->save($target))? $result : $favorite;
    }

    /**
     * 削除する
     * @param int $id
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(int $id)
    {
        return $this->Favorites->delete($this->Favorites->get($id));
    }

}
