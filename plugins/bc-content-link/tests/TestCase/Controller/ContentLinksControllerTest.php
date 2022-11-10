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

namespace BcContentLink\Test\TestCase\Controller;

use BaserCore\Test\Factory\ContentFactory;
use BcContentLink\Controller\ContentLinksController;
use BaserCore\TestSuite\BcTestCase;
use BcContentLink\Service\ContentLinksServiceInterface;
use BcContentLink\Test\Factory\ContentLinkFactory;

/**
 * ContentLinksControllerTest
 * @property ContentLinksController $ContentLinksController
 */
class ContentLinksControllerTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BcContentLink.Factory/ContentLinks',
        'plugin.BaserCore.Factory/Contents',
    ];

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ContentLinksController = new ContentLinksController($this->getRequest());
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->ContentLinksController);
        parent::tearDown();
    }

    /**
     * Test initialize method
     */
    public function test_initialize()
    {
        $this->assertNotEmpty($this->ContentLinksController->BcFrontContents);
    }

    /**
     * test view
     *
     * @return void
     */
    public function test_view(): void
    {
        ContentLinkFactory::make(['id' => 1, 'url' => '/test-new'])->persist();
        ContentFactory::make([
            'id' => 1,
            'plugin' => 'BcContentLink',
            'type' => 'ContentLink',
            'site_id' => 1,
            'title' => 'test new link',
            'lft' => 1,
            'rght' => 2,
            'entity_id' => 1,
            "status" => true,
        ])->persist();
        $request = $this->getRequest()->withAttribute('currentContent', ContentFactory::get(1));
        $controller = new ContentLinksController($request);

        $service = $this->getService(ContentLinksServiceInterface::class);
        $controller->view($service);
        $rs = $controller->viewBuilder()->getVars()['contentLink']->toArray();
        $this->assertEquals('/test-new', $rs['url']);
        $this->assertEquals('test new link', $rs['content']['title']);
    }
}
