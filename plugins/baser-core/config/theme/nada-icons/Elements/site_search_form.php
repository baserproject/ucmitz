<?php
/**
 * [PUBLISH] サイト内検索フォーム
 */
if (Configure::read('BcRequest.isMaintenance')) {
	return;
}
if (!empty($this->passedArgs['num'])) {
	$url = array('plugin' => null, 'controller' => 'search_indices', 'action' => 'search', 'num' => $this->passedArgs['num']);
} else {
	$url = array('plugin' => null, 'controller' => 'search_indices', 'action' => 'search');
}
?>


<div class="section search-box">
	<?php echo $this->BcAdminForm->create('SearchIndex', ['type' => 'get', 'url' => $url]) ?>
	<?php echo $this->BcAdminForm->control('SearchIndex.q', ['escape' => false]) ?>
	<?php echo $this->BcForm->hidden('SearchIndex.s', ['value' => 0]) ?>
	<?php echo $this->BcForm->submit('検索', array('div' => false, 'class' => 'submit_button')) ?>
	<?php echo $this->BcAdminForm->end() ?>
</div>
