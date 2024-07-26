<?php
// Получение данных из формы
$login = $_POST['login'];
$password = $_POST['password'];

// Подключение к бд
$db = new mysqli('localhost', 'teacher', 'real_teacher', 'alias');

// Проверка подключения
if ($db->connect_error) {
    die('Ошибка подключения: ' . $db->connect_error);
}

// Сохранение логина и пароля для дальнейшей проверки, если логин из бд равен логину из формы
$query = $db->prepare('SELECT login, password FROM login WHERE login = ?');
$query->bind_param('s', $login);
$query->execute();
$result = $query->get_result();

if ($result) {
    $row = $result->fetch_assoc();
    // Если пароль верный - стартует сессия админа и открывается редактор тем
    if ($row && $password == $row['password']) {
        session_start();
        $_SESSION['admin'] = true;
        $script = '../editor.html';
    } else { // Пароль неверный - пытаемся войти заново
        $script = 'login.html';
    }
}

// Закрытие подключений
$query->close();
$db->close();

header("Location: $script");