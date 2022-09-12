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

namespace BaserCore\Test\TestCase\Controller\Api;

use BaserCore\Utility\BcFileUploader;
use BaserCore\View\Helper\BcBaserHelper;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\View\View;

class ThemesControllerTest extends \BaserCore\TestSuite\BcTestCase
{

    /**
     * IntegrationTestTrait
     */
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
        'plugin.BaserCore.SiteConfigs',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.ContentFolders'
    ];

    /**
     * Access Token
     * @var string
     */
    public $accessToken = null;

    /**
     * Refresh Token
     * @var null
     */
    public $refreshToken = null;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $token = $this->apiLoginAdmin(1);
        $this->accessToken = $token['access_token'];
        $this->refreshToken = $token['refresh_token'];
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        Configure::clear();
        parent::tearDown();
    }

    /**
     * test View
     */
    public function testApply(): void
    {
        $this->post('/baser/api/baser-core/themes/apply/1/BcSpaSample.json?token=' . $this->accessToken);
//        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertCount(2, $result->themes);
        $this->assertEquals('BcThemeSample', $result->themes[0]->name);
        $this->assertEquals('BcFront', $result->themes[1]->name);
    }

}
