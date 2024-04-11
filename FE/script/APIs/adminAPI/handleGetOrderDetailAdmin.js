var orderID = window.location.search.slice(4);
console.log(orderID);

function getOrderDetail() {
    const getOrdersURL = `http://localhost:8000/order/order-list`; 

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
            console.log(data?.data);
            displayOrderDetail(data?.data);
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
    var html_select_status = "";
        if (myOrderDetail.order_status == 'shipping'){
            html_select_status = `
                <select
                class="inline-block bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-1/4 p-2.5"
                onchange="changeStatus(${myOrderDetail.order_id})"
                id="order-status-${myOrderDetail.order_id}"
                >
                    <option value="ordered">Ordered</option>
                    <option value="shipping" selected>Shipping</option>
                    <option value="rejected">Rejected</option>
                </select>
            `;
        }
        else if (myOrderDetail.order_status == 'rejected'){
            html_select_status = `
                <select
                class="inline-block bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-1/4 p-2.5"
                onchange="changeStatus(${myOrderDetail.order_id})"
                id="order-status-${myOrderDetail.order_id}"

                >
                    <option value="ordered">Ordered</option>
                    <option value="shipping" >Shipping</option>
                    <option value="rejected" selected>Rejected</option>
                </select>
            `;
        }
        else{
            html_select_status = `
                <select
                class="inline-block bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-1/4 p-2.5"
                onchange="changeStatus(${myOrderDetail.order_id})"
                id="order-status-${myOrderDetail.order_id}"
                >
                    <option value="ordered" selected>Ordered</option>
                    <option value="shipping" >Shipping</option>
                    <option value="rejected">Rejected</option>
                </select>
            `;
        }
    var subtotal = myOrderDetail.total_money+0;
    if (myOrderDetail.total_money > 200) var shipping = 0;
    else var shipping = 30;
    myOrderDetail.total_money += shipping;
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
            <p class="pb-[10px]">
                ${html_select_status}
            </p>
        </div>
        </div>
        <br />
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
            <p class="pb-[10px]">$0</p>
            <p class="pb-[10px]">$${shipping}</p>
        </div>
        </div>
        <hr class="border-b border-solid border-black" />
        <div class="flex py-[30px]">
        <div class="w-1/2 text-left">
            <p class="font-semibold text-lg">Total</p>
        </div>
        <div class="w-1/2 text-right">
            <p class="font-semibold text-lg">$${myOrderDetail.total_money}</p>
        </div>
        </div>
        <p>
        Please double-check your order details. Orders cannot be modified
        once submitted.
        </p>
    `;
}
function changeStatus(order_id){
    var newStatus = document.getElementById(`order-status-${order_id}`).value;
    fetch('http://localhost:8000/order/order-status', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${sessionStorage.getItem("token")}`
        },
        body: JSON.stringify({
            order_id: order_id,
            order_status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('change success:', data);
        getOrderDetail();
    })
    .catch(error => {
        console.error('Error when changing the status:', error);
    });
}

getOrderDetail();
