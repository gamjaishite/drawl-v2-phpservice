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

function showDialog(uuid, title) {
  const body = document.querySelector("body");

  const dialog = document.createElement("div");
  dialog.classList.add("dialog");
  dialog.id = `dialog-${uuid}`;
  dialog.innerHTML = `
        <div class="dialog__content">
            <h2>
                Delete Catalog
            </h2>
            <p>
                Are you sure you want to delete ${title}?
            </p>
            <div class="dialog__button-container">
                <button id="cancel">
                    Cancel
                </button>
                <button id="delete" class="btn-bold">
                    Delete
                </button>
            </div>
        </div>
    `;

  body.appendChild(dialog);

  const cancelButton = dialog.querySelector("#cancel");
  cancelButton.addEventListener("click", () => {
    dialog.remove();
  });

  const deleteButton = dialog.querySelector("#delete");
  deleteButton.addEventListener("click", () => {
    dialog.remove();
    const xhttp = new XMLHttpRequest();
    xhttp.open("DELETE", `/api/catalog/${uuid}/delete`, true);
    xhttp.setRequestHeader("Content-Type", "application/json");

    xhttp.onreadystatechange = function () {
      if (xhttp.readyState === 4) {
        if (xhttp.status === 200) {
          showToast("Success", `Catalog ${title} deleted`, "success");
          setTimeout(() => {
            window.location.reload();
          }, 1000);
        } else {
          try {
            const response = JSON.parse(xhttp.responseText);
            showToast("Error", response.message, "error");
          } catch (error) {
            showToast("Error", "Something went wrong", "error");
          }
        }
      }
    };

    xhttp.send();
  });
}

const deleteTriggerButtons = document.querySelectorAll(
  `.catalog-delete-trigger`
);
deleteTriggerButtons.forEach((deleteTriggerButton) => {
  if (deleteTriggerButton) {
    deleteTriggerButton.addEventListener("click", () => {
      const uuid = deleteTriggerButton.getAttribute("data-uuid");
      const title = deleteTriggerButton.getAttribute("data-title");
      showDialog(uuid, title);
    });
  }
});
