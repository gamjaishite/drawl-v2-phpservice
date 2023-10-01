const selects = document.querySelectorAll('.c-select-menu');

selects.forEach(select => {
    const selectBtn = select.querySelector('.c-select-btn-text');
    select.addEventListener('click', () => {
        options = select.querySelector('.c-select-options');
        options.classList.toggle('c-select-hide');
        option = options.querySelectorAll('.c-select-option');
        input = select.querySelector('#' + select.id);
        option.forEach(optn => {
            optn.addEventListener('click', (event) => {
                selectBtn.textContent = event.currentTarget.innerText;
                input.value = event.currentTarget.innerText.trim();
            })
        })
    })
});