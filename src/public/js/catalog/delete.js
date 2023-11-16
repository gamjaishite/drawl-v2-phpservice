function deleteCatalog(uuid, title) {
  const xhttp = new XMLHttpRequest();
  xhttp.open("DELETE", `/api/catalog/${uuid}`, true);
  xhttp.setRequestHeader("Content-Type", "application/json");

  xhttp.onreadystatechange = function () {
    if (xhttp.readyState === 4) {
      if (xhttp.status === 200) {
        showToast("Success", `Catalog ${title} deleted`, "success");
        setTimeout(() => {
          window.location.href = "/catalog";
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
}

const deleteTriggerButtons = document.querySelectorAll(
  `.catalog-delete-trigger`
);
deleteTriggerButtons.forEach((deleteTriggerButton) => {
  if (deleteTriggerButton) {
    deleteTriggerButton.addEventListener("click", () => {
      const uuid = deleteTriggerButton.getAttribute("data-uuid");
      const title = deleteTriggerButton.getAttribute("data-title");
      dialog(
        "Delete Catalog",
        `Are you sure you want to delete ${title}?`,
        uuid,
        "delete",
        "Delete",
        () => {
          deleteCatalog(uuid, title);
        }
      );
    });
  }
});
