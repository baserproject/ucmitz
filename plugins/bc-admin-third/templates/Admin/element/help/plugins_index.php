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

/**
 * [ADMIN] プラグイン一覧　ヘルプ
 */
?>


<p><?php echo sprintf(__d('baser', 'baserCMSのプラグインの管理を行います。<br />初期状態では、メールフォーム・フィードリーダー・ブログの３つのプラグインが標準プラグインとして同梱されており、インストールも完了しています。各プラグインの%sから各プラグインの管理が行えます。'), $this->BcBaser->getImg('admin/icn_tool_manage.png')) ?></p>
<div class="example-box">
  <div class="head"><?php echo __d('baser', '新しいプラグインのインストール方法') ?></div>
  <ol>
    <li><?php echo __d('baser', 'app/Plugin/ フォルダに、入手したプラグインのフォルダをアップロードします。') ?></li>
    <li><?php echo sprintf(__d('baser', 'プラグイン一覧に、新しいプラグインが表示されますので、その行の %s をクリックします。'), $this->BcBaser->getImg('admin/icn_tool_install.png')) ?></li>
    <li><?php echo __d('baser', '登録画面が表示されますので「登録」ボタンをクリックしてインストールを完了します。') ?></li>
  </ol>
</div>
