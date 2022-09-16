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
use BaserCore\TestSuite\BcTestCase;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Composer\Package\Archiver\ZipArchiver;
use Laminas\Diactoros\UploadedFile;
use Cake\TestSuite\IntegrationTestTrait;

class ThemesControllerTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;
    use IntegrationTestTrait;

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
     * test View
     */
    public function testIndex(): void
    {
        $this->get('/baser/api/baser-core/themes/index.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertCount(2, $result->themes);
        $this->assertEquals('BcThemeSample', $result->themes[0]->name);
        $this->assertEquals('BcFront', $result->themes[1]->name);
    }

    /**
     * test Add
     */
    public function testAdd(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->get('/baser/api/baser-core/themes/add.json?token=' . $this->accessToken);
        $this->assertResponseCode(405);

        $path = ROOT . DS . 'plugins' . DS . 'BcSpaSample';
        $zipSrcPath = TMP  . 'zip' . DS;
        $folder = new Folder();
        $folder->create($zipSrcPath, 0777);
        $folder->copy($zipSrcPath . 'BcSpaSample2', ['from' => $path, 'mode' => 0777]);
        $theme = 'BcSpaSample2';
        $zip = new ZipArchiver();
        $testFile = $zipSrcPath . $theme . '.zip';
        $zip->archive($zipSrcPath, $testFile, true);
        $_FILES = [
            'file' => [
                'error' => UPLOAD_ERR_OK,
                'name' => $theme . '.zip',
                'size' => 123,
                'tmp_name' => $testFile,
                'type' => 'application/zip'
            ]
        ];
        $this->configRequest([
            'Content-Type' => 'multipart/form-data',
            'files' => $_FILES
        ]);
        $this->post('/baser/api/baser-core/themes/add.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals($theme, $result->theme);
        $this->assertEquals('テーマファイル「' . $theme . '」を追加しました。', $result->message);

        $folder = new Folder();
        $folder->delete(ROOT . DS . 'plugins' . DS . $theme);
        $folder->delete($zipSrcPath);
    }
}
