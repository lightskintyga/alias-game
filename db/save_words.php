<?php
$db = new mysqli('localhost', 'teacher', 'real_teacher', 'alias');

if ($db->connect_error) {
    die('Ошибка подключения: ' . $db->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topic = $_POST['topic'];
    $words = explode("\n", trim($_POST['words']));

    $stmt = $db->prepare('DELETE FROM words WHERE topic = ?');
    $stmt->bind_param('s', $topic);
    $stmt->execute();

    $stmt = $db->prepare('INSERT INTO words (word, topic) VALUES (?, ?)');
    foreach ($words as $word) {
        $trim_word = trim($word);
        $stmt->bind_param('ss', $trim_word, $topic);
        $stmt->execute();
    }

    echo 'Слова успешно сохранены';

    $stmt->close();
}

$db->close();