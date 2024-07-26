<?php
// Подключение к бд
$db = new mysqli('localhost', 'teacher', 'real_teacher', 'alias');

// Проверка подключения
if ($db->connect_error) {
    die('Ошибка подключения: ' . $db->connect_error);
}

// Получение всех тем
$result = $db->query('SELECT topic FROM topics');
$topics = [];

// Добавление каждой темы в массив
while ($row = $result->fetch_assoc()) {
    $topics[] = $row['topic'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получение названия выбранной темы для редактирования
    $selected_topic = $_POST['topic'];

    // Получение слов из выбранной темы
    $stmt = $db->prepare('SELECT word FROM words WHERE topic = ?');
    $stmt->bind_param('s', $selected_topic);
    $stmt->execute();
    $result = $stmt->get_result();

    // Создание массива со словами
    $words = [];
    while ($row = $result->fetch_assoc()) {
        $words[] = $row['word'];
    }
    // Закрытие подключения
    $stmt->close();
} else { // Очистка данных
    $selected_topic = '';
    $words = [];
}

// Закрытие подключения
$db->close();
?>

<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Редактировать темы</title>
    <style>
        @import "../css/reset.css";

        @font-face {
            font-family: Montserrat-ExtraBold;
            src: url("../fonts/MontserratAlternates-ExtraBold.ttf");
        }

        @font-face {
            font-family: Montserrat-Medium;
            src: url("../fonts/MontserratAlternates-Medium.ttf");
        }

        @font-face {
            font-family: Montserrat-SemiBold;
            src: url("../fonts/MontserratAlternates-SemiBold.ttf");
        }

        body {
            background-image: url("../img/background.png");
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .topics {
            display: flex;
            position: relative;
            flex-direction: column;
            background-color: #FFFFFF;
            width: 83%;
            height: auto;
            border-radius: 14px;
            margin-bottom: 50px;
            margin-top: 80px;
            padding-left: 192px;
        }

        .topics__backBtn {
            width: 269px;
            height: 50px;
            position: absolute;
            top: 60px;
            right: 64px;
            font-family: Montserrat-ExtraBold;
            font-size: 20px;
            color: #F79810;
            border: 5px solid #F79810;
            border-radius: 12px;
            transition: 0.3s;
        }

        .topics__backBtn:hover {
            color: #FFFFFF;
            background-color: #F79810;
        }

        #words {
            width: 1004px;
            height: 632px;
            border: #00336D 3px solid;
            border-radius: 12px;
            font-family: Montserrat-Medium;
            font-size: 24px;
            padding-left: 20px;
            margin-bottom: 40px;
            padding-top: 15px;
        }

        .topics__header {
            font-family: Montserrat-ExtraBold;
            font-size: 40px;
            padding-top: 66px;
            padding-bottom: 134px;
        }

        .option__default {
            width: 501px;
            height: 52px;
            background-color: #C3E6FF;
            border: #000000 1px solid;
            font-family: Montserrat-Medium;
            font-size: 24px;
            padding-left: 20px;
            margin-bottom: 95px;
        }

        .topics__container {
            position: relative;
        }

        .topics__saveBtn {
            width: 241px;
            height: 52px;
            font-family: Montserrat-ExtraBold;
            font-size: 24px;
            color: #FFFFFF;
            background-color: #F79810;
            border-radius: 14px;
            position: absolute;
            top: 0;
            left: 534px;
            opacity: 0.7;
            transition: opacity 0.3s;
        }
        
        .topics__saveBtn:hover {
            cursor: not-allowed;
        }

        .topics__saveBtn.active {
            opacity: 1;
            cursor: pointer;
            transition: 0.3s;
        }

        .topics__saveBtn.active:hover {
            background-color: #EA8111;
        }

        .topics__deleteBtn {
            font-family: Montserrat-ExtraBold;
            font-size: 24px;
            width: 195px;
            height: 78px;
            background-color: #ED6666;
            color: #FFFFFF;
            border-radius: 14px;
            position: absolute;
            top: -13px;
            left: 810px;
            transition: 0.3s;
        }

        .topics__deleteBtn:hover {
            background-color: #CF4B4B;
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
    </style>
</head>
<body>
<div class="topics">
    <h3 class="topics__header">Редактировать темы</h3>
    <div class="topics__container">
        <form method="post">
            <select name="topic" id="topic" class="option__default" onchange="this.form.submit()">
                <option value="">Выберите тему</option>
                <?php
                foreach ($topics as $topic):
                    ?>
                    <!-- Каждая тема - отдельная опция в меню -->
                    <option value="<?= htmlspecialchars($topic) ?>" <?= $selected_topic === $topic ? 'selected' : '' ?>><?= htmlspecialchars($topic) ?></option>
                <?php
                endforeach;
                ?>
            </select>
        </form>
        <?php
        if ($selected_topic): // Если была выбрана какая-то тема
            ?>
            <form id="saveForm">
                <input type="hidden" name="topic" value="<?= htmlspecialchars($selected_topic) ?>">
                <!-- Отображение слов из выбранной темы -->
                <textarea name="words" id="words" class="words__textarea" oninput="handleInput()" onfocus="handleInput()"><?= htmlspecialchars(implode("\n", $words)) ?></textarea>
                <button type="button" class="topics__saveBtn" id="topics__saveBtn" disabled onclick="saveWords()">Сохранить</button>
            </form>
            <button class="topics__deleteBtn" id="deleteButton" onclick="deleteTopic()">Удалить тему</button>
        <?php
        endif;
        ?>
    </div>
    <button class="topics__backBtn" id="topics__backBtn">Вернуться назад</button>
</div>
<div class="overlay" id="overlay"></div>
<div class="modal" id="modal__saveChanges">
    <p class="modal__msg">Изменения выполнены успешно!</p>
    <button class="modal__closeBtn" id="modal__closeSaveChangesBtn">Ок</button>
</div>
<div class="modal" id="modal__deleteTopic">
    <p class="modal__msg">Тема успешно удалена!</p>
    <button class="modal__closeBtn" id="modal__closeDeleteTopicBtn">Ок</button>
</div>
<script>
    const backBtn = document.getElementById('topics__backBtn');
    const saveBtn = document.getElementById('topics__saveBtn');
    const deleteTopicBtn = document.getElementById('deleteButton');
    const closeSaveChangesBtn = document.getElementById('modal__closeSaveChangesBtn');
    const closeDeleteTopicBtn = document.getElementById('modal__closeDeleteTopicBtn');
    const overlay = document.getElementById('overlay');
    const modalDeleteTopic = document.getElementById('modal__deleteTopic');
    const modalSaveChanges = document.getElementById('modal__saveChanges');

    // Переход к стартовой странице админки
    backBtn.addEventListener('click', () => {
        document.location = '../editor.html';
    })

    // Автоматическое масштабирование текстового поля
    function autoResize(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 40 + 'px';
    }

    // Проверка внесения изменений в текстовое поле
    function handleInput() {
        const textarea = document.getElementById('words');
        const saveBtn = document.getElementById('topics__saveBtn');
        autoResize(textarea);
        if (textarea.value.trim() !== textarea.defaultValue.trim()) {
            saveBtn.classList.add('active');
            saveBtn.disabled = false;
        } else {
            saveBtn.classList.remove('active');
            saveBtn.disabled = true;
        }
    }

    // Отслеживание поведения текстового поля
    document.addEventListener('DOMContentLoaded', () => {
        const textarea = document.getElementById('words');
        if (textarea) {
            autoResize(textarea);
            handleInput();
        }
    })

    // Отправка post-запроса в файл save_words.php
    function saveWords() {
        const form = document.getElementById('saveForm');
        const formData = new FormData(form);

        fetch('save_words.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.text())
            .then(data => {
                console.log('Изменения выполнены успешно!');
            })
            .catch(error => {
                console.error('Error: ', error);
            })
    }

    // Отправка post-запроса в файл delete_topic.php
    function deleteTopic() {
        const form = document.getElementById('saveForm');
        const formData = new FormData(form);

        fetch('delete_topic.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.text())
            .then(data => {
                console.log('Тема успешно удалена!');
            })
            .catch(error => {
                console.error('Error: ', error);
            })
    }

    // Вывод модального окна об удалении темы
    deleteTopicBtn.addEventListener('click', () => {
        overlay.style.display = 'block';
        modalDeleteTopic.style.display = 'flex';
    })

    // Перезагрузка страницы после закрытия модального окна
    closeDeleteTopicBtn.addEventListener('click', () => {
        window.location = 'editTopic.php';
    })

    // Вывод модального окна о сохранении изменений в текстовом поле
    saveBtn.addEventListener('click', () => {
        overlay.style.display = 'block';
        modalSaveChanges.style.display = 'flex';
    })

    // Перезагрузка страницы после закрытия модального окна
    closeSaveChangesBtn.addEventListener('click', () => {
        window.location = 'editTopic.php';
    })
</script>
</body>
</html>
