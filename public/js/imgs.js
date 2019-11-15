const images = document.getElementsByClassName("img-display");

if (images) {
  for (let i = 0; i < images.length; i++) {
    let image = images[i];
    image.addEventListener("click", function() {
      const displayer = document.createElement("div");
      displayer.classList.add("displayer");

      const dispContent = document.createElement("div");
      dispContent.classList.add("display-content");

      const close = document.createElement("i");
      close.classList.add("fas");
      close.classList.add("fa-times");
      dispContent.appendChild(close);

      const content = document.createElement("img");
      content.setAttribute("src", image.getAttribute("src"));
      content.classList.add("displayer-image");
      dispContent.appendChild(content);

      displayer.appendChild(dispContent);
      document.body.insertBefore(displayer, document.body.firstChild);

      close.addEventListener("click", function() {
        document.body.removeChild(displayer);
      });
    });
  }
}
