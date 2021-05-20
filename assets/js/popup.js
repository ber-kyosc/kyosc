const popupBox = document.querySelector('#verif-popup');
const popupBoxInvitation = document.querySelector('#invit-popup');
const cross = document.querySelector('#close-cross');
const crossInvitation = document.querySelector('#close-cross-invit');

function closePopup() {
    popupBox.classList.remove('d-flex');
    popupBox.style.display = 'none';
}

function closeInvitationPopup() {
    popupBoxInvitation.classList.remove('d-flex');
    popupBoxInvitation.style.display = 'none';
}

if (cross) {
    cross.addEventListener('click', closePopup);
}

if (crossInvitation) {
    crossInvitation.addEventListener('click', closeInvitationPopup);
}
