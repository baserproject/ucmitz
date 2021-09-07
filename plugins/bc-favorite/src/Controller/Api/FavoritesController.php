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

use App\Controller\AppController;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BcFavorite\Service\FavoriteServiceInterface;
use Exception;

/**
 * Class Favorite
 */
class FavoritesController extends AppController
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
        $favorite = $favorites->create($this->request->getData());
        if (!$favorite->getErrors()) {
            $message = __d('baser', 'お気に入り「{0}」を追加しました。', $favorite->name);
        } else {
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

}
