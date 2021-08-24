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

namespace BaserCore\Test\TestCase\Service;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Service\ContentFoldersService;

/**
 * BaserCore\Model\Table\ContentFoldersTable Test Case
 *
 * @property ContentFoldersService $ContentFoldersService
 */
class ContentFoldersServiceTest extends BcTestCase
{

    /**
     * Test subject
     *
     * @var ContentFoldersService
     */
    public $ContentFolders;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.ContentFolders',
    ];

        /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ContentFoldersService = new ContentFoldersService();
        $this->Contents = $this->getTableLocator()->get('Contents');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->ContentFoldersService);
        parent::tearDown();
    }

    /**
     * Test create
     */
    public function testCreate()
    {
        $data = [
            'folder_template' => 'テストcreate',
            'content' => [
                "parent_id" => "1",
                "title" => "新しい フォルダー",
                "plugin" => 'BaserCore',
                "type" => "ContentFolder",
                "site_id" => "0",
                "alias_id" => "",
                "entity_id" => "",
            ]
        ];
        $result = $this->ContentFoldersService->create($data);
        $folderExpected = $this->ContentFoldersService->ContentFolders->find()->last();
        $contentExpected = $this->Contents->find()->last();
        $this->assertEquals($folderExpected->name, $result->name);
        $this->assertEquals("新しい フォルダー", $contentExpected->title);
    }
}
