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

namespace BcFavorite\Test\TestCase\Model\Validation;

use BcFavorite\Model\Validation\FavoriteValidation;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class FavoriteValidationTest
 * @package BcFavorite\Test\TestCase\Model\Validation
 * @property FavoriteValidation $FavoriteValidation
 */
class FavoriteValidationTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
         'plugin.BaserCore.Users',
         'plugin.BaserCore.UsersUserGroups',
         'plugin.BaserCore.UserGroups',
         'plugin.BaserCore.Plugins',
    ];

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
     * @dataProvider isPermittedDataProvider
     */
    public function testIsPermitted($isLogin, $url, $expected): void
    {
        if($isLogin) {
            $this->loginAdmin($this->getRequest('/'));
        }
        $this->assertEquals($expected, $this->FavoriteValidation->isPermitted($url));
    }

    public function isPermittedDataProvider()
    {
        return [
            [true, '/baser/admin/users/index', true]
        ];
    }
}
