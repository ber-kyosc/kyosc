const languageToggle = document.querySelector('.footer-flag-dropdown');
const languageSelect = document.querySelector('.flag-choice-dropdown');
const languageContainer = document.querySelector('.dropdown-flag-container');

function toggleFlag() {
    if (languageSelect.classList.contains('flag-hide')) {
        languageSelect.classList.remove('flag-hide');
        languageSelect.classList.add('flag-show');
        languageContainer.style.marginTop = '-5%';
    } else {
        languageSelect.classList.remove('flag-show');
        languageSelect.classList.add('flag-hide');
        languageContainer.style.marginTop = 'auto';
    }
}

languageToggle.addEventListener('click', toggleFlag);
