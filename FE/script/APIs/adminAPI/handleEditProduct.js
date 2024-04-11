document.getElementById("product-name").value = localStorage.getItem("proname");
document.getElementById("product-price").value = localStorage.getItem("price");
document.getElementById("product-colour").value = localStorage.getItem("color");
document.getElementById("product-stock").value = localStorage.getItem("quantity");
document.getElementById("product-size").value = JSON.parse(localStorage.getItem("size"));
document.getElementById("description").value = localStorage.getItem("description");
const url = localStorage.getItem("thumnails").split(',');
document.getElementById("img1-link").value = url[0];
document.getElementById("img2-link").value = url[1] || url[0];
document.getElementById("img3-link").value = url[2] || url[0];
document.getElementById("img4-link").value = url[3] || url[0];
localStorage.removeItem("proname");
localStorage.removeItem("price");
localStorage.removeItem("color");
localStorage.removeItem("quantity");
localStorage.removeItem("size");
localStorage.removeItem("description");
localStorage.removeItem("thumnails");
const id = Number(localStorage.getItem("proid"));
localStorage.removeItem("proid");

document.getElementById("edit-form").addEventListener('submit', (e) => {
    e.preventDefault();
    handleEditProduct({
        product_id: id,
        product_name: document.getElementById("product-name").value,
        price: document.getElementById("product-price").value,
        color: document.getElementById("product-colour").value,
        description: document.getElementById("description").value,
        size: document.getElementById("product-size").value.split(','),
        quantity: document.getElementById("product-stock").value,
        thumbnail: [
            document.getElementById("img1-link").value, 
            document.getElementById("img2-link").value, 
            document.getElementById("img3-link").value, 
            document.getElementById("img4-link").value
        ]
    })
    window.location.href = `./ProductDetail_admin.html?id=${id}`
})



function handleEditProduct(postData) {
    const URL = "http://localhost:8000/product/";
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