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

function handleInput() {
    if (topic.value.trim() !== topic.defaultValue.trim() && words.value.trim() !== topic.defaultValue.trim()) {
        saveBtn.classList.add('active');
        saveBtn.disabled = false;
    } else {
        saveBtn.classList.remove('active');
        saveBtn.disabled = true;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (topic && words) {
        handleInput();

        topic.addEventListener('input', handleInput);
        words.addEventListener('input', handleInput);
    }
})

backBtn.addEventListener('click', () => {
    document.location = 'editor.html';
})

document.getElementById('creator__form').addEventListener('submit', function(event) {
    event.preventDefault();

    const topic = document.getElementById('topic').value;
    const words = document.getElementById('words').value;
    const fileInput = document.getElementById('icon__input');
    const file = fileInput.files[0];

    if (file) {
        const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
        if (!allowedExtensions.exec(file.name)) {
            return;
        }
    }

    const formData = new FormData();
    formData.append('topic', topic);
    formData.append('words', words);
    if (file) {
        formData.append('icon', file);
    }

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'db/createTopic.php', true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            console.log('Тема успешно добавлена!');
        }
    }
    xhr.send(formData);
});

fileInput.addEventListener('change', () => {
    const file = fileInput.files[0];
    if (file) {
        fileNameSpan.textContent = `Выбран файл: ${file.name}`;
    } else {
        fileNameSpan.textContent = '';
    }
})

saveBtn.addEventListener('click', () => {
    overlay.style.display = 'block';
    modal.style.display = 'flex';
})

closeBtn.addEventListener('click', () => {
    location.reload();
})

tooltipBtn.addEventListener('mouseover', () => {
    tooltip.style.display = 'block';
})

tooltipBtn.addEventListener('mouseout', () => {
    tooltip.style.display = 'none';
})