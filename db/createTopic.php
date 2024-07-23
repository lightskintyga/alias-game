<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $topic = $_POST['topic'];
    $words = $_POST['words'];

    $db = new mysqli('localhost', 'teacher', 'real_teacher', 'main');

    if ($db->connect_error) {
        die('Ошибка подключения: ' . $db->connect_error);
    }

    $stmt = $db->prepare("INSERT INTO topics (topic) VALUES (?)");
    $stmt->bind_param('s', $topic);
    $stmt->execute();

    $wordsArray = explode("\n", $words);
    foreach ($wordsArray as $word) {
        $word = trim($word);
        if (!empty($word)) {
            $stmt = $db->prepare("INSERT INTO words (word, topic) VALUES (?, ?)");
            $stmt->bind_param('ss', $word, $topic);
            $stmt->execute();
        }
    }
    $stmt->close();
    $db->close();

    header("Location: ../createTopic.html");
    exit();
}