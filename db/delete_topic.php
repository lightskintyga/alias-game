<?php
$db = new mysqli('localhost', 'teacher', 'real_teacher', 'main');

if ($db->connect_error) {
    die('Ошибка подключения: ' . $db->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topic = $_POST['topic'];

    $stmt = $db->prepare('DELETE FROM words WHERE topic = ?');
    $stmt->bind_param('s', $topic);
    $stmt->execute();

    $stmt = $db->prepare('DELETE FROM topics WHERE topic = ?');
    $stmt->bind_param('s', $topic);
    $stmt->execute();

    echo 'Тема успешно удалена!';

    $stmt->close();
}

$db->close();