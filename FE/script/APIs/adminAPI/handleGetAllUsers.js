function convertTime(utcTimestamp) {
    const dateUtc = new Date(utcTimestamp);
    const utcMilliseconds = dateUtc.getTime();
    const gmtPlus7Offset = 7 * 60 * 60 * 1000;
    const gmtPlus7Milliseconds = utcMilliseconds + gmtPlus7Offset;
    const dateGmtPlus7 = new Date(gmtPlus7Milliseconds);
    return dateGmtPlus7.toLocaleDateString();
}
function getAllUsers() {
    const URL = "http://localhost:8000/user/all-user";
    fetch(URL, {
        method: 'GET',
        headers: {
          'Authorization': `Bearer ${sessionStorage.getItem("token")}`,
          'Content-Type': 'application/json',
        }
      })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log(data.data);
            displayUsers(data.data);
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
}

getAllUsers();

const displayUsers = (users) => {
    const container = document.getElementById('users-container');
    let html = "";
    document.getElementById("total-users").textContent = users.length;
    users.forEach((user, index) => {
        html += `<div
        class="flex flex-row text-base text-black bg-white p-[20px] border border-[#d1d1d1] rounded-lg mt-2.5">
        `;
        html += `
        <div class="min-w-[15%]">${user.user_name}</div>
              <div class="w-[10%]">${user.user_id}</div>
              <div class="w-[25%]">${user.email}</div>
              <div class="w-[20%]">${convertTime(user.created_at)}</div>
              <div class="w-[15%]">${user.role}</div>
              <div
                class="w-[15%] flex flex-row justify-start items-center text-xl gap-4"
              >
                <i class="fa-solid fa-circle-info cursor-pointer"></i>
                <i class="fa-solid fa-user-xmark cursor-pointer"></i>
                <i class="fa-solid fa-user-minus cursor-pointer"></i>
              </div>
            </div>
        `;
        container.innerHTML = html;
    })
}
