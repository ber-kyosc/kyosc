const toggleDetailsShow = document.querySelector('.ch-cookie-consent__toggle-details-show');
const toggleDetailsHide = document.querySelector('.ch-cookie-consent__toggle-details-hide');
const allButton = document.querySelector('#cookie_consent_use_all_cookies');
const noButton = document.querySelector('#cookie_consent_use_only_functional_cookies');
const preferencesButton = document.querySelector('#cookie_consent_save');

function showPreferencesButton() {
    preferencesButton.style.display = 'block';
    allButton.style.display = 'none';
    noButton.style.display = 'none';
}

function hidePreferencesButton() {
    preferencesButton.style.display = 'none';
    allButton.style.display = 'block';
    noButton.style.display = 'block';
}

toggleDetailsShow.addEventListener('click', showPreferencesButton);
toggleDetailsHide.addEventListener('click', hidePreferencesButton);
