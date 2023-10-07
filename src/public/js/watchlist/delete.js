const btnDelete = document.querySelector("button.btn__delete");

btnDelete.addEventListener("click", () => {
    let data = {
        watchlistUUID: btnDelete.dataset.id,
    }
    data = JSON.stringify(data);

    const xhttp = new XMLHttpRequest();

    xhttp.open("DELETE", "/api/watchlist", true);
    xhttp.setRequestHeader("Content-Type", "application/json");

    xhttp.onreadystatechange = function () {
        if (xhttp.readyState === 4) {
            const response = JSON.parse(xhttp.response);
            if (xhttp.status !== 200) {
                showToast("Failed to Delete Watchlist", response.message);
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
})