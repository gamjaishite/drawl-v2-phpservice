const CATEGORY = ["ANIME", "DRAMA"];

function validateInput(formData, update = false) {
  if (!formData.get("title") || formData.get("title").trim() === "") {
    return {
      valid: false,
      message: "Title is required.",
    };
  }
  if (formData.get("title").length > 40) {
    return {
      valid: false,
      message: "Title is too long. Maximum 40 chars.",
    };
  }
  if (formData.get("description") && formData.get("description").length > 255) {
    return {
      valid: false,
      message: "Description is too long. Maximum 255 chars.",
    };
  }
  if (
    !formData.get("category") ||
    !CATEGORY.includes(formData.get("category").trim())
  ) {
    return {
      valid: false,
      message: "Category is invalid.",
    };
  }

  if (!update && !formData.get("poster").name) {
    return {
      valid: false,
      message: "Poster is required.",
    };
  }
  return {
    valid: true,
  };
}

function createCatalogReq(form) {
  const apiUrl = `/api/v2/catalog-request`;
  const formData = new FormData(form);

  const validate = validateInput(formData);

  if (!validate.valid) {
    showToast("Error", validate.message, "error");
    return;
  }

  const xhttp = new XMLHttpRequest();
  xhttp.open("POST", apiUrl, true);

  xhttp.onreadystatechange = function () {
    if (xhttp.readyState === 4) {
      const response = JSON.parse(xhttp.responseText);
      if (xhttp.status === 200 && response.status === 200) {
        showToast(
          "Success",
          `Request for catalog ${response.data.title} created`,
          "success"
        );
        setTimeout(() => {
          window.location.href = `/catalog`;
        }, [1000]);
      } else {
        try {
          showToast("Error", response.message);
        } catch (e) {
          showToast("Error", "Something went wrong", "error");
        }
      }
    }
  };

  xhttp.send(formData);
}

const form = document.querySelector("form#catalog-create-update");

form.addEventListener("submit", function (event) {
  event.preventDefault();

  dialog(
    "Request Catalog",
    `Are you sure you want to request this catalog?`,
    "request",
    "request",
    "Confirm",
    () => {
      createCatalogReq(form);
    }
  );
});

posterImg = document.querySelector("img#poster");
if (posterImg) {
  posterInput = document.querySelector("input#posterField");
  posterInput.addEventListener("change", function (event) {
    if (this.files[0]) {
      var reader = new FileReader();

      reader.readAsDataURL(this.files[0]);

      reader.onloadend = function () {
        posterImg.src = reader.result;
      };
    }
  });
}

trailerVideo = document.querySelector("video#trailer");
if (trailerVideo) {
  trailerInput = document.querySelector("input#trailerField");
  trailerInput.addEventListener("change", function (event) {
    if (this.files[0]) {
      var reader = new FileReader();

      reader.readAsDataURL(this.files[0]);

      reader.onloadend = function () {
        trailerVideo.src = reader.result;
      };
    }
  });
}
