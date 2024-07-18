const settingsBtn = document.getElementById('menu__settingsBtn');
const playBtn = document.getElementById('menu__playBtn');

settingsBtn.addEventListener('click', () => {
    document.location = 'admin/login.html';
})

playBtn.addEventListener('click', () => {
    document.location = 'getTopics.php';
})