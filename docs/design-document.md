## 設計(アーキテクチャ)
### 1. 概要・大まかなプロセス
- LINEでメッセージを送信・やり取りするプログラムをPHPに記述する
- メッセージはMySQLで管理
- Windows タスクスケジューラに、毎時(8:50 / 14:30)「実行するPHPファイルを曜日・時間でラベル分け」したバッチファイルをセット

### 2. 処理フロー と ディレクトリ構成
- 平日・朝(8:50)に1日を応援するメッセージを送信する`morning-message.php`を実行
- 平日・夕(14:30)に帰りを待つメッセージを送信する`backhome-message.php`を実行
- 休日・朝(8:00)に休日を喜ぶメッセージを送信する`holiday-message.php`を実行

``` ディレクトリ構成
/kongo
    │
    ├ /php
    │   ├ /db
    │   │   ├ db_set.php
    │   ├ backhome-message.php
    │   ├ holiday-message.php
    │   ├ morning-message.php
    │   └ reply-message.php
    │
    ├ /vendor
    │   └ autoload.php
    │
    ├ pushmessage.bat
    ├ composer.json
    ├ composer.lock
    │
    └ README.md
```

``` データベース構成
kongo
  ├ backhome_message : 帰りを急かすメッセージ
  ├ soon_message : 帰りが早いときのメッセージ
  ├ late_message : 帰りが遅いときのメッセージ
  ├ love_message : 好きと言われたときのメッセージ
  ├ imissyou_message : 会いたいと言われたときのメッセージ
  └ morning_message : 朝学校についたときのメッセージ
```


### 3. 詳細仕様
- 返信に"遅", "まだ"を含む場合は、`late_message`テーブルからランダムにメッセージを取得
- 返信に"すぐ", "今から", "早[速]い[く]"を含む場合は、`soon_message`テーブルからランダムにメッセージを取得
- 返信に"好き", "愛して"が含まれる場合は、`love_message`テーブルからランダムにメッセージを取得
- 返信に"会いたい", "帰りたい"が含まれる場合は、`imissyou_message`テーブルからランダムにメッセージを取得
- 返信に指定ワード以外が含まれる場合は「ワードが指定されていないヨ」と返す

### 4. エラーハンドリング
- DB接続エラー等でめせーじを取得できなかった場合は、エラーであること, エラーの詳細メッセージをLINEで送信する。
- LINEのメッセージ送信ができない場合は、そもそもメッセージが送信できない
    - ひと月に送れるメッセージ数を満たしているのが原因かもしれないので要注意