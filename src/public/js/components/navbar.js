navbarToggle = document.getElementById("navbar-toggle");
if (navbarToggle) {
  navbarToggle.addEventListener("click", function () {
    navbarMenu = document.getElementById("navbar-menu");
    if (navbarMenu.classList.contains("collapsed")) {
      navbarMenu.classList.remove("collapsed");
    } else {
      navbarMenu.classList.add("collapsed");
    }
  });
}
