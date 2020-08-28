if (
  window.location.pathname == "/login" ||
  window.location.pathname == "/register"
) {
  let show = document.getElementsByClassName("showBtn")[0];

  show.onclick = () => showPassword();

  function showPassword() {
    let password = document.getElementsByClassName("pass")[0];

    password.getAttribute("type") == "password"
      ? password.setAttribute("type", "text")
      : password.setAttribute("type", "password");
  }
}
