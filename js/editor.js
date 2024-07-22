const backBtn = document.getElementById('menu__BackBtn');
const createBtn = document.getElementById('menu__variants-createBtn');
const editBtn = document.getElementById('menu__variants-editBtn');

backBtn.addEventListener('click', () => {
    document.location = 'index.html';
})

createBtn.addEventListener('click', () => {
    document.location = 'createTopic.html';
})

editBtn.addEventListener('click', () => {
    document.location = 'db/editTopic.php';
})