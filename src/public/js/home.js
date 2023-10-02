const btnSort = document.querySelector(".btn-sort");
const sortAsc = document.querySelector(".btn-sort-asc");
const sortDesc = document.querySelector(".btn-sort-desc");
const images = document.querySelectorAll(".poster");
const order = document.querySelector("#order");
const btnApply = document.querySelector("#btn-apply");

if (btnSort) {
  btnSort.addEventListener("click", () => {
    sortAsc.classList.toggle("hidden");
    sortDesc.classList.toggle("hidden");
    order.value = order.value == "asc" ? "desc" : "asc";
  });
}

if (images) {
  images.forEach((element, i) => {
    element.style.zIndex = `${images.length - i}`;
  });
}
