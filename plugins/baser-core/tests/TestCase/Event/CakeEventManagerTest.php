<?php
// TODO : コード確認要
return;
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.Event
 * @since           baserCMS v 4.0.9
 * @license         https://basercms.net/license/index.html
 */

App::uses('CakeEventManager', 'Event');

/**
 * Class CakeEventManagerTest
 *
 * @package Baser.Test.Case.Event
 * @property  CakeEventManager $CakeEventManager
 */
class CakeEventManagerTest extends BaserTestCase
{

	/**
	 * set up
	 *
	 * @return void
	 */
	public function setUp()
	{
		parent::setUp();
	}

	/**
	 * tearDown
	 *
	 * @return void
	 */
	public function tearDown()
	{
		parent::tearDown();
	}

	/**
	 * Dispatches a new event to all configured listeners
	 */
	public function testDispatch()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
