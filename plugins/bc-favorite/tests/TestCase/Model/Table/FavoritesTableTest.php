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
use BaserCore\Utility\BcUtil;

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
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.Permissions',
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

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        BcUtil::includePluginClass('BcFavorite');
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
     * Test initialize
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertEquals('favorites', $this->Favorites->getTable());
        $this->assertEquals('name', $this->Favorites->getDisplayField());
        $this->assertEquals('id', $this->Favorites->getPrimaryKey());
        $this->assertTrue($this->Favorites->hasBehavior('Timestamp'));
        $this->assertEquals('Users', $this->Favorites->getAssociation('Users')->getName());
    }

    /**
     * Test validationDefault
     *
     * @return void
     * @dataProvider validationDefaultDataProvider
     */
    public function testValidationDefault($fields, $messages): void
    {
        $this->loginAdmin($this->getRequest(), 2);
        $favorite = $this->Favorites->newEntity($fields);
        $this->assertSame($messages, $favorite->getErrors());
    }

    public function validationDefaultDataProvider()
    {
        return [
            [
                ['url' => 1],
                ['url' => ['isPermitted' => 'このURLの登録は許可されていません。']]
            ],
            [
                ['url' => '/baser/admin/favorites/add'],
                []
            ],
        ];
    }

}
