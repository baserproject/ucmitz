<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Controller\Admin;

use BaserCore\Service\BcDatabaseService;
use Cake\TestSuite\IntegrationTestTrait;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Controller\Admin\UtilitiesController;

/**
 * class UtilitiesControllerTest
 * @package BaserCore\Controller\Admin\UtilitiesController;
 */
class UtilitiesControllerTest extends BcTestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Contents'
    ];

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $request = $this->getRequest();
        $this->loginAdmin($request);
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test clear_cache
     *
     * @return void
     */
    public function testClear_cache(): void
    {

        $this->get('/baser/admin/baser-core/utilities/clear_cache');
        $this->assertResponseCode(302);
    }

    /**
     * test ajax_save_search_box
     *
     * @return void
     */
    public function testAjax_save_search_box(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test info
     */
    public function test_info()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        $this->get('/baser/admin/baser-core/utilities/info');
        $this->assertResponseOk();
    }

    /**
     * test info
     */
    public function test_verity_contents_tree()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        // コンテンツのツリー構造に問題がある場合
        $this->post('/baser/admin/baser-core/utilities/verity_contents_tree/');
        $this->assertResponseCode(302);
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'utilities',
            'action' => 'index'
        ]);
        $this->assertFlashMessage("コンテンツのツリー構造に問題があります。ログを確認してください。");

        // コンテンツのツリー構造に問題がない場合
        $BcDatabaseService = new BcDatabaseService();
        $BcDatabaseService->truncate('contents');
        $this->post('/baser/admin/baser-core/utilities/verity_contents_tree/');
        $this->assertResponseCode(302);
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'utilities',
            'action' => 'index'
        ]);
        $this->assertFlashMessage("コンテンツのツリー構造に問題はありません。");
    }

}
