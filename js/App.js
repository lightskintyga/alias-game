const settingsBtn = document.getElementById('menu__settingsBtn');
const playBtn = document.getElementById('menu__playBtn');

// Переход к странице авторизации
settingsBtn.addEventListener('click', () => {
    document.location = 'admin/login.html';
})

// Переход к странице с темами
playBtn.addEventListener('click', () => {
    document.location = 'getTopics.php';
})