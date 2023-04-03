<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] エディタテンプレートー登録・編集
 *
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BcEditorTemplate\Model\Entity\EditorTemplate $editorTemplate
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcBaser->js('BcEditorTemplate.admin/editor_templates/form.bundle', false);
$this->BcAdmin->setTitle(__d('baser_core', 'エディタテンプレート新規登録'));
$this->BcAdmin->setHelp('editor_templates_form');
$this->BcBaser->css('admin/ckeditor/editor', true);
?>


<?php echo $this->BcAdminForm->create($editorTemplate, ['type' => 'file']) ?>

<?php $this->BcBaser->element('EditorTemplates/form') ?>

<!-- button -->
<div class="bca-actions">
  <div class="bca-actions__main">
    <?php echo $this->BcAdminForm->button(__d('baser_core', '保存'), [
      'type' => 'submit',
      'id' => 'BtnSave',
      'div' => false,
      'class' => 'button bca-btn bca-actions__item',
      'data-bca-btn-type' => 'save',
      'data-bca-btn-size' => 'lg',
      'data-bca-btn-width' => 'lg',
    ]) ?>
  </div>
</div>

<?php echo $this->BcAdminForm->end() ?>
