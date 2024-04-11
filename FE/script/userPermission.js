function handlePermission() {
  if (
    sessionStorage.getItem("role") !== "STUDENT" &&
    sessionStorage.getItem("role") !== "CUSTOMER" &&
    sessionStorage.getItem("role") !== "ADMIN"
  ) {
    alert("You need to login");
    window.location.href = "./login.html";
  }
}

handlePermission();
