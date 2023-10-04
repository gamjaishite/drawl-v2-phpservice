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
            }
        }
    }
    xhttp.open("GET", url, true);
    xhttp.send();
}

let search = () => {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set("search", inputSearch.value);
    urlParams.delete("page");
    if (urlParams.get("ajax") == null) {
        urlParams.append("ajax", "true");
    } else {
        urlParams.set("ajax", "true");
    }
    window.history.pushState({}, '', `?${urlParams.toString()}`);
    fetchWatchlist(window.location.href);
}

search = debounce(search, 500);

inputSearch.addEventListener("keyup", search);