const box = document.getElementsByClassName("report-box")[0];

if (box) {
  const reports = document.getElementsByClassName("report");

  Array.from(reports).forEach(
    (rep) => (rep.onclick = () => reportBox(rep.getAttribute("data-id")))
  );
}

function reportBox(id) {
  const repID = document.getElementsByClassName("report-id")[0];
  const sendRep = document.getElementsByClassName("send-rep")[0];
  const note = document.getElementById("note");

  repID.setAttribute("value", id);
  box.style.display = "block";

  if (repID.getAttribute("value") != "") sendRep.removeAttribute("disabled");
  sendRep.onclick = () => {
    let xhr = new XMLHttpRequest();
    let fd = new FormData();
    fd.append(
      "data",
      JSON.stringify({
        id: id,
        reasonId: document.getElementById("reason").value,
        note: note.value,
      })
    );
    xhr.open("POST", `/report`);
    xhr.onreadystatechange = () => {
      if (xhr.readyState == 4) {
        if (xhr.status == 200) {
          box.style.display = "none";
          repID.setAttribute("value", "");
          note.innerText = "";
          console.info("Success");
        } else {
          alert("Error occurred. Check console and contact developer.");
          let parser = new DOMParser();
          console.error(
            parser
              .parseFromString(xhr.responseText, "text/html")
              .getElementsByTagName("title")[0].innerText
          );
        }
      }
    };
    xhr.send(fd);
  };

  document.getElementsByClassName("close-form")[0].onclick = () => {
    box.style.display = "none";
    repID.setAttribute("value", "");
    note.innerText = "";
  };
}
