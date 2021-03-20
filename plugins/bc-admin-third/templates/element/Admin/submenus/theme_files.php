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
 * [ADMIN] テーマファイル管理メニュー
 */
$types = [
	'Layouts' => __d('baser', 'レイアウト'),
	'Elements' => __d('baser', 'エレメント'),
	'Emails' => __d('baser', 'Eメール'),
	'etc' => __d('baser', 'コンテンツ'),
	'css' => 'CSS',
	'img' => __d('baser', 'イメージ'),
	'js' => 'Javascript'
];
if ($theme == 'core') {
	$themeFiles = [0 => ['name' => '', 'title' => __d('baser', 'コア')]];
	$Plugin = ClassRegistry::init('Plugin');
	$plugins = $Plugin->find('all', ['fields' => ['name', 'title']]);
	$themeFiles = am($themeFiles, Hash::extract($plugins, '{n}.Plugin'));
} else {
	$themeFiles = [0 => ['name' => '', 'title' => $theme]];
}
?>

<div class="bca-main__submenu">
	<?php foreach($themeFiles as $themeFile): ?>
		<h2 class="bca-main__submenu-title"><?php echo $themeFile['title'] ?>
			｜<?php echo __d('baser', 'テーマ管理メニュー') ?></h2>
		<ul class="bca-main__submenu-list clearfix">
			<?php foreach($types as $key => $type): ?>
				<li class="bca-main__submenu-list-item"><?php $this->BcBaser->link(sprintf(__d('baser', '%s 一覧'), $type), ['action' => 'index', $theme, $themeFile['name'], $key]) ?></li>
			<?php endforeach ?>
		</ul>
	<?php endforeach ?>
</div>
