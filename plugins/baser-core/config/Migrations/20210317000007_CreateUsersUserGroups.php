<?php
declare(strict_types=1);

use BaserCore\Database\Migration\BcMigration;

class CreateUsersUserGroups extends BcMigration
{
    /**
     * Up Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-up-method
     * @return void
     */
    public function up()
    {
        $this->table('users_user_groups', [
            'collation' => 'utf8mb4_general_ci'
         ])
            ->addColumn('user_id', 'integer', [
                'comment' => 'ユーザーID',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('user_group_id', 'integer', [
                'comment' => 'ユーザーグループID',
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();
    }

    /**
     * Down Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-down-method
     * @return void
     */
    public function down()
    {
        $this->table('users_user_groups')->drop()->save();
    }
}
