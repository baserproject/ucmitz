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

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Utility\BcFileUploader;
use BaserCore\View\Helper\BcBaserHelper;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\View\View;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class ThemesControllerTest extends \BaserCore\TestSuite\BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/Sites',
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
        $this->loadFixtureScenario(InitAppScenario::class);
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
     * テーマを適用するAPI
     */
    public function testApply(): void
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $theme = 'BcSpaSample';
        $this->post('/baser/api/baser-core/themes/apply/1/'. $theme . '.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals($theme, $result->theme);
        $this->assertEquals('テーマ「' . $theme . '」を適用しました。', $result->message);
    }

}
