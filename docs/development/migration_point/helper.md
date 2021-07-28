# ヘルパーにおける注意点

## 継承先の変更

`AppHelper` はなくなりました。  
継承先を、一旦 `AppHelper` から `Helper` に変更します。

## トレイトの利用

`BcHelperTrait` を利用します。

```php
class ClassName extends Cake\View\Helper {

    /**
     * Trait
     */
    use BcHelperTrait;
}    
```

## FormHelperの変更点
### $this->Form->hidden変更点

hiddenメソッドではcakephp4系からIDが付与されなくなった
なので代わりに、Form->control('name', ['type' => 'hidden']);を使うようにする

```php
例: cakephp2

echo $this->Form->hidden('id');
出力結果:

<input name="data[User][id]" id="UserId" type="hidden" />

例: cakephp4

echo $this->Form->hidden('id');
出力結果: idがなくなる

<input name="id" value="10" type="hidden" />

例:　idを使用したい場合の代替案
echo $this->Form->control('id', ['type' => 'hidden']);

<input name="id" id="id" type="hidden" />
```
### $this->Form->controlの注意点

id名でドットを境にアッパーキャメルになってたのが、ハイフン区切りになってる点に注意

**2系・・・アッパーキャメル（ViewSetting.mode → ViewSettingMode）**

**3系以降・・・ハイフン区切り（ViewSetting.mode → viewsetting-mode）**

```php
echo $this->BcAdminForm->control('ViewSetting.mode', ['type' => 'hidden', 'value' => 'index']);

<input type="hidden" name="ViewSetting[mode]" class="bca-hidden__input" id="viewsetting-mode" value="index">
```
