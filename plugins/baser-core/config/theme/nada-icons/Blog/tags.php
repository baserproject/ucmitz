<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @package			Blog.View
 * @since           baserCMS v 4.0.5
 * @license         https://basercms.net/license/index.html
 */

/**
 * タグ別記事一覧
 *
 * @var BcAppView $this
 */
$this->BcBaser->css(array('Blog.style'), array('inline' => false));
$this->BcBaser->setDescription($this->BcBaser->getContentsTitle() . __('のアーカイブ一覧です。'));
?>


<script type="text/javascript">
$(function(){
	if($("a[rel='colorbox']").colorbox) $("a[rel='colorbox']").colorbox({transition:"fade"});
});
</script>


<h2 class="contents-head">
<?php $this->BcBaser->contentsTitle() ?>
</h2>


<?php if (!empty($posts)): ?>
	<?php foreach ($posts as $post): ?>
		<div class="post">
			<h3 class="contents-head">
			<?php $this->Blog->postTitle($post) ?>
			</h3>
			<?php $this->Blog->postContent($post, true, true) ?>
			<div class="meta"><span>
				<?php $this->Blog->category($post) ?>&nbsp;<?php $this->Blog->postDate($post) ?><?php $this->Blog->author($post) ?>
			</span></div>
			<?php if (!empty($post['BlogTag'])) : ?>
				<div class="tag">タグ：<?php $this->Blog->tag($post, ['crossing' => true]) ?></div>
			<?php endif ?>
		</div>
	<?php endforeach; ?>
<?php else: ?>
	<p class="no-data"><?php echo __('記事がありません。') ?></p>
<?php endif; ?>


<?php $this->BcBaser->pagination('simple'); ?>
