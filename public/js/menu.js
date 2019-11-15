const navBar = document.getElementsByClassName("navbar")[0];

if (navBar) {
  const menu = document.getElementsByClassName("fa-ellipsis-h")[0];
  let click = 1;
  menu.addEventListener("click", function() {
    if (click === 1) {
      menu.style.transform = "rotate(-90deg)";
      click++;
    } else {
      menu.style.transform = "rotate(0deg)";
      click--;
    }
  });
}
