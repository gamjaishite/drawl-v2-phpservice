function updateCatalog(form) {
  const urlParts = window.location.pathname.split("/");
  const uuidIndex = urlParts.indexOf("catalog") + 1;
  const uuid = urlParts[uuidIndex];
  const apiUrl = `/api/catalog/${uuid}/update`;
  console.log(form);
  const formData = new FormData(form);

  formData.forEach((value, key) => {
    console.log(`${key}: ${value}`);
  });

  const xhttp = new XMLHttpRequest();
  xhttp.open("POST", apiUrl, true);

  xhttp.onreadystatechange = function () {
    if (xhttp.readyState === 4) {
      console.log(xhttp);
      if (xhttp.status === 200) {
        showToast("Success", "Catalog updated", "success");
        setTimeout(() => {
          window.location.href = `/catalog/${uuid}`;
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

  xhttp.send(formData);
}

const form = document.getElementById("catalog-edit-form");

form.addEventListener("submit", function (event) {
  event.preventDefault();

  dialog(
    "Update Catalog",
    `Are you sure you want to update this catalog?`,
    "update",
    "update",
    "Update",
    () => {
      updateCatalog(form);
    }
  );
});
