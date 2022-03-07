<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */
namespace BaserCore\Test\TestCase\Model\Behavior;

use ArrayObject;
use Cake\ORM\Entity;
use ReflectionClass;
use Cake\Filesystem\File;
use BaserCore\TestSuite\BcTestCase;
use Laminas\Diactoros\UploadedFile;
use BaserCore\Model\Table\PagesTable;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Model\Table\ContentsTable;
use BaserCore\Model\Behavior\BcSearchIndexManager;
use BaserCore\Service\ContentServiceInterface;
use BaserCore\Model\Behavior\BcSearchIndexManagerBehavior;

/**
 * Class BcSearchIndexManagerBehavioreTest
 *
 * @package Baser.Test.Case.Model
 */
class BcSearchIndexManagerBehaviorTest extends BcTestCase
{

    public $fixtures = [
        'plugin.BaserCore.Pages',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.ContentFolders',
        'plugin.BaserCore.SearchIndexes',
    ];

    /**
     * @var PagesTable|BcSearchIndexManagerBehavior
     */
    public $table;

    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->table = $this->getTableLocator()->get('BaserCore.Pages');
        $this->table->setPrimaryKey(['id']);
        $this->table->addBehavior('BaserCore.BcSearchIndexManager');
        $this->BcSearchIndexManager = $this->table->getBehavior('BcSearchIndexManager');
        parent::setUp();
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->table, $this->BcSearchIndexManager);
        parent::tearDown();
    }

    /**
     * testInitialize
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $this->assertNotEmpty($this->BcSearchIndexManager->Contents);
        $this->assertInstanceOf("BaserCore\Model\Table\PagesTable", $this->BcSearchIndexManager->table);
        $this->assertNotEmpty($this->BcSearchIndexManager->SearchIndexes);
    }


    /**
     * コンテンツデータを登録する
     *
     * @param Model $model
     * @param array $data
     * @return boolean
     */
    public function testSaveSearchIndex()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $page = $this->table->find()->contain('Contents')->first();
        $pageSearchIndex = ['SearchIndex' => [
            'model_id' => $page->id,
            'type' => 'ページ',
            'content_id' => $page->content->id,
            'title' => $page->content->title,
            'detail' => $page->content->description . ' ' . $page->contents,
            'url' => $page->content->url,
            'status' => $page->content->status,
            'site_id' => $page->content->site_id,
            'publish_begin' => $page->content->publish_begin ?? '',
            'publish_end' => $page->content->publish_end ?? '',
        ]];
        $result = $this->table->saveSearchIndex($pageSearchIndex);
    }

    /**
     * コンテンツデータを削除する
     */
    public function testDeleteSearchIndex()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * コンテンツメタ情報を更新する
     */
    public function testUpdateSearchIndexMeta()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
