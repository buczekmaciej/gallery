const navBar = document.getElementsByClassName("navbar")[0];

if (navBar) {
  const menu = document.getElementsByClassName("fa-ellipsis-h")[0];
  let click = 1;
  const dropDown = document.getElementsByClassName("drop-menu")[0];
  menu.addEventListener("click", function() {
    if (click === 1) {
      menu.style.transform = "rotate(-90deg)";
      dropDown.classList.add("expanded");
      click++;
    } else {
      menu.style.transform = "rotate(0deg)";
      dropDown.classList.remove("expanded");
      click--;
    }
  });
}
