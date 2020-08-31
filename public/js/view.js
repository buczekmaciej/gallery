if (window.location.pathname == "/")
  setListener(document.getElementsByClassName("home-gallery-elem"));

if (window.location.pathname.includes("/explore/"))
  setListener(document.getElementsByClassName("explore-elem"));

function setListener(elems) {
  Array.from(elems).forEach(
    (elem) =>
      (elem.children[0].onclick = () => sendView(elem.getAttribute("data-id")))
  );
}

function sendView(id) {
  let xhr = new XMLHttpRequest();
  xhr.open("POST", `/check-view/${id}`);
  xhr.onreadystatechange = () => {
    if (xhr.readyState == 4) {
      if (xhr.status == 200 || xhr.status == 202)
        console.info(xhr.responseText);
      if (xhr.status == 500 || xhr.status == 404)
        console.error(xhr.responseText);
    }
  };
  xhr.send();
}
