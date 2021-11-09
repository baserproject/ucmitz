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

use BaserCore\View\BcAdminAppView;

/**
 * コンテンツ一覧
 *
 * @var BcAdminAppView $this
 * @var array $folders
 * @var array $authors
 */
?>


<?= $this->BcAdminForm->create(null, ['type' => 'get', 'url' => ['action' => 'index'], 'id' => 'ContentIndexForm'], ) ?>
<?= $this->BcAdminForm->control('open', ['type' => 'hidden', 'value' => true]) ?>
<!-- NOTE: list_typeとsite_idが足りないため補完する -->
<?= $this->BcAdminForm->control('list_type', ['type' => 'hidden', 'value' => 2]) ?>
<?= $this->BcAdminForm->control('site_id', ['type' => 'hidden', 'value' => $contents->first()->site_id]) ?>
<p class="bca-search__input-list">
	<span class="bca-search__input-item">
		<?= $this->BcAdminForm->label('folder_id', __d('baser', 'フォルダ'), ['class' => 'bca-search__input-item-label']) ?>
    <?= $this->BcAdminForm->control('folder_id', ['type' => 'select', 'options' => $folders, 'empty' => __d('baser', '指定なし')]) ?>
	</span>
  <span class="bca-search__input-item">
		<?= $this->BcAdminForm->label('name', __d('baser', '名称'), ['class' => 'bca-search__input-item-label']) ?>
    <?= $this->BcAdminForm->control('name', ['type' => 'text', 'size' => 20]) ?>
	</span>
  <span class="bca-search__input-item">
		<?= $this->BcAdminForm->label('type', __d('baser', 'タイプ'), ['class' => 'bca-search__input-item-label']) ?>
    <?= $this->BcAdminForm->control('type', ['type' => 'select', 'options' => $this->BcAdminContent->getTypes(), 'empty' => __d('baser', '指定なし')]) ?>
	</span>
  <span class="bca-search__input-item">
		<?= $this->BcAdminForm->label('self_status', __d('baser', '公開状態'), ['class' => 'bca-search__input-item-label']) ?>
    <? # echo $this->BcAdminForm->control('self_status', ['type' => 'select', 'options' => $this->BcText->booleanMarkList(), 'empty' => __d('baser', '指定なし')]) ?>
    <?= $this->BcAdminForm->control('self_status', ['type' => 'select', 'options' => '', 'empty' => __d('baser', '指定なし')]) ?>
	</span>
  <span class="bca-search__input-item">
		<?= $this->BcAdminForm->label('author_id', __d('baser', '作成者'), ['class' => 'bca-search__input-item-label']) ?>
    <?= $this->BcAdminForm->control('author_id', ['type' => 'select', 'options' => $this->BcAdminContent->getAuthors(), 'empty' => __d('baser', '指定なし')]) ?>
	</span>
  <? # echo $this->BcSearchBox->dispatchShowField($this->request); ?>
</p>
<div class="button bca-search__btns">
<?php echo $this->BcAdminForm->submit(__d('baser', '検索'), [
      'class' => 'button bca-btn',
      'data-bca-btn-type' => 'save',
      'data-bca-btn-size' => 'lg',
      'data-bca-btn-width' => 'lg',
      'div' => false
    ]) ?>
  <div  class="bca-search__btns-item"><?php #$this->BcBaser->link(__d('baser', '検索'), "javascript:void(0)", ['id' => 'BtnSearchSubmit', 'class' => 'bca-btn', 'data-bca-btn-type' => 'search']) ?></div>
  <div class="bca-search__btns-item"><?php $this->BcBaser->link(__d('baser', 'クリア'), "javascript:void(0)", ['id' => 'BtnSearchClear', 'class' => 'bca-btn', 'data-bca-btn-type' => 'clear']) ?></div>
</div>
<?= $this->BcAdminForm->end() ?>
