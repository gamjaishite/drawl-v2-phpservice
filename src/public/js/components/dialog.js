dialogTrigger = document.querySelector(".dialog-trigger");
dialogTrigger.addEventListener("click", () => {
  document.querySelector(".dialog").classList.add("is-active");
});

dialogClose = document.querySelector(".dialog #cancel");
dialogClose.addEventListener("click", () => {
  document.querySelector(".dialog").classList.remove("is-active");
});
