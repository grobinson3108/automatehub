/*
 * Set Theme (force bright/light mode for all users)
 *
 */

let lHtml = document.documentElement;
let rememberDarkMode = !lHtml.classList.contains("dark-custom-defined");
let rememberTheme = lHtml.classList.contains("remember-theme");

if (rememberDarkMode) {
  // Force light mode for all users
  localStorage.setItem("oneuiDarkMode", "off");
  lHtml.classList.remove("dark");
}

if (rememberTheme) {
  let colorTheme = localStorage.getItem("oneuiColorTheme");

  // Set Color Theme
  if (colorTheme) {
    let themeEl = document.getElementById("css-theme");

    if (themeEl && colorTheme === "default") {
      themeEl.parentNode.removeChild(themeEl);
    } else {
      if (themeEl) {
        themeEl.setAttribute("href", colorTheme);
      } else {
        let themeLink = document.createElement("link");

        themeLink.id = "css-theme";
        themeLink.setAttribute("rel", "stylesheet");
        themeLink.setAttribute("href", colorTheme);

        document
          .getElementById("css-main")
          .insertAdjacentElement("afterend", themeLink);
      }
    }
  }
}
