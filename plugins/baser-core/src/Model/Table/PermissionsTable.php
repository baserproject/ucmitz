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

namespace BaserCore\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;


/**
 * Class PermissionTable
 * @package BaserCore\Model\Table
 */
class PermissionsTable extends Table
{
    // 許可/拒否する対象メソッド
    const METHOD_LIST = [
        '*' => 'ALL',
        'GET' => 'GET',
        'POST' => 'POST',
    ];


	/**
	 * permissionsTmp
	 * ログインしているユーザーの拒否URLリスト
	 * キャッシュ用
     * TODO 未確認
	 *
	 * @var mixed
	 */
	public $permissionsTmp = -1;

	/**
	 * Permission constructor.
     * // TODO 未確認
	 *
	 * @param bool $id
	 * @param null $table
	 * @param null $ds
	 */
	// public function __construct($id = false, $table = null, $ds = null)
	// {
    //     // TODO 未確認
    //     return;
	// 	parent::__construct($id, $table, $ds);
	// 	$this->validate = [
	// 		'name' => [
	// 			['rule' => ['notBlank'], 'message' => __d('baser', '設定名を入力してください。')],
	// 			['rule' => ['maxLength', 255], 'message' => __d('baser', '設定名は255文字以内で入力してください。')]],
	// 		'user_group_id' => [
	// 			['rule' => ['notBlank'], 'message' => __d('baser', 'ユーザーグループを選択してください。'), 'required' => true]],
	// 		'url' => [
	// 			['rule' => ['notBlank'], 'message' => __d('baser', '設定URLを入力してください。')],
	// 			['rule' => ['maxLength', 255], 'message' => __d('baser', '設定URLは255文字以内で入力してください。')],
	// 			['rule' => ['checkUrl'], 'message' => __d('baser', 'アクセス拒否として設定できるのは認証ページだけです。')]]
	// 	];
	// }

    /**
     * Validation Default
     *
     * @param Validator $validator
     * @return Validator
     * @checked
     * @noTodo
     * @unitTest
     */
    public function validationDefault(Validator $validator): Validator
    {
        // $validator
        //     ->integer('id')
        //     ->allowEmptyString('id', null, 'create');
        // $validator
        //     ->scalar('name')
        //     ->maxLength('name', 255, __d('baser', '設定名は255文字以内で入力してください。'))
        //     ->notEmptyString('name', __d('baser', '設定名を入力してください。'));
        // $validator
        //     ->integer('id')
        //     ->notEmpty('id', __d('baser', 'ユーザーグループを選択してください。'));
        // $validator
        //     ->scalar('url')
        //     ->maxLength('name', 255, __d('baser', '設定URLは255文字以内で入力してください。'))
        //     ->notEmptyString('url', __d('baser', '設定URLを入力してください。'));
        // TODO validation

        return $validator;
    }

	/**
	 * 権限の必要なURLかチェックする
	 *
	 * @param array $check チェックするURL
	 * @return boolean True if the operation should continue, false if it should abort
	 */
	public function checkUrl($check)
	{
        // TODO 未確認
        return;
		if (!$check[key($check)]) {
			return true;
		}

		$url = $check[key($check)];

		if (preg_match('/^[^\/]/is', $url)) {
			$url = '/' . $url;
		}

		// ルーティング設定に合わせて変換
		$url = preg_replace('/^\/admin\//', '/' . Configure::read('Routing.prefixes.0') . '/', $url);

		if (preg_match('/^(\/[a-z_]+)\*$/is', $url, $matches)) {
			$url = $matches[1] . '/' . '*';
		}

		$params = Router::parse($url);

		if (empty($params['prefix'])) {
			return false;
		}

		return true;
	}

	/**
	 * 認証プレフィックスを取得する
	 *
	 * @param int $id PermissionのID
	 * @return string
	 */
	public function getAuthPrefix($id)
	{
        // TODO 未確認
        return;
		$data = $this->find('first', [
			'conditions' => ['Permission.id' => $id],
			'recursive' => 1
		]);
		if (isset($data['UserGroup']['auth_prefix'])) {
			return $data['UserGroup']['auth_prefix'];
		} else {
			return '';
		}
	}

	/**
	 * 初期値を取得する
	 * @return array
	 */
	public function getDefaultValue()
	{
        // TODO 未確認
        return;
		$data['Permission']['auth'] = 0;
		$data['Permission']['status'] = 1;
		return $data;
	}

	/**
	 * コントロールソースを取得する
	 *
	 * @param string フィールド名
	 * @return array コントロールソース
	 */
	public function getControlSource($field = null)
	{
        // TODO 未確認
        return;
		$controlSources['user_group_id'] = $this->UserGroup->find('list', ['conditions' => ['UserGroup.id <>' => Configure::read('BcApp.adminGroupId')]]);
		$controlSources['auth'] = ['0' => __d('baser', '不可'), '1' => __d('baser', '可')];
		if (isset($controlSources[$field])) {
			return $controlSources[$field];
		} else {
			return false;
		}
	}

	/**
	 * beforeSave
	 * urlの先頭に / を付けて絶対パスにする
	 *
	 * @param array $options
	 * @return boolean
	 */
	public function beforeSave($options = [])
	{
        // TODO 未確認
        return;
		if (isset($this->data['Permission'])) {
			$data = $this->data['Permission'];
		} else {
			$data = $this->data;
		}
		if (isset($data['url'])) {
			if (preg_match('/^[^\/]/is', $data['url'])) {
				$data['url'] = '/' . $data['url'];
			}
		}
		$this->data['Permission'] = $data;
		return true;
	}

	/**
	 * 権限チェックを行う
	 *
	 * @param array $url
	 * @param string $userGroupId
	 * @return boolean
	 */
	public function check($url, $userGroupId)
	{
        // TODO 未確認
        return;
		if ($userGroupId == Configure::read('BcApp.adminGroupId')) {
			return true;
		}

		$this->setCheck($userGroupId);
		$permissions = $this->permissionsTmp;

		if ($url != '/') {
			$url = preg_replace('/^\//is', '', $url);
		}
		$adminPrefix = Configure::read('Routing.prefixes.0');
		$url = preg_replace("/^{$adminPrefix}\//", 'admin/', $url);

		// ダッシュボード、ログインユーザーの編集とログアウトは強制的に許可とする
		$allows = [
			'/^admin$/',
			'/^admin\/$/',
			'/^admin\/dashboard\/.*?/',
			'/^admin\/dblogs\/.*?/',
			'/^admin\/users\/logout$/',
			'/^admin\/user_groups\/set_default_favorites$/'
		];
		$sessionKey = Configure::read('BcAuthPrefix.admin.sessionKey');
		if (!empty($_SESSION['Auth'][$sessionKey]['id'])) {
			$allows[] = '/^admin\/users\/edit\/' . $_SESSION['Auth'][$sessionKey]['id'] . '$/';
		}

		foreach($allows as $allow) {
			if (preg_match($allow, $url)) {
				return true;
			}
		}

		$ret = true;
		foreach($permissions as $permission) {
			if (!$permission['Permission']['status']) {
				continue;
			}
			if ($permission['Permission']['url'] != '/') {
				$pattern = preg_replace('/^\//is', '', $permission['Permission']['url']);
			} else {
				$pattern = $permission['Permission']['url'];
			}
			$pattern = addslashes($pattern);
			$pattern = str_replace('/', '\/', $pattern);
			$pattern = str_replace('*', '.*?', $pattern);
			$pattern = '/^' . str_replace('\/.*?', '(|\/.*?)', $pattern) . '$/is';
			if (preg_match($pattern, $url)) {
				$ret = $permission['Permission']['auth'];
			}
		}
		return (boolean)$ret;
	}

	/**
	 * アクセス制限データをコピーする
	 *
	 * @param int $id
	 * @param array $data
	 * @return mixed UserGroup Or false
	 */
	public function copy($id, $data = [])
	{
        // TODO 未確認
        return;
		if ($id) {
			$data = $this->find('first', ['conditions' => ['Permission.id' => $id], 'recursive' => -1]);
		}

		if (!isset($data['Permission']['user_group_id']) || !isset($data['Permission']['name'])) {
			return false;
		}

		if ($this->find('count', ['conditions' => ['Permission.user_group_id' => $data['Permission']['user_group_id'], 'Permission.name' => $data['Permission']['name']]])) {
			$data['Permission']['name'] .= '_copy';
			return $this->copy(null, $data); // 再帰処理
		}

		unset($data['Permission']['id']);
		unset($data['Permission']['modified']);
		unset($data['Permission']['created']);

		$data['Permission']['no'] = $this->getMax('no', ['user_group_id' => $data['Permission']['user_group_id']]) + 1;
		$data['Permission']['sort'] = $this->getMax('sort', ['user_group_id' => $data['Permission']['user_group_id']]) + 1;
		$this->create($data);
		$result = $this->save();
		if ($result) {
			$result['Permission']['id'] = $this->getInsertID();
			return $result;
		} else {
			return false;
		}
	}

	/**
	 * 権限チェックの準備をする
	 *
	 * @param $userGroupId
	 */
	public function setCheck($userGroupId)
	{
        // TODO 未確認
        return;
		if ($this->permissionsTmp === -1) {
			$conditions = ['Permission.user_group_id' => $userGroupId];
			$permissions = $this->find('all', [
				'fields' => ['url', 'auth', 'status'],
				'conditions' => $conditions,
				'order' => 'sort',
				'recursive' => -1
			]);
			if ($permissions) {
				$this->permissionsTmp = $permissions;
			} else {
				$this->permissionsTmp = [];
			}
		}
	}

	/**
	 * 権限チェック対象を追加する
	 *
	 * @param string $url
	 * @param bool $auth
	 */
	public function addCheck($url, $auth)
	{
        // TODO 未確認
        return;
		$this->setCheck(BcUtil::loginUser('admin')['user_group_id']);
		$this->permissionsTmp[] = [
			'Permission' => [
				'url' => $url,
				'auth' => $auth,
				'status' => true
			]
		];
	}

}
