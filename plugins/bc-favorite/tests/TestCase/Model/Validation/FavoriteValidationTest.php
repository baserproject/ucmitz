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

namespace BaserCore\Test\TestCase\Model\Validation;

use BaserCore\Model\Validation\FavoriteValidation;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class FavoriteValidationTest
 * @package BaserCore\Test\TestCase\Model\Validation
 * @property FavoriteValidation $FavoriteValidation
 */
class FavoriteValidationTest extends BcTestCase
{

    /**
     * Test subject
     *
     * @var FavoriteValidation
     */
    public $FavoriteValidation;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->FavoriteValidation = new FavoriteValidation();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->FavoriteValidation);
        parent::tearDown();
    }

    /**
     * test isPermitted
     *
     * @return void
     */
    public function isPermittedTest(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
