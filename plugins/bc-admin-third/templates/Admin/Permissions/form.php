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
 * [ADMIN] ユーザーグループ登録/編集フォーム
 *
 * @var BcAppView $this
 */
$this->BcBaser->js('admin/permissions/form', false);
?>


<?php echo $this->BcForm->create('Permission') ?>
<?php echo $this->BcFormTable->dispatchBefore() ?>
<?php echo $this->BcForm->input('Permission.id', ['type' => 'hidden']) ?>

<!-- form -->
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table bca-form-table">
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('Permission.user_group_id', __d('baser', 'ユーザーグループ')) ?></th>
			<td class="col-input bca-form-table__input">
				<?php $userGroups = $this->BcForm->getControlSource('user_group_id') ?>
				<?php echo $userGroups[$this->BcForm->value('Permission.user_group_id')] ?>
				<?php echo $this->BcForm->input('Permission.user_group_id', ['type' => 'hidden']) ?>
			</td>
		</tr>
		<?php if ($this->request->action == 'admin_edit'): ?>
			<tr>
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('Permission.id', 'No') ?></th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->value('Permission.no') ?>
				</td>
			</tr>
		<?php endif; ?>
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('Permission.name', __d('baser', 'ルール名')) ?>
				&nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
			</th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('Permission.name', ['type' => 'text', 'size' => 40, 'maxlength' => 255, 'autofocus' => true]) ?>
				<i class="bca-icon--question-circle btn help bca-help"></i>
				<?php echo $this->Form->error('Permission.name') ?>
				<div id="helptextName"
					 class="helptext"><?php echo __d('baser', 'ルール名には日本語が利用できます。特定しやすいわかりやすい名称を入力してください。') ?></div>
			</td>
		</tr>
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('Permission.url', __d('baser', 'URL設定')) ?>
				&nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
			</th>
			<td class="col-input bca-form-table__input">
				<strong>/<?php echo $permissionAuthPrefix ?>/</strong>
				<?php echo $this->BcForm->input('Permission.url', ['type' => 'text', 'size' => 40, 'maxlength' => 255]) ?>
				<i class="bca-icon--question-circle btn help bca-help"></i>
				<?php echo $this->Form->error('Permission.url') ?>
				<div id="helptextUrl" class="helptext">
					<ul>
						<li><?php echo __d('baser', 'baserCMSの設置URLを除いたスラッシュから始まるURLを入力してください。<br>（例）/admin/users/index') ?></li>
						<li><?php echo __d('baser', '管理画面など認証がかかっているURLしか登録できません。') ?></li>
						<li><?php echo __d('baser', '特定のフォルダ配下に対しアクセスできないようにする場合などにはワイルドカード（*）を利用します。<br>（例）ユーザー管理内のURL全てアクセスさせない場合： /admin/users* ') ?></li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('Permission.auth', __d('baser', 'アクセス')) ?></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('Permission.auth', ['type' => 'radio', 'options' => $this->BcText->booleanAllowList(__d('baser', 'アクセス'))]) ?>
				<?php echo $this->BcForm->error('Permission.auth') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('Permission.status', __d('baser', '利用状態')) ?></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('Permission.status', ['type' => 'checkbox', 'label' => __d('baser', '有効')]) ?>
				<?php echo $this->BcForm->error('Permission.status') ?>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<div class="submit bca-actions">
	<div class="bca-actions__main">
		<?php echo $this->BcForm->button(__d('baser', '保存'), ['div' => false, 'class' => 'button bca-btn bca-actions__item', 'id' => 'BtnSave',
			'data-bca-btn-type' => 'save',
			'data-bca-btn-size' => 'lg',
			'data-bca-btn-width' => 'lg',
		]) ?>
	</div>
	<?php if ($this->request->action == 'admin_edit'): ?>
		<div class="bca-actions__sub">
			<?php $this->BcBaser->link(__d('baser', '削除'), ['action' => 'delete', $this->request->params['pass'][0], $this->BcForm->value('Permission.id')], ['class' => 'submit-token button bca-btn bca-actions__item', 'data-bca-btn-status' => 'delete', 'data-bca-btn-size' => 'sm'], sprintf(__d('baser', '%s を本当に削除してもいいですか？'), $this->BcForm->value('Permission.name')), false); ?>
		</div>
	<?php endif; ?>
</div>

<?php echo $this->BcForm->end() ?>
