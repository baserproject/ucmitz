# BcFavorite plugin for baserCMS

## Installation

You can install this plugin into your baserCMS application using [composer](https://getcomposer.org).

The recommended way to install composer packages is:

```
composer require baserproject/bc-favorite
```

- [ ] API を作成する
  - [x] コントローラー実装
  - [x] コントローラーのテスト
- [x] Admin/Controller を移行する→廃止
- [ ] View を移行
  - Vue.js と APIで作成する
  - [x] メニューの上にイベントディスパッチャーを作る
  - [ ] BcFavoriteから上記イベントを横取りしてお気に入り一覧を表示
    - [ ] お気に入りのテンプレートを vue.js 化して読み込む
    - [ ] APIでJSONを取得して vue.js でレンダリング
  - [ ] 追加
  - [ ] 編集
  - [ ] 削除
  - [ ] 並び替え
- BcEventDispatcher::dispatch() の class の指定の仕様検討

## baserCMS4のイベント仕様

- プラグインのEventフォルダをチェック
- EventListener があれば読み込む
- $events プロパティをチェック
- イベント登録



