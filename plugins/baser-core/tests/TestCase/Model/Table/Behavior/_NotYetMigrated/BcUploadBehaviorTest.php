<?php
// TODO : コード確認要
return;
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.Model.Behavior
 * @since           baserCMS v 3.0.6
 * @license         https://basercms.net/license/index.html
 */

/**
 * Class BcUploadBehaviorTest
 *
 * @package Baser.Test.Case.Model
 * @property BcUploadBehavior $BcUploadBehavior
 * @property EditorTemplate $EditorTemplate
 */
class BcUploadBehaviorTest extends BaserTestCase
{

    public $fixtures = [
        'baser.Default.EditorTemplate',
        'baser.Default.Page',
    ];

    /**
     * setUp
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->EditorTemplate = ClassRegistry::init('EditorTemplate');
        $this->BcUploadBehavior = ClassRegistry::init('BcUploadBehavior');
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown()
    {
        session_unset();
        unset($this->EditorTemplate);
        unset($this->BcUploadBehavior);
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
     * testSaveFileの初期化を行う
     */
    public function initTestSaveFiles($id = 1, $data = [])
    {

        $fieldName = 'image';
        $tmp_name = 'tmp_file.gif';
        $data = array_merge(
            [
                'name' => 'basename.gif',
                'type' => 'basercms',
                'image' => ['basercms'],
                'size' => 5,
            ], $data
        );

        // パス
        $savePath = $this->BcUploadBehavior->savePath['EditorTemplate'];
        $imgPath = ROOT . '/lib/Baser/webroot/img/';
        $tmpSourcePath = $imgPath . 'baser.power.gif';
        $tmpPath = $savePath . $tmp_name;

        // 初期化
        $this->EditorTemplate->id = $id;

        $data['tmp_name'] = $tmpPath;
        $this->EditorTemplate->data['EditorTemplate'][$fieldName] = $data;

        // ダミーファイルを生成
        copy($tmpSourcePath, $tmpPath);
        $this->EditorTemplate->setupRequestData();
    }


    /**
     * testSaveFileで生成されたダミーファイルを削除する
     */
    public function deleteDummyOnTestSaveFiles()
    {
        $tmp_name = 'tmp_file.gif';

        $savePath = $this->BcUploadBehavior->savePath['EditorTemplate'];
        $tmpPath = $savePath . $tmp_name;
        @unlink($tmpPath);
    }

    /**
     * セットアップ
     */
    public function testSetup()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Before Validate
     */
    public function testBeforeValidate()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Before save
     */
    public function testBeforeSave()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * リクエストされたデータを処理しやすいようにセットアップする
     */
    public function testSetupRequestData()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->EditorTemplate->data = [
            'EditorTemplate' => [
                'name' => '',
                'link' => '',
            ]
        ];
        $this->EditorTemplate->save([
            'EditorTemplate' => [
                'name' => 'hoge',
                'link' => 'hoge',
            ]
        ]);
        $data = $this->EditorTemplate->find('all');

    }

    /**
     * 一時ファイルとして保存する
     *
     * @param Model $Model
     * @param array $data
     * @param string $tmpId
     */
    public function testSaveTmpFiles()
    {
        $this->initTestSaveFiles();
        $data = $this->EditorTemplate->saveTmpFiles($this->EditorTemplate->data, 1);
        $tmpId = $this->BcUploadBehavior->tmpId;
        $this->assertEquals('1.gif', $data['EditorTemplate']['image']['session_key'], 'saveTmpFiles()の返り値が正しくありません');
        $this->assertEquals(1, $tmpId, 'tmpIdが正しく設定されていません');
        $this->deleteDummyOnTestSaveFiles();
    }

    /**
     * saveFilesのテスト
     * ファイルを保存する
     *
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider saveFilesCanSaveDataProvider
     */
    public function testSaveFilesCanSave($tmpId, $message)
    {

        // TODO 2020/07/08 ryuring PHP7.4 で、gd が標準インストールされないため、テストがエラーとなるためスキップ
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        return;

        $this->initTestSaveFiles();

        // tmpIdを設定
        $this->BcUploadBehavior->tmpId = $tmpId;

        // パス情報
        $savePath = $this->BcUploadBehavior->savePath['EditorTemplate'];
        $targetPath = $savePath . 'basename.gif';

        // 保存を実行
        $data = $this->EditorTemplate->saveFiles($this->EditorTemplate->data);

        if (!$tmpId) {
            $this->assertFileExists($targetPath, 'saveFiles()でファイルを保存できません');
            $this->assertEquals('basename.gif', $data['EditorTemplate']['image'], $message);

        } else {
            $this->assertFileNotExists($targetPath, 'saveFiles()でファイルを正しく保存できません');
            $this->assertEquals('1.gif', $data['EditorTemplate']['image']['session_key'], $message);
        }

        // 生成されたファイルを削除
        @unlink($targetPath);
        $this->deleteDummyOnTestSaveFiles();

    }

    public function saveFilesCanSaveDataProvider()
    {
        return [
            [null, 'saveFiles()でファイルを保存できません'],
            [1, 'saveFiles()でファイルを保存できません'],
        ];
    }

    /**
     * deleteFiles のテスト
     * ファイルを削除する
     *
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider deleteFilesDataProvider
     */
    public function testDeleteFiles($id, $message)
    {

        $this->initTestSaveFiles($id);

        // パス情報
        $savePath = $this->BcUploadBehavior->savePath['EditorTemplate'];

        // 初期化
        $fieldName = 'image';
        $this->EditorTemplate->data['EditorTemplate'][$fieldName . '_delete'] = true;

        $templatePath = $savePath . 'template' . $id . '.gif';
        touch($templatePath);

        // 削除を実行
        $this->EditorTemplate->deleteFiles($this->EditorTemplate->data);

        $this->assertFileNotExists($templatePath, $message);

        // 生成されたファイルを削除
        $this->deleteDummyOnTestSaveFiles();

    }

    public function deleteFilesDataProvider()
    {
        return [
            [1, 'deleteFiles()でファイルを削除できません'],
            [2, 'deleteFiles()でファイルを削除できません'],
        ];
    }

    /**
     * 削除対象かチェックしながらファイルを削除する
     */
    public function testDeleteFileWhileChecking()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * ファイル群を保存する
     */
    public function testSaveFiles()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 保存対象かチェックしながらファイルを保存する
     */
    public function testSaveFileWhileChecking()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
        $savePath = $this->BcUploadBehavior->savePath['EditorTemplate'];
        $targetPath = $savePath . 'copy.gif';

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
        $savePath = $this->BcUploadBehavior->savePath['EditorTemplate'];
        $targetPath = $savePath . 'basename.gif';

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
        $savePath = $this->BcUploadBehavior->savePath['EditorTemplate'];
        $tmpPath = $savePath . $tmp_name;

        // 初期化
        $field = [
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
        $this->EditorTemplate->saveFile($field);

        //—————————————————————————
        // 本題
        //—————————————————————————

        // パス情報
        $targetName = $tmpId . '_' . $fieldName . '_' . $ext;
        $targetPath = $savePath . $targetName;

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
     * ファイルを保存する
     *
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider saveFileDataProvider
     */
    public function testSaveFile($prefix, $suffix, $namefield, $tmpId, $message = null)
    {

        $fieldName = 'fieldName';
        $tmp_name = 'tmp_file';
        $basename = 'basename';
        $ext = 'png';

        // パス情報
        $savePath = $this->BcUploadBehavior->savePath['EditorTemplate'];
        $tmpPath = $savePath . $tmp_name;

        if (!$tmpId) {
            $targetPath = $savePath . $prefix . $basename . $suffix . '.' . $ext;
        } else {
            $targetPath = $tmpId . '_' . $fieldName . '.' . $ext;
        }

        // 初期化
        $field = [
            'name' => $fieldName,
            'ext' => $ext,
            'prefix' => $prefix,
            'suffix' => $suffix,
            'namefield' => $namefield,
        ];

        $this->EditorTemplate->data['EditorTemplate'][$fieldName] = [
            'name' => $basename,
            'tmp_name' => $tmpPath,
            'type' => 'basercms',
        ];

        $this->BcUploadBehavior->tmpId = $tmpId;

        // ダミーファイルの作成
        touch($tmpPath);

        // ファイル保存を実行
        $result = $this->EditorTemplate->saveFile($field);

        if (!$tmpId) {
            $this->assertFileExists($targetPath, $message);

        } else {
            $this->assertEquals($targetPath, $result, $message);

            // セッションをチェック
            $sessionField = $tmpId . '_' . $fieldName . '_' . $ext;
            $expected[$sessionField] = array_merge($field, ['type' => 'basercms', 'data' => '']);
            $resultSession = $this->BcUploadBehavior->Session->read('Upload');
            $this->assertEquals($expected, $resultSession, $message);

        }


        // 生成されたファイルを削除
        @unlink($tmpPath);
        @unlink($targetPath);

    }

    public function saveFileDataProvider()
    {
        return [
            ['', '', null, null, 'ファイルを保存できません'],
            ['pre-', '-suf', null, null, 'プレフィックス付きのファイルを保存できません'],
            ['', '', 'hoge', 1, 'tmpIdとnamefieldに指定がある場合にファイルを保存できません'],
            ['', '', null, 1, 'tmpIdに指定がある場合にファイルを保存できません'],
        ];
    }

    /**
     * 保存用ファイル名を取得する
     */
    public function testGetSaveFileName()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 画像をExif情報を元に正しい確度に回転する
     */
    public function testRotateImage()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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

        // TODO 2020/07/08 ryuring PHP7.4 で、gd が標準インストールされないため、テストがエラーとなるためスキップ
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        return;

        $imgPath = ROOT . '/lib/Baser/webroot/img/';
        $savePath = $this->BcUploadBehavior->savePath['EditorTemplate'];
        $fileName = 'baser.power';

        $field = [
            'name' => 'image',
            'prefix' => $prefix,
            'suffix' => $suffix,
            'ext' => 'gif',
            'width' => 100,
            'height' => 100,
        ];

        $this->EditorTemplate->data = [
            'EditorTemplate' => [
                'image' => [
                    'name' => $fileName . '_copy' . '.' . $field['ext'],
                    'tmp_name' => $imgPath . $fileName . '.' . $field['ext'],
                ]
            ]
        ];

        // コピー先ファイルのパス
        $targetPath = $savePath . $field['prefix'] . $fileName . '_copy' . $field['suffix'] . '.' . $field['ext'];

        // コピー実行
        $this->EditorTemplate->copyImage($field);
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

        // TODO 2020/07/08 ryuring PHP7.4 で、gd が標準インストールされないため、テストがエラーとなるためスキップ
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        return;

        $imgPath = ROOT . '/lib/Baser/webroot/img/';
        $source = $imgPath . 'baser.power.gif';
        $distination = $imgPath . 'baser.power_copy.gif';

        $savePath = $this->BcUploadBehavior->savePath['EditorTemplate'];


        // コピー実行
        $this->BcUploadBehavior->resizeImage($source, $distination, $width, $height, $thumb);

        if (!$width && !$height) {
            $this->assertFileExists($distination, $message);

        } else {
            $result = $this->BcUploadBehavior->getImageSize($distination);
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
        $imgPath = ROOT . '/lib/Baser/webroot/img/' . $imgName;

        $result = $this->BcUploadBehavior->getImageSize($imgPath);
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
     * @param Model $Model
     * @return void
     */
    public function testBeforeDelete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

    }

    /**
     * 画像ファイル群を削除する
     *
     * @param Model $Model
     * @return boolean
     */
    public function testDelFiles()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        return;

        $savePath = $this->BcUploadBehavior->savePath['EditorTemplate'];
        $tmpPath = TMP;
        $fileName = 'dummy';
        $field = [
            'ext' => 'gif',
            'prefix' => $prefix,
            'suffix' => $suffix,
            'imagecopy' => $imagecopy,
            'name' => $fileName,
            'width' => 600,
            'height' => 600
        ];
        $targetPath = $savePath . $field['prefix'] . $fileName . $field['suffix'] . '.' . $field['ext'];

        // ダミーのファイルを生成
        touch($targetPath);

        // copyのダミーファイルを生成
        if (is_array($field['imagecopy'])) {
            copy(ROOT . '/lib/Baser/webroot/img/baser.power.gif', $tmpPath . $fileName . '.' . $field['ext']);
            $this->EditorTemplate->data['EditorTemplate'][$fileName] = [
                'name' => $fileName . '.' . $field['ext'],
                'tmp_name' => $tmpPath . $fileName . '.' . $field['ext'],
            ];
            foreach($field['imagecopy'] as $copy) {
                $copy['name'] = $fileName;
                $copy['ext'] = $field['ext'];
                $this->EditorTemplate->copyImage($copy);
            }

        }

        // 削除を実行
        $this->EditorTemplate->delFile($fileName, $field);

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
     *
     * @param string $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider renameToFieldBasenameDataProvider
     */
    public function testRenameToFieldBasename($oldName, $newName, $ext, $copy, $imagecopy, $message = null)
    {

        // 初期化
        $this->EditorTemplate->id = $newName;
        $oldName = $oldName . '.' . $ext;
        $this->EditorTemplate->data['EditorTemplate'] = ['image' => $oldName];
        $setting = $this->BcUploadBehavior->settings['EditorTemplate']['fields']['image'];

        if ($imagecopy) {
            $this->BcUploadBehavior->settings['EditorTemplate']['fields']['image']['imagecopy'] = $imagecopy;
        }

        // パス情報
        $savePath = $this->BcUploadBehavior->savePath['EditorTemplate'];
        $oldPath = $savePath . $oldName;
        $newPath = $savePath . $setting['imageresize']['prefix'] . $newName . '.' . $ext;

        // ダミーファイルの生成
        touch($oldPath);

        if ($imagecopy) {
            foreach($imagecopy as $copysetting) {
                $oldCopynames = $this->EditorTemplate->getFileName($copysetting, $oldName);
                touch($savePath . $oldCopynames);
            }
        }


        // テスト実行
        $this->EditorTemplate->renameToBasenameFields($copy);
        $this->assertFileExists($newPath, $message);


        // 生成されたファイルを削除
        @unlink($newPath);


        // ファイルを複数生成する場合テスト
        if ($copy) {
            $this->assertFileExists($oldPath, $message);
            @unlink($oldPath);
        }

        if ($imagecopy) {
            $newName = $this->EditorTemplate->getFileName($setting['imageresize'], $newName . '.' . $ext);

            foreach($imagecopy as $copysetting) {
                $newCopyname = $this->EditorTemplate->getFileName($copysetting, $newName);
                $this->assertFileExists($savePath . $newCopyname, $message);
                @unlink($savePath . $newCopyname);
            }
        }

    }

    public function renameToFieldBasenameDataProvider()
    {
        return [
            ['oldName', 'newName', 'gif', false, false, 'ファイル名をフィールド値ベースのファイル名に変更できません'],
            ['oldName', 'newName', 'gif', true, false, 'ファイル名をフィールド値ベースのファイル名に変更してコピーができません'],
            ['oldName', 'newName', 'gif', false, [
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
     * ファイル名をフィールド値ベースのファイル名に変更する
     */
    public function testRenameToBasenameField()
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
    public function testGetFieldBasename($namefield, $basename, $modelId, $setting, $expected, $message = null)
    {

        // 初期化
        $this->EditorTemplate->data['EditorTemplate'][$namefield] = $basename;
        $this->EditorTemplate->id = $modelId;

        $issetSubdirDataFormat = isset($setting['subdirDateFormat']);
        if ($issetSubdirDataFormat) {
            $this->BcUploadBehavior->settings = [];
            $this->BcUploadBehavior->settings['EditorTemplate']['subdirDateFormat'] = $setting['subdirDateFormat'];
        }

        $setting['namefield'] = $namefield;


        // テスト実行
        $result = $this->EditorTemplate->getFieldBasename($setting, 'ext');


        if (!$issetSubdirDataFormat) {
            $this->assertEquals($expected, $result, $message);

        } else {
            $savePath = $this->BcUploadBehavior->savePath['EditorTemplate'];
            $subDir = date($setting['subdirDateFormat']) . '/';

            $expected = $subDir . $expected;

            $this->assertEquals($expected, $result, $message);

            @$this->removeDir($savePath . $subDir);
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

        $result = $this->EditorTemplate->getFileName($setting, $fileName);
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

        $result = $this->EditorTemplate->getBasename($setting, $fileName);
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
        $setting = ['ext' => 'gif'];
        $savePath = $this->BcUploadBehavior->savePath['EditorTemplate'];
        touch($savePath . 'template1.gif');

        $result = $this->EditorTemplate->getUniqueFileName($fieldName, $fileName, $setting);
        $this->assertEquals($expected, $result, $message);

        @unlink($savePath . 'template1.gif');
    }

    public function getUniqueFileNameDataProvider()
    {
        return [
            ['image', 'hoge.gif', 'hoge.gif', '一意のファイル名を正しく取得できません'],
            ['image', 'template.gif', 'template.gif', '一意のファイル名を正しく取得できません'],
            ['image', 'template1.gif', 'template1__2.gif', '一意のファイル名を正しく取得できません'],
        ];
    }

    /**
     * 保存先のフォルダを取得する
     */
    public function testGetSaveDir()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 既に存在するデータのファイルを削除する
     */
    public function testDeleteExistingFiles()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 画像をコピーする
     */
    public function testCopyImages()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
