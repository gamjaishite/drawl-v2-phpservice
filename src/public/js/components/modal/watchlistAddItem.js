const PAGE_SIZE = 4;
const PLUS_ICON = `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus"><path d="M5 12h14" /><path d="M12 5v14" /></svg>`

const inputSearch = document.querySelector('.search__input');
const btnGetData = document.querySelector('.get__data');
const searchItems = document.querySelector('.search__items');

let watchlistItemContainer = document.querySelector('.watchlist-items');
let catalogSelected = [];
let page = 1;


function deleteItemAction(id) {
    const item = document.querySelector(`div[data-id="${id}"]`);
    if (item) {
         catalogSelected = catalogSelected.filter((e) => { return e !== id}) ;           
         const btnAddToList = searchItems.querySelector(`button.search-item__action[data-id=${id}]`);
         btnAddToList.innerHTML = PLUS_ICON;
         item.remove();
    }
}

function deleteItem() {
    const btnWatchlistItemDeletes = document.querySelectorAll('.watchlist-item__delete');
    btnWatchlistItemDeletes.forEach(btnWatchlistItemDelete => {
        btnWatchlistItemDelete.addEventListener('click', () => {
            deleteItemAction(btnWatchlistItemDelete.dataset.id);
        })
    })
}

function drag() {
    const watchlistItems = document.querySelectorAll('.watchlist-item')

    watchlistItems.forEach(watchlistItem => {
        watchlistItem.addEventListener('dragstart',  () => {
            setTimeout(() => watchlistItem.classList.add('dragging'), 0);
        });
        watchlistItem.addEventListener('dragend', () => watchlistItem.classList.remove("dragging"));
    });
}

function sortableItems(e) {
    e.preventDefault();
    const afterElement = getDragAfterElement(watchlistItemContainer, e.clientY);
    const draggable = document.querySelector('.dragging');
    if (afterElement === null) {
        watchlistItemContainer.appendChild(draggable);
    } else {
        watchlistItemContainer.insertBefore(draggable, afterElement);
    }
} 

function getDragAfterElement (container, y) {
    const draggableElements = [...container.querySelectorAll('.watchlist-item:not(.dragging)')];

    return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;
        if (offset < 0 && offset > closest.offset) {
            return { offset: offset, element: child }
        }
        return closest;
    }, { offset: Number.NEGATIVE_INFINITY }).element
}

function fetchSearch(replace = false) {
    replace ? page = 1 : page++;

    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState === 4) {
            replace ? searchItems.innerHTML = this.response : searchItems.innerHTML += this.response;
            const btnAddToList = searchItems.querySelectorAll('.search-item__action');
            btnAddToList.forEach(e => {
                if (catalogSelected.includes(e.dataset.id)) {
                    e.innerHTML = '✔️';
                }

                e.addEventListener('click', () => {
                    if (!catalogSelected.includes(e.dataset.id)) {
                        e.innerHTML = '✔️';
                        catalogSelected.push(e.dataset.id);
                        const xhttp = new XMLHttpRequest();
                        xhttp.onreadystatechange = function () {
                            if (this.readyState === 4) {
                               const wrapper = document.createElement('div');
                               wrapper.classList.add('watchlist-item');
                               wrapper.draggable = "true";
                               wrapper.dataset.id = e.dataset.id;
                               wrapper.innerHTML = this.response;
                               watchlistItemContainer.appendChild(wrapper);
                               deleteItem();
                               drag();
                            }
                        }
                        xhttp.open("POST", "/api/watchlist/item?id=" + e.dataset.id, true);
                        xhttp.send();
                    } else {
                        deleteItemAction(e.dataset.id);
                    }
                })
            });
        }
    }
    xhttp.open("GET", `/api/catalog?title=${inputSearch.value}&page=${page}&pageSize=${4}`, true);
    xhttp.send();
}


inputSearch.addEventListener('keydown', function (e) {
    if (e.keyCode === 13) {
        e.preventDefault();
    }
})

inputSearch.addEventListener('keyup', function () {
    fetchSearch(true);
})

searchItems.addEventListener('scroll', () => {
    if (searchItems.scrollHeight - (searchItems.scrollTop + searchItems.clientHeight) < 100) {
       fetchSearch();
    }
})

watchlistItemContainer.addEventListener("dragover", sortableItems);