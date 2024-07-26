const backBtn = document.getElementById('menu__BackBtn');
const createBtn = document.getElementById('menu__variants-createBtn');
const editBtn = document.getElementById('menu__variants-editBtn');

// Переход к стартовой странице игры
backBtn.addEventListener('click', () => {
    document.location = 'index.html';
})

// Переход к странице создания новых тем
createBtn.addEventListener('click', () => {
    document.location = 'createTopic.html';
})

// Переход к странице редактирования тем
editBtn.addEventListener('click', () => {
    document.location = 'db/editTopic.php';
})