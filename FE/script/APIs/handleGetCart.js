function convertTime(utcTimestamp) {
    const dateUtc = new Date(utcTimestamp);
    const utcMilliseconds = dateUtc.getTime();
    const gmtPlus7Offset = 7 * 60 * 60 * 1000;
    const gmtPlus7Milliseconds = utcMilliseconds + gmtPlus7Offset;
    const dateGmtPlus7 = new Date(gmtPlus7Milliseconds);
    return dateGmtPlus7.toISOString();
}
function deteleItem(deleteData) {
    const URL = "http://localhost:8000/order/cart";
    fetch(URL, {
        method: 'DELETE',
        headers: {
            'Authorization': `Bearer ${sessionStorage.getItem("token")}`,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(deleteData)
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {

        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
}
function getCart() {
    const URL = "http://localhost:8000/order/cart";
    fetch(URL, {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${sessionStorage.getItem("token")}`,
            'Content-Type': 'application/json'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            displayCart(data.data);
            console.log(data.data);
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
}

getCart();

function displayCart(data) {
    let html = "";
    html += `<strong class="md:text-[30px] text-[20px]">Your cart</strong><br><br>`;
    html += `<div class="lg:flex">`;
    html += `<div class="w-full lg:w-[50%]">`;
    for (let i = 0; i < data.length - 1; i++) {
        html += `
        <div class="cart-item p-[20px]">
            <div class="flex mb-[30px]">
                <div class="w-1/5">
                    <img src="../../images/puma.png" alt="">
                </div>
                <div class="w-3/5">
                    <p class="font-semibold text-lg mb-[10px]">${data[i].product_name}</p>
                    <ul class="list-disc ml-[20px]">
                        <li>Colour: ${data[i].color}</li>
                        <li>Size: ${data[i].size}</li>
                        <li>Quantity: ${data[i].quantity}</li>
                    </ul>
                </div>
                <div class="w-1/5">
                    <div class="price text-right">
                        <p>$${data[i].price}</p>
                    </div>
                    <div class="h-[80px]"></div>
                    <button
                        data-orderId=${data[i].order_detail_id}
                        class="delete-btn relative z-20 float-right py-3 px-6 text-xs font-bold transition-all hover:opacity-[0.85] focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                        data-ripple-light="true">
                        <i class="fa-regular fa-trash-can relative z-0 text-2xl " style="color: #000000;"></i>
                    </button>
                </div>
            </div>
            <hr class="border-b border-solid border-black" />
        </div>
        `
    }
    html += `</div>`;

    
    html += `
    <div class="w-full md:w-[60%] md:float-right md:mb-[50px] lg:w-[50%]">
    <div class="bill bg-[#F2F2F2] w-full p-[30px] rounded-xl">
        <div class="flex">
            <div class="w-1/2 text-left">
                <p class="pb-[10px]">Created At</p>
                <p class="pb-[10px]">Subtotal</p>
                <p class="pb-[10px]">Extimated Tax</p>
                <p class="pb-[10px]">Shipping</p>
            </div>
            <div class="w-1/2 text-right">
                <p class="pb-[10px]">${data.length <= 1 ? "Not available" : convertTime(data[data.length - 2]?.created_at).slice(0, 10) + " " + convertTime(data[data.length - 2].created_at)?.slice(11, 19)}</p>
                <p class="pb-[10px]">$${data[data.length - 1].superTotalMoney}</p>
                <p class="pb-[10px]">0</p>
                <p class="pb-[10px]">0</p>
            </div>
        </div>
        <hr class="border-b border-solid border-black" />
        <div class="flex py-[30px]">
            <div class="w-1/2 text-left">
                <p class="font-semibold text-lg">Total</p>
            </div>
            <div class="w-1/2 text-right">
                <p class="font-semibold text-lg">$${data[data.length - 1].superTotalMoney}</p>
            </div>
        </div>
        <div class="mb-[20px] ml-[40%]">
            <button id="checkout-btn"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded w-full ">
                Continue to checkout
                <i class="fa-solid fa-arrow-right float-right pt-[5px]" style="color: #ffffff;"></i>
            </button>
        </div>
        <hr class="border-b border-solid border-black" />
        <p class="pt-[10px]">30-day back guarantee</p>
        <p class="pt-[10px]">Free shipping on orders above $200</p>
        <p class="pt-[10px]">1-year limited warrantly</p>

    </div>
</div>
    `
    html += `</div>`;
    document.getElementById("cart-container").innerHTML = html;

    var deleteBtns = document.getElementsByClassName("delete-btn");
    for (let i = 0; i < deleteBtns.length; i++) {
        deleteBtns[i].addEventListener('click', (e) => {
            deteleItem({ order_detail_id: Number(deleteBtns[i].dataset.orderid) });
            location.reload();
        })
    }

    var checkoutBtn = document.getElementById("checkout-btn");
    checkoutBtn.addEventListener('click', () => {
        const postValue = {};
        if (data.length === 0 || data[data.length - 1].superTotalMoney === 0) {
            alert("Nothing to checkout");
            return;
        };
        postValue.email = sessionStorage.getItem("email");
        postValue.user_name = sessionStorage.getItem("user_name");
        handleCheckout({...postValue, "country": "VN",
        "province": "VT",
        "city":"ba ria",
        "zip_code":"saxb",
        "address":"123 phuoc nguyen",
        "phone_number":"156546",
        "card_name":"ABC",
        "card_number":"12313",
        "card_expiration":"2022-11-12",
        "vcc":"123123"});
        location.reload();
    })
}

function handleCheckout(postData) {
    const URL = "http://localhost:8000/order/checkout";
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
