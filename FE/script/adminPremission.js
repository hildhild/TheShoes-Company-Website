function handlePermission() {
  if (sessionStorage.getItem("role") !== "ADMIN") {
    window.location.href = "./404.html";
  }
}

handlePermission();
