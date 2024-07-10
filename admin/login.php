<?php
$login = $_POST['login'];
$password = $_POST['password'];
$db = new SQLite3('../db/main.db');

$query = $db->prepare('SELECT Login, Password FROM login WHERE Login = :login');
$query->bindValue(':login', $login, SQLITE3_TEXT);

$result = $query->execute();

if ($result) {
    $row = $result->fetchArray(SQLITE3_ASSOC);
    if ($row && $password == $row['Password']) {
        session_start();
        $_SESSION['admin'] = true;
        $script = '../editor.html';
    } else {
        $script = 'login.html';
    }
}

$db->close();

header("Location: $script");