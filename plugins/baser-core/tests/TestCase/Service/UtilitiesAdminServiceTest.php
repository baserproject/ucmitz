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

namespace BaserCore\Test\TestCase\Service;

use BaserCore\Service\UtilitiesAdminService;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class UtilitiesAdminServiceTest
 * @property UtilitiesAdminService $UtilitiesAdminService
 */
class UtilitiesAdminServiceTest extends BcTestCase
{

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->UtilitiesAdminService = new UtilitiesAdminService();
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
     * test getViewVarsForInfo
     */
    public function test_getViewVarsForInfo()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test _getDriver
     */
    public function test_getDriver()
    {
        $result = $this->execPrivateMethod($this->UtilitiesAdminService, '_getDriver');
        $this->assertEquals('MySQL', $result);
    }

    /**
     * test getViewVarsForLogMaintenance
     */
    public function test_getViewVarsForLogMaintenance()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
