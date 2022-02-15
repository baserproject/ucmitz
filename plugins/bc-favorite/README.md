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
- [x] View を移行
  - Vue.js と APIで作成する
  - [x] メニューの上にイベントディスパッチャーを作る
  - [x] BcFavoriteから上記イベントを横取りしてお気に入り一覧を表示
    - [x] 開閉ボタンの実装
      - [x] 開閉状態の保存処理を実装する
    - [x] お気に入りのテンプレートを vue.js 化して読み込む
      - [x] favorite_menu-navの中外をvue化する
    - [x] APIでJSONを取得して vue.js でレンダリング
  - [ ] 追加 (作成中)
    - [ ] リアルタイムバリデーション
      - [x] フォーム切り出し(切り分けた状態での保存は完了その他動作未確認)
      - [x] validationの設定
        - [x] FavoriteFormのref取得
        - [x] form.vueでのバリデーション結果受け渡し
        - [x] 初期値をフォームに入力した状態にする
          - 要件
          - nameとurl必須のみ
          - 既存値設定
      - [ ] タイトルを入力するとURLが消える
      - [ ] タイトルを空にしたときにバリデーションメッセージを表示する


    - [ ] ダイアログ関連をVueに移植する
      - [x] jQueryのコードをVueに移植
      - [x] 初期値設定
      - [ ] バリデーション
        - [ ] invalid時は保存ボタンをdisable化
      - [ ] 保存
      - [ ] デザインを他のダイアログに合わせる
    - [ ] サーバーバリデーション
  - [ ] 編集
  - [ ] 削除
  - [ ] 並び替え
    - [ ] initFavoriteList() の精査
- [x] BcEventDispatcher::dispatch() の class の指定の仕様検討
  - dispatchメソッドの呼び出し側で class を指定しないように設定可能だった

## baserCMS4のイベント仕様

- プラグインのEventフォルダをチェック
- EventListener があれば読み込む
- $events プロパティをチェック
- イベント登録



