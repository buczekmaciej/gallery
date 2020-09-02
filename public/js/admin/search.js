const searchVals = document.getElementsByClassName("search-field");
const search = document.getElementsByClassName("admin-search-inp")[0];

search.onkeyup = () => {
  Array.from(searchVals).forEach((elem) => {
      //TODO: Fix search bug
      //TODO: Hide section label if section gets empty
    console.log(search.value.toLowerCase(), elem.);
    elem.parentElement.style.display = !elem.innerText
      .toLowerCase()
      .includes(search.value.toLowerCase())
      ? "none"
      : "flex";
  });
};
