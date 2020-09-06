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
        .includes(search.value.toLowerCase()) ||
      elem.children[2].innerText
        .toLowerCase()
        .includes(search.value.toLowerCase())
        ? "flex"
        : "none";

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
  });
};

const sectionLabels = document.getElementsByClassName("section-label");

window.onload = () => {
  if (!document.getElementsByClassName("showing")[0])
    document
      .getElementsByClassName("reports-subsection")[0]
      .classList.add("showing");
};

Array.from(sectionLabels).forEach(
  (lab) =>
    (lab.onclick = () => {
      if (!lab.parentElement.classList.contains("showing")) {
        document
          .getElementsByClassName("showing")[0]
          .classList.remove("showing");
        lab.parentElement.classList.add("showing");
      }
    })
);
