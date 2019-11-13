const sideNav = document.getElementsByClassName("nav")[0];

if (sideNav) {
  for (let i = 0; i < sideNav.children.length; i++) {
    let child = sideNav.children[i];

    child.addEventListener("click", function() {
      let active = document.getElementsByClassName("expanded")[0];
      child.children[1].classList.toggle("expanded");
      if (active) {
        active.classList.remove("expanded");
      }
    });
  }
}
