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
namespace BaserCore\Test\TestCase\Model\Behavior;

use ArrayObject;
use Cake\ORM\Entity;
use ReflectionClass;
use BaserCore\TestSuite\BcTestCase;
use Laminas\Diactoros\UploadedFile;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Model\Table\ContentsTable;
use BaserCore\Model\Behavior\BcUploadBehavior;
use BaserCore\Service\ContentServiceInterface;

/**
 * Class BcUploadBehaviorTest
 *
 * @package Baser.Test.Case.Model
 * @property BcUploadBehavior $BcUploadBehavior
 * @property EditorTemplate $EditorTemplate
 * @property ContentsTable $ContentsTable
 * @property ContentServiceInterface $ContentService
 */
class BcUploadBehaviorTest extends BcTestCase
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Pages',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.ContentFolders',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.SiteConfigs',
    ];


    /**
     * @var ContentsTable|BcUploadBehavior
     */
    public $table;

    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->table = $this->getTableLocator()->get('BaserCore.Contents');
        $this->table->setPrimaryKey(['id']);
        $this->table->addBehavior('BaserCore.BcUpload');
        $this->BcUploadBehavior = $this->table->getBehavior('BcUpload');
        $this->ContentService = $this->getService(ContentServiceInterface::class);
        $this->uploadedData = [
            'eyecatch' => [
                "tmp_name" => "/tmp/testBcUpload.png",
                "error" => 0,
                "name" => "test.png",
                "type" => "image/png",
                "size" => 100
            ],
            'eyecatch_delete' => 0,
            'eyecatch_' => 'test.png',
        ];
        $this->eyecatchField = [
            'name' => 'eyecatch',
            'ext' => 'gif',
            'upload' => true,
            'type' => 'image',
            'getUniqueFileName' => true,
        ];
        $this->savePath = $this->BcUploadBehavior->savePath[$this->table->getAlias()];
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        session_unset();
        unset($this->table, $this->BcUploadBehavior, $this->savePath, $this->uploadedData, $this->eyecatchField);
        parent::tearDown();
    }


    /**
     * ファイル等が内包されたディレクトリも削除する
     *
     * testGetFieldBasename()で使用します
     *
     * @param string $dir 対象のディレクトリのパス
     * @return void
     */
    public function removeDir($dir)
    {
        if ($handle = opendir("$dir")) {
            while(false !== ($item = readdir($handle))) {
                if ($item != "." && $item != "..") {
                    if (is_dir("$dir/$item")) {
                        $this->removeDir("$dir/$item");
                    } else {
                        unlink("$dir/$item");
                    }
                }
            }
            closedir($handle);
            rmdir($dir);
        }
    }

    /**
     * testInitialize
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertNotEmpty($this->BcUploadBehavior->settings);
        $this->assertNotEmpty($this->BcUploadBehavior->savePath);
        $this->assertNotEmpty($this->BcUploadBehavior->existsCheckDirs);
        $this->assertNotEmpty($this->BcUploadBehavior->Session);
        // testフォルダがない場合作られるかテスト
        $this->BcUploadBehavior->initialize(['saveDir' => 'test']);
        $this->assertFileExists("/var/www/html/webroot/files/test/");
        rmdir("/var/www/html/webroot/files/test/");
    }

    /**
     * testGetSettings
     *
     * @return void
     */
    public function testGetSettings()
    {
        $config = [
            'saveDir' => "contents",
            'fields' => [
                'eyecatch' => [
                    "type" => "image",
                    "namefield" => "id",
                    "nameadd" => true,
                    "nameformat" => "%08d",
                    "subdirDateFormat" => "Y/m",
                    "imagecopy" => [],
                ]
            ]
        ];
        $setting = $this->BcUploadBehavior->getSettings($config);
        $this->assertEquals("eyecatch", $setting[$this->table->getAlias()]['fields']['eyecatch']['name']);
        $this->assertEquals(false, $setting[$this->table->getAlias()]['fields']['eyecatch']['imageresize']);
        $this->assertEquals(true, $setting[$this->table->getAlias()]['fields']['eyecatch']['getUniqueFileName']);
    }

    /**
     * testGetExistsCheckDirs
     *
     * @return void
     */
    public function testGetExistsCheckDirs()
    {
        $result = $this->execPrivateMethod($this->BcUploadBehavior, "getExistsCheckDirs", [$this->table->getAlias()]);
        $this->assertEquals("/var/www/html/webroot/files/contents/", $result[0]);
    }

    /**
     * Before Validate
     */
    public function testBeforeMarshal()
    {
        $result = $this->table->dispatchEvent('Model.beforeMarshal', ['data' => new ArrayObject($this->uploadedData), 'options' => new ArrayObject()]);
        // setupRequestDataが実行されてるか確認
        $this->assertNotNull($this->BcUploadBehavior->getUploadedFile());
        // 保存前にeyecatchをオブジェクトではなく、stringに変換してるか確認
        $this->assertEquals("test.png", $result->getData('data')['eyecatch']);
    }

    /**
     * Before save
     */
    public function testBeforeSave()
    {
        // 画像を新規追加する場合
        $imgPath = ROOT . '/plugins/bc-admin-third/webroot/img/';
        $fileName = 'baser.power';
        $this->eyecatchField['width'] = 100;
        $this->eyecatchField['height'] = 100;
        $tmp = '/tmp/baser.power.gif';
        copy($imgPath . $fileName . '.' . $this->eyecatchField['ext'], $tmp);
        $uploadedFile = [
            'eyecatch' => [
                'name' => $fileName . '.' . $this->eyecatchField['ext'],
                'tmp_name' => $tmp,
            ]
        ];
        $this->table->setUploadedFile($uploadedFile);
        $this->BcUploadBehavior->settings[$this->table->getAlias()]['fields']['eyecatch'] = $this->eyecatchField;
        // 新規保存の場合
        $entity = new Entity(['id' => 1, 'eyecatch' => 'baser.power.gif']);
        $return = $this->table->dispatchEvent('Model.beforeSave', ['entity' => $entity, 'options' => new ArrayObject()]);
        $this->assertTrue($return->getResult());
        $this->assertFileExists($this->savePath . 'baser.power.gif');
        // 削除の場合
        $uploadedFile = [
            'eyecatch' => [
                'name' => '',
                'tmp_name' => '',
            ],
            'eyecatch_delete' => 1,
        ];
        $this->table->setUploadedFile($uploadedFile);
        $return = $this->table->dispatchEvent('Model.beforeSave', ['entity' => $entity, 'options' => new ArrayObject()]);
        $this->assertTrue($return->getResult());
        $this->assertEmpty($return->getData('entity')->eyecatch);
        $this->assertFileNotExists($this->savePath . 'baser.power.gif');
    }

    /**
     * リクエストされたデータを処理しやすいようにセットアップする
     */
    public function testSetupRequestData()
    {
        // upload=falseの場合のテスト
        $data = [
            'eyecatch' => [
                "tmp_name" => "",
                "name" => "",
                "type" => "image/png",
                ]
        ];
        $this->BcUploadBehavior->setupRequestData($data);
        $this->assertFalse($this->BcUploadBehavior->settings[$this->table->getAlias()]['fields']['eyecatch']['upload']);
        // upload=trueの場合のテスト
        $this->BcUploadBehavior->setupRequestData($this->uploadedData);
        $this->assertTrue($this->BcUploadBehavior->settings[$this->table->getAlias()]['fields']['eyecatch']['upload']);
        $this->assertEquals("png", $this->BcUploadBehavior->settings[$this->table->getAlias()]['fields']['eyecatch']['ext']);
        //  新しいデータが送信されず、既存データを引き継ぐ場合
        $data = [
            'eyecatch' => [
                "type" => "image/png",
                "error" => 4,
            ],
            'eyecatch_' => 'test.png',
        ];
        $requestData = $this->BcUploadBehavior->setupRequestData($data);
        $this->assertFalse($this->BcUploadBehavior->settings[$this->table->getAlias()]['fields']['eyecatch']['upload']);
        $this->assertEquals("test.png", $requestData['eyecatch']);
        // tmpIdが存在する場合

    }

    /**
     * After save
     *
     * @param Model $Model
     * @param Model $created
     * @param Model $options
     * @return boolean
     */
    public function testAfterSave()
    {
        touch($this->savePath . 'test.png');
        $this->table->setUploadedFile(['eyecatch' => ['name' => 'test.png']]);
        $this->BcUploadBehavior->uploaded[$this->table->getAlias()] = true;
        $entity = $this->table->get(1);
        $entity->eyecatch = 'test.png';
        $return = $this->table->dispatchEvent('Model.afterSave', ['entity' => $entity, 'options' => new ArrayObject()]);
        $this->assertTrue($return->getResult());
        $this->assertEquals($return->getData('entity')->eyecatch, "00000001_eyecatch.png");
        unlink($this->savePath . "00000001_eyecatch.png");
    }

    /**
     * 一時ファイルとして保存する
     * セッション時のテスト
     * @param Model $Model
     * @param array $data
     * @param string $tmpId
     */
    public function testSaveTmpFiles()
    {
        touch($this->uploadedData['eyecatch']['tmp_name']);
        $data = $this->BcUploadBehavior->saveTmpFiles($this->uploadedData, 1);
        $tmpId = $this->BcUploadBehavior->tmpId;
        $this->assertEquals("00000001_eyecatch.png", $data['eyecatch']['session_key'], 'saveTmpFiles()の返り値が正しくありません');
        $this->assertEquals(1, $tmpId, 'tmpIdが正しく設定されていません');
        @unlink($this->uploadedData['tmp_name']);
    }

    /**
     * testDeleteFiles
     *
     * @return void
     */
    public function testDeleteFiles()
    {
        $this->BcUploadBehavior->settings[$this->table->getAlias()]['fields']['eyecatch'] = $this->eyecatchField;
        // 削除を実行
        $uploadedFile = [
            'eyecatch' => [
                "name" => 'dummy',
            ],
            'eyecatch_delete' => 1,
            'eyecatch_' => '<input />',
        ];
        $targetPath = $this->savePath . 'dummy.gif';
        $entity = $this->table->find()->last();
        $entity->eyecatch = 'dummy';
        touch($targetPath);
        $this->BcUploadBehavior->deleteFiles($entity, $uploadedFile);
        $this->assertFileNotExists($targetPath);
        @unlink($targetPath);
    }


    /**
     * 削除対象かチェックしながらファイルを削除する
     */
    public function testDeleteFileWhileChecking()
    {
        // $this->tmpIdがない場合
        $fileName = 'dummy';
        $uploadedFile = [
            'eyecatch' => [
                "name" => $fileName,
            ],
            'eyecatch_delete' => 1,
            'eyecatch_' => 'dummy.gif',
        ];
        $targetPath = $this->savePath . $fileName . '.' . $this->eyecatchField['ext'];
        // ダミーのファイルを生成
        touch($targetPath);
        $uploaded = $this->BcUploadBehavior->deleteFileWhileChecking($this->eyecatchField, $uploadedFile, $fileName);
        $this->assertFileNotExists($targetPath);
        @unlink($targetPath);
        // tmpIdがある場合 oldValueが入る
        $this->BcUploadBehavior->tmpId= 1;
        $actual = $this->BcUploadBehavior->deleteFileWhileChecking($this->eyecatchField, $uploadedFile, $fileName);
        $this->assertEquals($fileName, $actual['eyecatch']);
    }


    /**
     * ファイル群を保存する
     */
    public function testSaveFiles()
    {
        $this->eyecatchField['ext'] = 'png';
        $this->BcUploadBehavior->settings[$this->table->getAlias()]['fields']['eyecatch']  = $this->eyecatchField;
        $filePath = $this->savePath . $this->uploadedData['eyecatch']['name'];
        $tmp = $this->uploadedData['eyecatch']['tmp_name'];
        touch($tmp);
        $result = $this->BcUploadBehavior->saveFiles($this->uploadedData);
        $this->assertFileExists($filePath);
        unlink($filePath);
        $this->assertTrue($result);
    }

    /**
     * 保存対象かチェックしながらファイルを保存する
     */
    public function testSaveFileWhileChecking()
    {
        $this->eyecatchField['ext'] = 'png';
        $filePath = $this->savePath . $this->uploadedData['eyecatch']['name'];
        // nameが空の場合 新規画像なしでの保存など
        $this->BcUploadBehavior->saveFileWhileChecking($this->eyecatchField, ["eyecatch" => ['name' => '']]);
        $this->assertFileNotExists($filePath);
        // nameがある場合 新規画像保存の場合
        $tmp = $this->uploadedData['eyecatch']['tmp_name'];
        touch($tmp);
        $this->BcUploadBehavior->saveFileWhileChecking($this->eyecatchField, $this->uploadedData);
        $this->assertFileExists($filePath);
        $this->assertFileNotExists($tmp);
        unlink($filePath);
    }


    /**
     * saveFilesのテスト
     * ファイルをコピーする
     *
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider saveFilesCanCopyDataProvider
     */
    public function testSaveFilesCanCopy($imagecopy, $message)
    {

        // TODO 2020/07/08 ryuring PHP7.4 で、gd が標準インストールされないため、テストがエラーとなるためスキップ
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        return;

        $this->initTestSaveFiles(1, ['name' => 'copy.gif', 'type' => 'image']);

        // パス情報
        $targetPath = $this->savePath . 'copy.gif';

        // 初期化
        $this->BcUploadBehavior->settings['EditorTemplate']['fields']['image']['imagecopy'] = $imagecopy;

        // 保存を実行
        $this->EditorTemplate->saveFiles($this->EditorTemplate->data);
        $this->assertFileExists($targetPath, $message);

        // 生成されたファイルを削除
        @unlink($targetPath);
        $this->deleteDummyOnTestSaveFiles();

    }

    public function saveFilesCanCopyDataProvider()
    {
        return [
            [
                [['width' => 40, 'height' => 6]],
                'saveFiles()でファイルをコピーできません'
            ],
            [
                [
                    ['width' => 40, 'height' => 6],
                    ['width' => 30, 'height' => 6]
                ],
                'saveFiles()でファイルをコピーできません'
            ],
        ];
    }

    /**
     * saveFilesのテスト
     * ファイルをリサイズする
     *
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider saveFilesCanResizeDataProvider
     */
    public function testSaveFilesCanResize($imageresize, $expected, $message)
    {

        // TODO 2020/07/08 ryuring PHP7.4 で、gd が標準インストールされないため、テストがエラーとなるためスキップ
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        return;

        $this->initTestSaveFiles();

        // パス情報
        $targetPath = $this->savePath . 'basename.gif';

        // 初期化
        $this->BcUploadBehavior->settings['EditorTemplate']['fields']['image']['imageresize'] = $imageresize;

        // 保存を実行
        $this->EditorTemplate->saveFiles($this->EditorTemplate->data);

        $result = $this->BcUploadBehavior->getImageSize($targetPath);
        $this->assertEquals($expected, $result, $message);

        // 生成されたファイルを削除
        @unlink($targetPath);
        $this->deleteDummyOnTestSaveFiles();

    }

    public function saveFilesCanResizeDataProvider()
    {
        return [
            [['width' => 20, 'height' => 10, 'thumb' => false], ['width' => 20, 'height' => 2], 'saveFiles()でファイルをリサイズできません'],
            [['width' => 20, 'height' => 10, 'thumb' => true], ['width' => 20, 'height' => 10], 'saveFiles()でファイルをリサイズできません'],
        ];
    }


    /**
     * セッションに保存されたファイルデータをファイルとして保存する
     *
     * @param Model $Model
     * @param string $fieldName
     * @return void
     */
    public function testMoveFileSessionToTmp()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $tmpId = 1;
        $fieldName = 'fieldName';
        $tmp_name = 'basercms_tmp';
        $basename = 'basename';
        $ext = 'png';
        $namefield = 'hoge';

        //—————————————————————————
        // セッションを設定
        //—————————————————————————

        // パス情報
        $tmpPath = $this->savePath . $tmp_name;

        // 初期化
        $this->eyecatchField = [
            'name' => $fieldName,
            'ext' => $ext,
            'namefield' => $namefield,
        ];
        $this->BcUploadBehavior->tmpId = $tmpId;

        $this->EditorTemplate->data['EditorTemplate'][$fieldName] = [
            'name' => $basename,
            'tmp_name' => $tmpPath,
            'type' => 'basercms',
        ];

        // ダミーファイルの作成
        $file = new File($tmpPath);
        $file->write('dummy');
        $file->close();

        // セッションを設定
        $this->EditorTemplate->saveFile($this->eyecatchField);

        //—————————————————————————
        // 本題
        //—————————————————————————

        // パス情報
        $targetName = $tmpId . '_' . $fieldName . '_' . $ext;
        $targetPath = $this->savePath . $targetName;

        // 初期化
        $this->EditorTemplate->data['EditorTemplate'][$fieldName . '_tmp'] = $targetName;

        // セッションからファイルを保存
        $this->EditorTemplate->moveFileSessionToTmp($fieldName);

        // 判定
        $this->assertFileExists($targetPath, 'セッションに保存されたファイルデータをファイルとして保存できません');

        $result = $this->EditorTemplate->data['EditorTemplate'][$fieldName];
        $expected = [
            'error' => 0,
            'name' => $targetName,
            'tmp_name' => $targetPath,
            'size' => 5,
            'type' => 'basercms',
        ];
        $this->assertEquals($expected, $result, 'アップロードされたデータとしてデータを復元できません');

        // 生成されたファイルを削除
        @unlink($tmpPath);
        @unlink($targetPath);

    }

    /**
     * ファイルを保存する(tmpIdがない場合)
     *
     */
    public function testSaveFile()
    {
        $ext = 'png';
        $this->eyecatchField['ext'] = $ext;
        $targetPath = $this->savePath . $this->uploadedData['eyecatch']['name'];
        // ダミーファイルの作成
        touch($this->uploadedData['eyecatch']['tmp_name']);
        // ファイル保存を実行
        $result = $this->BcUploadBehavior->saveFile($this->uploadedData['eyecatch'], $this->eyecatchField);
        $this->assertFileExists($targetPath);
        // 生成されたファイルを削除
        @unlink($this->uploadedData['eyecatch']['tmp_name']);
        @unlink($targetPath);
    }

    /**
     * ファイルを保存する(tmpIdがある場合)
     *
     */
    public function testSaveFileWithTmp()
    {
        // tmpIdがある場合
        $this->BcUploadBehavior->tmpId = 1;
        touch($this->uploadedData['eyecatch']['tmp_name']);
        $fileData = 'testtest';
        file_put_contents($this->uploadedData['eyecatch']['tmp_name'], $fileData);
        // セッション書き込みを実行
        $result = $this->BcUploadBehavior->saveFile($this->uploadedData['eyecatch'], $this->eyecatchField);
        // saveFile内にてSessionが保存されているかをテスト
        $this->assertEquals($fileData, $this->BcUploadBehavior->Session->read('Upload.1_eyecatch_gif.data'));

    }

    /**
     * 保存用ファイル名を取得する
     */
    public function testGetSaveFileName()
    {
        $name = 'dummy.gif';
        $targetPath = $this->savePath . $name;
        touch($targetPath);
        $result = $this->BcUploadBehavior->getSaveFileName($this->eyecatchField, $name);
        $this->assertEquals('dummy_1.gif', $result);
        @unlink($targetPath);
    }

    /**
     * 画像をExif情報を元に正しい確度に回転する
     */
    public function testRotateImage()
    {
        // TODO: 現在php_exifがないためfalseになる
        $this->assertFalse($this->BcUploadBehavior->rotateImage('test.png'));
    }

    /**
     * 画像をコピーする
     *
     * @param array $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider copyImageDataProvider
     */
    public function testCopyImage($prefix, $suffix, $message = null)
    {
        $imgPath = ROOT . '/plugins/bc-admin-third/webroot/img/';
        $fileName = 'baser.power';
        $this->eyecatchField['prefix'] = $prefix;
        $this->eyecatchField['suffix'] = $suffix;
        $this->eyecatchField['width'] = 100;
        $this->eyecatchField['height'] = 100;
        $uploadedFile = [
            'eyecatch' => [
                'name' => $fileName . '_copy' . '.' . $this->eyecatchField['ext'],
                'tmp_name' => $imgPath . $fileName . '.' . $this->eyecatchField['ext'],
            ]
        ];
        $this->BcUploadBehavior->setUploadedFile($uploadedFile, $this->table->getAlias());
        // コピー先ファイルのパス
        $targetPath = $this->savePath . $this->eyecatchField['prefix'] . $fileName . '_copy' . $this->eyecatchField['suffix'] . '.' . $this->eyecatchField['ext'];
        // コピー実行
        $this->BcUploadBehavior->copyImage($this->table->getAlias(), $this->eyecatchField);
        $this->assertFileExists($targetPath, $message);
        // コピーしたファイルを削除
        @unlink($targetPath);
    }

    public function copyImageDataProvider()
    {
        return [
            ['', '', '画像ファイルをコピーできません'],
            ['pre-', '-suf', '画像ファイルの名前にプレフィックスを付けてコピーできません'],
        ];
    }

    /**
     * 画像ファイルをコピーする
     * リサイズ可能
     *
     * @param int $width 横幅
     * @param int $height 高さ
     * @param boolean $$thumb サムネイルとしてコピーするか
     * @param array $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider resizeImageDataProvider
     */
    public function testResizeImage($width, $height, $thumb, $expected, $message = null)
    {

        $imgPath = ROOT . '/plugins/bc-admin-third/webroot/img/';
        $source = $imgPath . 'baser.power.gif';
        $distination = $imgPath . 'baser.power_copy.gif';
        // コピー実行
        $this->table->resizeImage($source, $distination, $width, $height, $thumb);
        if (!$width && !$height) {
            $this->assertFileExists($distination, $message);
        } else {
            $result = $this->table->getImageSize($distination);
            $this->assertEquals($expected, $result, $message);

        }

        // コピーした画像を削除
        @unlink($distination);

    }

    public function resizeImageDataProvider()
    {
        return [
            [false, false, false, null, '画像ファイルをコピーできません'],
            [100, 100, false, ['width' => 98, 'height' => 13], '画像ファイルを正しくリサイズしてコピーできません'],
            [100, 100, true, ['width' => 100, 'height' => 100], '画像ファイルをサムネイルとしてコピーできません'],
        ];
    }

    /**
     * 画像のサイズを取得
     *
     * @param string $imgName 画像の名前
     * @param mixed $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider getImageSizeDataProvider
     */
    public function testGetImageSize($imgName, $expected, $message = null)
    {
        $imgPath = ROOT . '/plugins/bc-admin-third/webroot/img/' . $imgName;
        $result = $this->table->getImageSize($imgPath);
        $this->assertEquals($expected, $result, '画像のサイズを正しく取得できません');
    }

    public function getImageSizeDataProvider()
    {
        return [
            ['baser.power.gif', ['width' => 98, 'height' => 13], '画像のサイズを正しく取得できません'],
        ];
    }

    /**
     * Before delete
     * 画像ファイルの削除を行う
     * 削除に失敗してもデータの削除は行う
     *
     * @return void
     */
    public function testBeforeDelete()
    {
        $filePath = $this->savePath . 'test.png';
        touch($filePath);
        $trash = $this->ContentService->getIndex(['withTrash' => true, 'deleted_date!' => null])->first();
        $trash->eyecatch = 'test.png';
        $this->table->dispatchEvent('Model.beforeDelete', ['entity' => $trash, 'options' => new ArrayObject()]);
        $this->assertFileNotExists($filePath);
        @unlink($filePath);
    }

    /**
     * 画像ファイル群を削除する
     *
     * @param Model $Model
     * @return boolean
     */
    public function testCleanupFiles()
    {
        $fileName = 'dummy';
        $targetPath = $this->savePath . $fileName . '.' . $this->eyecatchField['ext'];
        // ダミーのファイルを生成
        touch($targetPath);
        $uploaded = [
            'name' => $fileName . '.' . $this->eyecatchField['ext'],
            'tmp_name' => TMP . $fileName . '.' . $this->eyecatchField['ext'],
        ];
        $this->BcUploadBehavior->settings[$this->table->getAlias()]['fields']['eyecatch'] = $this->eyecatchField;
        $this->BcUploadBehavior->cleanupFiles(['eyecatch' => $uploaded], $this->eyecatchField['name']);
        $this->assertFileNotExists($targetPath);
        @unlink($targetPath);
    }

    /**
     * ファイルを削除する
     *
     * @param string $prefix 対象のファイルの接頭辞
     * @param string $suffix 対象のファイルの接尾辞
     * @param array $imagecopy
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider delFileDataProvider
     */
    public function testDelFile($prefix, $suffix, $imagecopy, $message)
    {
        // TODO 2020/07/08 ryuring PHP7.4 で、gd が標準インストールされないため、テストがエラーとなるためスキップ
        $tmpPath = TMP;
        $fileName = 'dummy';
        $this->eyecatchField['name'] = $fileName;
        $this->eyecatchField['imagecopy'] = $imagecopy;
        $this->eyecatchField['prefix'] = $prefix;
        $this->eyecatchField['suffix'] = $suffix;

        $targetPath = $this->savePath . $this->eyecatchField['prefix'] . $fileName . $this->eyecatchField['suffix'] . '.' . $this->eyecatchField['ext'];

        // ダミーのファイルを生成
        touch($targetPath);

        // copyのダミーファイルを生成
        if (is_array($this->eyecatchField['imagecopy'])) {
            copy(ROOT . '/plugins/bc-admin-third/webroot/img/baser.power.gif', $tmpPath . $fileName . '.' . $this->eyecatchField['ext']);
            $uploaded = [
                'dummy' => [
                    'name' => $fileName . '.' . $this->eyecatchField['ext'],
                    'tmp_name' => $tmpPath . $fileName . '.' . $this->eyecatchField['ext'],
                ]
            ];
            $this->BcUploadBehavior->setUploadedFile($uploaded);
            foreach($this->eyecatchField['imagecopy'] as $copy) {
                $copy['name'] = $fileName;
                $copy['ext'] = $this->eyecatchField['ext'];
                $this->BcUploadBehavior->copyImage($this->table->getAlias(), $copy);
            }
        }
        // 削除を実行
        $this->table->delFile($fileName, $this->eyecatchField);
        $this->assertFileNotExists($targetPath, $message);
        @unlink($targetPath);
    }

    public function delFileDataProvider()
    {
        return [
            [null, null, null, 'ファイルを削除できません'],
            ['pre', null, null, '接頭辞を指定した場合のファイル削除ができません'],
            [null, 'suf', null, '接尾辞を指定した場合のファイル削除ができません'],
            ['pre', 'suf', null, '接頭辞と接尾辞を指定した場合のファイル削除ができません'],
            [null, null, [
                'thumb' => ['suffix' => 'thumb', 'width' => '150', 'height' => '150']
            ], 'ファイルを複数削除できません'],
            [null, null, [
                'thumb' => ['suffix' => 'thumb', 'width' => '150', 'height' => '150'],
                'thumb_mobile' => ['suffix' => 'thumb_mobile', 'width' => '100', 'height' => '100'],
            ], 'ファイルを複数削除できません'],
        ];
    }

    /**
     * ファイル名をフィールド値ベースのファイル名に変更する
     */
    public function testRenameToBasenameField()
    {
        touch($this->savePath . 'test.png');
        $entity = new Entity();
        $entity->id = 1;
        $setting = $this->BcUploadBehavior->settings[$this->table->getAlias()]['fields']['eyecatch'];
        $newFileName = $this->BcUploadBehavior->renameToBasenameField($entity, $this->uploadedData, $setting, false);
        $this->assertEquals("00000001_eyecatch.png", $newFileName);
        $this->assertFileExists($this->savePath . DS . $newFileName);
        @unlink($this->savePath . 'test.png');
        @unlink($this->savePath . DS . $newFileName);
    }
    /**
     * ファイル名をフィールド値ベースのファイル名に変更する
     * testRenameToBasenameFields
     * @param string $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider renameToFieldBasenameDataProvider
     */
    public function testRenameToFieldBasename($oldName, $ext, $copy, $imagecopy, $message = null)
    {
        // 初期化
        $entity = $this->table->get(1);
        $oldName = $oldName . '.' . $ext;
        $entity->eyecatch = $oldName;
        $this->BcUploadBehavior->setUploadedFile(['eyecatch' => ['name' => $oldName]]);
        $setting = $this->BcUploadBehavior->settings[$this->table->getAlias()]['fields']['eyecatch'];

        if ($imagecopy) {
            $this->BcUploadBehavior->settings[$this->table->getAlias()]['fields']['eyecatch']['imagecopy'] = $imagecopy;
        }

        // パス情報
        $oldPath = $this->savePath . $oldName;
        $newPath = $this->savePath . "00000001_eyecatch" . '.' . $ext;

        // ダミーファイルの生成
        touch($oldPath);

        if ($imagecopy) {
            foreach($imagecopy as $copysetting) {
                $oldCopynames = $this->BcUploadBehavior->getFileName($copysetting, $oldName);
                touch($this->savePath . $oldCopynames);
            }
        }


        // テスト実行
        $result = $this->BcUploadBehavior->renameToBasenameFields($entity, $copy);
        $this->assertFileExists($newPath, $message);


        // 生成されたファイルを削除
        @unlink($newPath);


        // ファイルを複数生成する場合テスト
        if ($copy) {
            $this->assertFileExists($oldPath, $message);
            @unlink($oldPath);
        }

        if ($imagecopy) {
            $newName = $this->BcUploadBehavior->getFileName($setting['imageresize'], "00000001_eyecatch" . '.' . $ext);

            foreach($imagecopy as $copysetting) {
                $newCopyname = $this->BcUploadBehavior->getFileName($copysetting, $newName);
                $this->assertFileExists($this->savePath . $newCopyname, $message);
                @unlink($this->savePath . $newCopyname);
            }
        }

    }

    public function renameToFieldBasenameDataProvider()
    {
        return [
            ['oldName', 'gif', false, false, 'ファイル名をフィールド値ベースのファイル名に変更できません'],
            ['oldName', 'gif', true, false, 'ファイル名をフィールド値ベースのファイル名に変更してコピーができません'],
            ['oldName', 'gif', false, [
                ['prefix' => 'pre-', 'suffix' => '-suf'],
                ['prefix' => 'pre2-', 'suffix' => '-suf2'],
            ], '複数のファイルをフィールド値ベースのファイル名に変更できません'],
        ];
    }

    /**
     * 全フィールドのファイル名をフィールド値ベースのファイル名に変更する
     */
    public function testRenameToBasenameFields()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * フィールドベースのファイル名を取得する
     *
     * @param string $namefield namefieldパラメータの値
     * @param string $basename basenameパラメータの値
     * @param string $basename $Model->idの値
     * @param array $setting 設定する値
     * @param string $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider getFieldBasenameDataProvider
     */
    public function testGetFieldBasename($namefield, $basename, $id, $setting, $expected, $message = null)
    {
        // 初期化
        $entity = new Entity();
        if ($namefield) {
            $entity->{$namefield} = $basename;
        }
        $entity->id = $id;

        $issetSubdirDataFormat = isset($setting['subdirDateFormat']);
        if ($issetSubdirDataFormat) {
            $this->BcUploadBehavior->settings = [];
            $this->BcUploadBehavior->settings[$this->table->getAlias()]['subdirDateFormat'] = $setting['subdirDateFormat'];
        }

        $setting['namefield'] = $namefield;


        // テスト実行
        $result = $this->BcUploadBehavior->getFieldBasename($entity, $setting, 'ext');


        if (!$issetSubdirDataFormat) {
            $this->assertEquals($expected, $result, $message);

        } else {
            $subDir = date($setting['subdirDateFormat']) . '/';

            $expected = $subDir . $expected;

            $this->assertEquals($expected, $result, $message);

            @$this->removeDir($this->savePath . $subDir);
        }

    }

    public function getFieldBasenameDataProvider()
    {
        return [
            ['namefield', 'basename', 'modelId', ['name' => 'name'],
                'basename_name.ext', 'フィールドベースのファイル名を正しく取得できません'],
            [null, 'basename', 'modelId', [],
                false, 'namefieldを指定しなかった場合にfalseが返ってきません'],
            ['id', null, 'modelId', ['name' => 'name'],
                'modelId_name.ext', 'namefieldがidかつbasenameが指定されていない場合のファイル名を正しく取得できません'],
            ['id', null, null, [],
                false, 'namefieldがidかつbasenameとModelIdが指定されていない場合にfalseが返ってきません'],
            ['namefield', null, 'modelId', [],
                false, 'basenameが指定されていない場合にfalseが返ってきません'],
            ['namefield', 'basename', 'modelId', ['name' => 'name', 'nameformat' => 'ho-%s-ge'],
                'ho-basename-ge_name.ext', 'formatを指定した場合に正しくファイル名を取得できません'],
            ['namefield', 'basename', 'modelId', ['name' => 'name', 'nameadd' => false],
                'basename.ext', 'formatを指定した場合に正しくファイル名を取得できません'],
            ['namefield', 'basename', 'modelId', ['name' => 'name', 'subdirDateFormat' => 'Y-m'],
                'basename_name.ext', 'formatを指定した場合に正しくファイル名を取得できません'],
        ];
    }


    /**
     * ベースファイル名からプレフィックス付のファイル名を取得する
     *
     * @param string $prefix 対象のファイルの接頭辞
     * @param string $suffix 対象のファイルの接尾辞
     * @param string $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider getFileNameDataProvider
     */
    public function testGetFileName($prefix, $suffix, $expected, $message = null)
    {
        $setting = [
            'prefix' => $prefix,
            'suffix' => $suffix,
        ];
        $fileName = 'hoge.gif';

        $result = $this->BcUploadBehavior->getFileName($setting, $fileName);
        $this->assertEquals($expected, $result, $message);
    }

    public function getFileNameDataProvider()
    {
        return [
            [null, null, 'hoge.gif', 'ベースファイル名からファイル名を取得できません'],
            ['pre-', null, 'pre-hoge.gif', 'ベースファイル名から接頭辞付きファイル名を取得できません'],
            [null, '-suf', 'hoge-suf.gif', 'ベースファイル名から接尾辞付きファイル名を取得できません'],
            ['pre-', '-suf', 'pre-hoge-suf.gif', 'ベースファイル名からプレフィックス付のファイル名を取得できません'],
        ];
    }

    /**
     * ファイル名からベースファイル名を取得する
     *
     * @param string $prefix 対象のファイルの接頭辞
     * @param string $suffix 対象のファイルの接尾辞
     * @param string $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider getBasenameDataProvider
     */
    public function testGetBasename($prefix, $suffix, $expected, $message = null)
    {
        $setting = [
            'prefix' => $prefix,
            'suffix' => $suffix,
        ];
        $fileName = 'pre-hoge-suf.gif';

        $result = $this->BcUploadBehavior->getBasename($setting, $fileName);
        $this->assertEquals($expected, $result, $message);
    }

    public function getBasenameDataProvider()
    {
        return [
            [null, null, 'pre-hoge-suf', 'ファイル名からベースファイル名を正しく取得できません'],
            ['pre-', null, 'hoge-suf', 'ファイル名からベースファイル名を正しく取得できません'],
            [null, '-suf', 'pre-hoge', 'ファイル名からベースファイル名を正しく取得できません'],
            ['pre-', '-suf', 'hoge', 'ファイル名からベースファイル名を正しく取得できません'],
        ];
    }

    /**
     * 一意のファイル名を取得する
     *
     * @param string $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider getUniqueFileNameDataProvider
     */
    public function testGetUniqueFileName($fieldName, $fileName, $expected, $message = null)
    {
        // eyecatchでtemplate1.gifをすでに持つデータとして更新し、テスト
        // BcUpload-beforeSaveを回避するため新規データ挿入時にremoveBehavior('BcUpload')を実行
        if ($fileName === 'template1.gif') {
            $table = $this->table->removeBehavior('BcUpload');
            $content = $table->find()->last();
            $this->ContentService->update($content, ['eyecatch' => 'template1.gif']);
        }
        $setting = ['name' => $fieldName, 'ext' => 'gif'];
        touch($this->savePath . 'template1.gif');
        $result = $this->BcUploadBehavior->getUniqueFileName($setting, $fileName);
        $this->assertEquals($expected, $result, $message);
        @unlink($this->savePath . 'template1.gif');
    }

    public function getUniqueFileNameDataProvider()
    {
        return [
            ['eyecatch', 'hoge.gif', 'hoge.gif', '一意のファイル名を正しく取得できません'],
            ['eyecatch', 'template.gif', 'template.gif', '一意のファイル名を正しく取得できません'],
            ['eyecatch', 'template1.gif', 'template1__2.gif', '一意のファイル名を正しく取得できません'],
        ];
    }

    /**
     * testIsFileExists
     * 重複ファイルがあるか確認する
     * @return void
     */
    public function testIsFileExists()
    {
        $fileName = 'test.txt';
        $this->assertFalse($this->BcUploadBehavior->isFileExists($fileName));
        $basePath = WWW_ROOT . 'files' . DS;
        $duplicate = "test/";
        // existsCheckDirsがある場合
        try {
            mkdir($basePath . $duplicate, 0777, false);
            touch($basePath . $duplicate . $fileName);
            $this->BcUploadBehavior->existsCheckDirs[$this->table->getAlias()] = [$duplicate];
            $this->assertTrue($this->BcUploadBehavior->isFileExists($fileName));
        } catch (\Exception $e) {
            $error = $e;
        } finally {
            if (file_exists($basePath . $duplicate . $fileName)) {
                unlink($basePath . $duplicate . $fileName);
                rmdir($basePath . $duplicate);
            }
            $this->BcUploadBehavior->existsCheckDirs[$this->table->getAlias()] = [];
        }
        // SavePathがある場合
        try {
            touch(WWW_ROOT . 'files/contents/' . $fileName);
            $this->savePath = WWW_ROOT . 'files/contents/';
            $this->assertTrue($this->BcUploadBehavior->isFileExists($fileName));
        } catch (\Exception $e) {
            $error = $e;
        } finally {
            if (file_exists(WWW_ROOT . 'files/contents/' . $fileName)) {
                unlink(WWW_ROOT . 'files/contents/' . $fileName);
            }
            $reflection = new ReflectionClass($this->BcUploadBehavior);
            $property = $reflection->getProperty('savePath');
            $property->setAccessible(true);
            $property->setValue($this->BcUploadBehavior, []);
        }
    }

    /**
     * 既に存在するデータのファイルを削除する
     */
    public function testDeleteExistingFiles()
    {
        // $uploadedされていなければ、returnで終了
        $this->BcUploadBehavior->setUploadedFile([]);
        $this->assertNull($this->BcUploadBehavior->deleteExistingFiles());
        // アップロードされていれば削除処理
        $fileName = 'dummy';
        $targetPath = $this->savePath . $fileName . '.' . $this->eyecatchField['ext'];

        // ダミーのファイルを生成
        touch($targetPath);
        $uploaded = [
            'name' => $fileName . '.' . $this->eyecatchField['ext'],
            'tmp_name' => TMP . $fileName . '.' . $this->eyecatchField['ext'],
        ];
        $this->BcUploadBehavior->setUploadedFile(['eyecatch' => $uploaded]);
        $this->BcUploadBehavior->settings[$this->table->getAlias()]['fields']['eyecatch'] = $this->eyecatchField;
        $this->BcUploadBehavior->deleteExistingFiles();
        $this->assertFileNotExists($targetPath);
        @unlink($targetPath);
    }

    /**
     * 画像をコピーする
     * @param array $size 画像サイズ
     * @param bool $copied 画像がコピーされるかどうか
     * @return void
     * @dataProvider copyImagesDataProvider
     */
    public function testCopyImages($size, $copied): void
    {
        $this->eyecatchField['imagecopy'] = ['thumb' => $size];
        $this->savePath = ROOT . '/plugins/bc-admin-third/webroot/img/';
        $this->BcUploadBehavior->savePath[$this->table->getAlias()] = $this->savePath;
        $fileName = 'baser.power';
        $uploadedFile = [
            'eyecatch' => [
                'name' => $fileName . '_copy' . '.' . $this->eyecatchField['ext'],
                'tmp_name' => $this->savePath . $fileName . '.' . $this->eyecatchField['ext'],
            ]
        ];
        $this->BcUploadBehavior->setUploadedFile($uploadedFile);
        // コピー先ファイルのパス
        $targetPath = $this->savePath . $fileName . '_copy' . '.' . $this->eyecatchField['ext'];
        // コピー実行
        $result = $this->BcUploadBehavior->copyImages($this->eyecatchField, $fileName . '.' . $this->eyecatchField['ext']);
        $this->assertTrue($result);
        if ($copied) {
            $this->assertFileExists($targetPath);
            // コピーしたファイルを削除
            @unlink($targetPath);
        } else {
            $this->assertFileNotExists($targetPath);
        }
    }

    public function copyImagesDataProvider()
    {
        return [
            // コピー画像が元画像より大きい場合はスキップして作成しない
            [['width' => 300, 'height' => 300], false],
            // コピーが生成される場合
            [['width' => 20, 'height' => 20], true],
        ];
    }

    /**
     * testSetAndGetUploadedFile
     *
     * @return void
     */
    public function testSetAndGetUploadedFile()
    {
        $this->BcUploadBehavior->setUploadedFile($this->uploadedData);
        $this->assertEquals($this->uploadedData, $this->BcUploadBehavior->getUploadedFile());
    }
}
