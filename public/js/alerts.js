const alertBox = document.getElementsByClassName("alert")[0];

if (alertBox) {
  setInterval(() => {
    alertBox.classList.add("close");
    alertBox.addEventListener("animationend", function() {
      alertBox.parentElement.removeChild(alertBox);
      alertBox.removeEventListener();
      clearInterval();
    });
  }, 5000);
}
