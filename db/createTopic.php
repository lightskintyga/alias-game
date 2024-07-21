<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $topic = $_POST['topic'];
    $words = $_POST['words'];

    $db = new SQLite3('main.db');

    $stmt = $db->prepare("INSERT INTO topics (topic) VALUES (:topic)");
    $stmt->bindValue(':topic', $topic);
    $stmt->execute();

    $wordsArray = explode("\n", $words);
    foreach ($wordsArray as $word) {
        $word = trim($word);
        if (!empty($word)) {
            $stmt = $db->prepare("INSERT INTO words (word, topic) VALUES (:word, :topic)");
            $stmt->bindValue(':word', $word);
            $stmt->bindValue(':topic', $topic);
            $stmt->execute();
        }
    }
    header("Location: ../createTopic.html");
    exit();
}
