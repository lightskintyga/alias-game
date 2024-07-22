<?php
$db = new SQLite3('main.db');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topic = $_POST['topic'];
    $words = explode("\n", trim($_POST['words']));

    $stmt = $db->prepare('DELETE FROM words WHERE topic = :topic');
    $stmt->bindValue(':topic', $topic);
    $stmt->execute();

    $stmt = $db->prepare('INSERT INTO words (word, topic) VALUES (:word, :topic)');
    foreach ($words as $word) {
        $stmt->bindValue(':word', trim($word));
        $stmt->bindValue(':topic', $topic);
        $stmt->execute();
    }

    echo 'Слова успешно сохранены';
}