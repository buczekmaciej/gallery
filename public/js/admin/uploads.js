const searchVals = document.getElementsByClassName("search-field");
const search = document.getElementsByClassName("admin-search-inp")[0];

search.onkeyup = () => {
  Array.from(searchVals).forEach((elem) => {
    elem.style.display =
      elem.children[0].innerText
        .toLowerCase()
        .includes(search.value.toLowerCase()) ||
      elem.children[2].innerText
        .toLowerCase()
        .includes(search.value.toLowerCase()) ||
      elem.children[3].innerText
        .toLowerCase()
        .includes(search.value.toLowerCase()) ||
      elem.children[4].innerText
        .toLowerCase()
        .includes(search.value.toLowerCase())
        ? "flex"
        : "none";
  });
};
