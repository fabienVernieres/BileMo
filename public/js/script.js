let coll = document.getElementsByClassName("table-heading");
let i;
window.onload = function () {
  for (i = 0; i < coll.length; i++) {
    coll[i].addEventListener("click", function () {
      this.classList.toggle("active");
      let content = this.nextElementSibling;
      if (content.style.visibility === "visible") {
        content.style.visibility = "hidden";
        content.style.height = "0";
        content.style.opacity = "0";
      } else {
        content.style.visibility = "visible";
        content.style.height = "auto";
        content.style.opacity = "1";
      }
    });
  }
};
