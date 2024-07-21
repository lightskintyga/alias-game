<?php
$login = $_POST['login'];
$password = $_POST['password'];
$db = new SQLite3('../db/main.db');

$query = $db->prepare('SELECT login, password FROM login WHERE login = :login');
$query->bindValue(':login', $login, SQLITE3_TEXT);

$result = $query->execute();

if ($result) {
    $row = $result->fetchArray(SQLITE3_ASSOC);
    if ($row && $password == $row['password']) {
        session_start();
        $_SESSION['admin'] = true;
        $script = '../editor.html';
    } else {
        $script = 'login.html';
    }
}

$db->close();

header("Location: $script");