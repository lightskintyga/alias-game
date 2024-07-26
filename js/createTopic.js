const backBtn = document.getElementById('creator__backBtn');
const saveBtn = document.getElementById('creator__saveBtn');
const closeBtn = document.getElementById('modal__closeBtn');
const topic = document.getElementById('topic');
const words = document.getElementById('words');
const modal = document.getElementById('modal');
const overlay = document.getElementById('overlay');
const fileInput = document.getElementById('icon__input');
const fileNameSpan = document.getElementById('fileName');
const tooltipBtn = document.getElementById('tooltip');
const tooltip = document.getElementById('tooltip__info');

// Проверка внесения изменений в инпутах
function handleInput() {
    if (topic.value.trim() !== topic.defaultValue.trim() && words.value.trim() !== topic.defaultValue.trim()) {
        saveBtn.classList.add('active');
        saveBtn.disabled = false;
    } else {
        saveBtn.classList.remove('active');
        saveBtn.disabled = true;
    }
}

// Отслеживание поведения инпутов
document.addEventListener('DOMContentLoaded', () => {
    if (topic && words) {
        handleInput();

        topic.addEventListener('input', handleInput);
        words.addEventListener('input', handleInput);
    }
})

// Переход к стартовой странице админки
backBtn.addEventListener('click', () => {
    document.location = 'editor.html';
})

// ajax-запрос для формы
document.getElementById('creator__form').addEventListener('submit', function(event) {
    event.preventDefault();

    const topic = document.getElementById('topic').value;
    const words = document.getElementById('words').value;
    const fileInput = document.getElementById('icon__input');
    const file = fileInput.files[0];

    // Проверка расширения файла
    if (file) {
        const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
        if (!allowedExtensions.exec(file.name)) {
            return;
        }
    }

    // Добавление данных для дальнейшей передачи
    const formData = new FormData();
    formData.append('topic', topic);
    formData.append('words', words);
    if (file) {
        formData.append('icon', file);
    }

    // post-запрос к файлу createTopic.php
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'db/createTopic.php', true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            console.log('Тема успешно добавлена!');
        }
    }
    xhr.send(formData);
});

// Добавление названия приложенного файла
fileInput.addEventListener('change', () => {
    const file = fileInput.files[0];
    if (file) {
        fileNameSpan.textContent = `Выбран файл: ${file.name}`;
    } else {
        fileNameSpan.textContent = '';
    }
})

// Вывод модального окна о создании новой темы
saveBtn.addEventListener('click', () => {
    overlay.style.display = 'block';
    modal.style.display = 'flex';
})

// Перезагрузка страницы
closeBtn.addEventListener('click', () => {
    location.reload();
})

// Появление подсказки при наведении
tooltipBtn.addEventListener('mouseover', () => {
    tooltip.style.display = 'block';
})

// Отсутствие подсказки, если курсор не на ней
tooltipBtn.addEventListener('mouseout', () => {
    tooltip.style.display = 'none';
})