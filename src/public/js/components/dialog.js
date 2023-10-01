dialogTrigger = document.querySelector(".dialog-trigger");

if (dialogTrigger) {
  dialogTrigger.addEventListener("click", () => {
    document.querySelector(".dialog").classList.add("is-active");
  });
}

dialogClose = document.querySelector(".dialog #cancel");
if (dialogClose) {
  dialogClose.addEventListener("click", () => {
    document.querySelector(".dialog").classList.remove("is-active");
  });
}
