function showToast(title, message, type = "error") {
    const toast = document.getElementById("toast");
    if (toast) {
        toast.classList.remove("hidden");
        toast.setAttribute("data-type", type);
        h3 = toast.querySelector("h3");
        h3.textContent = title;
        p = toast.querySelector("p");
        p.textContent = message;
    }
}

function dialog(
    title,
    message,
    dialogId,
    actionId,
    actionButtonText,
    onaction
) {
    const body = document.querySelector("body");

    const dialog = document.createElement("div");
    dialog.classList.add("dialog");
    dialog.id = `dialog-${dialogId}`;
    dialog.innerHTML = `
        <div class="dialog__content">
            <h2>
                ${title}
            </h2>
            <p>
                ${message}
            </p>
            <div class="dialog__button-container">
                <button id="cancel">
                    Cancel
                </button>
                <button id=${actionId} class="btn-bold">
                    ${actionButtonText}
                </button>
            </div>
        </div>
    `;

    body.appendChild(dialog);

    const cancelButton = dialog.querySelector("#cancel");
    cancelButton.addEventListener("click", () => {
        dialog.remove();
    });

    const actionButton = dialog.querySelector(`#${actionId}`);
    actionButton.addEventListener("click", () => {
        dialog.remove();
        onaction();
    });
}

// Lik and Save Watchlist

function like() {
    const btnLikes = document.querySelectorAll(".btn__like");
    btnLikes.forEach(btn => {
        if (btn.dataset.id) {
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
                        console.log(JSON.parse(this.response));
                    }
                }

                xhttp.send(data);
            })
        }
    })
}

function save() {
    const btnSaves = document.querySelectorAll(".btn__save");
    btnSaves.forEach(btn => {
        if (btn.dataset.id) {
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
                        console.log(JSON.parse(this.response));
                    }
                }

                xhttp.send(data);
            })
        }
    })
}

toastClose = document.querySelector("#toast button");

if (toastClose) {
    toastClose.addEventListener("click", () => {
        const toast = document.querySelector("#toast");
        toast.classList.add("hidden");
    });
}

logoutBtn = document.querySelector("button#logout");
if (logoutBtn) {
    logoutBtn.addEventListener("click", () => {
        const xhttp = new XMLHttpRequest();
        xhttp.open("POST", "/api/auth/logout", true);
        xhttp.setRequestHeader("Content-Type", "application/json");

        xhttp.onreadystatechange = function () {
            if (this.readyState === 4) {
                if (xhttp.status === 200) {
                    showToast("Success", "Logout success", "success");
                    setTimeout(() => {
                        window.location.href = `/`;
                    }, [1000]);
                } else {
                    try {
                        const response = JSON.parse(xhttp.responseText);
                        showToast("Error", response.message);
                    } catch (e) {
                        showToast("Error", "Something went wrong", "error");
                    }
                }
            }
        };

        xhttp.send();
    });
}
