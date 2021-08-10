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

use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;

/**
 * FavoritesService
 */
class FavoritesService
{

    /**
     * Favorites Table
     * @var \Cake\ORM\Table
     */
    public $Favorites;

    /**
     * FavoritesService constructor.
     */
    public function __construct()
    {
        $this->Favorites = TableRegistry::getTableLocator()->get('BaserCore.Favorites');
    }

    /**
     * お気に入りを取得する
     * @param int $id
     * @return EntityInterface
     */
    public function get($id): EntityInterface
    {
        return $this->Favorites->get($id);
    }

    /**
     * お気に入り一覧を取得
     * @param array $queryParams
     * @return Query
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
     */
    public function getNew(): EntityInterface
    {
        return $this->Favorites->newEntity([]);
    }

    /**
     * 新規登録する
     * @param array $postData
     * @return EntityInterface
     */
    public function create(array $postData)
    {
        $favorite = $this->Favorites->newEmptyEntity();
        $favorite = $this->Favorites->patchEntity($favorite, $postData);
        return ($result = $this->Favorites->save($favorite))? $result : $favorite;
    }

    /**
     * 編集する
     * @param EntityInterface $target
     * @param array $postData
     * @return mixed
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
     */
    public function delete(int $id)
    {
        return $this->Favorites->delete($this->Favorites->get($id));
    }

}
