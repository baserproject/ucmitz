<?php
declare(strict_types=1);

namespace BaserCore\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * PermissionsFixture
 */
class PermissionsFixture extends TestFixture
{
    /**
     * Import
     *
     * @var array
     */
    public $import = ['table' => 'permissions'];

    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'no' => 1,
                'sort' => 1,
                'name' => 'システム管理',
                'user_group_id' => 2,
                'url' => '/admin/*',
                'auth' => 0,
                'status' => 1,
                'created' => '2015-09-30 01:21:40',
                'modified' => null,
            ],
            [
                'id' => 2,
                'no' => 2,
                'sort' => 2,
                'name' => 'よく使う項目',
                'user_group_id' => 2,
                'url' => '/admin/favorites/*',
                'auth' => 1,
                'status' => 1,
                'created' => '2015-09-30 01:21:40',
                'modified' => null,
            ],
            [
                'id' => 3,
                'no' => 3,
                'sort' => 3,
                'name' => 'ページ管理',
                'user_group_id' => 2,
                'url' => '/admin/pages/*',
                'auth' => 1,
                'status' => 1,
                'created' => '2015-09-30 01:21:40',
                'modified' => null,
            ],
            [
                'id' => 4,
                'no' => 4,
                'sort' => 4,
                'name' => 'ページテンプレート読込・書出',
                'user_group_id' => 2,
                'url' => '/admin/pages/*_page_files',
                'auth' => 0,
                'status' => 1,
                'created' => '2015-09-30 01:21:40',
                'modified' => null,
            ],
            [
                'id' => 7,
                'no' => 7,
                'sort' => 7,
                'name' => '新着情報記事管理',
                'user_group_id' => 2,
                'url' => '/admin/blog/blog_posts/*',
                'auth' => 1,
                'status' => 1,
                'created' => '2015-09-30 01:21:40',
                'modified' => '2016-08-16 19:29:56',
            ],
            [
                'id' => 9,
                'no' => 9,
                'sort' => 9,
                'name' => '新着情報カテゴリ管理',
                'user_group_id' => 2,
                'url' => '/admin/blog/blog_categories/*',
                'auth' => 1,
                'status' => 1,
                'created' => '2015-09-30 01:21:40',
                'modified' => '2016-08-16 19:30:12',
            ],
            [
                'id' => 10,
                'no' => 10,
                'sort' => 10,
                'name' => '新着情報コメント一覧',
                'user_group_id' => 2,
                'url' => '/admin/blog/blog_comments/*',
                'auth' => 1,
                'status' => 1,
                'created' => '2015-09-30 01:21:40',
                'modified' => '2016-08-16 19:30:19',
            ],
            [
                'id' => 11,
                'no' => 11,
                'sort' => 11,
                'name' => 'ブログタグ管理',
                'user_group_id' => 2,
                'url' => '/admin/blog/blog_tags/*',
                'auth' => 1,
                'status' => 1,
                'created' => '2015-09-30 01:21:40',
                'modified' => null,
            ],
            [
                'id' => 13,
                'no' => 13,
                'sort' => 13,
                'name' => 'お問い合わせ管理',
                'user_group_id' => 2,
                'url' => '/admin/mail/mail_fields/*',
                'auth' => 1,
                'status' => 1,
                'created' => '2015-09-30 01:21:40',
                'modified' => '2016-08-16 19:30:34',
            ],
            [
                'id' => 14,
                'no' => 14,
                'sort' => 14,
                'name' => 'お問い合わせ受信メール一覧',
                'user_group_id' => 2,
                'url' => '/admin/mail/mail_messages/*',
                'auth' => 1,
                'status' => 1,
                'created' => '2015-09-30 01:21:40',
                'modified' => '2016-08-16 19:29:11',
            ],
            [
                'id' => 15,
                'no' => 15,
                'sort' => 15,
                'name' => 'エディタテンプレート呼出',
                'user_group_id' => 2,
                'url' => '/admin/editor_templates/js',
                'auth' => 1,
                'status' => 1,
                'created' => '2015-09-30 01:21:40',
                'modified' => null,
            ],
            [
                'id' => 16,
                'no' => 16,
                'sort' => 16,
                'name' => 'アップローダー',
                'user_group_id' => 2,
                'url' => '/admin/uploader/*',
                'auth' => 1,
                'status' => 1,
                'created' => '2015-09-30 01:21:40',
                'modified' => null,
            ],
            [
                'id' => 17,
                'no' => 17,
                'sort' => 17,
                'name' => 'コンテンツ管理',
                'user_group_id' => 2,
                'url' => '/admin/contents/*',
                'auth' => 1,
                'status' => 1,
                'created' => '2016-08-16 19:28:39',
                'modified' => '2016-08-16 19:28:39',
            ],
            [
                'id' => 18,
                'no' => 18,
                'sort' => 18,
                'name' => 'リンク管理',
                'user_group_id' => 2,
                'url' => '/admin/content_links/*',
                'auth' => 1,
                'status' => 1,
                'created' => '2016-08-16 19:28:56',
                'modified' => '2016-08-16 19:28:56',
            ],
            [
                'id' => 19,
                'no' => 19,
                'sort' => 19,
                'name' => 'DebugKit 管理',
                'user_group_id' => 2,
                'url' => '/admin/debug_kit/*',
                'auth' => 1,
                'status' => 1,
                'created' => '2021-05-06 15:25:59',
                'modified' => '2021-05-06 15:25:59',
            ],
        ];
        parent::init();
    }
}
