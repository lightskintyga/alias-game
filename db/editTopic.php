<?php
$db = new SQLite3('main.db');

$result = $db->query('SELECT topic FROM topics');
$topics = [];

while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $topics[] = $row['topic'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_topic = $_POST['topic'];

    $stmt = $db->prepare('SELECT word FROM words WHERE topic = :topic');
    $stmt->bindValue(':topic', $selected_topic);
    $result = $stmt->execute();
    $words = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $words[] = $row['word'];
    }
} else {
    $selected_topic = '';
    $words = [];
}
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

        .topics__saveBtn {
            width: 241px;
            height: 52px;
            font-family: Montserrat-ExtraBold;
            font-size: 24px;
            color: #FFFFFF;
            background-color: #F79810;
            border-radius: 14px;
            position: absolute;
            opacity: 0.7;
            transition: opacity 0.3s;
        }

        .topics__saveBtn.active {
            opacity: 1;
        }
    </style>
</head>
<body>
<div class="topics">
    <h3 class="topics__header">Редактировать темы</h3>
    <form method="post">
        <select name="topic" id="topic" class="option__default" onchange="this.form.submit()">
            <option value="">Выберите тему</option>
            <?php
            foreach ($topics as $topic):
            ?>
            <option value="<?= htmlspecialchars($topic) ?>" <?= $selected_topic === $topic ? 'selected' : '' ?>><?= htmlspecialchars($topic) ?></option>
            <?php
            endforeach;
            ?>
        </select>
    </form>
    <?php
    if ($selected_topic):
    ?>
    <form method="post" action="save_words.php">
        <input type="hidden" name="topic" value="<?= htmlspecialchars($selected_topic) ?>">
        <textarea name="words" id="words" class="words__textarea" oninput="handleInput()" onfocus="handleInput()"><?= htmlspecialchars(implode("\n", $words)) ?></textarea>
        <button type="submit" class="topics__saveBtn" id="topics__saveBtn" disabled>Сохранить</button>
    </form>
    <?php
    endif;
    ?>
        <button class="topics__backBtn" id="topics__backBtn">Вернуться назад</button>
</div>
<script>
    const backBtn = document.getElementById('topics__backBtn');

    backBtn.addEventListener('click', () => {
        document.location = '../editor.html';
    })

    function autoResize(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 40 + 'px';
    }

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

    document.addEventListener('DOMContentLoaded', () => {
        const textarea = document.getElementById('words');
        if (textarea) {
            autoResize(textarea);
            handleInput();
        }
    })
</script>
</body>
</html>
