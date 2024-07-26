<?php
// Запуск сессии
session_start();

// Подключение к бд
$db = new mysqli('localhost', 'teacher', 'real_teacher', 'alias');

// Проверка подключения
if ($db->connect_error) {
    die('Ошибка подключения: ' . $db->connect_error);
}

// Получение названия темы
$topic = $_GET['topic'];

// Инициализация в сессии массива для угаданных слов
if (!isset($_SESSION['used_words'])) {
    $_SESSION['used_words'] = [];
}

// Инициализация в сессии массива для пропущенных слов
if (!isset($_SESSION['skipped_words'])) {
    $_SESSION['skipped_words'] = [];
}

// Инициализация в сессии счетчика
if (!isset($_SESSION['counter'])) {
    $_SESSION['counter'] = 0;
}

// Инициализация в сессии текущего слова
if (!isset($_SESSION['current_word'])) {
    $_SESSION['current_word'] = '';
}

// Алгоритм выдачи случайного слова из бд
function getRandomWord($db, $topic) {
    // Получение массива использованных слов из сессии
    $used_words = $_SESSION['used_words'];

    // Получение массива пропущенных слов из сессии (извлекаем только сами слова)
    $skipped_words = array_column($_SESSION['skipped_words'], 'word');

    // Создание строки из вопросительных знаков для использования в запросе SQL
    // Количество знаков определяется количеством использованных и пропущенных слов
    $placeholders = implode(',', array_fill(0, count($used_words) + count($skipped_words), '?'));

    // Формирование запроса SQL с учетом темы и исключением использованных и пропущенных слов
    $query = 'SELECT word FROM words WHERE topic = ? AND word NOT IN (' . $placeholders . ') ORDER BY RAND() LIMIT 1';

    // Подготовка SQL-запроса к выполнению
    $stmt = $db->prepare($query);

    // Объединение параметров для запроса (тема, использованные и пропущенные слова)
    $params = array_merge([$topic], $used_words, $skipped_words);

    // Определение типов параметров для bind_param (все параметры строковые)
    $types = str_repeat('s', count($params));

    // Привязывание параметров к подготовленному запросу
    $stmt->bind_param($types, ...$params);

    // Выполнение запроса
    $stmt->execute();

    // Получение результата запроса
    $result = $stmt->get_result();

    // Извлечение первой строки результата в виде ассоциативного массива
    $row = $result->fetch_assoc();

    // Возвращение слова, если оно найдено, иначе false
    return $row ? $row['word'] : false;
}

$_SESSION['counter']++;
// Алгоритм удаления слова из списка пропущенных в общую раздачу спустя 5-10 угаданных слов
foreach ($_SESSION['skipped_words'] as $key => $skipped_word) {
    if ($_SESSION['counter'] - $skipped_word['turn'] >= rand(5, 10)) {
        unset($_SESSION['skipped_words'][$key]);
    }
}

// Если слово пропущено, то оно добавляется в массив пропущенных слов
if (isset($_GET['skip']) && $_GET['skip'] == 'true') {
    $_SESSION['skipped_words'][] = ['word' => $_SESSION['current_word'], 'turn' => $_SESSION['counter']];
} else { // Если слово угадано, то оно добавляется в массив использованных (угаданных) слов
    $_SESSION['used_words'][] = $_SESSION['current_word'];
}

// Получение случайного слова из бд
$randomWord = getRandomWord($db, $topic);
if ($randomWord) {
    $_SESSION['current_word'] = $randomWord;
} else { // Если слова в выдаче закончились, то сессия уничтожается
    echo 'Все слова угаданы!';
    session_destroy();
    exit;
}

// Используется для ajax-запроса
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
            height: 471px;
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
            padding-top: 35px;
            padding-bottom: 34px;
        }

        .timer {
            display: flex;
            justify-content: center;
            position: relative;
        }

        .word {
            font-family: Montserrat-ExtraBold;
            font-size: 68px;
            color: #00336D;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            background-color: rgba(0, 0, 0, 0.4);
            width: 100%;
            height: 100%;
            z-index: 3;
        }

        .modal {
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: space-evenly;
            position: fixed;
            width: 752px;
            height: 364px;
            z-index: 4;
            background-color: #FFFFFF;
            border-radius: 12px;
        }

        .modal__msg {
            font-family: Montserrat-ExtraBold;
            font-size: 32px;
            width: 408px;
            text-align: center;
        }

        .modal__closeBtn {
            width: 165px;
            height: 60px;
            background-color: #F79810;
            font-family: Montserrat-ExtraBold;
            font-size: 32px;
            color: #FFFFFF;
            border-radius: 12px;
            transition: 0.3s;
        }

        .modal__closeBtn:hover {
            background-color: #EA8111;
        }

        .timer__settingsBtn {
            background-image: url("img/settingsTimer.svg");
            width: 64px;
            height: 57px;
            transition: 0.3s;
            margin-top: 12px;
            position: absolute;
            top: 0;
            right: 23%;
        }

        .timer__settingsBtn:hover {
            background-image: url("img/settingsTimer-hover.svg");
        }

        .timer__settings {
            width: 659px;
            height: 284px;
            background-color: #C3E6FF;
            display: none;
            flex-direction: column;
            position: absolute;
            bottom: 110px;
            border-radius: 12px;
            align-items: center;
            justify-content: space-around;
        }

        .timer__settingsHeader {
            font-family: Montserrat-ExtraBold;
            font-size: 28px;
            color: #00336D;
        }

        .timer__settingsLabel {
            font-family: Montserrat-ExtraBold;
            font-size: 20px;
            color: #00336D;
            width: 270px;
        }

        .timer__settingsInput {
            width: 125px;
            height: 43px;
            background-color: #FFFFFF;
            font-family: Montserrat-ExtraBold;
            font-size: 20px;
            color: #00336D;
            padding-left: 10px;
        }

        .timer__settingsSaveBtn {
            width: 170px;
            height: 44px;
            background-color: #00336D;
            color: #FFFFFF;
            font-family: Montserrat-ExtraBold;
            font-size: 20px;
            border-radius: 12px;
            transition: 0.3s;
        }

        .timer__settingsSaveBtn:hover {
            background-color: #2E629E;
        }

        .timer__settingsSetSeconds {
            display: flex;
            flex-direction: row;
            align-self: flex-start;
            padding-left: 46px;
        }

        .timerDisplay {
            display: none;
            font-family: Montserrat-ExtraBold;
            font-size: 64px;
            color: #00336D;
            height: 82px;
        }

        .score {
            font-family: Montserrat-ExtraBold;
            font-size: 38px;
            color: #00336D;
            text-align: center;
            padding-top: 34px;
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
                touch-action: manipulation;
            }

            .nextWord {
                width: 147px;
                height: 45px;
                font-size: 14px;
                touch-action: manipulation;
            }

            .skipWord {
                width: 155px;
                height: 45px;
                font-size: 14px;
                touch-action: manipulation;
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

            .score {
                padding-top: 16px;
                font-size: 16px;
            }

            .timer {
                align-items: center;
                width: 316px;
                justify-content: unset;
                column-gap: 17px;
            }

            .timer__settingsBtn {
                position: unset;
                background-image: url("img/settingsTimerMobile.svg");
                width: 35px;
                height: 37px;
                margin: 0;
                touch-action: manipulation;
            }

            .timer__settingsBtn:hover {
                background-image: url("img/settingsTimerMobile.svg");
                width: 35px;
                height: 37px;
            }

            .timer__settings {
                width: 308px;
                height: 259px;
                bottom: 62px;
                left: 4px;
            }

            .timer__settingsHeader {
                font-size: 18px;
                width: 162px;
                text-align: center;
            }

            .timer__settingsSetSeconds {
                padding-left: 29px;
            }

            .timer__settingsLabel {
                font-size: 14px;
                width: 173px;
                height: unset;
                margin-right: 19px;
            }

            .timer__settingsInput {
                width: 55px;
                height: 32px;
                font-size: 14px;
            }

            .timer__settingsSaveBtn {
                width: 141px;
                height: 36px;
                font-size: 16px;
                touch-action: manipulation;
            }

            .timerDisplay {
                font-size: 34px;
                height: 45px;
                margin: 0 auto;
            }

            .modal {
                width: 316px;
                height: 258px;
            }

            .modal__msg {
                font-size: 20px;
            }

            .modal__closeBtn {
                width: 100px;
                height: 38px;
                font-size: 22px;
                touch-action: manipulation;
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
    <div class="score" id="score">Угадано слов: 0</div>
    <div class="wordsOps">
        <button class="nextWord" onclick="getNextWord()">Следующее</button>
        <button class="skipWord" onclick="getNextWord(true)">Пропустить</button>
    </div>
    <div class="timer">
        <button class="startTimer" id="startTimer">Запустить таймер</button>
        <div class="timerDisplay" id="timerDisplay">1:00</div>
        <button class="timer__settingsBtn" id="timer__settingsBtn"></button>
        <div class="timer__settings" id="timer__settings">
            <h3 class="timer__settingsHeader">Настройка таймера:</h3>
            <div class="timer__settingsSetSeconds">
                <label for="customSeconds" class="timer__settingsLabel">Введите количество секунд:</label>
                <input type="number" id="customSeconds" class="timer__settingsInput" value="60">
            </div>
            <button class="timer__settingsSaveBtn" id="timer__settingsSaveBtn">Применить</button>
        </div>
    </div>
</div>
<div class="overlay" id="overlay"></div>
<div class="modal" id="modal">
    <p class="modal__msg">Все слова использованы!</p>
    <div class="modal__msg" id="scoreModal">Угадано слов: 0</div>
    <button class="modal__closeBtn" id="modal__closeBtn">Ок</button>
</div>
<div class="modal" id="modal__timer">
    <p class="modal__msg">Время истекло!</p>
    <button class="modal__closeBtn" id="modal__closeBtnTimer">Ок</button>
</div>
<script>
    const backBtn = document.getElementById('game__backBtn');
    const closeBtn = document.getElementById('modal__closeBtn');
    const closeTimerBtn = document.getElementById('modal__closeBtnTimer');
    const overlay = document.getElementById('overlay');
    const modal = document.getElementById('modal');
    const modalTimer = document.getElementById('modal__timer');
    const timerSettingsBtn = document.getElementById('timer__settingsBtn');
    const timerSettings = document.getElementById('timer__settings');
    const timerSettingsSaveBtn = document.getElementById('timer__settingsSaveBtn');
    const startTimer = document.getElementById('startTimer');
    const customSecondsInput = document.getElementById('customSeconds');
    const timerDisplay = document.getElementById('timerDisplay');
    const score = document.getElementById('score');
    const scoreModal = document.getElementById('scoreModal');

    let countdown;
    let totalSeconds = 60;
    let guessedWords = 0;

    // Переход к странице со всеми темами
    backBtn.addEventListener('click', () => {
        fetch('destroy_session.php')
            .then(() => {
                document.location = './getTopics.php';
            });
    });

    // Отображение следующего слова
    function getNextWord(skip = false) {
        let url = 'game.php?topic=<?php echo urlencode($topic); ?>&ajax=true'
        incrementScore(skip);
        if (skip) {
            url += '&skip=true';
        }
        fetch(url)
            .then(response => response.text())
            .then(word => {
                if (word === 'Все слова угаданы!') {
                    console.log(word);
                    showModal();
                } else {
                    document.getElementById('currentWord').innerText = word;
                }
            });
    }

    // Отображение модального окна
    function showModal() {
        overlay.style.display = 'block';
        modal.style.display = 'flex';
    }

    // Обновление отображения таймера
    function updateTimerDisplay() {
        const minutes = Math.floor(totalSeconds / 60);
        const seconds = totalSeconds % 60;
        timerDisplay.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
    }

    // Увеличение счетчика угаданных слов, если была нажата кнопка "следующее", а не "пропустить"
    function incrementScore(skip) {
        if (skip === false) {
            guessedWords++;

            score.textContent = `Угадано слов: ${guessedWords}`;
            scoreModal.textContent = `Угадано слов: ${guessedWords}`;
        }
    }

    // Запуск таймера
    startTimer.addEventListener('click', () => {
        // Убираем кнопки запуска таймера и его настроек, отображаем сам таймер
        startTimer.style.display = 'none';
        timerSettingsBtn.style.display = 'none';
        timerDisplay.style.display = 'unset';

        // Очищение любого ранее запущенного интервала, чтобы избежать наложения таймеров
        clearInterval(countdown);

        // Получение значения секунд из пользовательского ввода
        const customSeconds = parseInt(customSecondsInput.value, 10);

        // Если значение введено и оно положительное, устанавливаем его как общее количество секунд
        if (!isNaN(customSeconds) && customSeconds > 0) {
            totalSeconds = customSeconds;
        }

        // Обновление отображения таймера на экране
        updateTimerDisplay();

        // Установка нового интервала, который будет уменьшать общее количество секунд каждую секунду
        countdown = setInterval(() => {
            if (totalSeconds > 0) {
                totalSeconds--;
                // Обновление отображения таймера на экране каждую секунду
                updateTimerDisplay();
            } else {
                // Остановка таймера, когда он достигнет нуля
                clearInterval(countdown);
                // Возвращение кнопок запуска и настроек таймера, скрываем сам таймер
                startTimer.style.display = 'unset';
                timerSettingsBtn.style.display = 'unset';
                timerDisplay.style.display = 'none';
                // Отображение модального окна, информирующего пользователя о завершении таймера
                overlay.style.display = 'block';
                modalTimer.style.display = 'flex';
            }
        }, 1000); // Интервал в 1 секунду
    })

    // Переход к странице со всеми темами
    closeBtn.addEventListener('click', () => {
        window.location = './getTopics.php';
    })

    // Отображение настроек таймера
    timerSettingsBtn.addEventListener('click', () => {
        timerSettings.style.display = 'flex';
    })

    // Закрытие окна с настройками таймера
    timerSettingsSaveBtn.addEventListener('click', () => {
        timerSettings.style.display = 'none';
    })

    // Закрытие модального окна
    closeTimerBtn.addEventListener('click', () => {
        overlay.style.display = 'none';
        modalTimer.style.display = 'none';
    })
</script>
</body>
</html>