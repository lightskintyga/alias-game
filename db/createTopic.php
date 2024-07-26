<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получение данных из формы
    $topic = $_POST['topic'];
    $words = $_POST['words'];
    $uploadDir = '../img/topics/'; // Папка для загрузки иконок тем
    $relativeUploadDir = 'img/topics/'; // Папка для добавления пути к изображению в бд (image_path в таблице topics)
    $imagePath = null;

    // Создание папки, если она отсутствует
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Если приложен файл
    if (!empty($_FILES['icon']['name'])) {
        $uploadFile = $uploadDir . basename($_FILES['icon']['name']); // Путь для загружаемого файла
        $relativeUploadFile = $relativeUploadDir . basename($_FILES['icon']['name']); // Путь для занесения в бд
        $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION)); // Расширение файла в нижнем регистре

        // Проверка разрешения загружаемого файла
        if ($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg') {
            error_log('Допускаются только JPG, JPEG и PNG файлы');
        }

        // Пересоздание изображения. Используется библиотека GD, она должна быть добавлена в php.ini
        $sourceImg = null;
        if ($imageFileType == 'jpg' || $imageFileType == 'jpeg') { // если файл формата jpg или jpeg
            $sourceImg = imagecreatefromjpeg($_FILES['icon']['tmp_name']); // создаем новое изображение
        } elseif ($imageFileType == 'png') { // если файл формата png
            $sourceImg = imagecreatefrompng($_FILES['icon']['tmp_name']); // создаем новое изображение
        }

        // Создание изображение формата 495x240
        $resizedImage = imagecreatetruecolor(495, 240);
        imagecopyresampled($resizedImage, $sourceImg, 0, 0, 0, 0, 495, 240, imagesx($sourceImg), imagesy($sourceImg));

        // Загрузка файла в папку в зависимости от расширения
        if ($imageFileType == 'jpg' || $imageFileType == 'jpeg') {
            imagejpeg($resizedImage, $uploadFile, 90); // загрузка jpg, jpeg файла
        } elseif ($imageFileType == 'png') {
            imagepng($resizedImage, $uploadFile, 9); // загрузка png файла
        }

        // Удаление изображений
        imagedestroy($sourceImg);
        imagedestroy($resizedImage);

        // Путь к загруженному изображению для бд (без '../')
        $imagePath = $relativeUploadFile;
    }

    // Подключение к бд
    $db = new mysqli('localhost', 'teacher', 'real_teacher', 'alias');

    // Проверка подключения
    if ($db->connect_error) {
        error_log('Ошибка подключения: ' . $db->connect_error);
    }

    // Если иконка загружена
    if ($imagePath) {
        $stmt = $db->prepare("INSERT INTO topics (topic, image_path) VALUES (?, ?)"); // Добавление названия темы и пути до иконки в бд
        $stmt->bind_param('ss', $topic, $imagePath);
    } else { // Если иконка отсутствует
        $stmt = $db->prepare("INSERT INTO topics (topic) VALUES (?)"); // Добавление только название темы в бд
        $stmt->bind_param('s', $topic);
    }
    // Проверка выполнения запроса
    if (!$stmt->execute()) {
        error_log('Ошибка выполнения запроса: ' . $stmt->error);
    }

    // Добавление каждой фразы в бд
    $wordsArray = explode("\n", $words); // Текстовое поле со словами построчно в виде массива
    foreach ($wordsArray as $word) {
        $word = trim($word);
        if (!empty($word)) {
            $stmt = $db->prepare("INSERT INTO words (word, topic) VALUES (?, ?)"); // Добавление в бд слова и его темы
            // Проверка выполнения запроса
            if (!$stmt) {
                error_log('Ошибка подготовки запроса: ' . $db->error);
            }
            $stmt->bind_param('ss', $word, $topic);
            // Проверка выполнения запроса
            if (!$stmt->execute()) {
                error_log('Ошибка выполнения запроса: ' . $stmt->error);
            }
        }
    }

    // Закрытие подключений
    $stmt->close();
    $db->close();

    // Завершение выполнения скрипта
    exit();
}