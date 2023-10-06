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