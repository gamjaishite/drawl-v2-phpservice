dialogTriggers = document.getElementsByClassName("dialog-trigger");
dialogs = document.getElementsByClassName("dialog");
console.log(dialogTriggers);

for (let i = 0; i < dialogTriggers.length; i++) {
  dialogTriggers[i].addEventListener("click", () => {
    dialogs[i].classList.add("is-active");
  });
}

for (let i = 0; i < dialogs.length; i++) {
  dialogs[i].querySelector("#cancel").addEventListener("click", () => {
    dialogs[i].classList.remove("is-active");
  });
}
// if (dialogTrigger) {
//   dialogTrigger.addEventListener("click", () => {
//     document.querySelector(".dialog").classList.add("is-active");
//   });
// }

// dialogClose = document.querySelector(".dialog #cancel");
// if (dialogClose) {
//   dialogClose.addEventListener("click", () => {
//     document.querySelector(".dialog").classList.remove("is-active");
//   });
// }
