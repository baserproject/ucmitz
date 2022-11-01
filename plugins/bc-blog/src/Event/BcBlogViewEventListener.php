<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.Config
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

namespace BcBlog\Event;

use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BcBlogViewEventListener
 */
class BcBlogViewEventListener extends \BaserCore\Event\BcViewEventListener
{

    /**
     * Events
     * @var string[]
     */
    public $events = [
        'beforeRender'
    ];

    /**
     * Before render
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeRender()
    {
        if(!BcUtil::isAdminSystem()) return;
        $this->setAdminMenu();
    }

    /**
     * 管理画面メニュー用のデータをセットする
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setAdminMenu()
    {
        /* @var \BcBlog\Model\Table\BlogContentsTable $BlogContent */
        $blogContentTable = \Cake\ORM\TableRegistry::getTableLocator()->get('BcBlog.BlogContents');
        $blogContents = $blogContentTable->find()
            ->contain('Contents')
            ->where($blogContentTable->Contents->getConditionAllowPublish())
            ->order(['BlogContents.id'])
            ->all();
        $blogContentMenus = [];
        foreach($blogContents as $blogContent) {
            $menus = function($blogContent) {
                $route = ['Admin' => true, 'plugin' => 'BcBlog', 'action' => 'index', $blogContent->id];
                $menus = [
                    'BlogPosts' . $blogContent->id => [
                        'title' => '記事',
                        'url' => array_merge($route, ['controller' => 'blog_posts']),
                        'currentRegex' => '{/blog/blog_posts/[^/]+?/' . $blogContent->id . '($|/)}s'
                    ],
                    'BlogCategories' . $blogContent->id => [
                        'title' => 'カテゴリ',
                        'url' => array_merge($route, ['controller' => 'blog_categories']),
                        'currentRegex' => '{/blog/blog_categories/[^/]+?/' . $blogContent->id . '($|/)}s'
                    ]
                ];
                if ($blogContent->tag_use) {
                    $menus = array_merge($menus, [
                        'BlogTags' . $blogContent->id => [
                            'title' => 'タグ',
                            'url' => array_merge($route, ['controller' => 'blog_tags']),
                            'currentRegex' => '{/blog/blog_tags/[^/]+?/}s'
                        ]
                    ]);
                }
                if ($blogContent->comment_use) {
                    $menus = array_merge($menus, [
                        'BlogComments' . $blogContent->id => [
                            'title' => 'コメント',
                            'url' => array_merge($route, ['controller' => 'blog_comments'])
                        ]
                    ]);
                }
                $menus = array_merge($menus, [
                    'BlogContentsEdit' . $blogContent->id => [
                        'title' => '設定',
                        'url' => array_merge($route, ['controller' => 'blog_contents', 'action' => 'edit'])
                    ]
                ]);
                return $menus;
            };
            $blogContentMenus['BlogContent' . $blogContent->id] = [
                'siteId' => $blogContent->content->site_id,
                'title' => $blogContent->content->title,
                'type' => 'blog-content',
                'icon' => 'bca-icon--blog',
                'menus' => $menus($blogContent)
            ];
        }
        Configure::write('BcApp.adminNavigation.Contents', array_merge(
            Configure::read('BcApp.adminNavigation.Contents'),
            $blogContentMenus
        ));
    }

}
