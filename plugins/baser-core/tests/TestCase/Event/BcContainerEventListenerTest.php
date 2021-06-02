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

namespace BaserCore\Test\TestCase\Event;

use App\Application;
use BaserCore\Event\BcContainerEventListener;
use BaserCore\TestSuite\BcTestCase;
use Cake\Event\Event;
use Cake\Event\EventManager;

/**
 * Class BcContainerEventListenerTest
 *
 * @package Baser.Test.Case.Event
 * @property  BcContainerEventListener $bcContainerEventListener
 */
class BcContainerEventListenerTest extends BcTestCase
{

    /**
     * @var EventManager|null
     */
    public $eventManager;

    /**
     * @var BcContainerEventListener|null
     */
    public $bcContainerEventListener;

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->eventManager = EventManager::instance();
        $this->bcContainerEventListener = new BcContainerEventListener();
        foreach($this->bcContainerEventListener->implementedEvents() as $key => $event) {
            $this->eventManager->off($key);
        }
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        $this->eventManager = null;
        $this->bcContainerEventListener = null;
        parent::tearDown();
    }

    /**
     * implementedEvents
     */
    public function testImplementedEvents()
    {
        $this->assertTrue(is_array($this->bcContainerEventListener->implementedEvents()));
    }

    /**
     * initialize
     */
    public function testInitialize()
    {
        $listener = $this->getMockBuilder(BcContainerEventListener::class)
            ->getMock();

        $listener->method('implementedEvents')
            ->willReturn(['Application.buildContainer' => ['callable' => 'buildContainer']]);

        $listener->expects($this->once())
            ->method('buildContainer');

        $this->eventManager
            ->on($listener)
            ->dispatch(new Event('Application.buildContainer', new Application(ROOT . '/config'), []));
    }

}
