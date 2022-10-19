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

namespace BcBlog\Model\Table;

/**
 * ブログタグモデル
 *
 * @package Blog.Model
 */
class BlogTagsTable extends BlogAppTable
{

    /**
     * ビヘイビア
     *
     * @var array
     */
    public $actsAs = ['BcCache'];

    /**
     * ファインダーメソッド
     *
     * @var array
     */
    public $findMethods = ['customParams' => true];

    /**
     * BlogTag constructor.
     *
     * @param bool $id
     * @param null $table
     * @param null $ds
     * TODO ucmitz 一旦、コメントアウト
     */
//    public function __construct($id = false, $table = null, $ds = null)
//    {
//        parent::__construct($id, $table, $ds);
//        $this->validate = [
//            'name' => [
//                'notBlank' => ['rule' => ['notBlank'], 'message' => __d('baser', 'ブログタグを入力してください。')],
//                'duplicate' => ['rule' => ['duplicate', 'name'], 'message' => __d('baser', '既に登録のあるタグです。')]
//            ]
//        ];
//    }

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('blog_tags');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsToMany('BlogPosts', [
            'className' => 'BcBlog.BlogPosts',
            'foreignKey' => 'blog_tag_id',
            'targetForeignKey' => 'blog_post_id',
            'joinTable' => 'blog_posts_blog_tags',
        ]);
    }

    /**
     * カスタムパラメーター検索
     * ※ カスタムファインダーメソッド
     *
     * @param string $state
     * @param array $query
     * @param array $results
     * @return array
     */
    public function _findCustomParams($state, $query, $results = [])
    {
        if ($state == 'before') {
            $query = array_merge([
                'conditions' => [],        // 検索条件のベース
                'direction' => 'ASC',    // 並び方向
                'sort' => 'name',        // 並び順対象のフィールド
                'contentId' => null,    // 《条件》ブログコンテンツID
                'contentUrl' => null,    // 《条件》コンテンツURL
                'siteId' => null,        // 《条件》サイトID
                'recursive' => 0,
            ], $query);
            $assocContent = false;
            $conditions = $query['conditions'];
            if (!is_null($query['siteId'])) {
                $assocContent = true;
                $conditions['Content.site_id'] = $query['siteId'];
            }
            if ($query['contentId']) {
                $assocContent = true;
                $conditions['Content.entity_id'] = $query['contentId'];
            }
            if ($query['contentUrl']) {
                $assocContent = true;
                $conditions['Content.url'] = $query['contentUrl'];
            }
            $query['conditions'] = $conditions;
            if ($assocContent) {
                $query['joins'] = [
                    [
                        'type' => 'INNER',
                        'table' => 'blog_posts_blog_tags',
                        'alias' => 'BlogPostsBlogTag',
                        'conditions' => "BlogPostsBlogTag.blog_tag_id=BlogTag.id"
                    ],
                    [
                        'type' => 'INNER',
                        'table' => 'blog_posts',
                        'alias' => 'BlogPost',
                        'conditions' => "BlogPostsBlogTag.blog_post_id=BlogPost.id"
                    ],
                    [
                        'type' => 'INNER',
                        'table' => 'blog_contents',
                        'alias' => 'BlogContent',
                        'conditions' => "BlogPost.blog_content_id=BlogContent.id"
                    ],
                    [
                        'type' => 'INNER',
                        'table' => 'contents',
                        'alias' => 'Content',
                        'conditions' => "Content.entity_id=BlogContent.id AND Content.type='BlogContent'",
                    ]
                ];
                if ($query['fields']) {
                    if (is_array($query['fields'])) {
                        $query['fields'][0] = 'DISTINCT ' . $query['fields'][0];
                    } else {
                        $query['fields'] = 'DISTINCT ' . $query['fields'];
                    }
                } else {
                    //============================================================
                    // 全フィールド前提で、DISTINCT を付けたいが、PostgresSQL の場合に
                    // DISTINCT * と指定するとSQLの解析でけされてしまっていたので
                    // フィールドを明示的に指定
                    //============================================================
                    $query['fields'] = ['DISTINCT BlogTag.id', 'BlogTag.name'];
                }
            }
            $order = "BlogTag.{$query['sort']} {$query['direction']}";
            if ($query['order']) {
                $query['order'] = array_merge([$order], $query['order']);
            } else {
                $query['order'] = $order;
            }
            unset($query['sort'], $query['direction'], $query['contentId'], $query['contentUrl'], $query['siteId']);
            return $query;
        }
        return $results;
    }

    /**
     * アクセス制限としてブログタグの新規追加ができるか確認する
     *
     * Ajaxを利用する箇所にて BcBaserHelper::link() が利用できない場合に利用
     *
     * @param int $userGroupId ユーザーグループID
     * @param int $blogContentId ブログコンテンツID
     */
    public function hasNewTagAddablePermission($userGroupId, $blogContentId)
    {
        if (ClassRegistry::isKeySet('Permission')) {
            $Permission = ClassRegistry::getObject('Permission');
        } else {
            $Permission = ClassRegistry::init('Permission');
        }
        $ajaxAddUrl = preg_replace('|^/index.php|', '', Router::url(['plugin' => 'blog', 'controller' => 'blog_tags', 'action' => 'ajax_add', $blogContentId]));
        return $Permission->check($ajaxAddUrl, $userGroupId);
    }

    /**
     * 指定した名称のブログタグ情報を取得する
     *
     * @param string $name
     * @return array
     */
    public function getByName($name)
    {
        return $this->find('first', [
            'conditions' => ['BlogTag.name' => $name],
            'recursive' => -1,
            'callbacks' => false,
        ]);
    }
}
