const popupBox = document.querySelector('#verif-popup');
const cross = document.querySelector('#close-cross');

function closePopup() {
    popupBox.classList.remove('d-flex');
    popupBox.style.display = 'none';
}

if (cross) {
    cross.addEventListener('click', closePopup);
}
