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

namespace BaserCore\Test\TestCase\Controller;

use Cake\Event\Event;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\ORM\TableRegistry;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Controller\AppController;

/**
 * BaserCore\Controller\AppController Test Case
 */
class AppControllerTest extends BcTestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Sites'
    ];

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->AppController = new AppController($this->getRequest());
        $this->RequestHandler = $this->AppController->components()->load('RequestHandler');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertNotEmpty($this->AppController->BcMessage);
        $this->assertNotEmpty($this->AppController->Security);
        $this->assertNotEmpty($this->AppController->Paginator);
    }

    /**
     * Test beforeRender
     *
     * @return void
     * @dataProvider beforeRenderDataProvider
     */
    public function testBeforeRender($param, $expectedClassName, $expectedTheme): void
    {
        $event = new Event('Controller.beforeRender', $this->AppController);

        if (!empty($param)) {
            $this->AppController->setRequest($this->AppController->getRequest()->withParam($param[0], $param[1]));
            $this->RequestHandler->startup($event);
        }

        $this->AppController->beforeRender($event);

        if (!empty($expectedClassName)) {
            $this->assertEquals($expectedClassName, $this->AppController->viewBuilder()->getClassName());
        } else {
            $this->assertEmpty($this->AppController->viewBuilder()->getClassName());
        }

        if (!empty($expectedTheme)) {
            $this->assertEquals($expectedTheme, $this->AppController->viewBuilder()->getTheme());
        } else {
            $this->assertEmpty($this->AppController->viewBuilder()->getTheme());
        }
    }

    public function beforeRenderDataProvider()
    {
        $sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
        $site = $sites->find()->first();

        return [
            [null, 'BaserCore.App', null],
            [['Site', $site], 'BaserCore.App', $site->theme],
            [['_ext', 'json'], null, null]
        ];
    }

}
