<?php

// DB接続
try {
    $pdo = new PDO("mysql:localhost=localhost; dbname=kongo; charset=utf8mb4", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "データベース接続成功\n";
} catch (PDOException $e) {
    die("データベース接続エラー: {$e->getMessage()}\n");
}

// LINE接続
require_once("C:/xampp/htdocs/kongo/vendor/autoload.php");

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

$channelAccessToken = "ljPT6qc82dERXpGMm/WD7rClnDsfwl59Vz0IZDR909QrsKRsvEPcwPIvyen5I3byXs9b6ym0OhZw25b5gSPbiuto4u3XaYIj0yC48JgSLhIL7spdjskkSLQfYUjn+euGsdRJTz1GjHK1AhbFTuufVAdB04t89/1O/w1cDnyilFU=";
$channelSecret = "03a9bebe0aeb286c394af3761b877566";
$userId = "Ucdae0a774d5085c3991e83baf678b508";

$httpClient = new CurlHTTPClient($channelAccessToken);
$bot = new LINEBot($httpClient, ["channelSecret" => $channelSecret]);

// メッセージをDBから取り出し、送信
$sql = "SELECT message FROM holiday_message WHERE id=:id";
$stmt = $pdo->prepare($sql);
$todayMessageId = rand(1, 5);
$stmt->bindValue(":id", $todayMessageId, PDO::PARAM_INT);
$stmt->execute();

$todayMessage = $stmt->fetch(PDO::FETCH_ASSOC);
$todayMessage = $todayMessage["message"];

$textMessageBuilder = new TextMessageBuilder($todayMessage);
$response = $bot->pushMessage($userId, $textMessageBuilder);
