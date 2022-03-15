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

namespace BcFavorite\Controller\Api;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Controller\Api\BcApiController;
use BcFavorite\Service\FavoriteServiceInterface;
use Exception;

/**
 * Class FavoritesController
 */
class FavoritesController extends BcApiController
{

    /**
     * お気に入り情報取得
     * @param FavoriteServiceInterface $favorites
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(FavoriteServiceInterface $favorites, $id)
    {
        $this->set([
            'favorite' => $favorites->get($id)
        ]);
        $this->viewBuilder()->setOption('serialize', ['favorite']);
    }

    /**
     * お気に入り情報一覧取得
     * @param FavoriteServiceInterface $favorites
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(FavoriteServiceInterface $favorites)
    {
        $this->set([
            'favorites' => $this->paginate($favorites->getIndex($this->request->getQueryParams()))
        ]);
        $this->viewBuilder()->setOption('serialize', ['favorites']);
    }

    /**
     * お気に入り情報登録
     * @param FavoriteServiceInterface $favorites
     * @checked
     * @noTodo
     */
    public function add(FavoriteServiceInterface $favorites)
    {
        $this->request->allowMethod(['post', 'delete']);
        try {
            $favorite = $favorites->create($this->request->getData());
            $message = __d('baser', 'お気に入り「{0}」を追加しました。', $favorite->name);
        } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
            $favorite = $e->getEntity();
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '入力エラーです。内容を修正してください。');
        }
        $this->set([
            'message' => $message,
            'favorite' => $favorite,
            'errors' => $favorite->getErrors(),
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'favorite', 'errors']);
    }

    /**
     * お気に入り情報編集
     * @param FavoriteServiceInterface $favorites
     * @param $id
     * @checked
     * @noTodo
     */
    public function edit(FavoriteServiceInterface $favorites, $id)
    {
        $this->request->allowMethod(['post', 'put']);
        $favorite = $favorites->update($favorites->get($id), $this->request->getData());
        if (!$favorite->getErrors()) {
            $message = __d('baser', 'お気に入り「{0}」を更新しました。', $favorite->name);
        } else {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '入力エラーです。内容を修正してください。');
        }
        $this->set([
            'message' => $message,
            'favorite' => $favorite,
            'errors' => $favorite->getErrors(),
        ]);
        $this->viewBuilder()->setOption('serialize', ['favorite', 'message', 'errors']);
    }

    /**
     * お気に入り情報削除
     * @param FavoriteServiceInterface $favorites
     * @param int $id
     * @checked
     * @noTodo
     */
    public function delete(FavoriteServiceInterface $favorites, $id)
    {
        $this->request->allowMethod(['post', 'delete']);
        $favorite = $favorites->get($id);
        try {
            if ($favorites->delete($id)) {
                $message = __d('baser', 'お気に入り: {0} を削除しました。', $favorite->name);
            }
        } catch (Exception $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
        }
        $this->set([
            'message' => $message,
            'favorite' => $favorite
        ]);
        $this->viewBuilder()->setOption('serialize', ['favorite', 'message']);
    }

    /**
     * [ADMIN] 並び替えを更新する
     *
     * @return void
     */
    public function update_sort()
    {
        $user = $this->BcAuth->user();
        if ($this->request->data) {
            if ($this->Favorite->changeSort($this->request->getData('Sort.id'), $this->request->getData('Sort.offset'), ['Favorite.user_id' => $user['id']])) {
                clearDataCache();
                exit(true);
            }
        }
        $this->ajaxError(400, __d('baser', '無効な処理です。'));
        exit();
    }

    /**
     * よく使う項目の表示状態を保存する
     *
     * @param mixed $open 1 Or ''
     */
    public function save_favorite_box($open = '')
    {
        if ($open === '1' || $open === '') {
            $this->request->getSession()->write('Baser.favorite_box_opened', $open);
        } else {
            $this->setResponse($this->response->withStatus(400));
        }
        $this->viewBuilder()->setOption('serialize', []);
    }

    /**
     * よく使う項目の表示状態を取得する
     *
     * @return  1 Or ''
     */
    public function get_favorite_box_opened()
    {
        $result = $this->request->getSession()->read('Baser.favorite_box_opened');
        $this->set([
            'result' => $result
        ]);
        $this->viewBuilder()->setOption('serialize', ['result']);
    }
}
