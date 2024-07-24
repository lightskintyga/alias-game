const backBtn = document.getElementById('creator__backBtn');
const saveBtn = document.getElementById('creator__saveBtn');
const closeBtn = document.getElementById('modal__closeBtn');
const topic = document.getElementById('topic');
const words = document.getElementById('words');
const modal = document.getElementById('modal');
const overlay = document.getElementById('overlay');

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

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'db/createTopic.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            console.log('Тема успешно добавлена!');
        }
    }
    xhr.send('topic=' + encodeURIComponent(topic) + '&words=' + encodeURIComponent(words));
});

saveBtn.addEventListener('click', () => {
    overlay.style.display = 'block';
    modal.style.display = 'flex';
})

closeBtn.addEventListener('click', () => {
    location.reload();
})