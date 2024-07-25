<?php
$db = new mysqli('localhost', 'teacher', 'real_teacher', 'alias');

if ($db->connect_error) {
    die('Ошибка подключения: ' . $db->connect_error);
}

$result = $db->query('SELECT topic, image_path FROM topics');
?>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Выбор темы</title>
    <style>
        @import "css/reset.css";

        @font-face {
            font-family: Montserrat-ExtraBold;
            src: url("fonts/MontserratAlternates-ExtraBold.ttf");
        }

        @font-face {
            font-family: Montserrat-Medium;
            src: url("fonts/MontserratAlternates-Medium.ttf");
        }

        @font-face {
            font-family: Montserrat-SemiBold;
            src: url("fonts/MontserratAlternates-SemiBold.ttf");
        }

        body {
            background-image: url("img/background.png");
            background-size: cover;
            display: flex;
            position: relative;
            align-items: center;
            justify-content: center;
        }

        .topics {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #FFFFFF;
            position: relative;
            width: 83%;
            height: auto;
            border-radius: 14px;
            padding-bottom: 72px;
            margin-top: 60px;
            margin-bottom: 60px;
        }

        .topics__header {
            font-family: Montserrat-ExtraBold;
            font-size: 40px;
            padding-top: 60px;
            padding-bottom: 97px;
        }

        .topics__row {
            display: flex;
            flex-direction: row;
            justify-content: center;
            column-gap: 45px;
            row-gap: 72px;
            flex-wrap: wrap;
        }

        .topic {
            width: 495px;
            height: 240px;
            border-radius: 12px;
            font-family: Montserrat-Medium;
            font-size: 34px;
            color: #FFFFFF;
            transition: 0.3s;
        }

        .topics__backBtn {
            position: absolute;
            top: 40px;
            right: 82px;
            width: 263px;
            height: 56px;
            font-family: Montserrat-ExtraBold;
            font-size: 20px;
            color: #FFFFFF;
            background-color: #00336D;
            border-radius: 12px;
            transition: 0.3s;
        }

        .topics__backBtn:hover {
            background-color: #2E629E;
        }

        @media screen and (max-width: 500px) {
            .topics__backBtn {
                display: none;
            }

            .topic {
                background-image: unset !important;
                background-color: #65B2E9;
                width: 238px;
                height: 40px;
                color: #000000;
                font-family: Montserrat-SemiBold;
                font-size: 16px;
                touch-action: manipulation;
            }

            .topics__row {
                row-gap: 17px;
            }

            .topics__header {
                font-size: 24px;
                padding-top: 47px;
                padding-bottom: 77px;
            }
        }
    </style>
</head>
<body>
<div class="topics" id="topics">
    <button class="topics__backBtn" id="topics__backBtn">Вернуться назад</button>
    <h3 class="topics__header">Выберите тему</h3>
    <div class="topics__row">
    <?php while ($row = mysqli_fetch_array($result)): ?>
        <button class="topic" onclick="startGame('<?php echo htmlspecialchars($row['topic']); ?>')" style="background-image: url('<?php if (empty($row['image_path'])) {
            $row['image_path'] = 'img/cats.png';
        }
        echo htmlspecialchars($row['image_path']); ?>')"
        <span class="topic__span" style="z-index: 2;"><?php echo htmlspecialchars($row['topic']); ?></span>
        </button>
    <?php endwhile; ?>
    </div>
</div>
<script>
    const topicsBlock = document.getElementById('topics');
    const backBtn = document.getElementById('topics__backBtn');

    function startGame(topic) {
        window.location.href = 'game.php?topic=' + encodeURIComponent(topic);
    }

    backBtn.addEventListener('click', () => {
        document.location = 'index.html';
    })
</script>
</body>
</html>
