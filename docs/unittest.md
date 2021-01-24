# ユニットテスト

baserCMSでは、[CakePHPのテストの仕組み](https://book.cakephp.org/4/ja/development/testing.html) を使用してユニットテストを作成します。

## テストの準備

事前に Composer で、PHPUnit などをインストールしておく必要があります。

```shell
composeer install
```

## テストの作成

### テストの配置先

`/plugins/baser-core/tests/[Type]` ディレクトリ

`[type]` は、 `TestCase` か、`Fixture` となります。

### テストのファイル名

ファイル名の最後を `Test.php` とします。

### クラス名

クラス名もファイル名と同じにします。（もちろん、拡張子を除く）

### クラスの継承

`BaserCore\TestSuite\BcTestCase` クラスを継承します。

### メソッド名

テストを含むメソッドは、先頭を `test` とします。

```php
// （例）BcTestHelper の hoge メソッド
// /plugins/baser-core/tests/TestCase/View/Helper/BcTestHelperTest.php

class BcTestHelperTest extends BaserCore\TestSuite\BcTestCase {
    public function testHoge() {

    }
}
```

### プライベートメソッドに対するテスト

プライベートメソッドに対するテストはパブリックメソッド経由で行うものとします。

## テストの実行

テストを実行するには次のコマンドを実行します。

```shell
vendor/bin/phpunit
```

### 特定のテストだけを実行

```shell
vendor/bin/phpunit plugins/baser-core/tests/TestCase/Model/Table/ArticlesTableTest
```

### 特定のテストメソッドだけを実行

```shell
vendor/bin/phpunit --filter testSave plugins/baser-core/tests/TestCase/Model/Table/ArticlesTableTest
```

## 新しいプラグインのテストを追加する

`/phpunit.xml.dist` ファイルに、新しいプラグインの `<testsuite>` セクションを追加することでプラグインのテストを一緒に実行することができます。

## フィクスチャを利用する

`fixtures` プロパティを定義することでフィクスチャを利用することができます。

```php
class UsersTableTest extends TestCase
{
    protected $fixtures = ['plugin.BaserCore.Users'];
}
```

### テストメソッドの中でフィクスチャを呼び出する

```php
$this->loadFixtures('plugin.BaserCore.Users', 'etc...');
```
