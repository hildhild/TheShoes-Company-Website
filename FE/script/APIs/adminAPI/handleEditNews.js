document.getElementById("grid-title").value = localStorage.getItem("title");
document.getElementById("grid-content").value = localStorage.getItem("content");
document.getElementById("grid-img-link").value = localStorage.getItem("img");

localStorage.removeItem("title");
localStorage.removeItem("content");
localStorage.removeItem("img");
const id = localStorage.getItem("id");

document.getElementById("edit-form").addEventListener('submit', (e) => {
    e.preventDefault();
    const title = document.getElementById("grid-title").value;
    const content = document.getElementById("grid-content").value;
    const img = document.getElementById("grid-img-link").value;
    handleEditNews({
        title: title,
        content: content,
        image_url: img,
        user_id: sessionStorage.getItem("user_id")
    })
    window.location.href = `./NewsDetailAdmin.html?id=${id}`;
})


localStorage.removeItem("id");
function handleEditNews(postData) {
    const URL = "http://localhost:8000/news/";
    fetch(URL + id, {
        method: 'PATCH',
        headers: {
            'Authorization': `Bearer ${sessionStorage.getItem("token")}`,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(postData)
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('POST request successful:', data);
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
}
