<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $topic = $_POST['topic'];
    $words = $_POST['words'];
    $uploadDir = '../img/topics/';
    $relativeUploadDir = 'img/topics/';
    $imagePath = null;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (!empty($_FILES['icon']['name'])) {
        $uploadFile = $uploadDir . basename($_FILES['icon']['name']);
        $relativeUploadFile = $relativeUploadDir . basename($_FILES['icon']['name']);
        $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

        echo 'Temp file: ' . $_FILES['icon']['tmp_name'] . '<br>';
        echo 'Upload file: ' . $uploadFile . '<br>';

        if ($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg') {
            error_log('Допускаются только JPG, JPEG и PNG файлы');
        }

        $sourceImg = null;
        if ($imageFileType == 'jpg' || $imageFileType == 'jpeg') {
            $sourceImg = imagecreatefromjpeg($_FILES['icon']['tmp_name']);
        } elseif ($imageFileType == 'png') {
            $sourceImg = imagecreatefrompng($_FILES['icon']['tmp_name']);
        }

        $resizedImage = imagecreatetruecolor(495, 240);
        imagecopyresampled($resizedImage, $sourceImg, 0, 0, 0, 0, 495, 240, imagesx($sourceImg), imagesy($sourceImg));

        if ($imageFileType == 'jpg' || $imageFileType == 'jpeg') {
            imagejpeg($resizedImage, $uploadFile, 90);
        } elseif ($imageFileType == 'png') {
            imagepng($resizedImage, $uploadFile, 9);
        }

        imagedestroy($sourceImg);
        imagedestroy($resizedImage);

        $imagePath = $relativeUploadFile;
    }

    $db = new mysqli('localhost', 'teacher', 'real_teacher', 'alias');

    if ($db->connect_error) {
        error_log('Ошибка подключения: ' . $db->connect_error);
    }

    if ($imagePath) {
        $stmt = $db->prepare("INSERT INTO topics (topic, image_path) VALUES (?, ?)");
        $stmt->bind_param('ss', $topic, $imagePath);
    } else {
        $stmt = $db->prepare("INSERT INTO topics (topic) VALUES (?)");
        $stmt->bind_param('s', $topic);
    }
    if (!$stmt->execute()) {
        error_log('Ошибка выполнения запроса: ' . $stmt->error);
    }

    $wordsArray = explode("\n", $words);
    foreach ($wordsArray as $word) {
        $word = trim($word);
        if (!empty($word)) {
            $stmt = $db->prepare("INSERT INTO words (word, topic) VALUES (?, ?)");
            if (!$stmt) {
                error_log('Ошибка подготовки запроса: ' . $db->error);
            }
            $stmt->bind_param('ss', $word, $topic);
            if (!$stmt->execute()) {
                error_log('Ошибка выполнения запроса: ' . $stmt->error);
            }
        }
    }
    $stmt->close();
    $db->close();

    exit();
}