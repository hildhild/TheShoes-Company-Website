document.getElementById('add-product-form').addEventListener('submit', (e) => {
    e.preventDefault();
    const name = document.getElementById('product-name').value;
    const price = document.getElementById('product-price').value;
    const color = document.getElementById('product-colour').value;
    const sizes = document.getElementById('product-size').value;
    const quantity = document.getElementById('product-stock').value;
    const link1 = document.getElementById('img1-link').value;
    const link2 = document.getElementById('img2-link').value;
    const link3 = document.getElementById('img3-link').value;
    const link4 = document.getElementById('img4-link').value;
    const category = document.getElementById('category').value;
    const description = document.getElementById('description').value
    handleAddProduct({
        product_name: name,
        price: price,
        color: color,
        quantity: quantity,
        category: category,
        description: description,
        size: sizes.split(','),
        thumbnail: [link1, link2, link3, link4]
    })
    window.location.href = "./ProductAdmin.html";
})

function handleAddProduct(postData) {
    const URL = "http://localhost:8000/product";
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