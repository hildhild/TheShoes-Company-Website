function handleRegister() {
  event.preventDefault();

  const nameValue = document.getElementById("register-name").value;
  const emailValue = document.getElementById("register-email").value;
  const passwordValue = document.getElementById("register-password").value;
  const confirmPasswordValue = document.getElementById(
    "register-password-confirm"
  ).value;

  if (nameValue && emailValue && passwordValue && confirmPasswordValue) {
    if (passwordValue !== confirmPasswordValue) {
      alert("Passwords do not match.");
      return;
    }

    const data = {
      user_name: nameValue,
      email: emailValue,
      password: passwordValue,
      confirm_password: confirmPasswordValue,
    };

    console.log("Sending data to the server:", data);

    fetch("http://localhost:8000/auth/register", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    })
      .then((response) => response.json())
      .then((data) => {
        console.log(data);
        alert("Registration successful. Please log in.");
        // Redirect to login page or handle as needed
        location.reload();
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Registration failed. Please try again.");
        location.reload();
      });
  } else {
    alert("Please fill in all fields.");
  }
}
