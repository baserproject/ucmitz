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

namespace BcFavorite\Model\Table;

use BaserCore\Model\AppTable;
use BaserCore\Service\PermissionServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use Cake\Validation\Validator;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class FavoritesTable
 */
class FavoritesTable extends AppTable
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Initialize
     *
     * @param array $config テーブル設定
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('favorites');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('Users', [
            'className' => 'BaserCore.Users',
            'foreignKey' => 'user_id',
            'targetForeignKey' => 'id',
        ]);
    }

    /**
     * Validation Default
     *
     * @param Validator $validator
     * @return Validator
     * @checked
     * @note("titleもバリデーション入れる")
     * @unitTest
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator->setProvider('favorite', 'BcFavorite\Model\Validation\FavoriteValidation');
        $validator
            ->scalar('url')
            ->add('url', 'isPermitted', [
                'rule' => ['isPermitted', $this->getService(PermissionServiceInterface::class)],
                'provider' => 'favorite',
                'message' => __d('baser', 'このURLの登録は許可されていません。')])
            ->notEmptyString('url', __d('baser', 'URLは必須です。'));
        $validator
            ->scalar('user_id')
            ->notEmptyString('user_id', __d('baser', 'ユーザーIDは必須です。'));
        return $validator;
    }

}
