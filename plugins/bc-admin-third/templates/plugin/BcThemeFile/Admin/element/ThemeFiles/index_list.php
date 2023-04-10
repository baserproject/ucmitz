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

/**
 * [ADMIN] テーマファイル一覧　テーブル
 *
 * @var \BaserCore\View\BcAdminAppView $this
 * @var string $theme
 * @var string $plugin
 * @var string $type
 * @var string $path
 * @var bool $isDefaultTheme
 * @checked
 * @unitTest
 * @noTodo
 */
$this->BcListTable->setColumnNumber(3);
?>


<div class="bca-data-list__top">
  <!-- 一括処理 -->
  <?php if ($this->BcBaser->isAdminUser() && !$isDefaultTheme): ?>
    <div class="bca-action-table-listup">
      <?php echo $this->BcAdminForm->control('batch', ['type' => 'select',
        'options' => [
          'delete' => __d('baser_core', '削除')
        ],
        'empty' => __d('baser_core', '一括処理'), 'data-bca-select-size' => 'lg']) ?>
      <?php echo $this->BcAdminForm->button(__d('baser_core', '適用'), [
        'id' => 'BtnApplyBatch',
        'disabled' => 'disabled',
        'class' => 'bca-btn',
        'data-bca-btn-size' => 'lg'
      ]) ?>
    </div>
  <?php endif ?>
</div>


<table class="list-table bca-table-listup" id="ListTable">
  <thead class="bca-table-listup__thead">
  <tr>
    <th class="list-tool bca-table-listup__thead-th bca-table-listup__thead-th--select" title="<?php echo __d('baser_core', '一括選択') ?>">
      <?php if ($this->BcBaser->isAdminUser() && !$isDefaultTheme): ?>
        <?php echo $this->BcAdminForm->control('checkall', ['type' => 'checkbox', 'label' => ' ', 'title' => __d('baser_core', '一括選択')]) ?>
      <?php endif ?>

      <?php if ($path): ?>
        <?php $this->BcBaser->link('', ['action' => 'index', $theme, $plugin, $type, dirname($path)], [
          'title' => __d('baser_core', '上へ移動'),
          'class' => 'bca-btn-icon',
          'data-bca-btn-type' => 'up-directory',
          'data-bca-btn-size' => 'lg',
          'aria-label' => __d('baser_core', '一つ上の階層へ')
        ]) ?>
      <?php endif ?>
    </th>
    <th class="bca-table-listup__thead-th"><?php echo __d('baser_core', 'フォルダ名') ?>
      ／<?php echo __d('baser_core', 'テーマファイル名') ?></th>
    <?php echo $this->BcListTable->dispatchShowHead() ?>
    <th class="bca-table-listup__thead-th">
      <?php echo __d('baser_core', 'アクション') ?>
    </th>
  </tr>
  </thead>
  <tbody class="bca-table-listup__tbody">
  <?php if (!empty($themeFiles)): ?>
    <?php foreach($themeFiles as $themeFile): ?>
      <?php $this->BcBaser->element('ThemeFiles/index_row', ['themeFile' => $themeFile]) ?>
    <?php endforeach; ?>
  <?php else: ?>
    <tr>
      <td colspan="<?php echo $this->BcListTable->getColumnNumber() ?>">
        <p class="no-data">
          <?php echo __d('baser_core', 'データが見つかりませんでした。') ?>
        </p>
      </td>
    </tr>
  <?php endif; ?>
  </tbody>
</table>
