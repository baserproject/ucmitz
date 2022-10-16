<?php
/**
 * baserCMS :  Based Webcontent Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Controller\Api;

use BaserCore\Error\BcException;
use Exception;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\Note;
use BaserCore\Service\ContentsService;
use BaserCore\Model\Table\SiteConfigsTable;
use BaserCore\Service\ContentsServiceInterface;

/**
 * Class ContentsController
 */
class ContentsController extends BcApiController
{

    /**
     * コンテンツ情報取得
     * @param ContentsServiceInterface $Contents
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(ContentsServiceInterface $contentService, $id)
    {
        $this->set([
            'content' => $contentService->get($id)
        ]);
        $this->viewBuilder()->setOption('serialize', ['content']);
    }
    /**
     * ゴミ箱情報取得
     * @param ContentsServiceInterface $Contents
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view_trash(ContentsServiceInterface $contentService, $id)
    {
        $this->set([
            'trash' => $contentService->getTrash($id)
        ]);
        $this->viewBuilder()->setOption('serialize', ['trash']);
    }

    /**
     * コンテンツ情報一覧取得
     *
     * @param  ContentsServiceInterface $contentService
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(ContentsServiceInterface $contentService, $type="index")
    {
        switch ($type) {
            case "index":
                $data = $this->paginate($contentService->getIndex($this->request->getQueryParams()));
                break;
            case "trash":
                $data = $this->paginate($contentService->getTrashIndex($this->request->getQueryParams(), 'threaded')->order(['site_id', 'lft']));
                break;
            case "tree":
                $data = $this->paginate($contentService->getTreeIndex($this->request->getQueryParams()));
                break;
            case "table":
                $data = $this->paginate($contentService->getTableIndex($this->request->getQueryParams()));
                break;
        }
        $this->set([
            'contents' => $data
        ]);
        $this->viewBuilder()->setOption('serialize', ['contents']);
    }

    /**
     * コンテンツ情報削除(論理削除)
     * ※ 子要素があれば、子要素も削除する
     * @param ContentsServiceInterface $contentService
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(ContentsServiceInterface $contentService)
    {
        $this->request->allowMethod(['post', 'delete']);
        $id = $this->request->getData('id');
        $content = $contentService->get($id);
        $children = $contentService->getChildren($id);
        try {
            if($contentService->deleteRecursive($id)) {
                $text = "コンテンツ: " . $content->title . "を削除しました。";
                if ($children) {
                    $content = array_merge([$content], $children->toArray());
                    foreach ($children as $child) {
                        $text .= "\nコンテンツ: " . $child->title . "を削除しました。";
                    }
                }
                $message = __d('baser', $text);
                $this->set(['content' => $content]);
            }
        } catch (Exception $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
        }
        $this->set(['message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['contents', 'message']);
    }

    /**
     * ゴミ箱内コンテンツ情報を削除する(物理削除)
     * @param ContentsServiceInterface $contentService
     * @param $id
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete_trash(ContentsServiceInterface $contentService, $id)
    {
        $this->request->allowMethod(['post', 'delete']);
        $trash = $contentService->getTrash($id);
        try {
            if ($contentService->hardDeleteWithAssoc($id)) {
                $message = __d('baser', 'ゴミ箱: {0} を削除しました。', $trash->title);
            }
        } catch (Exception $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
        }
        $this->set([
            'message' => $message,
            'trash' => $trash
        ]);
        $this->viewBuilder()->setOption('serialize', ['trash', 'message']);
    }

    /**
     * ゴミ箱を空にする(物理削除)
     * @param ContentsServiceInterface $contentService
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function trash_empty(ContentsServiceInterface $contentService)
    {
        $this->request->allowMethod(['post', 'delete']);
        $trash = $contentService->getTrashIndex($this->request->getQueryParams())->order(['plugin', 'type']);
        $text = "ゴミ箱: ";
        // EVENT Contents.beforetrash_empty
        $this->dispatchLayerEvent('beforetrash_empty', [
            'data' => $trash
        ]);
        try {
            $result = true;
            foreach ($trash as $entity) {
                if(!$contentService->hardDeleteWithAssoc($entity->id)) $result = false;
            }
            $message = __d('baser', 'ゴミ箱を空にしました。');
            // EVENT Contents.aftertrash_empty
            $this->dispatchLayerEvent('aftertrash_empty', [
                'data' => $result
            ]);
        } catch (Exception $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
        }
        $this->set([
            'message' => $message,
            'trash' => $trash
        ]);
        $this->viewBuilder()->setOption('serialize', ['trash', 'message']);
    }
    /**
     * コンテンツ情報編集
     * @param ContentsServiceInterface $contents
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(ContentsServiceInterface $contents, $id)
    {
        $this->request->allowMethod(['post', 'put']);
        try {
            $content = $contents->update($contents->get($id), $this->request->getData());
            $message = __d('baser', 'コンテンツ「{0}」を更新しました。', $content->title);
        } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '入力エラーです。内容を修正してください。');
        }
        $this->set([
            'message' => $message,
            'content' => $content,
            'errors' => $content->getErrors(),
        ]);
        $this->viewBuilder()->setOption('serialize', ['content', 'message', 'errors']);
    }

    /**
     * trash_return
     *
     * コンテンツ情報を元に戻す
     * @param ContentsServiceInterface $contents
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function trash_return(ContentsServiceInterface $contents, $id)
    {
        $this->request->allowMethod(['get', 'head']);
        try {
            if ($restored = $contents->restore($id)) {
                $message = __d('baser', 'ゴミ箱: {0} を元に戻しました。', $restored->title);
            } else {
                $message = __d('baser', 'ゴミ箱の復元に失敗しました');
            }
        } catch (Exception $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
        }
        $this->set([
            'message' => $message,
            'content' => $restored
        ]);
        $this->viewBuilder()->setOption('serialize', ['content', 'message']);
    }

    /**
     * 公開状態を変更する
     * @param ContentsServiceInterface $contentService
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function change_status(ContentsServiceInterface $contentService)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $id = $this->request->getData('id');
        $status = $this->request->getData('status');

		// EVENT Contents.beforeChangeStatus
		$this->dispatchLayerEvent('beforeChangeStatus', [
		    'id' => $id,
		    'status' => $status
		]);

        $result = false;
        if ($id && $status) {
            try {
                switch($status) {
                    case 'publish':
                        $content = $contentService->publish($id);
                        $message = __d('baser', 'コンテンツ: {0} を公開しました。', $content->title);
                        break;
                    case 'unpublish':
                        $content = $contentService->unpublish($id);
                        $message = __d('baser', 'コンテンツ: {0} を非公開にしました。', $content->title);
                        break;
                }
                $result = true;
            } catch (\Exception $e) {
                $this->setResponse($this->response->withStatus(500));
                $message = __d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
            }
        } else {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser',  '無効な処理です。') . "データが不足しています";
        }

		// EVENT Contents.afterChangeStatus
		$this->dispatchLayerEvent('afterChangeStatus', [
		    'id' => $id,
		    'result' => $result
		]);

        $this->set([
            'message' => $message,
        ]);
        $this->viewBuilder()->setOption('serialize', ['content', 'message']);
    }

    /**
     * get_full_url
     *
     * @param  ContentsServiceInterface $contentService
     * @param  int $id
     * @checked
     * @unitTest
     * @noTodo
     */
    public function get_full_url(ContentsServiceInterface $contentService, $id)
    {
        $this->request->allowMethod(['get']);
        if ($id) {
            $this->set(['fullUrl' => $contentService->getUrlById($id, true)]);
        } else {
            $this->setResponse($this->response->withStatus(400));
            $this->set(['message' => __d('baser',  '無効な処理です。')]);
        }
        $this->viewBuilder()->setOption('serialize', ['message', 'fullUrl']);
    }

    /**
     * 指定したIDのコンテンツが存在するか確認する
     * ゴミ箱のものは無視
     *
     * @param  ContentsServiceInterface $contentService
     * @param $id
     * @return Response
     * @checked
     * @noTodo
     * @unitTest
     */
    public function exists(ContentsServiceInterface $contentService, $id)
    {
        $this->request->allowMethod(['get']);
        $this->set(['exists' => $contentService->exists($id)]);
        $this->viewBuilder()->setOption('serialize', ['exists']);
    }

    /**
     * サイトに紐付いたフォルダリストを取得
     * @param ContentsServiceInterface $contentService
     * @param int $siteId
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get_content_folder_list(ContentsServiceInterface $contentService, $siteId)
    {
        $this->request->allowMethod(['get']);
        $this->set(['list' => $contentService->getContentFolderList($siteId,['conditions' => ['site_root' => false]])]);
        $this->viewBuilder()->setOption('serialize', ['list']);
    }

    /**
     * リネーム
     *
     * 新規登録時の初回リネーム時は、name にも保存する
     * @param  ContentsServiceInterface $contentService
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function rename(ContentsServiceInterface $contentService)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        if (empty($this->request->getData('id')) || empty($this->request->getData('title'))) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser', '無効な処理です。');
        }else {
            $oldContent = $contentService->get($this->request->getData('id'));
            $oldTitle = $oldContent->title;
            try {
                $newContent = $contentService->update($oldContent, ['title' => $this->request->getData('title')], ['validate' => false]);
                $this->setResponse($this->response->withStatus(200));
                $url = $contentService->getUrlById($this->request->getData('title'));
                $this->set(['url' => $url]);
                $message = sprintf(
                    '%s%s',
                    Configure::read(
                        sprintf(
                            'BcContents.items.%s.%s.title',
                            $newContent->plugin,
                            $newContent->type
                        )
                    ),
                    sprintf(
                        __d('baser', '「%s」を「%s」に名称変更しました。'),
                        $oldTitle,
                        $newContent->title
                    )
                );
            } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
                $content = $e->getEntity();
                $this->setResponse($this->response->withStatus(500));
                $message = $content->getErrors();
            }
        }
        $this->set(['message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['message', 'url']);
    }

    /**
     * add_alias
     *
     * @param  ContentsServiceInterface $contentService
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add_alias(ContentsServiceInterface $contentService)
    {
        $this->request->allowMethod(['post']);
        try {
            $alias = $contentService->alias($this->request->getData('content'));
            $message = __d('baser', '{0} を作成しました。', $alias->title);
        } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
            $alias = $e->getEntity();
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', "無効な処理です。\n" . $alias->getErrors());
        }
        $this->set(['content' => $alias, 'message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['message', 'content']);
    }

    /**
     * 指定したURLのパス上のコンテンツでフォルダ以外が存在するか確認
     * @param  ContentsServiceInterface $contentService
     * @return \Cake\Http\Response
     * @checked
     * @noTodo
     * @unitTest
     */
    public function is_unique_content(ContentsServiceInterface $contentService)
    {
        $this->request->allowMethod(['post']);
        if (!$this->request->getData('url')) {
            $this->setResponse($this->response->withStatus(500));
        } else {
            return $this->response->withType("application/json")->withStringBody(
                json_encode(!$contentService->existsContentByUrl($this->request->getData('url')))
            );
        }
    }

    /**
     * 並び順を移動する
     * @param  ContentsServiceInterface $contentService
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function move(ContentsServiceInterface $contentService)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $siteConfig = TableRegistry::getTableLocator()->get('BaserCore.SiteConfigs');
        if (empty($this->request->getData())) {
            $message = __d('baser', '無効な処理です。');
            $this->setResponse($this->response->withStatus(400));
        } elseif(!$contentService->exists($this->request->getData('origin.id'))) {
            $message = __d('baser', 'データが存在しません。');
            $this->setResponse($this->response->withStatus(500));
        } elseif($siteConfig->isChangedContentsSortLastModified($this->request->getData('listDisplayed'))) {
            $message = __d('baser', 'コンテンツ一覧を表示後、他のログインユーザーがコンテンツの並び順を更新しました。<br>一度リロードしてから並び替えてください。');
            $this->setResponse($this->response->withStatus(500));
        } elseif (!$contentService->isMovable($this->request->getData('origin.id'), $this->request->getData('target.parentId'))) {
            $message = __d('baser', '同一URLのコンテンツが存在するため処理に失敗しました。（現在のサイトに存在しない場合は、関連サイトに存在します）');
            $this->setResponse($this->response->withStatus(500));;
        } else {
            // 正常系
            $message = $this->execMove($contentService, $siteConfig);
        }
        $this->set(['message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['message', 'url', 'content']);
    }

    /**
     * execMove
     *
     * @param  ContentsService $contentService
     * @param  SiteConfigsTable $siteConfig
     * @return string
     */
    protected function execMove($contentService, $siteConfig)
    {
            $content = $contentService->get($this->request->getData('origin.id'));
            $beforeUrl = $content->url;
            try {
                $result = $contentService->move($this->request->getData('origin'), $this->request->getData('target'));
                if ($this->request->getData('origin.parentId') == $this->request->getData('target.parentId')) {
                    // 親が違う場合は、Contentモデルで更新してくれるが同じ場合更新しない仕様のためここで更新する
                    $siteConfig->updateContentsSortLastModified();
                }
                $message = sprintf(__d('baser', "コンテンツ「%s」の配置を移動しました。\n%s > %s"), $result->title, rawurldecode($beforeUrl), rawurldecode($result->url));
                $url = $contentService->getUrlById($result->id, true);
                $this->set(['url' => $url]);
                $this->set(['content' => $result]);
            } catch(Exception $e) {
                $message = __d('baser', 'データ保存中にエラーが発生しました。' . $e->getMessage());
                $this->setResponse($this->response->withStatus(500));
            }
            return $message;
    }

    /**
     * batch
     *
     * @param  ContentsServiceInterface $contentService
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function batch(ContentsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);
        $allowMethod = [
            'publish' => '公開',
            'unpublish' => '非公開に',
            'delete' => '削除',
        ];
        $method = $this->getRequest()->getData('batch');
        if (!isset($allowMethod[$method])) {
            $this->setResponse($this->response->withStatus(500));
            $this->viewBuilder()->setOption('serialize', []);
            return;
        }
        $targets = $this->getRequest()->getData('batch_targets');
        try {
            $names = $service->getTitlesById($targets);
            $service->batch($method, $targets);
            $this->BcMessage->setSuccess(
                sprintf(__d('baser', 'コンテンツ 「%s」 を %s しました。'), implode('」、「', $names), $allowMethod[$method]),
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
}
