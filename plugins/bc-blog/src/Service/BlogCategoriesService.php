<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcBlog\Service;

use BaserCore\Error\BcException;
use BcBlog\Model\Entity\BlogCategory;
use BcBlog\Model\Table\BlogCategoriesTable;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * BlogCategoriesService
 */
class BlogCategoriesService implements BlogCategoriesServiceInterface
{

    /**
     * @var BlogCategoriesTable|Table
     */
    public Table|BlogCategoriesTable $BlogCategories;

    /**
     * Construct
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct()
    {
        $this->BlogCategories = TableRegistry::getTableLocator()->get("BcBlog.BlogCategories");
    }

    /**
     * 単一レコードを取得する
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     */
    public function get(int $id): EntityInterface
    {
        return $this->BlogCategories->get($id);
    }

    /**
     * 一覧を取得する
     * @param int $blogContentId
     * @param array $queryParams
     * @param string $type
     * @return Query
     * @checked
     * @noTodo
     */
    public function getIndex(int $blogContentId, array $queryParams, $type = 'all'): Query
    {
        $conditions = [];
        if($blogContentId) $conditions = ['BlogCategories.blog_content_id' => $blogContentId];
        return $this->BlogCategories->find($type)->where($conditions);
    }

    /**
     * getTreeIndex
     *
     * @param int $blogContentId
     * @param array $queryParams
     * @return array
     * @checked
     * @noTodo
     */
    public function getTreeIndex(int $blogContentId, array $queryParams): array
    {
        $srcCategories = $this->getIndex($blogContentId, $queryParams, 'treeList')->order(['lft'])->all();
        $categories = [];
        foreach ($srcCategories->toArray() as $key => $value) {
            /* @var BlogCategory $category */
            $category = $this->BlogCategories->find()->where(['id' => $key])->first();
            if (!preg_match("/^([_]+)/i", $value, $matches)) {
                $category->depth = 0;
                $category->layered_title = $category->title;
                $categories[] = $category;
                continue;
            }
            $category->layered_title = sprintf(
                "%s└%s",
                str_replace('_', '&nbsp;&nbsp;&nbsp;&nbsp;', $matches[1]),
                $category->title
            );
            $category->depth = strlen($matches[1]);
            $categories[] = $category;
        }
        return $categories;
    }

    /**
     * コントロールソース取得
     * @param string $field
     * @param array $options
     * @return mixed
     * @checked
     * @noTodo
     */
    public function getControlSource(string $field, array $options): mixed
    {
        switch ($field) {
            case 'parent_id':
                if (!isset($options['blogContentId'])) {
                    return false;
                }
                $conditions = [];
                if (isset($options['conditions'])) {
                    $conditions = $options['conditions'];
                }
                $conditions['BlogCategories.blog_content_id'] = $options['blogContentId'];
                if (!empty($options['excludeParentId'])) {
                    $children = $this->BlogCategories->find('children', ['for' => $options['excludeParentId']]);
                    $excludeIds = [$options['excludeParentId']];
                    foreach ($children as $child) {
                        $excludeIds[] = $child->id;
                    }
                    $conditions['NOT']['BlogCategories.id IN'] = $excludeIds;
                }
                $parents = $this->BlogCategories->find('treeList')->where($conditions)->order(['lft'])->all();
                $controlSources['parent_id'] = [];
                foreach ($parents as $key => $parent) {
                    if (preg_match("/^([_]+)/i", $parent, $matches)) {
                        $parent = preg_replace("/^[_]+/i", '', $parent);
                        $prefix = str_replace('_', '　　　', $matches[1]);
                        $parent = $prefix . '└' . $parent;
                    }
                    $controlSources['parent_id'][$key] = $parent;
                }
                break;
        }

        return $controlSources[$field] ?? false;
    }

    /**
     * 新規エンティティ取得
     * @param int $blogContentId
     * @return EntityInterface
     * @checked
     * @noTodo
     */
    public function getNew(int $blogContentId): EntityInterface
    {
        return $this->BlogCategories->newEntity([
            'blog_content_id' => $blogContentId,
        ], [
            'validate' => false,
        ]);
    }

    /**
     * 新規作成
     * @param int $blogContentId
     * @param array $postData
     * @return EntityInterface|null
     * @checked
     * @noTodo
     */
    public function create(int $blogContentId, array $postData): ?EntityInterface
    {
        $postData['no'] = $this->BlogCategories->getMax('no', [
                'BlogCategories.blog_content_id' => $blogContentId
            ]) + 1;
        $postData['blog_content_id'] = $blogContentId;
        $blogCategory = $this->BlogCategories->newEmptyEntity();
        $blogCategory = $this->BlogCategories->patchEntity($blogCategory, $postData);
        return $this->BlogCategories->saveOrFail($blogCategory);
    }

    /**
     * 更新する
     * @param EntityInterface $target
     * @param array $postData
     * @return EntityInterface|null
     * @throws PersistenceFailedException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(EntityInterface $target, array $postData): ?EntityInterface
    {
        $blogCategory = $this->BlogCategories->patchEntity($target, $postData);
        return $this->BlogCategories->saveOrFail($blogCategory);
    }

    /**
     * 削除する
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     */
    public function delete(int $id): bool
    {
        try {
            $blogCategory = $this->BlogCategories->get($id);
            $result = $this->BlogCategories->deleteOrFail($blogCategory);
        } catch(RecordNotFoundException) {
            $result = true;
        }
        return $result;
    }

    /**
     * 一括処理
     * @param string $method
     * @param array $ids
     * @return bool
     * @checked
     * @noTodo
     */
    public function batch(string $method, array $ids): bool
    {
        if (!$ids) return true;
        $db = $this->BlogCategories->getConnection();
        $db->begin();
        foreach($ids as $id) {
            if (!$this->$method($id)) {
                $db->rollback();
                throw new BcException(__d('baser', 'データベース処理中にエラーが発生しました。'));
            }
        }
        $db->commit();
        return true;
    }

    /**
     * IDを指定して名前リストを取得する
     * @param $ids
     * @return array
     * @checked
     * @noTodo
     */
    public function getNamesById($ids): array
    {
        return $this->BlogCategories->find('list')->where(['id IN' => $ids])->toArray();
    }

    /**
     *ブログカテゴリーリスト取得
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getList($blogContentId): array
    {
        $conditions = [];
        if ($blogContentId) $conditions = ['BlogCategories.blog_content_id' => $blogContentId];
        return $this->BlogCategories->find('list', ['keyField' => 'id', 'valueField' => 'title'])->where($conditions)->toArray();
    }
}
