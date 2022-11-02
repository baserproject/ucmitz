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

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Error\BcException;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use BcBlog\Model\Table\BlogContentsTable;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;

/**
 * BlogContentsService
 * @property BlogContentsTable $BlogContents
 */
class BlogContentsService implements BlogContentsServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Construct
     * 
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct()
    {
        $this->BlogContents = TableRegistry::getTableLocator()->get("BcBlog.BlogContents");
    }

    /**
     * 一覧データを取得
     * 
     * @param array $queryParams
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $queryParams = []): Query
    {
        $query = $this->BlogContents->find()->order([
            'BlogContents.id'
        ]);

        if (!empty($queryParams['limit'])) {
            $query->limit($queryParams['limit']);
        }

        if (!empty($queryParams['description'])) {
            $query->where(['description LIKE' => '%' . $queryParams['description'] . '%']);
        }

        return $query;
    }

    /**
     * 単一データ取得
     * 
     * @param int $id
     * @param array $options
     *  - `status`: ステータス。 publish を指定すると公開状態のもののみ取得（初期値：全て）
     * @return \Cake\Datasource\EntityInterface|array|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get(int $id, $options = [])
    {
        $options = array_merge([
            'status' => ''
        ], $options);
        $conditions = ['BlogContents.id' => $id];
        if($options['status'] === 'publish') {
            $conditions = array_merge($conditions, $this->BlogContents->Contents->getConditionAllowPublish());
        }
        return $this->BlogContents->get($id, [
            'conditions' => $conditions,
            'contain' => ['Contents' => ['Sites']]
        ]);
    }

    /**
     * 初期値を取得する
     * 
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew()
    {
        return $this->BlogContents->newEntity([
            'comment_use' => true,
            'comment_approve' => false,
            'layout' => 'default',
            'template' => 'default',
            'list_count' => 10,
            'list_direction' => 'DESC',
            'feed_count' => 10,
            'auth_captcha' => 1,
            'tag_use' => false,
            'status' => false,
            'eye_catch_size_thumb_width' => Configure::read('BcBlog.eye_catch_size_thumb_width'),
            'eye_catch_size_thumb_height' => Configure::read('BcBlog.eye_catch_size_thumb_height'),
            'eye_catch_size_mobile_thumb_width' => Configure::read('BcBlog.eye_catch_size_mobile_thumb_width'),
            'eye_catch_size_mobile_thumb_height' => Configure::read('BcBlog.eye_catch_size_mobile_thumb_height'),
            'use_content' => true
        ], [
            'validate' => false,
        ]);
    }

    /**
     * 更新
     * 
     * @param EntityInterface $target
     * @param array $postData
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(EntityInterface $target, array $postData)
    {
        if (BcUtil::isOverPostSize()) {
            throw new BcException(__d(
                'baser',
                '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。',
                ini_get('post_max_size')
            ));
        }
        $blogContent = $this->BlogContents->patchEntity($target, $postData);
        /* @var \BcBlog\Model\Entity\BlogContent $blogContent */
        $blogContent = $this->BlogContents->deconstructEyeCatchSize($blogContent);
        return $this->BlogContents->saveOrFail($blogContent);
    }

    /**
     * ブログ登録
     * 
     * @param array $data
     * @param array $options
     * @return \Cake\Datasource\EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $postData, $options = []): ?EntityInterface
    {
        $blogContent = $this->getNew();
        $blogContent = $this->BlogContents->patchEntity($blogContent, $postData, $options);
        /* @var \BcBlog\Model\Entity\BlogContent $blogContent */
        $blogContent = $this->BlogContents->deconstructEyeCatchSize($blogContent);
        return $this->BlogContents->saveOrFail($blogContent);
    }

    /**
     * ブログをコピーする
     *
     * @param array $postData
     * @return EntityInterface $result
     * @checked
     * @unitTest
     * @noTodo
     */
    public function copy($postData)
    {
        return $this->BlogContents->copy(
            $postData['entity_id'],
            $postData['parent_id'],
            $postData['title'],
            BcUtil::loginUser()->id,
            $postData['site_id']
        );
    }

    /**
     * ブログを削除する
     * 
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(int $id): bool
    {
        $blogContent = $this->get($id);
        return $this->BlogContents->delete($blogContent);
    }

    /**
     * リストを取得する
     * 
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getList(): array
    {
        return $this->BlogContents
            ->find('list', ['keyField' => 'id', 'valueField' => 'content.title'])
            ->contain('Contents')->toArray();
    }

}
