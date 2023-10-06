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

const form = document.getElementById("catalog-edit-form");

form.addEventListener("submit", function (event) {
  event.preventDefault();

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
  xhttp.setRequestHeader("Content-Type", "multipart/form-data");

  xhttp.onreadystatechange = function () {
    if (xhttp.readyState === 4) {
      if (xhttp.status === 200) {
        showToast("Success", "Catalog updated", "success");
        window.location.href = `/catalog/${uuid}`;
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
});
