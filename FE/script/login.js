function handleHeader() {
  var token = sessionStorage.getItem("token");
  var user_name = sessionStorage.getItem("user_name");

  var loginButton = document.getElementById("loginbutton1.1");
  var logoutButton = document.getElementById("logoutbutton1.1");
  var userNameField = document.getElementById("userName");
  // var HistoryNav = document.getElementById("HistoryID");
  
  userNameField.textContent = user_name;

  if (token) {
    loginButton.classList.add("hidden");
    logoutButton.classList.add("inline-block");
    userNameField.classList.add("inline-block");
    // HistoryNav.classList.add("inline-block");
  } else {
    loginButton.classList.add("inline-block");
    logoutButton.classList.add("hidden");
    userNameField.classList.add("hidden");
    // HistoryNav.classList.add("hidden");
  }

  logoutButton.addEventListener("click", function () {
    sessionStorage.removeItem("token");
    sessionStorage.removeItem("user_id");
    sessionStorage.removeItem("user_name");
    sessionStorage.removeItem("email");
    sessionStorage.removeItem("role");
    location.reload();
    console.log("Clickl logout");
  });
}
handleHeader();

function handleLogin() {
  event.preventDefault();

  var emailValue = document.getElementById("email").value;
  var passwordValue = document.getElementById("password").value;

  if (emailValue && passwordValue) {
    var data = {
      email: emailValue,
      password: passwordValue,
    };

    console.log(data);
    fetch("http://localhost:8000/auth/login", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    })
      .then((response) => response.json())
      .then((data) => {
        console.log(data);
        sessionStorage.setItem("token", data.token);
        sessionStorage.setItem("user_id", data.data.user_id);
        sessionStorage.setItem("user_name", data.data.user_name);
        sessionStorage.setItem("email", data.data.email);
        sessionStorage.setItem("role", data.data.role);

        handleHeader();
        if (data.data.role === "ADMIN") {
          window.location.href = "../pages/admin/";
        } else {
          window.location.href = "../index.html";
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        sessionStorage.removeItem("token");
        sessionStorage.removeItem("user_id");
        sessionStorage.removeItem("user_name");
        sessionStorage.removeItem("email");
        sessionStorage.removeItem("role");
        location.reload();

        alert("Login Failed");
      });
  } else {
    alert("Please enter both email and password.");
  }
}
