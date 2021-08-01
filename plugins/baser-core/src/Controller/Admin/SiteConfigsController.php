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

namespace BaserCore\Controller\Admin;

use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Utility\BcUtil;

/**
 * Class SiteConfigsController
 * @package BaserCore\Controller\Admin
 */
class SiteConfigsController extends BcAdminAppController
{

    /**
     * コンポーネント
     *
     * @var array
     */
    // TODO 未実装のため代替措置
    /* >>>
    public $components = ['BcManager'];
    <<< */

    /**
     * initialize
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['admin_ajax_credit', 'jquery_base_url']);
    }

    /**
     * 基本設定
     */
    public function index(SiteConfigsServiceInterface $siteConfigs)
    {
        if ($this->request->is('post')) {
            $siteConfig = $siteConfigs->update($this->getRequest()->getData());
            if (!$siteConfig->getErrors()) {
                BcUtil::clearAllCache();
                $this->BcMessage->setInfo(__d('baser', 'システム設定を保存しました。'));
                return $this->redirect(['action' => 'index']);
            }
            $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
        } else {
            $siteConfig = $siteConfigs->get();
        }
        $this->set('siteConfig', $siteConfig);
    }

    /**
     * キャッシュファイルを全て削除する
     */
    public function admin_del_cache()
    {
        $this->_checkReferer();
        BcUtil::clearAllCache();
        $this->BcMessage->setInfo(__d('baser', 'サーバーキャッシュを削除しました。'));
        $this->redirect($this->referer());
    }

    /**
     * [ADMIN] PHPINFOを表示する
     */
    public function admin_info()
    {

        $this->setTitle(__d('baser', '環境情報'));
        $datasources = ['csv' => 'CSV', 'sqlite' => 'SQLite', 'mysql' => 'MySQL', 'postgres' => 'PostgreSQL'];
        $db = ConnectionManager::getDataSource('default');
        [$type, $name] = explode('/', $db->config['datasource'], 2);
        $datasource = preg_replace('/^bc/', '', strtolower($name));
        $this->set('datasource', @$datasources[$datasource]);
        $this->set('baserVersion', $this->siteConfigs['version']);
        $this->set('cakeVersion', Configure::version());
        $this->subMenuElements = ['site_configs', 'tools'];
        $this->crumbs = [
            ['name' => __d('baser', 'システム設定'), 'url' => ['controller' => 'site_configs', 'action' => 'index']],
            ['name' => __d('baser', 'ユーティリティ'), 'url' => ['controller' => 'tools', 'action' => 'index']]
        ];

    }

    /**
     * [ADMIN] PHP INFO
     */
    public function admin_phpinfo()
    {
        $this->layout = 'empty';
    }

    /**
     * メールの送信テストを実行する
     */
    public function admin_check_sendmail()
    {

        if (empty($this->request->getData('SiteConfig'))) {
            $this->ajaxError(500, __d('baser', 'データが送信できませんでした。'));
        }
        $this->siteConfigs = $this->request->getData('SiteConfig');
        if (!$this->sendMail(
            $this->siteConfigs['email'], __d('baser', 'メール送信テスト'),
            sprintf('%s からのメール送信テストです。', $this->siteConfigs['formal_name']) . "\n" . Configure::read('BcEnv.siteUrl')
        )) {
            $this->ajaxError(500, __d('baser', 'ログを確認してください。'));
            return;
        }

        exit();
    }

    /**
     * クレジット表示用データをレンダリング
     */
    public function admin_ajax_credit()
    {

        $this->layout = 'ajax';
        Configure::write('debug', 0);

        $specialThanks = [];
        if (!Configure::read('Cache.disable') && Configure::read('debug') == 0) {
            $specialThanks = Cache::read('special_thanks', '_bc_env_');
        }

        if ($specialThanks) {
            $json = json_decode($specialThanks);
        } else {
            try {
                $json = file_get_contents(Configure::read('BcApp.specialThanks'), true);
            } catch (Exception $ex) {
            }
            if ($json) {
                if (!Configure::read('Cache.disable')) {
                    Cache::write('special_thanks', $json, '_bc_env_');
                }
                $json = json_decode($json);
            } else {
                $json = null;
            }

        }

        if ($json == false) {
            $this->ajaxError(500, __d('baser', 'スペシャルサンクスデータが取得できませんでした。'));
        }
        $this->set('credits', $json);

    }

}
