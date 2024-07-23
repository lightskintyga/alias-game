<?php
$login = $_POST['login'];
$password = $_POST['password'];

$db = new mysqli('localhost', 'teacher', 'real_teacher', 'main');

if ($db->connect_error) {
    die('Ошибка подключения: ' . $db->connect_error);
}

$query = $db->prepare('SELECT login, password FROM login WHERE login = ?');
$query->bind_param('s', $login);
$query->execute();
$result = $query->get_result();

if ($result) {
    $row = $result->fetch_assoc();
    if ($row && $password == $row['password']) {
        session_start();
        $_SESSION['admin'] = true;
        $script = '../editor.html';
    } else {
        $script = 'login.html';
    }
}

$query->close();
$db->close();

header("Location: $script");