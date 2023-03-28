# バイテックグリーンエナジー株式会社様 Fシステム開発方針 [仮]

## 開発環境

|Name|Version|Description|
|:---|:---|:---|
|CentOS|7.3 or 7.4 64bit|OS|
|Apache|2.4.x|Web Server|
|MySQL|5.7.x|RDBMS|
|PHP|7.2.x|Programming Language|
|Laravel|5.5.x|PHP Framework|

## アプリケーション構成

|Name|Description|
|:---|:---|
|app|アプリケーション本体 (詳細は後述)|
|config|設定ファイル|
|database|データベース管理用ファイル (詳細は後述)|
|public|公開ディレクトリ、アセットコンパイルの成果物の設置|
|resources|viewテンプレート、アセット設置|
|routes|URLなどの定義|
|storage|ソースコード以外のファイルを設置|
|vendor| 依存パッケージ一覧 (このディレクトリの中身は編集厳禁)|
|.env| 環境変数一覧|

### app/Console/Kernel.php

バッチ処理の統括  
(Artisanコマンドへの登録、タスクスケジューリングなど)

### app/Console/Commands

バッチ処理クラスの定義  
**※ ビジネスロジックは記述しない**

### app/Events

イベントクラスの定義

### app/Exceptions

例外ハンドリング処理

### app/Extension

Laravel/Lumenフレームワーク拡張

### app/Http/Kernel.php

画面処理の統括  
(グローバル/ルートミドルウェアの定義など)

### app/Http/Controllers

画面処理  
**※ ビジネスロジックは記述しない**

### app/Http/Requests

リクエストパラメータのバリデーション  
単純なバリデーションであればここに記述 **(Controllerに記述しない)**

### app/Http/ViewComposers
テンプレート用変数の定義  
ラジオボタンやセレクトボックス用のオプションはここで定義すること **(Controllerに記述しない)**

### app/Listeners

イベントリスナークラス定義

### app/Models

データベースアクセスクラスの定義

### app/Providers

サービスプロバイダー定義

### app/Services

Command/ControllerクラスとModelクラスの中間処理  
ビジネスロジックやトランザクションを担当  
**!! 単純な処理は呼び出し元にて担い、Fatにならないよう十分に留意すること !!**

### app/Traits

トレイト定義

### app/Validators

バリデーション処理  
CSVの取込やロジックを用いたものなど、複雑なバリデーションを担う

### database/migrations

マイグレーションクラス  
(データベースの定義をPHPにて記述する)

### database/seeds

シーダークラス  
(開発用ダミーデータをPHPにて記述する)

### routes/web.php

ルーティング(URL)情報  
**ControllerやテンプレートにはURLを直接書くことなく、ここで定義したrouteNameを用いること**

## コーディングスタイル

- 以下の規約に準拠すること
  - [PSR-1](http://www.infiniteloop.co.jp/docs/psr/psr-1-basic-coding-standard.html)
  - [PSR-2](http://www.infiniteloop.co.jp/docs/psr/psr-2-coding-style-guide.html)
- エディタに以下に示すような拡張機能をインストールすることが好ましい
  - [EditorConfig](http://editorconfig.org/)
  - [php-cs-fixer](https://atom.io/packages/php-cs-fixer)

## ルーティング方針

- 画面表示を担う処理はGETメソッドに対応させること
- 状態の変化/副作用(セッションへの保存、DBの更新)を担う処理はPOSTメソッドに対応させること
- 画面表示と状態の変化を1つのアクションでは扱わないこと
  - ※ 状態の変化を扱うアクションは、処理後に必ず次の画面表示にリダイレクトさせる

## 禁止事項

- マジックナンバーの使用
- Controller / viewテンプレートへのURLの直書き
- Serviceクラス以外でのトランザクション処理

## 非推奨事項

- 比較演算子として「ゆるい」判定を使用
  - 判定には "===" や "!==" を使用する
- array() メソッドの使用
  - short array syntaxで代替する
- date() メソッドの使用
  - Carbon\CarbonクラスやCake\Chronosクラスで代替する
- 真偽値型の変数名に対する"flag"サフィックス
  - 自然言語として通るように変数名をつける
- 配列の変数名に対する"arr"サフィックス
  - 英語の複数形で代替する、不可算名詞や日本語は"list"サフィックスなどで対応する
- 変数を初期化せずに宣言する
  - ブロック内で初めて宣言された変数をブロック外から参照することを指す
