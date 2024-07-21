<?php
session_start();
$db = new SQLite3('db/main.db');
$topic = $_GET['topic'];

if (!isset($_SESSION['used_words'])) {
    $_SESSION['used_words'] = [];
}
if (!isset($_SESSION['skipped_words'])) {
    $_SESSION['skipped_words'] = [];
}
if (!isset($counter)) {
    $counter = 0;
}

function getRandomWord($db, $topic) {
    $used_words = $_SESSION['used_words'];
    $skipped_words = array_column($_SESSION['skipped_words'], 'word');
    $placeholders = implode(',', array_fill(0, count($used_words) + count($skipped_words), '?'));
    $query = 'SELECT word FROM words WHERE topic = ? AND word NOT IN (' . $placeholders . ') ORDER BY RANDOM() LIMIT 1';
    $stmt = $db->prepare($query);
    $stmt->bindValue(1, $topic, SQLITE3_TEXT);
    $index = 2;
    foreach (array_merge($used_words, $skipped_words) as $word) {
        $stmt->bindValue($index++, $word, SQLITE3_TEXT);
    }
    $result = $stmt->execute();
    return $result->fetchArray(SQLITE3_ASSOC)['word'];
}

$counter++;
foreach ($_SESSION['skipped_words'] as $key => $skipped_word) {
    if ($counter - $skipped_word['turn'] >= rand(5, 10)) {
        unset($_SESSION['skipped_words'][$key]);
    }
}

$randomWord = getRandomWord($db, $topic);
$_SESSION['used_words'][] = $randomWord;

if (isset($_GET['skip']) && $_GET['skip'] == 'true') {
    $_SESSION['skipped_words'][] = ['word' => $randomWord, 'turn' => $counter];
}
?>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($topic) ?></title>
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
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

        .game {
            display: flex;
            flex-direction: column;
            padding-top: 130px;
        }

        .game__backBtn {
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
        }

        .randomWord {
            display: flex;
            width: 1100px;
            height: 501px;
            background-color: #FFFFFF;
            border-radius: 14px;
            align-items: center;
            justify-content: center;
        }

        .nextWord {
            width: 301px;
            height: 84px;
            font-family: Montserrat-ExtraBold;
            font-size: 28px;
            background-color: #9CFFB2;
            color: #368F49;
            border-radius: 14px;
        }

        .skipWord {
            width: 304px;
            height: 84px;
            font-family: Montserrat-ExtraBold;
            font-size: 28px;
            background-color: #FFB7B7;
            color: #FF3737;
            border-radius: 14px;
        }

        .startTimer {
            width: 428px;
            height: 82px;
            font-family: Montserrat-ExtraBold;
            font-size: 28px;
            background-color: #89B1FF;
            color: #1043A5;
            border-radius: 14px;
        }

        .wordsOps {
            display: flex;
            justify-content: center;
            column-gap: 155px;
            padding-top: 45px;
            padding-bottom: 44px;
        }

        .timer {
            display: flex;
            justify-content: center;
        }

        .word {
            font-family: Montserrat-ExtraBold;
            font-size: 68px;
            color: #00336D;
        }
    </style>
</head>
<body>
<div class="game" id="game">
    <button class="game__backBtn" id="game__backBtn">Вернуться в меню</button>
    <div class="randomWord">
        <p class="word"><?php echo htmlspecialchars($randomWord); ?></p>
    </div>
    <div class="wordsOps">
        <button class="nextWord" onclick="getNextWord()">Следующее</button>
        <button class="skipWord" onclick="getNextWord(true)">Пропустить</button>
    </div>
    <div class="timer">
        <button class="startTimer">Запустить таймер</button>
    </div>
</div>
<script>
    const backBtn = document.getElementById('game__backBtn');

    backBtn.addEventListener('click', () => {
        document.location = './getTopics.php';
        <?php session_destroy(); ?>
    })

    function getNextWord(skip = false) {
        let url = 'game.php?topic=<?php echo urlencode($topic); ?>'
        if (skip) {
            url += '&skip=true';
        }
        fetch(url)
            .then(response => response.text())
            .then(html => {
                document.body.innerHTML = html;
            });
    }
</script>
</body>
</html>
