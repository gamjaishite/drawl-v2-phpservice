function deleteAccount() {
  const xhttp = new XMLHttpRequest();
  xhttp.open("DELETE", `/api/auth/delete`, true);
  xhttp.setRequestHeader("Content-Type", "application/json");

  xhttp.onreadystatechange = function () {
    if (xhttp.readyState === 4) {
      if (xhttp.status === 200) {
        showToast("Success", `User deleted`, "success");
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
}

const deleteTriggerButton = document.getElementById("delete-account");
if (deleteTriggerButton) {
  deleteTriggerButton.addEventListener("click", () => {
    dialog(
      "Delete Account",
      `Are you sure you want to delete your account?`,
      "delete-account",
      "delete",
      "Delete",
      () => {
        deleteAccount();
      }
    );
  });
}

function updateAccount(form) {
  const xhttp = new XMLHttpRequest();
  xhttp.open("PUT", `/api/auth/update`, true);
  xhttp.setRequestHeader("Content-Type", "application/json");

  xhttp.onreadystatechange = function () {
    if (xhttp.readyState === 4) {
      const response = JSON.parse(xhttp.responseText);
      if (xhttp.status === 200 && response.status === 200) {
        showToast("Success", `Profile updated`, "success");
        nameText = document.getElementById("name");
        nameText.innerText = response.name;
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

  xhttp.send(
    JSON.stringify({
      name: form.name.value,
      oldPassword: form.oldPassword.value,
      newPassword: form.newPassword.value,
    })
  );
}

const form = document.getElementById("profile-edit-form");
form.addEventListener("submit", function (event) {
  event.preventDefault();

  dialog(
    "Update Account",
    `Are you sure you want to update your account?`,
    "update",
    "update",
    "Confirm",
    () => {
      updateAccount(form);
    }
  );
});
