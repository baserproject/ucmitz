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
 * [ADMIN] ブログ年別アーカイブ一覧ウィジェット設定
 */
$title = __d('baser', '年別アーカイブ一覧');
$description = __d('baser', 'ブログの年別アーカイブー一覧を表示します。');
?>


<?php echo $this->BcForm->label($key . '.limit', __d('baser', '表示数')) ?>&nbsp;
<?php echo $this->BcForm->input($key . '.limit', ['type' => 'text', 'size' => 6, 'default' => null]) ?>&nbsp;件<br/>
<?php echo $this->BcForm->label($key . '.view_count', __d('baser', '記事数表示')) ?>&nbsp;
<?php echo $this->BcForm->input($key . '.view_count', ['type' => 'radio', 'options' => $this->BcText->booleanDoList(''), 'legend' => false, 'default' => 0]) ?>
<br/>
<?php echo $this->BcForm->label($key . '.start_month', __d('baser', '年度別の場合の開始月')) ?>&nbsp;
<?php echo $this->BcForm->input($key . '.start_month', ['type' => 'int', 'size' => 2, 'default' => 1]) ?>&nbsp;月<br/>
<?php echo $this->BcForm->label($key . '.blog_content_id', __d('baser', 'ブログ')) ?>&nbsp;
<?php echo $this->BcForm->input($key . '.blog_content_id', ['type' => 'select', 'options' => $this->BcForm->getControlSource('Blog.BlogContent.id')]) ?>
<br/>
<small><?php echo __d('baser', 'ブログページを表示している場合は、上記の設定に関係なく、対象ブログの年別アーカイブ一覧を表示します。') ?></small>
