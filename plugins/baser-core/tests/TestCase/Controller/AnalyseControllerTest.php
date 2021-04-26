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

use Cake\TestSuite\IntegrationTestTrait;
use BaserCore\TestSuite\BcTestCase;
use ReflectionClass;
use BaserCore\Controller\AnalyseController;

/**
 * BaserCore\Controller\AnalyseController Test Case
 */
class AnalyseControllerTest extends BcTestCase
{
    use IntegrationTestTrait;
    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->controller = new AnalyseController($this->getRequest());
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
     * Test index
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get('/baser/analyse/index/baser-core.json');
        $this->assertResponseOk();
        $this->assertHeader('Content-Type', 'application/json');
        $this->assertResponseContains('"0": {
            "file": "content_folders.php",
            "path": "\/plugins\/baser-core\/config\/Schema\/content_folders.php",
            "class": "",
            "method": "",
            "checked": false,
            "unitTest": false,
            "noTodo": false
        }');
    }

    /**
     * Test getList
     *
     * @return void
     */
    public function testGetList()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test getAnnotations
     *
     * @return void
     */
    public function testGetAnnotations()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test getTraitMethod
     *
     * @return void
     */
    public function testGetTraitMethod()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test pathToClass
     *
     * @return void
     */
    public function testPathToClass()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
