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

namespace BaserCore\Test\TestCase\Controller\Component;

use BaserCore\TestSuite\BcTestCase;
use Cake\Controller\Controller;
use Cake\Controller\ComponentRegistry;
use BaserCore\Controller\Component\BcContentsComponent;


/**
 * Class BcMessageTestController
 *
 * @package BaserCore\Test\TestCase\Controller\Component
 * @property BcMessageComponent $BcMessage
 */
class BcContentsTestController extends Controller
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel('BaserCore.Contents');
        $this->Contents->addBehavior('Tree', ['level' => 'level']);
    }
}

/**
 * Class BcContentsComponentTest
 *
 * @package Baser.Test.Case.Controller.Component
 */
class BcContentsComponentTest extends BcTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Contents',
    ];

    /**
     * set up
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->getRequest();
        $this->Controller = new BcContentsTestController();
        $this->ComponentRegistry = new ComponentRegistry($this->Controller);
        $this->BcContents = new BcContentsComponent($this->ComponentRegistry);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($_SESSION);
        parent::tearDown();
    }

    public function testInitialize()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    public function testSetupAdmin()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    public function testSetupFront()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    public function testGetCrumbs()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    public function testGetContent()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    public function testBeforeRender()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    public function testSettingForm()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    public function testGetParentLayoutTemplate()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    public function testGetType()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
