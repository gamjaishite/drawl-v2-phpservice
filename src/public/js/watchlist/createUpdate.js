const formUpdateWatchlist = document.querySelector("form#update-watchlist");
const VISIBILITY = ["PUBLIC", "PRIVATE"];

function validateWatchlistCreateUpdateRequest(request) {
    if (!request.title || request.title.trim() === "") {
        return {
            valid: false,
            message: "Title is required."
        };
    }
    if (request.title.length > 40) {
        return {
            valid: false,
            message: "Title is too long. Maximum 40 chars."
        }
    }
    if (request.description && request.description.length > 255) {
        return {
            valid: false,
            message: "Description is too long. Maximum 255 chars."
        }
    }
    if (!request.visibility || !VISIBILITY.includes(request.visibility.trim())) {
        return {
            valid: false,
            message: "Visibility is invalid."
        }
    }
    if (!request.items || request.items.length === 0) {
        return {
            valid: false,
            message: "Watchlist must contain 1 item."
        }
    }
    if (request.items.length > 50) {
        return {
            valid: false,
            message: "Too many items. Maximum 50 items."
        }
    }

    let maxDescExceeded = false;
    let title;
    for (let i = 0; i < request.items.length; i++) {
        if (request.items[i].description.length > 255) {
            title = request.items[i].title;
            maxDescExceeded = true;
            break;
        }
    }
    if (maxDescExceeded) {
        return {
            valid: false,
            message: `Description is too long for item ${title}. Maximum 255 chars.`
        }
    }
    return {
        valid: true
    }
}

function createEditWatchlist() {
    // Parse url
    const currentUrl = window.location.href;
    const url = new URL(currentUrl);
    const urlPathname = url.pathname.split("/");
    const action = urlPathname[urlPathname.length - 1];

    const titleEl = document.querySelector("input#title");
    const descriptionEl = document.querySelector("textarea#description");
    const visibilityEl = document.querySelector("input#visibility");
    const itemsEl = document.querySelectorAll("div.watchlist-item");
    const tagsEl = document.querySelectorAll("input.watchlist-tag");

    const title = titleEl.value;
    const description = descriptionEl.value;
    const visibility = visibilityEl.value;
    const items = [];
    const tags = [];

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

    tagsEl.forEach(item => {
        if (item.checked) {
            tags.push({
                id: item.value
            })
        }
    })

    // validate request
    let data = {
        title,
        description,
        visibility,
        items,
        tags
    }

    let validationResult = validateWatchlistCreateUpdateRequest(data);
    if (!validationResult.valid) {
        showToast("Invalid Request", validationResult.message);
        return;
    }

    // ajax request
    if (action === "edit") {
        data["watchlistUUID"] = url.pathname.split("/")[2];
    }

    data = JSON.stringify(data);

    const xhttp = new XMLHttpRequest();

    xhttp.open(action === "create" ? "POST" : "PUT", "/api/watchlist", true);
    xhttp.setRequestHeader("Content-Type", "application/json");

    xhttp.onreadystatechange = function () {
        if (xhttp.readyState === 4) {
            const response = JSON.parse(xhttp.response);
            if (xhttp.status !== 200) {
                showToast("Invalid Request", response.message)
            } else {
                showToast("Success", response.message, "success");
                setTimeout(() => {
                    if (response.redirectTo !== null && response.redirectTo !== undefined) {
                        window.location.href = response.redirectTo;
                        window.history.pushState({}, "", response.redirectTo);
                        window.location.reload();
                    }
                }, 1000)
            }
        }
    }

    xhttp.send(data);
}

formUpdateWatchlist.addEventListener("submit", e => {
    e.preventDefault();

    dialog(
        "Update Watchlist",
        `Are you sure you want to update this watchlist?`,
        "update",
        "update",
        "Confirm",
        () => {
            createEditWatchlist();
        }
    );


})