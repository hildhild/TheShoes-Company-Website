document
  .querySelector(".header__navbar-icon")
  .addEventListener("click", function () {
    document
      .querySelector(".header__navbar-container")
      .classList.toggle("custom-hidden");
  });

var token = getStoredToken();

var historyLink = document.getElementById("historyLink");
var historyItem = document.getElementById("HistoryID");

if (token) {
  historyItem.style.display = "block";
} else {
  historyItem.style.display = "none";
}

function getStoredToken() {
  return sessionStorage.getItem("token");
}
