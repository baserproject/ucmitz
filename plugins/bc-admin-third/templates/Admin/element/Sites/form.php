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
 * サブサイトフォーム
 *
 * @var BcAdminAppView $this
 * @var array $themes
 */
$useSiteDeviceSetting = $this->BcAdminSite->isUseSiteDeviceSetting();
$useSiteLangSetting = $this->BcAdminSite->isUseSiteLangSetting();
?>


<?php echo $this->BcForm->hidden('id') ?>

<table class="form-table bca-form-table">
  <?php if ($this->request->getParam('action') === 'admin_edit'): ?>
    <tr>
      <th class="bca-form-table__label"><?php echo $this->BcForm->label('id', 'No') ?></th>
      <td class=" bca-form-table__input">
        <?php echo $this->BcForm->value('id') ?>
      </td>
    </tr>
  <?php endif ?>
  <tr>
    <th class="bca-form-table__label"><?php echo $this->BcForm->label('name', __d('baser', '識別名称')) ?>
      &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span></th>
    <td class=" bca-form-table__input">
      <?php echo $this->BcAdminForm->control('name', ['type' => 'text', 'size' => '30', 'autofocus' => true]) ?>
      <i class="bca-icon--question-circle btn help bca-help"></i>
      <div
        class="helptext"><?php echo __d('baser', 'サブサイトを特定する事ができる識別名称を入力します。半角英数とハイフン（-）・アンダースコア（_）のみが利用できます。エイリアスを入力しない場合は、URLにも利用されます。') ?></div>
      　<span
        style="white-space: nowrap;"><small>[<?php echo $this->BcForm->label('alias', __d('baser', 'エイリアス')) ?>]</small>
			<?php echo $this->BcAdminForm->control('alias', ['type' => 'text', 'size' => '10']) ?></span>
      <i class="bca-icon--question-circle btn help bca-help"></i>
      <div
        class="helptext"><?php echo __d('baser', 'サブサイトの識別名称とは別のURLにしたい場合、別名を入力する事ができます。エイリアスは半角英数に加えハイフン（-）・アンダースコア（_）・スラッシュ（/）・ドット（.）が利用できます。') ?></div>
      <?php echo $this->BcForm->error('name') ?>
      <?php echo $this->BcForm->error('alias') ?>
    </td>
  </tr>
  <tr>
    <th class="bca-form-table__label"><?php echo $this->BcForm->label('display_name', __d('baser', 'サイト名')) ?>
      &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span></th>
    <td class=" bca-form-table__input">
      <?php echo $this->BcAdminForm->control('display_name', ['type' => 'text', 'size' => '60']) ?>
      <i class="bca-icon--question-circle btn help bca-help"></i>
      <div
        class="helptext"><?php echo __d('baser', 'サブサイト名を入力します。管理システムでの表示に利用されます。日本語の入力が可能ですのでわかりやすい名前をつけてください。') ?></div>
      <?php echo $this->BcForm->error('display_name') ?>
    </td>
  </tr>
  <tr>
    <th class="bca-form-table__label"><?php echo $this->BcForm->label('title', __d('baser', 'サイトタイトル')) ?>
      &nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span></th>
    <td class="bca-form-table__input">
      <?php echo $this->BcAdminForm->control('title', ['type' => 'text', 'size' => '60']) ?>
      <i class="bca-icon--question-circle btn help bca-help"></i>
      <div class="helptext"><?php echo __d('baser', 'サブサイトのタイトルを入力します。タイトルタグに利用されます。') ?></div>
      <?php echo $this->BcForm->error('title') ?>
    </td>
  </tr>
  <tr>
    <th
      class="bca-form-table__label"><?php echo $this->BcForm->label('keyword', __d('baser', 'サイト基本キーワード')) ?></th>
    <td
      class="bca-form-table__input"><?php echo $this->BcAdminForm->control('keyword', ['type' => 'text', 'size' => 55, 'maxlength' => 255, 'counter' => true, 'class' => 'bca-textbox__input full-width']) ?>
      <i class="bca-icon--question-circle btn help bca-help"></i>
      <div id="helptextKeyword"
           class="helptext"><?php echo __d('baser', 'テンプレートで利用する場合は、<br>&lt;?php $this->BcBaser->metaKeywords() ?&gt; で出力します。') ?></div>
      <?php echo $this->BcForm->error('keyword') ?>
    </td>
  </tr>
  <tr>
    <th
      class="bca-form-table__label"><?php echo $this->BcForm->label('description', __d('baser', 'サイト基本説明文')) ?></th>
    <td
      class="bca-form-table__input"><?php echo $this->BcAdminForm->control('description', ['type' => 'textarea', 'cols' => 20, 'rows' => 6, 'maxlength' => 255, 'counter' => true]) ?>
      <i class="bca-icon--question-circle btn help bca-help"></i>
      <div id="helptextDescription"
           class="helptext"><?php echo __d('baser', 'テンプレートで利用する場合は、<br>&lt;?php $this->BcBaser->metaDescription() ?&gt; で出力します。') ?></div>
      <?php echo $this->BcForm->error('description') ?>
    </td>
  </tr>
  <tr>
    <th
      class="bca-form-table__label"><?php echo $this->BcForm->label('main_site_id', __d('baser', 'メインサイト')) ?></th>
    <td class=" bca-form-table__input">
      <?php echo $this->BcAdminForm->control('main_site_id', ['type' => 'select', 'options' => $this->BcAdminSite->getSiteList()]) ?>
      <?php echo $this->BcAdminForm->control('relate_main_site', ['type' => 'checkbox', 'label' => __d('baser', 'エイリアスを利用してメインサイトと自動連携する')]) ?>
      <i class="bca-icon--question-circle btn help bca-help"></i>
      <div class="helptext">
        <p><?php echo __d('baser', 'サブサイトの主として連携させたいサイトを選択します。') ?></p>
        <p><?php echo __d('baser', '「エイリアスを利用してメインサイトと自動連携する」にチェックを入れておくと、メインサイトでコンテンツの追加や削除が発生した場合、エイリアスを利用して自動的にサブサイトで同様の処理を実行します。') ?></p>
      </div>
      <?php echo $this->BcForm->error('main_site_id') ?>
    </td>
  </tr>
  <?php if ($useSiteDeviceSetting || $useSiteLangSetting): ?>
    <tr>
      <th class="bca-form-table__label"><?php echo $this->BcForm->label('device', __d('baser', 'デバイス・言語')) ?></th>
      <td class=" bca-form-table__input">
        <?php if ($useSiteDeviceSetting): ?>
          <small><?php echo __d('baser', '[デバイス]') ?></small>&nbsp;<?php echo $this->BcAdminForm->control('device', ['type' => 'select', 'options' => $this->BcAdminSite->getDeviceList()]) ?>
          <i class="bca-icon--question-circle btn help bca-help"></i>
          <div
            class="helptext"><?php echo __d('baser', 'サブサイトにデバイス属性を持たせ、サイトアクセス時、ユーザーエージェントを判定し適切なサイトを表示する機能を利用します。') ?></div>
        <?php else: ?>
          <?php echo $this->BcAdminForm->control('device', ['type' => 'hidden']) ?>
        <?php endif ?>
        <?php if ($useSiteLangSetting): ?>
          <small><?php echo __d('baser', '[言語]') ?></small><?php echo $this->BcAdminForm->control('lang', ['type' => 'select', 'options' => $this->BcAdminSite->getLangList()]) ?>
          <i class="bca-icon--question-circle btn help bca-help"></i>
          <div
            class="helptext"><?php echo __d('baser', 'サブサイトに言語属性を持たせ、サイトアクセス時、ブラウザの言語設定を判定し適切なサイトを表示する機能を利用します。') ?></div>
        <?php else: ?>
          <?php echo $this->BcAdminForm->control('lang', ['type' => 'hidden']) ?>
        <?php endif ?>
        <div id="SectionAccessType" style="display:none">
          <small><?php echo __d('baser', '[アクセス設定]') ?></small>
          <br>
          <span
            id="SpanSiteSameMainUrl"><?php echo $this->BcAdminForm->control('same_main_url', ['type' => 'checkbox', 'label' => __d('baser', 'メインサイトと同一URLでアクセス')]) ?>&nbsp;
					<i class="bca-icon--question-circle btn help bca-help"></i>
					<div
            class="helptext"><?php echo __d('baser', 'メインサイトと同一URLでアクセスし、デバイス設定や言語設定を判定し、適切なサイトを表示します。このオプションをオフにした場合は、エイリアスを利用した別URLを利用したアクセスとなります。') ?></div>
				</span>
          <br>
          <span
            id="SpanSiteAutoRedirect"><?php echo $this->BcAdminForm->control('auto_redirect', ['type' => 'checkbox', 'label' => __d('baser', 'メインサイトから自動的にリダイレクト')]) ?>&nbsp;
					<i class="bca-icon--question-circle btn help bca-help"></i>
					<span
            class="helptext"><?php echo __d('baser', 'メインサイトと別URLでアクセスする際、デバイス設定や言語設定を判定し、適切なサイトへリダイレクトします。') ?></span>　
				</span>
          <br>
          <span
            id="SpanSiteAutoLink"><?php echo $this->BcAdminForm->control('auto_link', ['type' => 'checkbox', 'label' => __d('baser', '全てのリンクをサブサイト用に変換する')]) ?>&nbsp;
					<i class="bca-icon--question-circle btn help bca-help"></i>
					<span
            class="helptext"><?php echo __d('baser', 'メインサイトと別URLでアクセスし、エイリアスを利用して同一コンテンツを利用する場合、コンテンツ内の全てのリンクをサブサイト用に変換します。') ?></span>
				</span>
        </div>
        <?php echo $this->BcForm->error('device') ?>
        <?php echo $this->BcForm->error('lang') ?>
      </td>
    </tr>
  <?php endif ?>
  <tr>
    <th class="bca-form-table__label"><?php echo $this->BcForm->label('theme', __d('baser', 'テーマ')) ?></th>
    <td class=" bca-form-table__input">
      <?php echo $this->BcAdminForm->control('theme', ['type' => 'select', 'options' => $this->BcAdminSite->getThemeList()]) ?>
      <i class="bca-icon--question-circle btn help bca-help"></i>
      <div
        class="helptext"><?php echo __d('baser', 'サブサイトのテンプレートは、各テンプレートの配置フォルダ内にサイト名のサブフォルダを作成する事で別途配置する事ができますが、テーマフォルダ自体を別にしたい場合はここでテーマを指定します。') ?></div>
      <?php echo $this->BcForm->error('theme') ?>
    </td>
  </tr>
  <tr>
    <th class="bca-form-table__label"><?php echo $this->BcForm->label('status', __d('baser', '公開状態')) ?></th>
    <td class=" bca-form-table__input">
      <?php echo $this->BcAdminForm->control('status', ['type' => 'radio', 'options' => [0 => __d('baser', '公開しない'), 1 => __d('baser', '公開する')]]) ?>
      <?php echo $this->BcForm->error('status') ?>
    </td>
  </tr>
  <?php echo $this->BcForm->dispatchAfterForm() ?>
</table>
