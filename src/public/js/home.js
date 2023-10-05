const btnSort = document.querySelector(".btn-sort");
const sortAsc = document.querySelector(".btn-sort-asc");
const sortDesc = document.querySelector(".btn-sort-desc");
const images = document.querySelectorAll(".poster");
const order = document.querySelector("#order");
const btnApply = document.querySelector("#btn-apply");
const watchlists = document.querySelector(".list__watchlist");
const inputSearch = document.querySelector(".input-search");

if (btnSort) {
    btnSort.addEventListener("click", () => {
        sortAsc.classList.toggle("hidden");
        sortDesc.classList.toggle("hidden");
        order.value = order.value == "asc" ? "desc" : "asc";
    });
}

function like() {
    const btnLikes = document.querySelectorAll(".btn__like");
    btnLikes.forEach(btn => {
        let liked = btn.dataset.liked;
        btn.addEventListener("click", () => {
            const iconLike = btn.querySelector('.icon-heart');
            const likeCountEl = document.querySelector(`span[data-id='${btn.dataset.id}']`);
            if (!liked) {
                likeCountEl.innerHTML = `${parseInt(likeCountEl.textContent) + 1}`;
                iconLike.dataset.type = "filled";
            } else {
                likeCountEl.innerHTML = `${parseInt(likeCountEl.textContent) - 1}`;
                iconLike.dataset.type = "unfilled";
            }
            liked = !liked;

            let data = {
                "watchlistUUID": btn.dataset.id
            }
            data = JSON.stringify(data);

            const xhttp = new XMLHttpRequest();
            xhttp.open("POST", "/api/watchlist/like", true);
            xhttp.setRequestHeader("Content-Type", "application/json");

            xhttp.onreadystatechange = function () {
                if (this.readyState === 4) {
                    console.log(this.response);
                }
            }

            xhttp.send(data);
        })
    })
}

function save() {
    const btnSaves = document.querySelectorAll(".btn__save");
    btnSaves.forEach(btn => {
        let saved = btn.dataset.saved;
        btn.addEventListener("click", () => {
            const iconSaved = btn.querySelector(".icon-bookmark");
            if (!saved) {
                iconSaved.dataset.type = "filled";
            } else {
                iconSaved.dataset.type = "unfilled";
            }
            saved = !saved;

            let data = {
                "watchlistUUID": btn.dataset.id
            }
            data = JSON.stringify(data);

            const xhttp = new XMLHttpRequest();
            xhttp.open("POST", "/api/watchlist/save", true);
            xhttp.setRequestHeader("Content-Type", "application/json");

            xhttp.onreadystatechange = function () {
                if (this.readyState === 4) {
                    console.log(this.response);
                }
            }

            xhttp.send(data);
        })
    })
}

const debounce = (fn, delay) => {
    let timer;
    return function () {
        clearTimeout(timer);
        timer = setTimeout(() => {
            fn();
        }, delay);
    }
}

const fetchWatchlist = (url) => {
    const loading = document.createElement("div");
    loading.classList.add('loading');
    loading.innerHTML = 'Loading...';
    watchlists.innerHTML = ""
    watchlists.appendChild(loading);

    const xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState === 4) {
            watchlists.innerHTML = "";
            if (!this.response) {
                const noRes = document.createElement("div");
                noRes.classList.add("loading");
                noRes.innerHTML = "No Results Found.";
                watchlists.appendChild(noRes);
            } else {
                watchlists.innerHTML = this.response;
                like();
                save();
                console.log('test');
            }
        }
    }
    xhttp.open("GET", url, true);
    xhttp.send();
}

let search = () => {
    const urlString = window.location.href;
    const url = new URL(urlString);
    url.pathname = '/api/watchlists';

    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set("search", inputSearch.value);
    urlParams.delete("page");

    fetchWatchlist(`${url.origin}${url.pathname}?${urlParams.toString()}`);
}

search = debounce(search, 500);

inputSearch.addEventListener("keyup", search);

like();
save();

