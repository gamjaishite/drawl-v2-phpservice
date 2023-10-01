btnClose = document.querySelector(".alert button");

if (btnClose) {
  btnClose.addEventListener("click", () => {
    document.querySelector(".alert").remove();
  });
}
