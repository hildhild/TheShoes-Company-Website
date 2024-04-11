var orderID = window.location.search.slice(4);
console.log(orderID);

function getOrderDetail() {
    const getOrdersURL = `http://localhost:8000/user/buying-history`; 

    fetch(getOrdersURL, {
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
            console.log(data?.data?.order);
            displayOrderDetail(data?.data?.order);
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
}

function displayOrderDetail(orders) {
    const orderDetailContainer = document.getElementById("order-detail-container");
    var myOrderDetail ={};
    for (var value of orders) {
        if (value.order_id == orderID){
            myOrderDetail = value;
            break;
        }
    }
    var subtotal = 0;

    var html = "";
    myOrderDetail.items.forEach((value) => {
        subtotal += value.total_money*value.quantity;
        html += `
            <div class="cart-item">
                <div class="flex mb-[30px]">
                    <div class="w-1/5">
                    <img src="${value.thumbnail}" alt="" />
                    </div>
                    <div class="w-3/5 pl-[20px]">
                    <p class="font-semibold text-lg mb-[10px]">
                        ${value.product_name}
                    </p>
                    <ul class="list-disc ml-[20px]">
                        <li>Colour: ${value.color}</li>
                        <li>Size: ${value.size}</li>
                        <li>Quantity: ${value.quantity}</li>
                    </ul>
                    </div>
                    <div class="w-1/5">
                    <div class="price text-right">
                        <p class="font-semibold">$${value.total_money}</p>
                    </div>
                    </div>
                </div>
            </div>
        `;
    })
    if (subtotal > 200) var shipping = 0;
    else var shipping = 30;
    var total = subtotal + shipping;
    orderDetailContainer.innerHTML = `
        <strong class="text-2xl">Order ID: ${myOrderDetail.order_id}</strong><br /><br />
        <div class="flex">
        <div class="w-1/2 text-left">
            <p class="pb-[10px]">User ID</p>
            <p class="pb-[10px]">Username</p>
            <p class="pb-[10px]">Email</p>
            <p class="pb-[10px]">Country</p>
            <p class="pb-[10px]">State/Province</p>
            <p class="pb-[10px]">City</p>
            <p class="pb-[10px]">Zip code</p>
            <p class="pb-[10px]">Address</p>
            <p class="pb-[10px]">Phone number</p>
            <p class="pb-[10px]">Created At</p>
            <p class="pb-[10px]">Status</p>
        </div>
        <div class="w-1/2 text-right">
            <p class="pb-[10px]">${myOrderDetail.user_id}</p>
            <p class="pb-[10px]">${myOrderDetail.user_name}</p>
            <p class="pb-[10px]">${myOrderDetail.email}</p>
            <p class="pb-[10px]">${myOrderDetail.country}</p>
            <p class="pb-[10px]">${myOrderDetail.province}</p>
            <p class="pb-[10px]">${myOrderDetail.city}</p>
            <p class="pb-[10px]">${myOrderDetail.zip_code}</p>
            <p class="pb-[10px]">${myOrderDetail.address}</p>
            <p class="pb-[10px]">${myOrderDetail.phone_number}</p>
            <p class="pb-[10px]">${myOrderDetail.created_at}</p>
            <p class="pb-[10px]">${myOrderDetail.order_status}</p>
        </div>
        </div>
        <br />
        <hr class="border-b border-solid border-black mb-[20px]" />
        <div class="lg:flex">
        <div class="w-full" id="list-items-of-order">
            
        </div>
        </div>
        <hr class="border-b border-solid border-black mb-[20px]" />
        <div class="flex">
        <div class="w-1/2 text-left">
            <p class="pb-[10px]">Delivery</p>
            <p class="pb-[10px]">Subtotal</p>
            <p class="pb-[10px]">Extimated Tax</p>
            <p class="pb-[10px]">Shipping</p>
        </div>
        <div class="w-1/2 text-right">
            <p class="pb-[10px]">5-10 days from ordering date</p>
            <p class="pb-[10px]">$${subtotal}</p>
            <p class="pb-[10px]">$0.00</p>
            <p class="pb-[10px]">$${shipping}</p>
        </div>
        </div>
        <hr class="border-b border-solid border-black" />
        <div class="flex py-[30px]">
        <div class="w-1/2 text-left">
            <p class="font-semibold text-lg">Total</p>
        </div>
        <div class="w-1/2 text-right">
            <p class="font-semibold text-lg">$${total}</p>
        </div>
        </div>
        <p>
        Please double-check your order details. Orders cannot be modified
        once submitted.
        </p>
    `;
    const listItemsOfOrder = document.getElementById("list-items-of-order");
    listItemsOfOrder.innerHTML= html;
}


getOrderDetail();