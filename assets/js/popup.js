const popupBox = document.querySelector('#verif-popup');
const popupBoxInvitation = document.querySelector('#invit-popup');
const popupBoxRequest = document.querySelector('#request-popup');
const cross = document.querySelector('#close-cross');
const crossInvitation = document.querySelector('#close-cross-invit');
const crossRequest = document.querySelector('#close-cross-request');

function closePopup() {
    popupBox.classList.remove('d-flex');
    popupBox.style.display = 'none';
}

function closeInvitationPopup() {
    popupBoxInvitation.classList.remove('d-flex');
    popupBoxInvitation.style.display = 'none';
}

function closeRequestPopup() {
    popupBoxRequest.classList.remove('d-flex');
    popupBoxRequest.style.display = 'none';
}

if (cross) {
    cross.addEventListener('click', closePopup);
}

if (crossInvitation) {
    crossInvitation.addEventListener('click', closeInvitationPopup);
}

if (crossRequest) {
    crossRequest.addEventListener('click', closeRequestPopup);
}
