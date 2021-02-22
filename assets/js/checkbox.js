function checkBoxClickHandler(event) {
    event.preventDefault();
    const div = event.target.closest('[data-checkBoxContainer]');
    const input = div.querySelector('input');
    if (input.checked) {
        input.checked = false;
        input.removeAttribute('checked');
        div.classList.remove('bg-warning');
    } else {
        input.checked = true;
        div.classList.add('bg-warning');
    }
}

const checkBoxes = document.querySelectorAll('[data-checkBoxContainer]');
checkBoxes.forEach(
    (checkBox) => {
        checkBox.addEventListener('click', checkBoxClickHandler);
    },
);
