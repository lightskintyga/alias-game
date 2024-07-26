<?php
// Подключение к бд
$db = new mysqli('localhost', 'teacher', 'real_teacher', 'alias');

// Проверка подключения
if ($db->connect_error) {
    die('Ошибка подключения: ' . $db->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получение названия темы
    $topic = $_POST['topic'];

    // Удаление слов соответствующей темы из бд
    $stmt = $db->prepare('DELETE FROM words WHERE topic = ?');
    $stmt->bind_param('s', $topic);
    $stmt->execute();

    // Удаление самой темы из бд
    $stmt = $db->prepare('DELETE FROM topics WHERE topic = ?');
    $stmt->bind_param('s', $topic);
    $stmt->execute();

    echo 'Тема успешно удалена!';

    // Закрытие подключения
    $stmt->close();
}

// Закрытие подключения
$db->close();