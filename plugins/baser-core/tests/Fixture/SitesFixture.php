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

namespace BaserCore\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SiteFixture
 */
class SitesFixture extends TestFixture
{

    public $import = ['table' => 'sites'];

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => '1',
            'main_site_id' => null,
            'name' => '',
            'display_name' => 'メインサイト',
            'title' => 'baserCMS inc.',
            'alias' => '',
            'theme' => 'bc_sample',
            'status' => true,
            'keyword' => '',
            'description' => '',
            'use_subdomain' => false,
            'relate_main_site' => false,
            'device' => '',
            'lang' => '',
            'same_main_url' => false,
            'auto_redirect' => false,
            'auto_link' => false,
            'domain_type' => null,
            'created' => '2021-07-01 21:20:15',
            'modified' => null
        ],
        [
            'id' => '2',
            'main_site_id' => 1,
            'name' => 'smartphone',
            'display_name' => 'スマホサイト',
            'title' => 'baserCMS inc.｜スマホ',
            'alias' => 's',
            'theme' => '',
            'status' => false,
            'keyword' => '',
            'description' => '',
            'use_subdomain' => false,
            'relate_main_site' => false,
            'device' => 'smartphone',
            'lang' => '',
            'same_main_url' => false,
            'auto_redirect' => true,
            'auto_link' => true,
            'domain_type' => null,
            'created' => '2021-07-01 21:20:15',
            'modified' => null
        ],
        [
            'id' => '3',
            'main_site_id' => 1,
            'name' => 'en',
            'display_name' => '英語サイト',
            'title' => 'baserCMS inc.｜English',
            'alias' => 'en',
            'theme' => '',
            'status' => true,
            'keyword' => '',
            'description' => '',
            'use_subdomain' => false,
            'relate_main_site' => false,
            'device' => 'smartphone',
            'lang' => '',
            'same_main_url' => false,
            'auto_redirect' => true,
            'auto_link' => false,
            'domain_type' => null,
            'created' => '2021-07-01 21:20:15',
            'modified' => null
        ],
    ];

}
