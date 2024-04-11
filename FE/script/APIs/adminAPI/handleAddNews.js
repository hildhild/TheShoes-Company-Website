const form = document.getElementById("add-form");
function handleAddNews(postData) {
    const URL = "http://localhost:8000/news";
    fetch(URL, {
        method: 'POST',
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
form.addEventListener('submit', (e) => {
    e.preventDefault();
    const title = document.getElementById("title").value;
    const content = document.getElementById("content").value;
    const img = document.getElementById("img-link").value;
    handleAddNews({
        title: title,
        content: content,
        image_url: img,
        user_id: sessionStorage.getItem("user_id")
    })
    window.location.href = "./NewsAdmin.html";
})