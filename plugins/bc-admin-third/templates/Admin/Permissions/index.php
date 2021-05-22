<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] アクセス制限設定一覧
 *
 * @var BcAppView $this
 */
// TODO
// $this->BcBaser->i18nScript([
// 	'sorttableAlertMessage1' => __d('baser', '並び替えの保存に失敗しました。')
// ]);
//$this->BcBaser->js('admin/libs/sorttable', false);

$this->BcAdmin->addAdminMainBodyHeaderLinks([
	'url' => ['action' => 'add', $userGroupId],

	'title' => __d('baser', '新規追加'),
]);
?>


<?php /*
<div id="AjaxBatchUrl"
	 style="display:none"><?php $this->BcBaser->url(['controller' => 'permissions', 'action' => 'ajax_batch']) ?></div>
<div id="AjaxSorttableUrl"
	 style="display:none"><?php $this->BcBaser->url(['controller' => 'permissions', 'action' => 'ajax_update_sort', $this->request->params['pass'][0]]) ?></div>
<div id="AlertMessage" class="message" style="display:none"></div>
<div id="MessageBox" style="display:none">
	<div id="flashMessage" class="notice-message"></div>
</div>
<div id="DataList" class="bca-data-list"><?php $this->BcBaser->element('permissions/index_list') ?></div>
*/ ?>

<section id="DataList">
    <?php $this->BcBaser->element('Permissions/index_list') ?>
</section>
