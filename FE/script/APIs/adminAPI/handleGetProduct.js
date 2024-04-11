var flag = true;
const sorts = document.getElementsByName("sort");
const categories = document.getElementsByClassName("category-item");
var sort = null;
var category = null;
for (let i = 0; i < categories.length; i++) {
    categories[i].addEventListener('click', () => {
        if (categories[i].classList.contains("font-semibold") && categories[i].classList.contains("bg-gray-300")) {

        }
        else {
            for (let j = 0; j < categories.length; j++) {
                categories[j].classList.remove("font-semibold");
                categories[j].classList.remove("bg-gray-300");
            }
            categories[i].classList.add("font-semibold");
            categories[i].classList.add("bg-gray-300");
            if (i === 0) category = null;
            else if (i === 1) category = "Men";
            else if (i === 2) category = "Women";
            else  category = "Kids";
            getProducts(sort, category);
            //location.reload();
        }
        
    })
}

for (let i = 0; i < sorts.length; i++) {
    sorts[i].addEventListener('click', () => {
        if (i === 0) sort = null;
        else if (i === 1) sort = "asc";
        else sort = "desc";
        console.log(sort, category, null)
        getProducts(sort, category, null);
        //location.reload();
    })
}
function getProducts(sort, category, search) {
    const getProductURL = "http://localhost:8000/product?";
    let params = {};
    if (sort === null && category === null) params = {};
    else if (sort !== null && category === null) params = {
        order_by: sort,
    }
    else if (sort === null && category !== null) params = {
        category: category,
    }
    else if (sort !== null && category !== null) params = {
        order_by: sort,
        category: category,
    }
    if (search && search !== "") params = {...params, ...{product_name: search}}
    console.log(params);
    fetch(getProductURL + new URLSearchParams(params))
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log(data.data);
            displayProducts(data.data);
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
}



if (flag) {
    getProducts(null, null, null);
    flag = false;
}
function displayProducts(data) {
    const productContainer = document.getElementById("product__container");
    const productCount = data.length;
    const p = document.createElement('p');
    p.textContent = `${productCount} products`;
    productContainer.appendChild(p);
    let html = '';
    data.forEach((value, index) => {
        if (index % 3 === 0) {
            html += '<div class="flex flex-col lg:flex-row lg:gap-[122.5px] mb-[25px]">';
        }
        html += `
        <div class="w-full lg:w-1/3 lg:mb-0 mb-[25px]">
            <a href="./ProductDetail_admin.html?id=${value.product_id}" class="block overflow-hidden">
                <div class="mb-2">
                    <img src=${value.thumbnails[0]} alt="" class="block h-[200px]">
                </div>
                <p class="text-base font-semibold">${value.product_name}</p>
                <p class="float-right mt-[10px]">${value.price}</p>
            </a>
        </div>
        `;
        if ((index + 1) % 3 === 0) {
            html += '</div>';
        }
    })
    productContainer.innerHTML = html;
}


document.getElementById("search-lg-btn").addEventListener("click", () => {
    const searchLgInput = document.getElementById("search-lg").value.trim();
    console.log(searchLgInput);
    if (searchLgInput !== "") {
        getProducts(sort, category, searchLgInput);
    }
    else {
        getProducts(sort, category, null);
    }
}) 

document.getElementById("search-mobile-btn").addEventListener('click', () => {
    const input = document.getElementById("search-mobile").value.trim();
    if (input !== "") {
        getProducts(sort, category, input);
    }
    else {
        getProducts(sort, category, null);
    }
})