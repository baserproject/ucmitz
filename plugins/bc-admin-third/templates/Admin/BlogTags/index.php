<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ブログタグ一覧
 */
$this->BcBaser->js([
	'admin/libs/jquery.baser_ajax_data_list',
	'admin/libs/jquery.baser_ajax_batch',
	'admin/libs/baser_ajax_data_list_config',
	'admin/libs/baser_ajax_batch_config'
]);
$this->BcAdmin->addAdminMainBodyHeaderLinks([
	'url' => ['action' => 'add'],
	'title' => __d('baser', '新規追加'),
]);
?>


<script type="text/javascript">
	$(function () {
		$.baserAjaxDataList.config.methods.del.confirm = "<?php echo __d('baser', 'このデータを本当に削除してもいいですか？\nこのタグに関連する記事は削除されません。') ?>";
		$.baserAjaxDataList.init();
		$.baserAjaxBatch.init({url: $("#AjaxBatchUrl").html()});
	});
</script>

<div id="AjaxBatchUrl"
	 style="display:none"><?php $this->BcBaser->url(['controller' => 'blog_tags', 'action' => 'ajax_batch']) ?></div>
<div id="AlertMessage" class="message" style="display:none"></div>
<div id="MessageBox" style="display:none">
	<div id="flashMessage" class="notice-message"></div>
</div>
<div id="DataList" class="bca-data-list"><?php $this->BcBaser->element('blog_tags/index_list') ?></div>
