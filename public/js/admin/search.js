const searchVals = document.getElementsByClassName("search-field");
const search = document.getElementsByClassName("admin-search-inp")[0];

search.onkeyup = () => {
  Array.from(searchVals).forEach((elem) => {
    elem.style.display =
      elem.children[0].innerText
        .toLowerCase()
        .includes(search.value.toLowerCase()) ||
      elem.children[1].innerText
        .toLowerCase()
        .includes(search.value.toLowerCase())
        ? "flex"
        : "none";

    if (window.location.pathname == "/admin/users") {
      if (!elem.nextSibling.nextSibling) {
        let hidden = 0;
        Array.from(elem.parentElement.children).forEach((child) => {
          if (
            child.classList.contains("search-field") &&
            child.getAttribute("style") == "display: none;"
          ) {
            hidden++;
          }
        });
        elem.parentElement.children[0].style.display =
          hidden == elem.parentElement.children.length - 1 ? "none" : "flex";
      }
    }
  });
};
