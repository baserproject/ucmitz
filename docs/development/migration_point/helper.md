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

## BcFormHelperの変更点
### $this->BcForm->hidden変更点

hiddenメソッドではcakephp4系からIDが付与されなくなった
なので代わりに、BcAdminForm->control('name', ['type' => 'hidden']);を使うようにする
### $this->BcAdminFormHelper->controlの注意点

id名でドットを境にアッパーキャメルになってたのが、ハイフン区切りになってる点に注意

**2系・・・アッパーキャメル（ViewSetting.mode → ViewSettingMode）**

**3系以降・・・ハイフン区切り（ViewSetting.mode → viewsetting-mode）**

```php
echo $this->BcAdminForm->control('ViewSetting.mode', ['type' => 'hidden', 'value' => 'index']);

<input type="hidden" name="ViewSetting[mode]" class="bca-hidden__input" id="viewsetting-mode" value="index">
```
