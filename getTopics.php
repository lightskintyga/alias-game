<?php
$db = new SQLite3('db/main.db');
$result = $db->query('SELECT topic, image_path FROM topics');
?>
<html lang="ru">
<head>
    <meta charset="UTF-8">
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
            background-color: #079CD8;
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
        }
    </style>
</head>
<body>
<div class="topics" id="topics">
    <h3 class="topics__header">Выберите тему</h3>
    <div class="topics__row">
    <?php while ($row = $result->fetchArray(SQLITE3_ASSOC)): ?>
        <button class="topic" onclick="startGame('<?php echo htmlspecialchars($row['topic']); ?>')" style="background-image: url('<?php if (empty($row['image_path'])) {
            $row['image_path'] = 'img/cats.png';
        }
        echo htmlspecialchars($row['image_path']); ?>')"
        <span style="z-index: 2;"><?php echo htmlspecialchars($row['topic']); ?></span>
        </button>
    <?php endwhile; ?>
    </div>
</div>
<script>
    const topicsBlock = document.getElementById('topics');

    function startGame(topic) {
        window.location.href = 'game.php?topic=' + encodeURIComponent(topic);
    }
</script>
</body>
</html>
