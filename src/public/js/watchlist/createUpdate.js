const formUpdateWatchlist = document.querySelector("form#update-watchlist");

formUpdateWatchlist.addEventListener("submit", e => {
    e.preventDefault();

    // Parse url
    const currentUrl = window.location.href;
    const url = new URL(currentUrl);
    const urlPathname = url.pathname.split("/");
    const action = urlPathname[urlPathname.length - 1];

    const titleEl = document.querySelector("input#title");
    const descriptionEl = document.querySelector("textarea#description");
    const visibilityEl = document.querySelector("input#visibility");
    const itemsEl = document.querySelectorAll("div.watchlist-item");

    const title = titleEl.value;
    const description = descriptionEl.value;
    const visibility = visibilityEl.value;
    const items = [];

    itemsEl.forEach(item => {
        const itemDescEl = item.querySelector("textarea.watchlist-item__description");
        const titleEl = item.querySelector(".watchlist-item__title");

        const catalogTitle = titleEl.textContent;
        const catalogId = itemDescEl.name.split("__")[0].split("[")[1];
        const catalogUUID = itemDescEl.name.split("__")[1];
        const catalogCategory = itemDescEl.name.split("__")[2].split("]")[0];
        const description = itemDescEl.value;

        items.push({
            "id": catalogId,
            "uuid": catalogUUID,
            "category": catalogCategory,
            "title": catalogTitle,
            description
        })
    })

    // ajax request

    let data = {
        title,
        description,
        visibility,
        items
    }

    if (action === "edit") {
        data["watchlistUUID"] = url.pathname.split("/")[2];
    }

    data = JSON.stringify(data);

    const xhttp = new XMLHttpRequest();

    xhttp.open(action === "create" ? "POST" : "PUT", "/api/watchlist", true);
    xhttp.setRequestHeader("Content-Type", "application/json");

    xhttp.onreadystatechange = function () {
        if (xhttp.readyState === 4) {
            if (xhttp.status !== 200) {
                document.body.innerHTML = xhttp.response;
            } else {
                const response = JSON.parse(xhttp.response);
                if (response.redirectTo !== null && response.redirectTo !== undefined) {
                    window.history.pushState({}, "", "/signin");
                    window.location.reload();
                }
            }
        }
    }

    xhttp.send(data);
})