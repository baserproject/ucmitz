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

namespace BaserCore\Service\Admin;

use BaserCore\Model\Entity\User;
use Cake\Datasource\EntityInterface;
use Cake\Http\ServerRequest;
use Cake\ORM\Query;
use Exception;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface UsersServiceInterface
 * @package BaserCore\Service
 */
interface UserManageServiceInterface
{

    /**
     * ログイン
     * @param ServerRequest $request
     * @param ResponseInterface $response
     * @param $id
     * @return array|false
     */
    public function login(ServerRequest $request, ResponseInterface $response, $id);

    /**
     * ログアウト
     * @param ServerRequest $request
     * @param ResponseInterface $response
     * @return array|false
     */
    public function logout(ServerRequest $request, ResponseInterface $response, $id);

    /**
     * 認証用のセッションキーを取得
     * @param string $prefix
     * @return false|string
     */
    public function getAuthSessionKey($prefix);

    /**
     * 再ログイン
     * @param ServerRequest $request
     * @param ResponseInterface $response
     * @return array|false
     */
    public function reLogin(ServerRequest $request, ResponseInterface $response);

    /**
     * ログイン状態の保存のキー送信
     * @param ResponseInterface
     * @param int $id
     * @return ResponseInterface
     */
    public function setCookieAutoLoginKey($response, $id): ResponseInterface;

    /**
     * ログインキーを削除する
     * @param int $id
     * @return int 削除行数
     */
    public function removeLoginKey($id);

    /**
     * ログイン状態の保存確認
     * @return ResponseInterface
     */
    public function checkAutoLogin(ServerRequest $request, ResponseInterface $response): ResponseInterface;

    /**
     * 代理ログインを行う
     * @param ServerRequest $request
     * @param int $id
     * @param string $referer
     */
    public function loginToAgent(ServerRequest $request, ResponseInterface $response, $id, $referer = '');

    /**
     * 代理ログインから元のユーザーに戻る
     * @param ServerRequest $request
     * @param ResponseInterface $response
     * @return array|mixed|string
     * @throws Exception
     */
    public function returnLoginUserFromAgent(ServerRequest $request, ResponseInterface $response);

    /**
     * サイト全体の設定値を取得する
     * @param string $name
     * @return mixed
     */
    public function getSiteConfig($name);

}
