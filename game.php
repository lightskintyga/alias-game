<?php
session_start();
$db = new mysqli('localhost', 'teacher', 'real_teacher', 'main');

if ($db->connect_error) {
    die('Ошибка подключения: ' . $db->connect_error);
}

$topic = $_GET['topic'];

if (!isset($_SESSION['used_words'])) {
    $_SESSION['used_words'] = [];
}

if (!isset($_SESSION['skipped_words'])) {
    $_SESSION['skipped_words'] = [];
}

if (!isset($_SESSION['counter'])) {
    $_SESSION['counter'] = 0;
}

if (!isset($_SESSION['current_word'])) {
    $_SESSION['current_word'] = '';
}

function getRandomWord($db, $topic) {
    $used_words = $_SESSION['used_words'];
    $skipped_words = array_column($_SESSION['skipped_words'], 'word');
    $placeholders = implode(',', array_fill(0, count($used_words) + count($skipped_words), '?'));
    $query = 'SELECT word FROM words WHERE topic = ? AND word NOT IN (' . $placeholders . ') ORDER BY RAND() LIMIT 1';
    $stmt = $db->prepare($query);
    $params = array_merge([$topic], $used_words, $skipped_words);
    $types = str_repeat('s', count($params));

    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $row ? $row['word'] : false;
}

$_SESSION['counter']++;
foreach ($_SESSION['skipped_words'] as $key => $skipped_word) {
    if ($_SESSION['counter'] - $skipped_word['turn'] >= rand(5, 10)) {
        unset($_SESSION['skipped_words'][$key]);
    }
}

if (isset($_GET['skip']) && $_GET['skip'] == 'true') {
    $_SESSION['skipped_words'][] = ['word' => $_SESSION['current_word'], 'turn' => $_SESSION['counter']];
} else {
    $_SESSION['used_words'][] = $_SESSION['current_word'];
}

$randomWord = getRandomWord($db, $topic);
if ($randomWord) {
    $_SESSION['current_word'] = $randomWord;
} else {
    echo 'Все слова угаданы!';
    session_destroy();
    exit;
}

if (isset($_GET['ajax'])) {
    echo htmlspecialchars($_SESSION['current_word']);
    exit;
}

$db->close();
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
            background-image: url("img/background.png");
            background-size: cover;
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
            transition: 0.3s;
        }

        .game__backBtn:hover {
            background-color: #2E629E;
        }

        .randomWord {
            display: flex;
            width: 1100px;
            height: 501px;
            background-color: #FFFFFF;
            border-radius: 14px;
            align-items: center;
            justify-content: center;
            box-shadow: 0px 0px 8px 0px rgba(34, 60, 80, 0.2);
        }

        .nextWord {
            width: 301px;
            height: 84px;
            font-family: Montserrat-ExtraBold;
            font-size: 28px;
            background-color: #9CFFB2;
            color: #368F49;
            border-radius: 14px;
            border: #4AB361 3px solid;
            transition: 0.3s;
        }
        
        .nextWord:hover {
            color: #226F33;
            background-color: #75E48D;
        }

        .skipWord {
            width: 304px;
            height: 84px;
            font-family: Montserrat-ExtraBold;
            font-size: 28px;
            background-color: #FFB7B7;
            color: #FF3737;
            border-radius: 14px;
            border: #DE6F6F 3px solid;
            transition: 0.3s;
        }

        .skipWord:hover {
            color: #D12727;
            background-color: #FFA5A5;
        }

        .startTimer {
            width: 428px;
            height: 82px;
            font-family: Montserrat-ExtraBold;
            font-size: 28px;
            background-color: #89B1FF;
            color: #1043A5;
            border-radius: 14px;
            border: #337DD2 3px solid;
            transition: 0.3s;
        }

        .startTimer:hover {
            color: #002E87;
            background-color: #6297FF;
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

        @media screen and (max-width: 500px) {
            .randomWord {
                width: 316px;
                height: 222px;
            }

            .word {
                font-size: 24px;
            }

            .game {
                align-items: center;
                padding-top: 90px;
            }

            .game__backBtn {
                width: 162px;
                height: 36px;
                font-size: 12px;
                right: 22px;
                top: 35px;
            }

            .nextWord {
                width: 147px;
                height: 45px;
                font-size: 14px;
            }

            .skipWord {
                width: 155px;
                height: 45px;
                font-size: 14px;
            }

            .startTimer {
                width: 246px;
                height: 45px;
                font-size: 14px;
            }

            .wordsOps {
                column-gap: 14px;
                padding-bottom: 30px;
            }
        }
    </style>
</head>
<body>
<div class="game" id="game">
    <button class="game__backBtn" id="game__backBtn">Вернуться в меню</button>
    <div class="randomWord">
        <p class="word" id="currentWord"><?php echo htmlspecialchars($_SESSION['current_word']); ?></p>
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
        fetch('destroy_session.php')
            .then(() => {
                document.location = './getTopics.php';
            });
    });

    function getNextWord(skip = false) {
        let url = 'game.php?topic=<?php echo urlencode($topic); ?>&ajax=true'
        if (skip) {
            url += '&skip=true';
        }
        fetch(url)
            .then(response => response.text())
            .then(word => {
                if (word === 'Все слова угаданы!') {
                    alert(word);
                    document.location = './getTopics.php';
                } else {
                    document.getElementById('currentWord').innerText = word;
                }
            });
    }
</script>
</body>
</html>