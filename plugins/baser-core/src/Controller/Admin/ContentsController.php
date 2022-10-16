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

namespace BaserCore\Controller\Admin;

use BaserCore\Controller\Component\BcAdminContentsComponent;
use BaserCore\Controller\Component\BcMessageComponent;
use BaserCore\Model\Table\ContentFoldersTable;
use BaserCore\Model\Table\ContentsTable;
use BaserCore\Model\Table\SiteConfigsTable;
use BaserCore\Model\Table\SitesTable;
use BaserCore\Model\Table\UsersTable;
use BaserCore\Service\Admin\ContentsAdminServiceInterface;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Service\SitesServiceInterface;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\ORM\ResultSet;
use Cake\Utility\Hash;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class ContentsController
 *
 * 統合コンテンツ管理 コントローラー
 *
 * baserCMS内のコンテンツを統合的に管理する
 *
 * @package BaserCore.Controller
 * @property ContentsTable $Contents
 * @property SiteConfigsTable $SiteConfigs
 * @property SitesTable $Sites
 * @property UsersTable $Users
 * @property ContentFoldersTable $ContentFolders
 * @property BcAdminContentsComponent $BcAdminContents
 * @property BcMessageComponent $BcMessage
 */

class ContentsController extends BcAdminAppController
{
    /**
     * initialize
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('BaserCore.BcAdminContents', ['entityVarName' => 'content']);
    }

    /**
     * beforeFilter
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Security->setConfig('unlockedActions', ['delete', 'batch', 'trash_return']);
    }

    /**
     * コンテンツ一覧
     * @param  ContentsAdminServiceInterface $service
     * @param  SiteConfigsServiceInterface $siteConfigService
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(ContentsAdminServiceInterface $service, SiteConfigsServiceInterface $siteConfigService)
    {
        $this->setViewConditions('Contents', ['default' => [
            'query' => [
                'site_id' => $this->request->getAttribute('currentSite')? $this->request->getAttribute('currentSite')->id : 1,
                'list_type' => $this->request->getQuery('list_type') ?? 1,
            ]
        ]]);

        switch($this->getRequest()->getQuery('list_type')) {
            case 1:
                // 並び替え最終更新時刻をリセット
                $siteConfigService->resetValue('contents_sort_last_modified');
                $contents = $service->getTreeIndex($this->request->getQueryParams());
                break;
            case 2:
                $this->setViewConditions('Contents', ['default' => [
                    'query' => [
                        'num' => $siteConfigService->getValue('admin_list_num'),
                        'sort' => 'id',
                        'direction' => 'asc',
                    ]
                ]]);

                // EVENT Contents.searchIndex
                $event = $this->dispatchLayerEvent('searchIndex', [
                    'request' => $this->request
                ]);
                if ($event !== false) {
                    $this->request = ($event->getResult() === null || $event->getResult() === true)? $event->getData('request') : $event->getResult();
                }

                $contents = $this->paginate($service->getTableIndex($this->request->getQueryParams()));
                break;
        }

        $this->setRequest($this->getRequest()->withData('ViewSetting.list_type', $this->getRequest()->getQuery('list_type')));
        $this->set($service->getViewVarsForIndex($this->getRequest(), $contents));
    }

    /**
     * ゴミ箱内のコンテンツ一覧を表示する
     *
     * @param  ContentsServiceInterface $contentService
     * @checked
     * @noTodo
     * @unitTest
     */
    public function trash_index(ContentsAdminServiceInterface $service)
    {
        $this->setViewConditions('Contents', ['default' => [
            'query' => [
                'site_id' => $this->request->getAttribute('currentSite')? $this->request->getAttribute('currentSite')->id : 1,
            ]
        ]]);
        $contents = $service->getTrashIndex($this->request->getQueryParams(), 'threaded')->order(['site_id', 'lft']);
        $this->set($service->getViewVarsForTrashIndex($contents));
    }

    /**
     * ゴミ箱のコンテンツを戻す
     *
     * @param  ContentsServiceInterface $contentService
     * @param  int $id
     * @return Response|void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function trash_return(ContentsServiceInterface $contentService, $id)
    {
        if (empty($id)) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }
        $this->disableAutoRender();
        // EVENT Contents.beforeTrashReturn
        $this->dispatchLayerEvent('beforeTrashReturn', [
            'data' => $id
        ]);
        if ($restored = $contentService->restore($id)) {
            $this->BcMessage->setSuccess(sprintf(__d('baser', 'ゴミ箱「%s」を戻しました。'), $restored->title));
        } else {
            $this->BcMessage->setError('ゴミ箱から戻す事に失敗しました。');
        }
        // EVENT Contents.afterTrashReturn
        $this->dispatchLayerEvent('afterTrashReturn', [
            'data' => $id
        ]);
        return $this->redirect(['action' => 'trash_index']);
    }

    /**
     * コンテンツ編集
     *
     * @param  int $id
     * @param  ContentsServiceInterface $contentService
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(ContentsServiceInterface $contentService, $id)
    {
        if (!$id && empty($this->request->getData())) {
            $this->BcMessage->setError(__d('baser', '無効な処理です。'));
            return $this->redirect(['action' => 'index']);
        }
        $content = $contentService->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            try {
                $content = $contentService->update($content, $this->request->getData('Contents'));
                $message = Configure::read('BcContents.items.' . $this->request->getData('Contents.plugin') . '.' . $this->request->getData('Contents.type') . '.title') .
                sprintf(__d('baser', '「%s」を更新しました。'), $this->request->getData('Contents.title'));
                $this->BcMessage->setSuccess($message);
                return $this->redirect(['action' => 'edit', $content->id]);
            } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
                $content = $e->getEntity();
                $this->BcMessage->setError('保存中にエラーが発生しました。入力内容を確認してください。');
            }
        }
        $this->request = $this->request->withData("Contents", $content);
        $this->set('content', $content);
    }

    /**
     * エイリアスを編集する
     *
     * @param $id
     * @param  ContentsServiceInterface $contentService
     * @return Response|null
     * @throws Exception
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit_alias(ContentsServiceInterface $contentService, $id)
    {
        if (!$id && empty($this->request->getData())) {
            $this->BcMessage->setError(__d('baser', '無効な処理です。'));
            return $this->redirect(['action' => 'index']);
        }
        $alias = $contentService->get($id);
        if ($this->request->is(['post', 'put'])) {
            if (BcUtil::isOverPostSize()) {
                $this->BcMessage->setError(__d('baser', '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。', ini_get('post_max_size')));
                $this->redirect(['action' => 'edit_alias', $id]);
            }
            try {
                $alias = $contentService->update($alias, $this->request->getData('Contents'));
                $content = $contentService->get($alias->alias_id);
                $message = Configure::read('BcContents.items.' . $content->plugin . '.' . $content->type . '.title') .
                sprintf(__d('baser', '「%s」のエイリアス「%s」を編集しました。'), $content->title, $alias->title);
                $this->BcMessage->setSuccess($message);
                $this->redirect(['action' => 'edit_alias', $id]);
            } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
                $alias = $e->getEntity();
                $this->BcMessage->setError("保存中にエラーが発生しました。入力内容を確認してください。\n" . $alias->getErrors());
            }
        } else {
            $this->request = $this->request->withData('Contents', $alias);
            if (!$this->request->getData()) {
                $this->BcMessage->setError(__d('baser', '無効な処理です。'));
                $this->redirect(['action' => 'index']);
            }
        }
        $this->set('content', $alias);
    }

    /**
	 * コンテンツ削除（論理削除）
     * @param  ContentsServiceInterface $contentService
	 */
	public function delete(ContentsServiceInterface $contentService)
	{
        $this->disableAutoRender();
        $this->viewBuilder()->disableAutoLayout();
		if (empty($this->request->getData())) {
			$this->notFound();
		}
        if ($this->request->is(['post', 'put', 'delete'])) {
            // TODO: ページ実装時に汎用化する
            $data = $this->request->getData('Contents') ?? $this->request->getData('ContentFolders.content');
            $id = $data['id'];
            // EVENT Contents.beforeDelete
            $this->dispatchLayerEvent('beforeDelete', [
                'data' => $id
            ]);
            $content = $contentService->get($id);
            if ($contentService->delete($id)) {
                // EVENT Contents.afterDelete
                $this->dispatchLayerEvent('afterDelete', [
                    'data' => $id
                ]);
                $typeName = Configure::read('BcContents.items.' . $content->plugin . '.' . $content->type . '.title');
                $trashMessage = $typeName . sprintf(__d('baser', '「%s」をゴミ箱に移動しました。'), $content->title);
                $aliasMessage = sprintf(__d('baser', '%s のエイリアス「%s」を削除しました。'), $typeName, $content->title);
                $this->BcMessage->setSuccess($content->alias_id ? $aliasMessage : $trashMessage, true);
                $this->redirect(['action' => 'index']);
            } else {
                $this->BcMessage->setError('削除中にエラーが発生しました。');
            }
        } else {
            $this->BcMessage->setError('不正なリクエストです。');
        }
	}

}
