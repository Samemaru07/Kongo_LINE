<?php
// DB接続
try {
    $pdo = new PDO("mysql:host=localhost; dbname=kongo; charset=utf8mb4", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "データベース接続成功\n";
} catch (PDOException $e) {
    $errorMessage = $e->getMessage();
}

// メッセージ送信準備
require_once("C:/xampp/htdocs/kongo/vendor/autoload.php");

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

$channelAccessToken = "AJTWayJmffmMSNugwMIOS1zu+7s+a3Q/Pnp7eSDQpmsgYUOTXqUsKZ0tWPY1gf7wHhOp+Zxu7HPC+bJ7RjbY9m77wTEY193K3RjnCe4AVOVHwWmZ+WHx2s0M3wd6s0Me/gFs8awSmar0j8agEo6J8AdB04t89/1O/w1cDnyilFU=";
$channelSecret = "ec792159c90768f8905e50732962006b";
$userId = "U314c810c6ed300f7100bc0e619c7dcfc";

$httpClient = new CurlHTTPClient($channelAccessToken);
$bot = new LINEBot($httpClient, ["channelSecret" => $channelSecret]);

// リプライ
$jsonRequestFromLineServer = file_get_contents("php://input");
$requestFromLineServer = json_decode($jsonRequestFromLineServer, true);
if (!isset($requestFromLineServer["events"][0])) {
    exit;
}

$fromUserMessageObject = $requestFromLineServer["events"][0];
if ($fromUserMessageObject["type"] == "message" && $fromUserMessageObject["message"]["type"] == "text") {
    $fromUserMessage = $fromUserMessageObject["message"]["text"];

    //  帰りが遅くなる場合の処理
    if (str_contains($fromUserMessage, "遅") || str_contains($fromUserMessage, "まだ")) {
        $sql = "SELECT message FROM late_message WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $todayMessageId = rand(1, 6);
        $stmt->bindValue(":id", $todayMessageId, PDO::PARAM_INT);
        $stmt->execute();
        $todayMessageFromDB = $stmt->fetch(PDO::FETCH_ASSOC);
        $todayMessage = $todayMessageFromDB["message"];

        $textMessageBuilder = new TextMessageBuilder($todayMessage);
        $bot->pushMessage($userId, $textMessageBuilder);

        // 帰りが早い場合の処理
    } elseif (str_contains($fromUserMessage, "すぐ") || str_contains($fromUserMessage, "今から") || str_contains($fromUserMessage, "速く") || str_contains($fromUserMessage, "早い") || str_contains($fromUserMessage, "早く")) {
        $sql = "SELECT message FROM soon_message WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $todayMessageId = rand(1, 6);
        $stmt->bindValue(":id", $todayMessageId, PDO::PARAM_INT);
        $stmt->execute();
        $todayMessageFromDB = $stmt->fetch(PDO::FETCH_ASSOC);
        $todayMessage = $todayMessageFromDB["message"];

        $textMessageBuilder = new TextMessageBuilder($todayMessage);
        $bot->pushMessage($userId, $textMessageBuilder);
        // 好きだといわれた場合の処理
    } elseif (str_contains($fromUserMessage, "好き") || str_contains($fromUserMessage, "愛して")) {
        $sql = "SELECT message FROM love_message WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $todayMessageId = rand(1, 10);
        $stmt->bindValue(":id", $todayMessageId, PDO::PARAM_STR);
        $stmt->execute();

        $todayMessageFromDB = $stmt->fetch(PDO::FETCH_ASSOC);
        $todayMessage = $todayMessageFromDB["message"];

        $textMessageBuilder = new TextMessageBuilder($todayMessage);
        $bot->pushMessage($userId, $textMessageBuilder);

    } elseif (str_contains($fromUserMessage, "会いたい") || str_contains($fromUserMessage, "帰りたい")) {
        $sql = "SELECT message FROM imissyou_message WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $todayMessageId = rand(1, 10);
        $stmt->bindValue(":id", $todayMessageId, PDO::PARAM_STR);
        $stmt->execute();

        $todayMessageFromDB = $stmt->fetch(PDO::FETCH_ASSOC);
        $todayMessage = $todayMessageFromDB["message"];

        $textMessageBuilder = new TextMessageBuilder($todayMessage);
        $bot->pushMessage($userId, $textMessageBuilder);
    } else {
        $textMessageBuilder = new TextMessageBuilder("ワードが指定されていないヨ");
        $bot->pushMessage($userId, $textMessageBuilder);
    }
} else {
    // エラーハンドリング
    $textMessageBuilder = new TextMessageBuilder("動作しないヨ");
    $ot->pushMessage($userId, $textMessageBuilder);

    $textMessageBuilder = new TextMessageBuilder($errorMessage);
    $bot->pushMessage($userId, $textMessageBuilder);

    exit;
}
?>