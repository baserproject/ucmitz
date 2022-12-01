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
namespace BcBlog\Controller\Api;

use BaserCore\Controller\Api\BcApiController;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Error\BcException;
use BcBlog\Service\BlogCommentsService;
use BcBlog\Service\BlogCommentsServiceInterface;
use Cake\ORM\Exception\PersistenceFailedException;
use Throwable;

/**
 * BlogCommentsController
 */
class BlogCommentsController extends BcApiController
{

    /**
     * [API] ブログコメント一覧取得
     *
     * @param BlogCommentsServiceInterface $blogCommentsService
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(BlogCommentsServiceInterface $blogCommentsService)
    {
        $this->set([
            'blogComments' => $this->paginate($blogCommentsService->getIndex($this->request->getQueryParams()))
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogComments']);
    }

    /**
     * [API] 単一ブログコメントー取得
     *
     * @param BlogCommentsServiceInterface $service
     * @param $blogCommentId
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(BlogCommentsServiceInterface $service, $blogCommentId)
    {
        $this->set([
            'blogComment' => $service->get($blogCommentId)
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogComment']);
    }

    /**
     * [API] ブログコメント削除
     * @param BlogCommentsServiceInterface $service
     * @param $blogCommentId
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(BlogCommentsServiceInterface $service, $blogCommentId)
    {
        $this->request->allowMethod(['post', 'put']);
        try {
            $blogComment = $service->get($blogCommentId);
            $service->delete($blogCommentId);
            $message = __d('baser', 'ブログコメント「{0}」を削除しました。', $blogComment->no);
        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $blogComment = $e->getEntity();
            $message = __d('baser', '入力エラーです。内容を修正してください。');
        } catch (Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }
        $this->set([
            'message' => $message,
            'blogComment' => $blogComment,
            'errors' => $blogComment->getErrors()
        ]);
        $this->viewBuilder()->setOption('serialize', ['blogComment', 'message', 'errors']);
    }

    /**
     * ブログコメントのバッチ処理
     *
     * 指定したブログのコメントに対して削除、公開、非公開の処理を一括で行う
     *
     * ### エラー
     * 受け取ったPOSTデータのキー名'batch'が'delete','publish','unpublish'以外の値であれば500エラーを発生させる
     *
     * @param BlogCommentsService $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function batch(BlogCommentsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);
        $allowMethod = [
            'delete' => '削除',
            'publish' => '公開',
            'unpublish' => '非公開に'
        ];
        $method = $this->getRequest()->getData('batch');
        if (!isset($allowMethod[$method])) {
            $this->setResponse($this->response->withStatus(500));
            $this->viewBuilder()->setOption('serialize', []);
            return;
        }
        $targets = $this->getRequest()->getData('batch_targets');
        try {
            $service->batch($method, $targets);
            $this->BcMessage->setSuccess(
                sprintf(__d('baser', 'ブログコメント「%s」を %s しました。'), implode(', ', $targets), $allowMethod[$method]),
                true,
                false
            );
            $message = __d('baser', '一括処理が完了しました。');
        } catch (BcException $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', $e->getMessage());
        }
        $this->set(['message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

    /**
     * [AJAX] ブログコメントを登録する
     *
     * 画像認証を行い認証されればブログのコメントを登録する
     * コメント承認を利用していないブログの場合、公開されているコメント投稿者にアラートを送信する
     *
     * @param string $blogContentId
     * @param string $blogPostId
     * @return void | bool
     */
    public function add($blogContentId, $blogPostId)
    {
        Configure::write('debug', 0);

        if (!$this->request->getData() || !$blogContentId || !$blogPostId) {
            $this->notFound();
            return;
        }

        if (empty($this->blogContent)) {
            $this->notFound();
            return;
        }

        if (!$this->blogContent['BlogContent']['comment_use']) {
            $this->notFound();
            return;
        }

        // 画像認証を行う
        $captchaResult = true;
        if ($this->blogContent['BlogContent']['auth_captcha']) {
            $captchaResult = $this->BcCaptcha->check(
                $this->request->getData('BlogComment.auth_captcha'),
                $this->request->getData('BlogComment.captcha_id')
            );
            if (!$captchaResult) {
                $this->set('dbData', false);
                return false;
            }
            $this->request = $this->request->withData('BlogComment.auth_captcha', null);
        }

        $result = $this->BlogComment->add(
            $this->request->getData(),
            $blogContentId,
            $blogPostId,
            $this->blogContent['BlogContent']['comment_approve']
        );
        if (!$result || !$captchaResult) {
            $this->set('dbData', false);
            return;
        }

        $content = $this->BlogPost->BlogContent->Content->findByType(
            'BcBlog.BlogContent',
            $this->blogContent['BlogContent']['id']
        );
        $this->request = $this->request->withData('Content',  $content['Content']);
        $this->_sendCommentAdmin(
            $blogPostId,
            $this->request->getData()
        );
        // コメント承認機能を利用していない場合は、公開されているコメント投稿者にアラートを送信
        if (!$this->blogContent['BlogContent']['comment_approve']) {
            $this->_sendCommentContributor(
                $blogPostId,
                $this->request->getData()
            );
        }
        $this->set('dbData', $result['BlogComment']);
    }

}
