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

namespace BcBlog\Service\Front;

use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use BcBlog\Model\Entity\BlogContent;
use BcBlog\Model\Entity\BlogPost;
use BcBlog\Service\BlogContentsService;
use BcBlog\Service\BlogContentsServiceInterface;
use BcBlog\Service\BlogPostsService;
use BcBlog\Service\BlogPostsServiceInterface;
use Cake\Controller\Controller;
use Cake\Datasource\EntityInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\ServerRequest;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\ORM\ResultSet;
use Cake\ORM\TableRegistry;

/**
 * BlogFrontService
 *
 * @property BlogPostsService $BlogPostsService
 * @property BlogContentsService $BlogContentsService
 */
class BlogFrontService implements BlogFrontServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Constructor
     *
     * サービスクラスを初期化する
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct()
    {
        $this->BlogContentsService = $this->getService(BlogContentsServiceInterface::class);
        $this->BlogPostsService = $this->getService(BlogPostsServiceInterface::class);
    }

    /**
     * 記事一覧用の view 変数を取得する
     *
     * @param ServerRequest $request
     * @return array[]
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getViewVarsForIndex(ServerRequest $request, BlogContent $blogContent, ResultSet $posts): array
    {
        return [
            'blogContent' => $blogContent,
            'posts' => $posts,
            'single' => false,
            'editLink' => BcUtil::loginUser()? [
                'prefix' => 'Admin',
                'plugin' => 'BcBlog',
                'controller' => 'BlogContents',
                'action' => 'edit',
                $blogContent->id
            ] : null
        ];
    }

    /**
     * プレビュー用のセットアップをする
     *
     * @param Controller $controller
     * @checked
     * @noTodo
     */
    public function setupPreviewForIndex(Controller $controller): void
    {
        // ブログコンテンツ取得
        $blogContent = $this->BlogContentsService->get(
            (int)$controller->getRequest()->getAttribute('currentContent')->entity_id
        );
        // ブログコンテンツをPOSTデータにより書き換え
        $blogContent = $this->BlogContentsService->BlogContents->patchEntity(
            $blogContent,
            $controller->getRequest()->getData()
        );
        // ブログコンテンツのアップロードファイルをPOSTデータにより書き換え
        $blogContent->content = $this->BlogContentsService->BlogContents->Contents->saveTmpFiles(
            $controller->getRequest()->getData('content'),
            mt_rand(0, 99999999)
        );
        // Request のカレンドコンテンツを書き換え
        $controller->setRequest($controller->getRequest()->withAttribute('currentContent', $blogContent->content));
        /* @var BlogContent $blogContent */
        $controller->set($this->getViewVarsForIndex(
            $controller->getRequest(),
            $blogContent,
            $controller->paginate($this->BlogPostsService->getIndex([
                'limit' => $blogContent->list_count,
                'status' => 'publish'
            ]))
        ));
        $controller->viewBuilder()->setTemplate($this->getIndexTemplate($blogContent));
    }

    /**
     * カテゴリー別アーカイブ一覧の view 変数を取得する
     *
     * @param ResultSet $posts
     * @param string $category
     * @param ServerRequest $request
     * @param EntityInterface $blogContent
     * @param array $crumbs
     * @return array
     * @checked
     * @noTodo
     */
    public function getViewVarsForArchivesByCategory(
        ResultSet       $posts,
        string          $category,
        ServerRequest   $request,
        EntityInterface $blogContent,
        array           $crumbs
    ): array
    {
        $blogCategoriesTable = TableRegistry::getTableLocator()->get('BcBlog.BlogCategories');
        $blogCategory = $blogCategoriesTable->find()->where([
            'BlogCategories.blog_content_id' => $blogContent->id,
            'BlogCategories.name' => urlencode($category)
        ])->first();
        if (!$blogCategory) {
            throw new NotFoundException();
        }
        return [
            'posts' => $posts,
            'blogCategory' => $blogCategory,
            'blogArchiveType' => 'category',
            'crumbs' => array_merge($crumbs, $this->getCategoryCrumbs(
                    $request->getAttribute('currentContent')->url,
                    $blogCategory->id
                ))
        ];
    }

    /**
     * カテゴリ用のパンくずを取得する
     *
     * @param string $baseUrl
     * @param int $categoryId
     * @return array
     * @checked
     * @noTodo
     */
    public function getCategoryCrumbs(string $baseUrl, int $categoryId, $isCategoryPage = true): array
    {
        $blogCategoriesTable = TableRegistry::getTableLocator()->get('BcBlog.BlogCategories');
        $query = $blogCategoriesTable->find('path', ['for' => $categoryId])->select(['name', 'title']);
        $count = $query->count();
        $crumbs = [];
        if ($count <= 1 && $isCategoryPage) return $crumbs;
        foreach($query->all() as $key => $blogCategory) {
            if ($key === ($count - 1) && $isCategoryPage) break;
            $crumbs[] = [
                'name' => $blogCategory->title,
                'url' => sprintf(
                    "%sarchives/category/%s",
                    $baseUrl,
                    $blogCategory->name
                )
            ];
        }
        return $crumbs;
    }

    /**
     * 著者別アーカイブ一覧の view 用変数を取得する
     * @param ResultSet $posts
     * @param string $author
     * @return array
     * @checked
     * @noTodo
     */
    public function getViewVarsForArchivesByAuthor(ResultSet $posts, string $author): array
    {
        $usersTable = TableRegistry::getTableLocator()->get('BaserCore.Users');
        $author = $usersTable->find('available')->where(['Users.name' => $author])->first();
        if (!$author) {
            throw new NotFoundException();
        }
        return [
            'posts' => $posts,
            'blogArchiveType' => 'author',
            'author' => $author
        ];
    }

    /**
     * タグ別アーカイブ一覧の view 用変数を取得する
     *
     * @param ResultSet $posts
     * @param string $tag
     * @param BlogContent $blogContent
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForArchivesByTag(ResultSet $posts, string $tag, BlogContent $blogContent): array
    {
        $tagsTable = TableRegistry::getTableLocator()->get('BcBlog.BlogTags');
        $tag = $tagsTable->find()->where(['name' => urldecode($tag)])->first();
        if (!$blogContent->tag_use || !$tag) throw new NotFoundException();
        return [
            'posts' => $posts,
            'blogArchiveType' => 'tag',
            'blogTag' => $tag
        ];
    }

    /**
     * 日付別アーカイブ一覧の view 用変数を取得する
     *
     * @param ResultSet $posts
     * @param string $year
     * @param string $month
     * @param string $day
     * @return array
     * @checked
     * @noTodo
     */
    public function getViewVarsForArchivesByDate(ResultSet $posts, string $year, string $month, string $day): array
    {
        if ($day) {
            $type = 'daily';
        } elseif ($month) {
            $type = 'monthly';
        } else {
            $type = 'yearly';
        }
        return [
            'posts' => $posts,
            'blogArchiveType' => $type,
            'year' => $year,
            'month' => $month,
            'day' => $day
        ];
    }

    /**
     * ブログ記事詳細ページの view 用変数を取得する
     *
     * @param ServerRequest $request
     * @param EntityInterface $blogContent
     * @param array $crumbs
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForSingle(ServerRequest $request, EntityInterface $blogContent, array $crumbs): array
    {
        $no = $request->getParam('pass.0');
        if (!$no) throw new NotFoundException();
        /* @var BlogPost $post */
        $post = $this->BlogPostsService->BlogPosts->getPublishByNo($blogContent->id, $no);

        // ナビゲーションを設定
        if ($post->blog_category_id) {
            $crumbs = array_merge($crumbs, $this->getCategoryCrumbs(
                $request->getAttribute('currentContent')->url,
                $post->blog_category->id,
                false
            ));
        }

        $isPreview = (bool) $request->getQuery('preview');

        return [
            'post' => $post,
            'blogContent' => $blogContent,
            'editLink' => BcUtil::loginUser()? [
                'prefix' => 'Admin',
                'plugin' => 'BcBlog',
                'controller' => 'BlogPosts',
                'action' => 'edit',
                $post->blog_content_id,
                $post->id
            ] : '',
            'commentUse' => ($isPreview)? false : $blogContent->comment_use,
            'single' => true,
            'crumbs' => $crumbs
        ];
    }

    /**
     * プレビュー用のセットアップをする
     * @param Controller $controller
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setupPreviewForArchives(Controller $controller): void
    {
        // ブログコンテンツ取得
        /* @var BlogContent $blogContent */
        $blogContent = $this->BlogContentsService->get(
            (int)$controller->getRequest()->getAttribute('currentContent')->entity_id
        );
        // view 用編集を取得
        $vars = $this->getViewVarsForSingle(
            $controller->getRequest(),
            $blogContent,
            $controller->viewBuilder()->getVar('crumbs')
        );
        // ブログ記事をPOSTデータにより書き換え
        if($controller->getRequest()->getData()) {
            $events = BcUtil::offEvent($this->BlogPostsService->BlogPosts->getEventManager(), 'Model.beforeMarshal');
            $request = $controller->getRequest();
            $postArray = $request->getData();
            if ($request->getQuery('preview') === 'draft') {
                $postArray['detail'] = $postArray['detail_draft'];
            }
            $this->BlogPostsService->BlogPosts->patchEntity(
                $vars['post'],
                $this->BlogPostsService->BlogPosts->saveTmpFiles($postArray, mt_rand(0, 99999999))->toArray()
            );
            BcUtil::onEvent($this->BlogPostsService->BlogPosts->getEventManager(), 'Model.beforeMarshal', $events);
        }

        $controller->set($vars);
        $controller->viewBuilder()->setTemplate($this->getSingleTemplate($blogContent));
    }

    /**
     * 一覧用のテンプレート名を取得する
     * @param BlogContent|EntityInterface|array $blogContent
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndexTemplate(BlogContent $blogContent): string
    {
        return 'Blog/' . $blogContent->template . DS . 'index';
    }

    /**
     * アーカイブページ用のテンプレート名を取得する
     *
     * ブログコンテンツの設定に依存する
     *
     * @param BlogContent $blogContent
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getArchivesTemplate(BlogContent $blogContent): string
    {
        return 'Blog/' . $blogContent->template . DS . 'archives';
    }

    /**
     * ブログ詳細ページ用のテンプレート名を取得する
     *
     * ブログコンテンツの設定に依存する
     *
     * @param BlogContent $blogContent
     * @return string
     * @checked
     * @noTodo
     */
    public function getSingleTemplate(BlogContent $blogContent): string
    {
        return 'Blog/' . $blogContent->template . DS . 'single';
    }

}
