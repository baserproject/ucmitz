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

namespace BaserCore\Model\Table;

use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Model\AppTable;
use BaserCore\Model\Behavior\BcKeyValueBehavior;
use Cake\Validation\Validator;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Utility\BcUtil;
use Cake\Utility\Inflector;
use Cake\Filesystem\Folder;

/**
 * Class SiteConfig
 *
 * システム設定モデル
 *
 * @package Baser.Model
 * @mixin BcKeyValueBehavior
 */
class SiteConfigsTable extends AppTable
{

    /**
     * Trait
     */
    use BcEventDispatcherTrait;

    /**
     * Initialize
     *
     * @param array $config テーブル設定
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('BaserCore.BcKeyValue');
    }

    /**
     * Validation Default
     *
     * @param Validator $validator
     * @return Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('name')
            ->maxLength('name', 255, __d('baser', '255文字以内で入力してください。'))
            ->notEmptyString('name', __d('baser', '設定名を入力してください。'));
        $validator
            ->scalar('value')
            ->maxLength('value', 65535, __d('baser', '65535文字以内で入力してください。'))
            ->notEmptyString('value', __d('baser', '設定値を入力してください。'));
        return $validator;
    }

    /**
     * Validation Default
     *
     * @param Validator $validator
     * @return Validator
     */
    public function validationKeyValue(Validator $validator): Validator
    {
        $validator->setProvider('siteConfig', 'BaserCore\Model\Validation\SiteConfigValidation');
        $validator
            ->scalar('formal_name')
            ->maxLength('formal_name', 255, __d('baser', 'Webサイト名は255文字以内で入力してください。'))
            ->notEmptyString('formal_name', __d('baser', 'Webサイト名を入力してください。'));
        $validator
            ->scalar('email')
            ->email('email', 255, __d('baser', '管理者メールアドレスの形式が不正です。'))
            ->notEmptyString('email', __d('baser', '管理者メールアドレスを入力してください。'));
        $validator
            ->scalar('mail_encode')
            ->notEmptyString('mail_encode', __d('baser', 'メール送信文字コードを入力してください。初期値は「ISO-2022-JP」です。'));
        $validator
            ->scalar('main_site_display_name')
            ->notEmptyString('main_site_display_name', __d('baser', 'メインサイト表示名を入力してください。'));
        $validator
            ->scalar('site_url')
            ->notEmptyString('site_url', __d('baser', 'WebサイトURLを入力してください。'));
        $validator
            ->scalar('admin_ssl')
            ->add('admin_ssl', [
                'adminSSlSslUrlExists' => [
                    'rule' => 'sslUrlExists',
                    'provider' => 'siteConfig',
                    'message' => __d('baser', '管理画面をSSLで利用するには、SSL用のWebサイトURLを入力してください。')
                ]]);
        return $validator;
    }

    /**
     * テーマの一覧を取得する
     * @return array
     */
    public function getThemes()
    {
        $themes = [];
        $themeFolder = new Folder(APP . 'View' . DS . 'theme' . DS);
        $_themes = $themeFolder->read(true, true);
        foreach($_themes[0] as $theme) {
            $themes[$theme] = Inflector::camelize($theme);
        }
        $themeFolder = new Folder(WWW_ROOT . 'theme' . DS);
        $_themes = array_merge($themes, $themeFolder->read(true, true));
        foreach($_themes[0] as $theme) {
            $themes[$theme] = Inflector::camelize($theme);
        }
        return $themes;
    }

    /**
     * コントロールソースを取得する
     * @param string $field
     * @return mixed array | false
     */
    public function getControlSource($field = null)
    {
        $controlSources['mode'] = [-1 => __d('baser', 'インストールモード'), 0 => __d('baser', 'ノーマルモード'), 1 => __d('baser', 'デバッグモード１'), 2 => __d('baser', 'デバッグモード２')];
        if (isset($controlSources[$field])) {
            return $controlSources[$field];
        } else {
            return false;
        }
    }

    /**
     * コンテンツ一覧を表示してから、コンテンツの並び順が変更されていないかどうか
     * @param $listDisplayed
     * @return bool
     */
    public function isChangedContentsSortLastModified($listDisplayed)
    {
        $siteConfigs = $this->getKeyValue();
        $changed = false;
        if (!empty($siteConfigs['contents_sort_last_modified'])) {
            $user = BcUtil::loginUser();
            $lastModified = $siteConfigs['contents_sort_last_modified'];
            [$lastModified, $userId] = explode('|', $lastModified);
            $lastModified = strtotime($lastModified);
            if ($user['id'] != $userId) {
                $listDisplayed = strtotime($listDisplayed);
                // 60秒はブラウザのロード時間を加味したバッファ
                if ($lastModified >= ($listDisplayed - 60)) {
                    $changed = true;
                }
            }
        }
        return $changed;
    }

    /**
     * コンテンツ並び順変更時間を更新する
     */
    public function updateContentsSortLastModified()
    {
        $siteConfigs = $this->getKeyValue();
        $user = BcUtil::loginUser();
        $siteConfigs['contents_sort_last_modified'] = date('Y-m-d H:i:s') . '|' . $user['id'];
        $this->saveKeyValue($siteConfigs);
    }

    /**
     * コンテンツ並び替え順変更時間をリセットする
     */
    public function resetContentsSortLastModified()
    {
        $siteConfigs['contents_sort_last_modified'] = '';
        $this->saveKeyValue($siteConfigs);
    }

    /**
     * 指定したフィールドの値がDBのデータと比較して変更状態か確認
     *
     * @param string $field フィールド名
     * @param string $value 値
     * @return bool
     */
    public function isChange($field, $value)
    {
        $siteConfig = $this->getKeyValue();
        if (isset($siteConfig[$field])) {
            return !($siteConfig[$field] === $value);
        } else {
            return false;
        }
    }

}
