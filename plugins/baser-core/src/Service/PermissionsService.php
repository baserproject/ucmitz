<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Permission Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Permission Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Service;

use BaserCore\Error\BcException;
use BaserCore\Model\Entity\Permission;
use BaserCore\Model\Table\PermissionsTable;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Datasource\EntityInterface;
use BaserCore\Utility\BcUtil;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;

/**
 * Class PermissionsService
 * @property PermissionsTable $Permissions
 */
class PermissionsService implements PermissionsServiceInterface
{

    /**
     * Permissions Table
     * @var \Cake\ORM\Table
     */
    public $Permissions;

    /**
     * @var string
     */
    public $adminUrlPrefix;

    /**
     * @var array
     */
    private $defaultAllows = [];

    /**
     * @var array
     */
    private $defaultDenies = [];

    /**
     * PermissionsService constructor.
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct()
    {
        $this->Permissions = TableRegistry::getTableLocator()->get('BaserCore.Permissions');
        $this->adminUrlPrefix = BcUtil::getPrefix();
        $this->setDefaultAllow();
    }

    /**
     * パーミッションの新規データ用の初期値を含んだエンティティを取得する
     * @param int $userGroupId
     * @return Permission
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew($userGroupId = null): EntityInterface
    {
        return $this->Permissions->newEntity(
            $this->autoFillRecord(['user_group_id' => $userGroupId]),
            ['validate' => 'plain']
        );
    }

    /**
     * リストデータを取得
     * 対応しない
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getList(): array
    {
        return [];
    }

    /**
     * パーミッションを取得する
     * @param int $id
     * @return EntityInterface
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get($id): EntityInterface
    {
        return $this->Permissions->get($id, [
            'contain' => ['UserGroups'],
        ]);
    }

    /**
     * パーミッション管理の一覧用のデータを取得
     * @param array $queryParams
     * @return Query
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $queryParams = []): Query
    {
        $options = [];
        if (!empty($queryParams['user_group_id'])) {
            $options = ['conditions' => ['Permissions.user_group_id' => $queryParams['user_group_id']]];
        }
        $query = $this->Permissions->find('all', $options)->order('sort', 'ASC');
        return $query;
    }

    /**
     * パーミッション登録
     * @param ServerRequest $request
     * @return EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $postData): EntityInterface
    {
        $postData = $this->autoFillRecord($postData);
        $permission = $this->Permissions->newEmptyEntity();
        $permission = $this->Permissions->patchEntity($permission, $postData, ['validate' => 'default']);
        return $this->Permissions->saveOrFail($permission);
    }

    /**
     * パーミッション情報を更新する
     * @param EntityInterface $target
     * @param array $data
     * @return EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(EntityInterface $target, array $data): EntityInterface
    {
        $permission = $this->Permissions->patchEntity($target, $data);
        return $this->Permissions->saveOrFail($permission);
    }

    /**
     * 有効状態にする
     *
     * @param int $id
     * @return bool
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function publish($id): bool
    {
        $permission = $this->get($id);
        $permission->status = true;
        return ($this->Permissions->save($permission)) ? true: false;
    }

    /**
     * 無効状態にする
     *
     * @param int $id
     * @return bool
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function unpublish($id): bool
    {
        $permission = $this->get($id);
        $permission->status = false;
        return ($this->Permissions->save($permission)) ? true: false;
    }

    /**
     * 複製する
     *
     * @param int $permissionId
     * @return EntityInterface|false
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy(int $permissionId)
    {
        $permission = $this->get($permissionId);
        $permission->id = null;
        $permission->no = null;
        $permission->sort = null;
        $data = $permission->toarray();
        $data = $this->autoFillRecord($data);
        try {
            return $this->create($data);
        } catch (\Exception $e) {
            return false;
        }
    }



    /**
     * パーミッション情報を削除する
     * @param int $id
     * @return bool
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(int $id): bool
    {
        $Permission = $this->get($id);
        return $this->Permissions->delete($Permission);
    }

    /**
     * 許可・拒否を指定するメソッドのリストを取得
     *
     * @return array
     * @noTodo
     * @unitTest
     * @checked
     */
    public function getMethodList() : array
    {
        return (array) $this->Permissions::METHOD_LIST;
    }

    /**
     * 権限リストを取得
     *
     * @return array
     * @noTodo
     * @unitTest
     * @checked
     */
    public function getAuthList() : array
    {
        return (array) $this->Permissions::AUTH_LIST;
    }

    /**
     *  レコード作成に必要なデータを代入する
     * @param array $data
     * @return array $data
     *
     * @noTodo
     * @unitTest
     * @checked
     */
    protected function autoFillRecord($data = []): array
    {
        if (empty($data['no'])) {
            $data['no'] = $this->Permissions->getMax('no') + 1;
        }
        if (empty($data['sort'])) {
            $data['sort'] = $this->Permissions->getMax('sort') + 1;
        }
        if (!isset($data['auth']) || $data['auth'] === null) {
            $data['auth'] = false;
        }
        if (empty($data['method'])) {
            $data['method'] = '*';
        }
        if (!isset($data['status']) || $data['status'] === null) {
            $data['status'] = true;
        }
        return $data;
    }

    /**
     * 権限チェックを行う
     *
     * @param string $url
     * @param array $userGroupId
     * @return bool
     * @checked
     * @unitTest
     * @noTodo
     */
    public function check(string $url, array $userGroupId): bool
    {
        if (in_array(Configure::read('BcApp.adminGroupId'), $userGroupId)) {
            return true;
        }
        if ($this->checkDefaultDeny($url)) {
            return false;
        }
        if ($this->checkDefaultAllow($url)) {
            return true;
        }

        $permissionGroupList = $this->Permissions->getTargePermissions($userGroupId);

        foreach($permissionGroupList as $permissionGroup) {
            if ($this->checkGroup($url, $permissionGroup)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 標準アクセス許可リクエストを設定
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    private function setDefaultAllow(): void
    {
        // ダッシュボード、ログインユーザーの編集とログアウトは強制的に許可とする
        $allows = [
            '/^' . preg_quote($this->adminUrlPrefix . '/', '/') . '?$/',
            '/^' . preg_quote($this->adminUrlPrefix . '/baser-core/dashboard/', '/') . '.*?/',
            '/^' . preg_quote($this->adminUrlPrefix . '/baser-core/dblogs/', '/') . '.*?/',
            '/^' . preg_quote($this->adminUrlPrefix . '/baser-core/users/logout', '/') . '$/',
            '/^' . preg_quote($this->adminUrlPrefix . '/baser-core/user_groups', '/') . '$/',
        ];
        $sessionKey = Configure::read('BcPrefixAuth.Admin.sessionKey');
        if (!empty($_SESSION[$sessionKey]['id'])) {
            $allows[] = '/^' . preg_quote($this->adminUrlPrefix . '/baser-core/users/edit/' . $_SESSION[$sessionKey]['id'], '/') . '$/';
        }
        $this->defaultAllows = $allows;
    }

    /**
     * 標準アクセス許可リストからURLを検証
     *
     * @param string $url
     * @return boolean
     * @checked
     * @unitTest
     * @noTodo
     */
    private function checkDefaultAllow(string $url): bool
    {
        foreach($this->defaultAllows as $allow) {
            if (preg_match($allow, $url)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 標準アクセス拒否リストからURLを検証
     *
     * @param string $url
     * @return boolean
     * @checked
     * @unitTest
     * @noTodo
     */
    private function checkDefaultDeny(string $url): bool
    {
        foreach($this->defaultDenies as $deny) {
            if (preg_match($deny, $url)) {
                return true;
            }
        }
        return false;
    }

    /**
     * パーミッションリストを検証する
     *
     * @param string $url
     * @param array $groupPermission
     * @return boolean
     * @checked
     * @unitTest
     * @noTodo
     */
    private function checkGroup(string $url, array $groupPermission): bool
    {
        $ret = false;
        foreach($groupPermission as $permission) {
            $pattern = $permission->url;
            $pattern = preg_quote($pattern, '/');
            $pattern = str_replace('\*', '.*?', $pattern);
            $pattern = '/^' . str_replace('\/.*?', '(|\/.*?)', $pattern) . '$/is';
            if (preg_match($pattern, $url)) {
                $ret = $permission->auth;
            }
        }
        return (boolean)$ret;
    }

    /**
     * 権限チェック対象を追加する
     *
     * @param string $url
     * @param bool $auth
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function addCheck(string $url, bool $auth)
    {
        $pattern = preg_quote($url, '/');
        $pattern = str_replace('\*', '.*?', $pattern);
        $pattern = '/^' . str_replace('\/.*?', '(|\/.*?)', $pattern) . '$/is';

        if ($auth) {
            $this->defaultAllows[] = $pattern;
        } else {
            $this->defaultDenies[] = $pattern;
        }
    }

    /**
     * 優先度を変更する
     *
     * @param int $id
     * @param int $offset
     * @param array $conditions
     * @return bool
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function changeSort(int $id, int $offset, array $conditions = []): bool
    {
        $offset = intval($offset);
        if ($offset === 0) {
            return true;
        }

        $current = $this->get($id);

        // currentを含め変更するデータを取得
        if ($offset > 0) { // DOWN
            $order = ["sort"];
            $conditions["sort >="] = $current->sort;
        } else { // UP
            $order = ["sort DESC"];
            $conditions["sort <="] = $current->sort;
        }

        $result = $this->Permissions->find()
            ->where($conditions)
            ->order($order)
            ->limit(abs($offset) + 1)
            ->all();

        $count = $result->count();
        if (!$count) {
            return false;
        }
        $permissions = $result->toList();

        //データをローテーション
        $currentNewValue = $permissions[$count - 1]->sort;
        for($i = $count - 1; $i > 0; $i--) {
            $permissions[$i]->sort = $permissions[$i - 1]->sort;
        }
        $permissions[0]->sort = $currentNewValue;
        if (!$this->Permissions->saveMany($permissions)) {
            return false;
        }

        return true;
    }

    /**
     * 一括処理
     * @param array $ids
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function batch($method, array $ids): bool
    {
        if (!$ids) return true;
        $db = $this->Permissions->getConnection();
        $db->begin();
        foreach($ids as $id) {
            if (!$this->{$method}($id)) {
                $db->rollback();
                throw new BcException(__d('baser', 'データベース処理中にエラーが発生しました。'));
            }
        }
        $db->commit();
        return true;
    }

    /**
     * IDを指定して名前リストを取得する
     * @param $ids
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNamesById($ids): array
    {
        return $this->Permissions->find('list')->where(['id IN' => $ids])->toArray();
    }

    public function getSelectOptions(): array
    {
        $selectOptions = [
            'plugins' => [
                [
                    'value' => '',
                    'text' => 'プラグイン',
                    'disabled' => true,
                ],
            ],
            'prefixes' => [
                [
                    'value' => '',
                    'text' => '認証タイプ',
                    'disabled' => true,
                ],
            ],
            'controllers' => [
                [
                    'value' => '',
                    'text' => '機能',
                    'disabled' => true,
                ], [
                    'value' => '*',
                    'text' => '*',
                ],
            ],
            'actions' => [
                [
                    'value' => '',
                    'text' => '画面',
                    'disabled' => true,
                ], [
                    'value' => '*',
                    'text' => '*',
                ],
            ],
        ];

        foreach (Configure::read('BcApp.permissions') as $pluginName => $pluginConfig) {
            $selectOptions['plugins'][] = [
                'value' => $pluginName,
                'text' => $pluginConfig['title'],
            ];
            foreach ($pluginConfig['prefixes'] as $prefixName => $prefixConfig) {
                $selectOptions['prefixes'][] = [
                    'value' => $prefixName,
                    'text' => $prefixConfig['title'],
                    'data-plugin' => $pluginName,
                ];
                foreach ($prefixConfig['controllers'] as $controllerName => $controllerConfig) {
                    $selectOptions['controllers'][] = [
                        'value' => $controllerName,
                        'text' => $controllerConfig['title'],
                        'data-plugin' => $pluginName,
                        'data-prefix' => $prefixName,
                    ];
                    foreach ($controllerConfig['actions'] as $actionName => $actionConfig) {
                        $selectOptions['actions'][] = [
                            'value' => $actionName,
                            'text' => $actionConfig['title'],
                            'data-plugin' => $pluginName,
                            'data-prefix' => $prefixName,
                            'data-controller' => $controllerName,
                            'data-message' => $actionConfig['message'] ?? '',
                        ];
                    }
                }
            }
        }

        return $selectOptions;
    }

    public function getSelectOptionsB(): array
    {
        $selectOptions = [];

        foreach (Configure::read('BcApp.permissions') as $pluginName => $pluginConfig) {
            foreach ($pluginConfig['prefixes'] as $prefixName => $prefixConfig) {
                $selectOptions[] = [
                    'value' => '*',
                    'text' => $pluginConfig['title'] . ' ' . $prefixConfig['title'] . ' *',
                    'data-plugin' => $pluginName,
                    'data-prefix' => $prefixName,
                ];
                foreach ($prefixConfig['controllers'] as $controllerName => $controllerConfig) {
                    $selectOptions[] = [
                        'value' => '*',
                        'text' => $pluginConfig['title'] . ' ' . $prefixConfig['title'] . ' ' . $controllerConfig['title'] . ' *',
                        'data-plugin' => $pluginName,
                        'data-prefix' => $prefixName,
                        'data-controller' => $controllerName,
                    ];
                    foreach ($controllerConfig['actions'] as $actionName => $actionConfig) {
                        $selectOptions[] = [
                            'value' => $actionName,
                            'text' => $pluginConfig['title'] . ' ' . $prefixConfig['title'] . ' ' . $controllerConfig['title'] . ' ' . $actionConfig['title'],
                            'data-plugin' => $pluginName,
                            'data-prefix' => $prefixName,
                            'data-controller' => $controllerName,
                            'data-message' => $actionConfig['message'] ?? '',
                        ];
                    }
                }
            }
        }

        return $selectOptions;
    }

}
