const btnSort = document.querySelector('.btn-sort');
const sortAsc = document.querySelector('.btn-sort-asc');
const sortDesc = document.querySelector('.btn-sort-desc');
const images = document.querySelectorAll('.catalog-list-cover-image');

btnSort.addEventListener('click', () => {
    sortAsc.classList.toggle('hidden');
    sortDesc.classList.toggle('hidden');
})

images.forEach((element, i) => {
    element.style.zIndex = `${images.length - i}`;
});


