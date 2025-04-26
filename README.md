# 推しからLINEが来る！

## 0. 目次
- [推しからLINEが来る！](#推しからlineが来る)
  - [0. 目次](#0-目次)
  - [1. 概要](#1-概要)
  - [2. 環境](#2-環境)
  - [3. ディレクトリ構成](#3-ディレクトリ構成)
  - [4. 開発環境構築・LINE公式アカウント作成(無料)](#4-開発環境構築line公式アカウント作成無料)
    - [4-1. Composerのインストール](#4-1-composerのインストール)
      - [必要理由](#必要理由)
      - [注意点](#注意点)
      - [インストール方法 (Windows / 実行ファイルをダウンロード の場合)](#インストール方法-windows--実行ファイルをダウンロード-の場合)
    - [4-2. SDKのインストール](#4-2-sdkのインストール)
      - [必要理由](#必要理由-1)
      - [注意点](#注意点-1)
      - [インストール方法](#インストール方法)
    - [4-3. ngrokのインストール](#4-3-ngrokのインストール)
      - [必要理由](#必要理由-2)
      - [注意点](#注意点-2)
      - [インストール方法(Windows / Zipファイルをダウンロードする場合)](#インストール方法windows--zipファイルをダウンロードする場合)
    - [LINE公式アカウント作成方法](#line公式アカウント作成方法)
      - [注意点](#注意点-3)
      - [作成方法](#作成方法)
      - [各種定数確認方法](#各種定数確認方法)
        - [チャネルアクセストークン](#チャネルアクセストークン)
        - [チャネルシークレット](#チャネルシークレット)
        - [ユーザーID](#ユーザーid)
    - [動作確認](#動作確認)
      - [1. Apacheの動作確認](#1-apacheの動作確認)
      - [2. LINEメッセージ送信方法](#2-lineメッセージ送信方法)
  - [5. トラブルシューティング](#5-トラブルシューティング)
    - [1. localhostにアクセスできない](#1-localhostにアクセスできない)
      - [原因](#原因)
      - [対処法](#対処法)
    - [2. LINEが送信されない](#2-lineが送信されない)
      - [原因](#原因-1)
      - [対処法](#対処法-1)

## 1. 概要
PHP, MySQL(XAMPP), LINE Messaging APIを使用して、**推しからLINEが来る**というシステムを実装しました。
データベースに、ChatGPTが生成した複数のメッセージを複数のテーブルに格納。
ユーザーからのメッセージに含まれる文字列で、メッセージを取り出すテーブルを変更、ランダムに選択したメッセージを送信するという処理を複数のPHPファイルに記述。
休or平日・時間で実行するPHPファイルを分けるバッチファイルを作成。

## 2. 環境
- Windows11 Home
- PHP, MySQLはXAMPPを使用
| 言語・使用ツール | バージョン |
| :-------------- | :-------- |
| PHP             | 8.2.12    |
| line-bot-sdk-php| 3.11      |
| XAMPP           | 3.3.0     |
| Apache          | 2.4.58    |
| ngrok           | 3.22.1    |

※その他のパッケージのバージョンについては、composer.jsonやcomposer.lockをご参照ください。


## 3. ディレクトリ構成
```
/kongo
    ├ /docs
    │    └ design-document.md : 設計書
    │
    ├ /php
    │    └ /prepare
    │    │    └ db-set.php : DB設計
    │    ├ backhome-message.php : 帰りを急かすLINEを送信する
    │    ├ reply-message.php : メッセージのやり取り(エンドポイント)
    │    ├ morning-message.php : 朝のメッセージを送信
    │    └ holiday-message.php : 休日のメッセージを送信
    │
    ├ pushmessage.bat : タスクスケジューラにセット
    ├ /vendor
    │    └ autoload.php : 自動で生成されるが、各PHPファイルで読み込み
    ├ composer.json
    ├ composer.lock
    └ README.md
```

## 4. 開発環境構築・LINE公式アカウント作成(無料)
### 4-1. Composerのインストール
#### 必要理由
Composerとは、PHPの「パッケージ管理ツール」と呼ばれるものです。(Python->pip, Windows->wingetなどなど)
これは後に説明するSDK(パッケージ)をダウンロードするために必要なのですが、なぜそんなツールを使う必要があるのかというと、それはパッケージをインストールするのが簡単になるからです。

例えば、パッケージAをインストールしたいとします。しかしAを使うには他のパッケージBが必要です。このComposerを使えば、パッケージBまでもインストールしてくれるからです。

#### 注意点
特にありません。

#### インストール方法 (Windows / 実行ファイルをダウンロード の場合)
1. [Composerの公式サイト](https://ngrok.com/)にアクセス
2. Downloadをクリック
3. "Windows Installer"の"Composer-Setup.exe"をクリックして、実行ファイルをダウンロード
4. エクスプローラで実行ファイルをダブルクリックで起動
5. インストールするアカウントを選択(どちらでもいい。なお全ユーザーにインストールする場合は管理者権限が必要)
6. Developer Mode はオフ(オンだとアンインストール用のファイルがインストールされない)
7. Nextを選択(これ以降、いじるものやチェックを入れるものはない)
8. その後、Install
9. PowerShell or cmd で `composer`と実行(composerコマンドの一覧が表示されれば成功)


### 4-2. SDKのインストール
#### 必要理由
SDKとは、「特定のシステムやプラットフォームで、ソフトウェアを開発するためのツール一式をまとめたもの」です。

今回はLINEという外部のプラットフォームでオリジナルの機能を実装します。
ですが、LINE公式さんはありがたく、さまざまな言語で公式アカウント(Bot)を開発できるようなSDKを配布してくれています。

今回はPHPで開発するので、"**[line-bot-sdk-php](https://github.com/line/line-bot-sdk-php)**(**Ver. 3.11**)"をインストールしました。

#### 注意点
::: note warn
なぜ低いバージョンのものを使うのかというと、line-bot-sdkはバージョンがアップするに連れ、ディレクトリ構成が大幅に変更され、名前空間やモジュールの有無にも影響が出ています。私が参考にしたサイトでは古いバージョンで使われているものが多かったので、わざと低いバージョンを使いました。
ちなみにPHP Ver. 5.6以上に対応しています。
:::
#### インストール方法
1. ホームディレクトリ(プロジェクトのトップフォルダ)をxamppのhtdocs直下にあらかじめ作成し、パスを覚えるかコピー(`Ctrl+Shift+C`)
2. PowerShell or cmdで`cd C:/xampp/htdocs/kongo`などでカレントディレクトリ(現在地)をホームディレクトリに移動
3. `composer require linecorp/line-bot-sdk:^3.11`と実行
4. ホームディレクトリに/vendorディレクトリが作成されていればOK

### 4-3. ngrokのインストール
#### 必要理由
WebhookURLは、HTTP**S**通信で行われるため、ローカル環境(localhost)ではできません。
ですので、ローカルPCのWebサーバーを外部公開(http->http*s*に)できるツールが必要でした。

#### 注意点
ngrokは、WindowsのMicrosoft Defenderをウイルスと判断します。
Microsoft Defenderのリアルタイム保護をオフにしてインストール・起動確認をしましょう。

- Microsoft Defenderの「リアルタイム保護」をオフ
(設定 -> Windows セキュリティ -> Microsoft Defender -> ウイルスと脅威の防止 -> ウイルスと脅威の防止の設定の"設定の管理" -> リアルタイム保護)
- 解凍後の`ngrok.exe`のパスをコピー(エクスプローラで`Ctrl+Shift+C`)してリアルタイム保護から除外
(上記のリアルタイム保護のページを下にスクロール -> 除外の"除外の追加または削除" -> "除外の追加"をクリック -> "ファイル"を選択し、"ngrok.exe"をダブルクリック)
**インストールが完了して、ngrokの起動確認と実行ファイルの脅威防止からの除外が完了したら、必ずリアルタイム保護をオンにしましょう**

#### インストール方法(Windows / Zipファイルをダウンロードする場合)
1. (ngrok公式サイト)[https://ngrok.com/]にアクセス
2. 少し下にある"Get started for free"をクリック
3. AgentsがWindowsが選択されている状態で、"Download for Windows"をクリック(普通は64-Bitです。ご使用のPCに合わせてお選びください)
4. ダウンロード完了後、ご自分の好きなフォルダで解凍。
5. 解凍された`ngrok.exe`のパスをコピー(`Ctrl+Shift+C`でもできます)
6. それを、Microsoft Defenderのリアルタイム保護から除外([注意点](#注意点)参照)
7. `ngrok.exe`を起動(PowerShell or cmdで`ngrok http 80`を実行)
8. 起動出来たら、リアルタイム保護を再度オン

### LINE公式アカウント作成方法
#### 注意点
無料プランでは、ひと月に遅れるメッセージ数が200件まで。

#### 作成方法
1. [LINE Official Account Manager](https://manager.line.biz/)で、普段使っているLINEアカウントでいいのでログイン
2. 左側の「作成」をクリックし、手順に従って公式アカウントを作成
3. 作成で来たら、LINE OAMのトップ -> 設定 -> Messaging APIをオンに
4. [LINE Developers](https://developers.line.biz/ja/)で上の「コンソール」をクリックしログイン
5. 該当プロバイダ -> Messaging API設定からチャネルアクセストークンを発行し取得
6. LINE Dev -> 該当プロバイダ -> Messaging API設定 -> 下にスクロールして「Webhookの利用をオン」

#### 各種定数確認方法
##### チャネルアクセストークン
- チャネルを使用する権限を持っているかどうかを確認する際に用いる文字列
- LINE Dev -> 該当プロバイダ -> Messaging API設定 から取得可能

##### チャネルシークレット
- API通信の認証, Webhookの署名検証
- LINE OAM -> 設置絵 -> Messaging API からチャネルシークレット取得可能

##### ユーザーID
- LINE Dev -> 該当プロバイダ -> チャネル基本設定 からユーザーID取得可能

### 動作確認
#### 1. Apacheの動作確認
Apache起動後に`http://localhost:80`(`http://127.0.0.1`)(パスが指定されていなければ/dashboard)にアクセスできれば成功です。

#### 2. LINEメッセージ送信方法
1. PowerShell or cmd で`ngrok http 80`(80:ポート番号)と入力しクリック
2. ngrokが起動したら、「Forwarding」の`https:// ... .ngrok-free.app`までをコピー
3. LINE Dev -> 該当プロバイダ -> Messaging API設定 -> 「Webhook URL」に、**先ほどコピーしたngrokのURL/ホームディレクトリ(kongo)/...(php)/○○.php(reply-message.php)**を入力
4. Apache, ngrokが起動したまま、「検証」ボタンを押して、「成功」 + ngrokのログに「200 OK」(成功レスポンス)が出ればOK

::: note warn
**ngrokを閉じてしまうと、httpsのリンクは再発行**となる。よって、PCの再起動後には必ずWebhook URLの書き換えが必要
:::


## 5. トラブルシューティング
### 1. localhostにアクセスできない
#### 原因
ポート番号の競合が起こっている。

#### 対処法
ポート番号の変更方法は、xamppのコントロールパネル -> apacheのconfigをクリック -> Apache(httpd.config)を開く -> 60行目, 228行目の数字を両方変更。(例 80->8080)
変更したら`Ctrl+S`で保存しましょう。

### 2. LINEが送信されない
#### 原因
1. `autoload.php`が読み込まれていない
2. 定数設定(チャネルアクセストークン, チャネルシークレット, ユーザーID)が正しくない
3. ひと月に送信できるメッセージ数の上限を超えている
4. Apache, MySQL, ngrokが起動していない

#### 対処法
- `/vendor/autoload.php`が処理の前に読み込まれているか確認(`require_once("C:/xampp/htdocs/kongo/vendor/autoload.php"))
- [LINE Official Account Manager](https://account.line.biz/login?redirectUri=https%3A%2F%2Faccount.line.biz%2Foauth2%2Fcallback%3Fclient_id%3D10%26code_challenge%3DfBu9ZeJTKyxzXUTsdVdk57tNbuDYsQEcHFziUiXyDZ0%26code_challenge_method%3DS256%26redirect_uri%3Dhttps%253A%252F%252Fmanager.line.biz%252Fapi%252Foauth2%252FbizId%252Fcallback%26response_type%3Dcode%26state%3Dhbggedf0JXZ3KGwxFcQk2vspJiylUiYc)にアクセスしログイン -> 該当アカウントを選択 -> ホームタブを下にスクロール -> メッセージ配信の欄の左下に残り送信できるメッセージ数が書かれている。
- Apache, MySQL, ngrokを起動、ngrokのURLがWebhook URLと一緒か確認
