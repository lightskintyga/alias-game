const backBtn = document.getElementById('menu__BackBtn');
const createBtn = document.getElementById('menu__variants-createBtn');

backBtn.addEventListener('click', () => {
    document.location = 'index.html';
})

createBtn.addEventListener('click', () => {
    document.location = 'createTopic.html';
})