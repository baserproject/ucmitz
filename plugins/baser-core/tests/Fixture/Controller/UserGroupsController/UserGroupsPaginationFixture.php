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

namespace BaserCore\Test\Fixture\Controller\UserGroupsController;

use Cake\TestSuite\Fixture\TestFixture;
use Cake\ORM\TableRegistry;

/**
 * Class UserGroupsPaginationFixture
 * @package BaserCore\Test\Fixture
 */
class UserGroupsPaginationFixture extends TestFixture
{
    public $import = ['table' => 'user_groups'];

    /**
     * Initialize the fixture.
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [];

        for ($i = 1; $i <= 21; $i++) {
            $this->records[] = [
                'name' => 'pagination'.$i,
                'title' => 'ページネーション'.$i,
                'auth_prefix' => 'admin',
                'use_admin_globalmenu' => false,
                'default_favorites' => 'YTo3OntpOjA7YToyOntzOjQ6Im5hbWUiO3M6MjE6IuOCs+ODs+ODhuODs+ODhOeuoeeQhiI7czozOiJ1cmwiO3M6MjE6Ii9hZG1pbi9jb250ZW50cy9pbmRleCI7fWk6MTthOjI6e3M6NDoibmFtZSI7czoxODoi5paw552A5oOF5aCx566h55CGIjtzOjM6InVybCI7czozMDoiL2FkbWluL2Jsb2cvYmxvZ19wb3N0cy9pbmRleC8xIjt9aToyO2E6Mjp7czo0OiJuYW1lIjtzOjMwOiLmlrDnnYDmg4XloLHjgrPjg6Hjg7Pjg4jkuIDopqciO3M6MzoidXJsIjtzOjMzOiIvYWRtaW4vYmxvZy9ibG9nX2NvbW1lbnRzL2luZGV4LzEiO31pOjM7YToyOntzOjQ6Im5hbWUiO3M6MjQ6IuOBiuWVj+OBhOWQiOOCj+OBm+ioreWumiI7czozOiJ1cmwiO3M6MzE6Ii9hZG1pbi9tYWlsL21haWxfZmllbGRzL2luZGV4LzEiO31pOjQ7YToyOntzOjQ6Im5hbWUiO3M6MjQ6IuOBiuWVj+OBhOWQiOOCj+OBm+S4gOimpyI7czozOiJ1cmwiO3M6MzM6Ii9hZG1pbi9tYWlsL21haWxfbWVzc2FnZXMvaW5kZXgvMSI7fWk6NTthOjI6e3M6NDoibmFtZSI7czoyNDoi44Ki44OD44OX44Ot44O844OJ566h55CGIjtzOjM6InVybCI7czozMToiL2FkbWluL3VwbG9hZGVyL3VwbG9hZGVyX2ZpbGVzLyI7fWk6NjthOjI6e3M6NDoibmFtZSI7czoxNToi44Kv44Os44K444OD44OIIjtzOjM6InVybCI7czoyMDoiamF2YXNjcmlwdDpjcmVkaXQoKTsiO319',
                'use_move_contents' => false,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s')
            ];
        }

        parent::init();
    }
}
