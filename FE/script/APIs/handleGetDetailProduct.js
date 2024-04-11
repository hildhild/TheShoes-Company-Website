var proId = window.location.search.slice(4);
function convertTime(utcTimestamp) {
    const dateUtc = new Date(utcTimestamp);
    
    const utcMilliseconds = dateUtc.getTime();
    const gmtPlus7Offset = 7 * 60 * 60 * 1000;
    const gmtPlus7Milliseconds = utcMilliseconds + gmtPlus7Offset;
    const dateGmtPlus7 = new Date(gmtPlus7Milliseconds);
    console.log(dateGmtPlus7.toLocaleString())
    return dateGmtPlus7.toLocaleString();
}
function getProductDetail() {
    const getProductURL = "http://localhost:8000/product/";
    fetch(getProductURL + proId)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log(data.data);
            displayProductDetail(data.data);
            displayComments(data.data);
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
}
function addtoCart(postData) {
    const requestOptions = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${sessionStorage.getItem("token")}`
        },
        body: JSON.stringify(postData)
    }

    const URL = `http://localhost:8000/order/addProductToCart`;
    fetch(URL, requestOptions)
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
            console.error('There was a problem with the POST request:', error);
        });

}
getProductDetail();

function displayProductDetail(data) {
    const detailContainer = document.getElementById("product-detail-container");
    let html = "";
    html += `<div class="flex flex-col gap-[10px]">`;
    html += `<p class="text-2xl font-semibold">${data.product_name}</p>`;
    html += `<p class="text-2xl font-semibold">${data.price}</p>`;
    html += `<p class="text-base"><span class="font-semibold">Colour </span>${data.color}</p>`;
    html += `<p class="text-base font-semibold">Size</p>`;

    html += `<div class="flex flex-row flex-wrap text-base gap-[10px]">`;

    const sizes = data.sizes;
    html += `<select id="size" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">`
    sizes.forEach((val, index) => {
        html += `<option value=${val}>${val}</option>`
    })
    html += `</select>`;
    html += `
    <div class="mt-4 block w-full">
    <label for="quantity" class="text-base font-semibold">Choose quantity</label>
    <input value="1" min="1" step="1" id="quantity" name="quantity" type="number" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
  </div>
  
    `
    html += `</div>`;
    html += `</div>`;

    html += `<div class="mt-[32px] md:mt-0">
    <button id="add-to-cart-btn" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-base px-5 py-2.5 mt-2.5 focus:outline-none md:min-w-[180px]">Add
        to cart
        <i class="fa-solid fa-arrow-right inline-block ml-[30px]"></i>
    </button>
</div>`;
    detailContainer.innerHTML = html;
    const img = document.querySelectorAll(".product__img > img");
    for (let idx = 0; idx < img.length; idx++) {
        img[idx].src = data.thumbnails[idx] ? data.thumbnails[idx] : data.thumbnails[0]
    }
    const desc = document.getElementById("product-desc");
    let descHtml = "";

    descHtml + `<h2 class="text-2xl font-semibold mt-[20px]">Product Description</h2>`;
    descHtml += `<p class="text-base font-semibold mt-[20px]">${data.product_name}</p>`;
    descHtml += `<p class="text-sm text-justify mt-[20px]">${data.description}</p>`;
    desc.innerHTML = descHtml;
    document.getElementById("add-to-cart-btn").addEventListener('click', () => {
        const params = {
            "product_id": Number(proId),
            "size": Number(document.getElementById('size').value),
            "quantity": Number(document.getElementById('quantity').value),
            "price": Number(`${data.price}`),
            "product_name": `${data.product_name}`,
            "thumbnail": `${data.thumbnails[0]}`,
            "color": `${data.color}`
        }
        addtoCart(params);
        location.href = "./Cart.html";
    })
    
}

function addNewsComment(postData) {
    const requestOptions = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${sessionStorage.getItem("token")}`
        },
        body: JSON.stringify(postData)
    }

    const URL = `http://localhost:8000/product/${proId}/comment`;
    fetch(URL, requestOptions)
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
            console.error('There was a problem with the POST request:', error);
        });

}
function displayComments(data) {
    const reviews = document.getElementById("reviews");
    let html = "";
    html += `<h2 class="text-2xl font-semibold mt-[20px] flex">Reviews (${data.comments.length})</h2>`;

    html += `
    <div class="my-[20px]">
        <textarea name="review-content" id="review-content" rows="3" class="w-full border-gray border-2 rounded-lg p-[8px]"></textarea>
        <button id="add-cmt-btn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded w-1/8">
            Write a Review
        </button>
    </div>
    `;

    data.comments.forEach((val, index) => {
        html += `
        <div class="review-item py-[20px]">
        <div class="review-info flex">
            <p class="review-own text-base font-semibold mt-[5px] mr-[10px]">${val.user_name}</p>
            <p class="review-date text-base font-semibold mt-[5px]"> - ${convertTime(val.created_at)}</p>
        </div>
        <p class="text-sm text-justify mt-[5px]">
            ${val.content}
        </p>
    </div>
        `;
    });
    reviews.innerHTML = html;
    
    document.getElementById("add-cmt-btn").addEventListener('click', () => {
        const commentContent = document.getElementById("review-content").value;
        console.log(commentContent);
        if (commentContent === "") return;
        const params = {
            user_id: sessionStorage.getItem("user_id"),
            content: commentContent,
            title: commentContent,
            user_name: sessionStorage.getItem("user_name"),
            avatar_url: ""
        }
        addNewsComment(params);
        location.reload();
    })
}
