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

use Cake\Core\Configure;

/**
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BcUploader\Model\Entity\UploaderFile $uploaderFile
 * @var string $size
 * @checked
 * @noTodo
 * @unitTest
 */
$url = rtrim(Configure::read('BcEnv.siteUrl'), '/') . $this->BcBaser->getUrl($this->Uploader->getFileUrl($uploaderFile->name));
?>

<div class="uploader-file-image-inner">
  <p class="url">
    <a href="<?php echo h($url) ?>" target="_blank"><?php echo h($url) ?></a>
  </p>
  <p class="image">
    <a href="<?php echo h($url) ?>" target="_blank">
      <?php echo $this->Uploader->file($uploaderFile, [
        'size' => $size,
        'alt' => $uploaderFile->name,
        'class' => 'uploader-file-image__image'
      ]) ?>
    </a>
  </p>
</div>
