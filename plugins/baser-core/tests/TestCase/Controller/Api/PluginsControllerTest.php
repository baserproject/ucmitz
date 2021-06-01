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

namespace BaserCore\Test\TestCase\Controller\Api;

use BaserCore\TestSuite\BcTestCase;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * BaserCore\Controller\Api\PluginsController Test Case
 */
class PluginsControllerTest extends BcTestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Plugins',
    ];

    /**
     * Token
     * @var string
     */
    public $token = null;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        Configure::config('baser', new PhpConfig());
        Configure::load('BaserCore.setting', 'baser');
        $this->token = Configure::read('BcApp.apiToken');
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get('/baser/api/baser-core/plugins/index.json?token=' . $this->token);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('BcBlog', $result->plugins[0]->name);
    }






}
