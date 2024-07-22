<?php
$db = new SQLite3('main.db');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topic = $_POST['topic'];

    $stmt = $db->prepare('DELETE FROM words WHERE topic = :topic');
    $stmt->bindValue(':topic', $topic);
    $stmt->execute();

    $stmt = $db->prepare('DELETE FROM topics WHERE topic = :topic');
    $stmt->bindValue(':topic', $topic);
    $stmt->execute();

    echo 'Тема успешно удалена!';
}