<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Favorite Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Favorite Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BcFavorite\Test\TestCase\Model\Table;

use BaserCore\TestSuite\BcTestCase;
use BcFavorite\Model\Table\FavoritesTable;

/**
 * Class FavoriteTableTest
 * @package BcFavorite\Test\TestCase\Model\Table
 */
class FavoritesTableTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.Plugins',
        'plugin.BcFavorite.Favorites',
    ];

    /**
     * @var Favorites
     */
    public $Favorites;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Favorites')? [] : ['className' => 'BcFavorite\Model\Table\FavoritesTable'];
        $this->Favorites = $this->getTableLocator()->get('Favorites', $config);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Favorites);
        parent::tearDown();
    }

    /**
     * Test validationDefault
     *
     * @return void
     * @dataProvider validationDefaultDataProvider
     */
    public function testValidationDefault($fields): void
    {
        $favorite = $this->Favorites->newEntity($fields);
        $a = $favorite->getErrors();
        $this->assertSame($messages, $favorite->getErrors());
    }
    public function validationDefaultDataProvider()
    {
        return [
            [
                'url' => 1,
            ],
        ];
    }



}
