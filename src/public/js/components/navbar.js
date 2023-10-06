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

profileMenuToggle = document.getElementById("profile-menu-toggle");
if (profileMenuToggle) {
  profileMenuToggle.addEventListener("click", function () {
    profileMenu = document.getElementById("profile-menu");
    if (profileMenu.classList.contains("collapsed")) {
      profileMenu.classList.remove("collapsed");
      profileMenuToggle.focus();
      this.setAttribute("aria-expanded", "true");
    } else {
      profileMenu.classList.add("collapsed");
      profileMenuToggle.blur();
      this.setAttribute("aria-expanded", "false");
    }
  });

  profileMenuToggle.addEventListener("blur", function (e) {
    if (
      e.relatedTarget &&
      e.relatedTarget.parentElement.id === "profile-menu"
    ) {
      return;
    } else {
      profileMenu = document.getElementById("profile-menu");
      profileMenu.classList.add("collapsed");
      profileMenuToggle.blur();
      this.setAttribute("aria-expanded", "false");
    }
  });
}
