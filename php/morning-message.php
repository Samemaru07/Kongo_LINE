<?php

// DB接続
try {
    $pdo = new PDO("mysql:host=localhost; dbname=kongo; charset=utf8mb4", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "DB接続成功\n";
} catch (PDOException $e) {
    die("DB接続エラー: {$e->getMessage()}");
}


// LINE接続
$channelAccessToken = "";
$channelSecret = "";
$userId = "";

require_once("C:/xampp/htdocs/kongo/vendor/autoload.php");
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;

$httpClient = new CurlHTTPClient($channelAccessToken);
$bot = new LINEBot($httpClient, ["channelSecret" => $channelSecret]);


// メッセージをDBから取得し送信
$sql = "SELECT message FROM morning_message WHERE id=:id";
$stmt = $pdo->prepare($sql);
$todayMessageId = rand(1, 10);
$stmt->bindValue(":id", $todayMessageId, PDO::PARAM_STR);
$stmt->execute();

$todayMessageFromDB = $stmt->fetch(PDO::FETCH_ASSOC);
$todayMessage = $todayMessageFromDB["message"];

$textMessageBuilder = new TextMessageBuilder($todayMessage);
$bot->pushMessage($userId, $textMessageBuilder);
?>