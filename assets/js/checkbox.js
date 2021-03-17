function checkBoxClickHandler(event) {
    event.preventDefault();
    const div = event.target.closest('[data-checkBoxContainer]');
    const input = div.querySelector('input');
    if (input.checked) {
        input.checked = false;
        input.removeAttribute('checked');
        div.classList.remove('my-sport-check');
    } else {
        input.checked = true;
        div.classList.add('my-sport-check');
    }
}

const checkBoxes = document.querySelectorAll('[data-checkBoxContainer]');
checkBoxes.forEach(
    (checkBox) => {
        checkBox.addEventListener('click', checkBoxClickHandler);
    },
);
