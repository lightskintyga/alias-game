const backBtn = document.getElementById('creator__backBtn');

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
            alert('Тема успешно добавлена!');
            location.reload();
        }
    }
    xhr.send('topic=' + encodeURIComponent(topic) + '&words=' + encodeURIComponent(words));
});