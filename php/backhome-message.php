<?php


// DB接続
try {
    $pdo = new PDO("mysql:host=localhost; dbname=kongo; charset=utf8mb4", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "データベース接続成功\n";
} catch (PDOException $e) {
    die("データベース接続エラー: {$e->getMessage()}\n");
}


// メッセージ送信準備
require_once("C:/xampp/htdocs/kongo/vendor/autoload.php");

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

$channelAccessToken = "";
$channelSecret = "";
$userId = "";

$httpClient = new CurlHTTPClient($channelAccessToken);
$bot = new LINEBot($httpClient, ["channelSecret" => $channelSecret]);


// DBからメッセージをランダムで取得しLINEに送信
$sql = "SELECT message FROM backhome_message WHERE id=:id";
$stmt = $pdo->prepare($sql);

$todayMessageId = rand(1, 28);
$stmt->bindValue(":id", $todayMessageId, PDO::PARAM_INT);

$stmt->execute();
$todayMessageFromDB = $stmt->fetch(PDO::FETCH_ASSOC);

$todayMessage = $todayMessageFromDB["message"];

$textMessageBuilder = new TextMessageBuilder($todayMessage);
$succeed = $bot->pushMessage($userId, $textMessageBuilder);

if ($succeed) {
    echo "メッセージ送信成功";
} else {
    echo "メッセージ送信失敗";
}

?>