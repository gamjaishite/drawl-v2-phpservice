navbarToggle = document.getElementById("navbar-toggle");
if (navbarToggle) {
  navbarToggle.addEventListener("click", function () {
    navbarMenu = document.getElementById("navbar-menu");
    if (navbarMenu.classList.contains("collapsed")) {
      navbarMenu.classList.remove("collapsed");
      navbarToggle.focus();
      this.setAttribute("aria-expanded", "true");
    } else {
      navbarMenu.classList.add("collapsed");
      navbarToggle.blur();
      this.setAttribute("aria-expanded", "false");
    }
  });

  navbarToggle.addEventListener("blur", function (e) {
    if (e.relatedTarget && e.relatedTarget.parentElement.id === "navbar-menu") {
      return;
    } else {
      navbarMenu = document.getElementById("navbar-menu");
      navbarMenu.classList.add("collapsed");
      navbarToggle.blur();
      this.setAttribute("aria-expanded", "false");
    }
  });
}
