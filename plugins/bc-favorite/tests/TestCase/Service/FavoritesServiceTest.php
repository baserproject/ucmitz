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

namespace BcFavorite\Test\TestCase\Service;

use BaserCore\TestSuite\BcTestCase;
use BcFavorite\Service\FavoritesService;

/**
 * Class FavoritesServiceTest
 * @package BcFavorite\Test\TestCase\Service
 * @property FavoritesService $FavoritesService
 */
class FavoritesServiceTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
         'plugin.BaserCore.Favorites',
    ];

    /**
     * FavoritesService
     *
     * @var FavoritesService
     */
    public $FavoritesService;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->FavoritesService = new FavoritesService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->FavoritesService);
        parent::tearDown();
    }

}
