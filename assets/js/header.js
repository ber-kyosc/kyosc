const hamburger = document.querySelector('.navbar-toggler');
const menu = document.querySelector('.sidenav');
const menuOverlay = document.querySelector('.nav-menu-overlay');

function openNav() {
    menu.style.width = '250px';
    menuOverlay.style.display = 'block';
}

function closeNav() {
    menu.style.width = '0';
    menuOverlay.style.display = 'none';
}

if (hamburger) {
    hamburger.addEventListener('click', openNav);
}

if (menuOverlay) {
    menuOverlay.addEventListener('click', closeNav);
}
