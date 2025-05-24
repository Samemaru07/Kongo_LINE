@echo off

start "" "C:\xampp\mysql_start.bat"
timeout /t 10 /nobreak >nul

for /f "tokens=1-2 delims= " %%a in ('date /t') do set today=%%b

for /f "tokens=2 delims=:" %%a in ('%TIME%') do set minute=%%a

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