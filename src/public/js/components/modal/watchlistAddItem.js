const inputSearch = document.querySelector('.search__input');

inputSearch.addEventListener('keydown', function (e) {
    if (e.keyCode === 13) {
        e.preventDefault();
    }
})
