@echo off

@REM 変数の値の変化をリアルタイムで反映する機能をONにする
setlocal enabledelayedexpansion

:: MySQLを起動する
start "" "C:\xampp\mysql_start.bat"
timeout /t 10 /nobreak >nul
@REM for /f : コマンドの実行結果orテキストファイルの内容を1行ずつ処理
    @REM "tokens=1-2 delims= " : スペース区切りで1番目と2番目のデータを取得
    @REM %%a : 1つ目のデータの受け皿
    @REM date /t : "2025/04/17 木"が得られる
    @REM do : 処理の開始
    @REM set today=%%b : 2つめのデータ(曜日)をtodayに代入
    @REM %%aや%%bはfor文内の一時変数で、ループないだけ有効でループ内の値を一時的に格納できる

for /f "tokens=1-2 delims= " %%a in ('date /t') do set today=%%b

@REM %TIME%は環境変数でコマンド実行前に展開される -> echo 13:45:22.34 となる。
for /f "tokens=2 delims=:" %%a in ('%TIME%') do set minute=%%a

@REM if文
    @REM /I : 大文字小文字を区別しない
    @REM 月～金ならラベルweekday系にジャンプ
    @REM それ以外(休日)ならラベルholidayにジャンプ
    @REM 変数名に%%をつけることで変数の中身を参照できる

@REM php -f ファイルパス : PHPファイルを実行し、
    @REM echo 実行結果の確認用メッセージ
    @REM exit /b : 呼び出し元に戻らず、このスクリプトを終了
if /I "%today%" == "月" (
    if /I "%minute%" == "30" (
        goto weekday_backhome
    ) else (
        goto weekday_morning
    )
) else if /I "%today%"=="火" (
    if /I "%minute%"=="30" (
        goto weekday_backhome
    ) else (
        goto weekday_morning
    )
) else if /I "%today%"=="水" (
    if /I "%minute%"=="30" (
        goto weekday_backhome
    ) else (
        goto weekday_morning
    )
) else if /I "%today%"=="木" (
    if /I "%minute%"=="30" (
        goto weekday_backhome
    ) else (
        goto weekday_morning
    )
) else if /I "%today%"=="金" (
    if /I "%minute%"=="30" (
        goto weekday_backhome
    ) else (
        goto weekday_morning
    )
) else (
    goto holiday
)

@REM :<ラベル> : `call :<ラベル名>`でその場所にジャンプして実行できる
:weekday_morning
php -f C:/xampp/htdocs/kongo/php/morning-message.php
echo "morning-message.phpを実行したよ"
exit /b

:weekday_backhome
php -f C:/xampp/htdocs/kongo/php/backhome-message.php
echo "backhome-message.phpを実行したよ"
exit /b

:holiday
php -f C:/xampp/htdocs/kongo/php/holiday-message.php
echo "holiday-message.phpを実行したよ"
exit /b