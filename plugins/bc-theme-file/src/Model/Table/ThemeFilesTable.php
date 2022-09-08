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

namespace BcThemeFile\Model\Table;

use BaserCore\Model\Table\AppTable;

/**
 * Class ThemeFile
 *
 * テーマファイルモデル
 *
 * @package Baser.Model
 */
class ThemeFilesTable extends AppTable
{

    /**
     * use table
     *
     * @var boolean
     */
    public $useTable = false;

    /**
     * ThemeFile constructor.
     *
     * @param bool $id
     * @param null $table
     * @param null $ds
     */
    public function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->validate = [
            'name' => [
                ['rule' => ['notBlank'], 'message' => __d('baser', 'テーマファイル名を入力してください。'), 'required' => true],
                ['rule' => ['duplicateThemeFile'], 'on' => 'create', 'message' => __d('baser', '入力されたテーマファイル名は、同一階層に既に存在します。')]]
        ];
    }

    /**
     * ファイルの重複チェック
     *
     * @param array $check
     * @return    boolean
     */
    public function duplicateThemeFile($check)
    {
        if (!$check[key($check)]) {
            return true;
        }
        $targetPath = $this->data['ThemeFile']['parent'] . $check[key($check)] . '.' . $this->data['ThemeFile']['ext'];
        if (is_file($targetPath)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * データの存在確認
     * validates の、on オプションを動作する為に定義
     * @param int $id
     * @return bool
     */
    public function exists($conditions): bool
    {
        $data = $this->data['ThemeFile'];
        if (empty($data['parent']) || empty($data['name']) || empty($data['ext'])) {
            return false;
        }
        return (is_file($data['parent'] . $data['name'] . '.' . $data['ext']) && $this->id !== false);
    }

}
