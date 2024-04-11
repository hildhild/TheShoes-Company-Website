function convertTime(utcTimestamp) {
    const dateUtc = new Date(utcTimestamp);
    const utcMilliseconds = dateUtc.getTime();
    const gmtPlus7Offset = 7 * 60 * 60 * 1000;
    const gmtPlus7Milliseconds = utcMilliseconds + gmtPlus7Offset;
    const dateGmtPlus7 = new Date(gmtPlus7Milliseconds);
    return dateGmtPlus7.toLocaleDateString();
}
function getAllOrders() {
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
            console.log(data.data);
            displayAllOrders(data.data);
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
}

getAllOrders();
function displayAllOrders(orders) {
    const myOrdersContainer = document.getElementById('orders-container');
    const totalOrders = document.getElementById("total-orders");
    const shippingOrders = document.getElementById("shipping-orders");
    const rejectedOrders = document.getElementById("rejected-orders");
    const orderedOrders = document.getElementById("ordered-orders");
    var countTotalOrders = 0;
    var countShippingOrders = 0;
    var countRejectedOrders = 0;
    var countOrderedOrders = 0;
    let html = "";
    orders.forEach((value) => {
        if (value.total_money <= 200) value.total_money += 30;
        countTotalOrders ++;
        var html_select_status = "";
        if (value.order_status == 'shipping'){
            countShippingOrders ++;
            html_select_status = `
                <select
                class="inline-block bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5"
                onchange="changeStatus(${value.order_id})"
                id="order-status-${value.order_id}"
                >
                    <option value="ordered">Ordered</option>
                    <option value="shipping" selected>Shipping</option>
                    <option value="rejected">Rejected</option>
                </select>
            `;
        }
        else if (value.order_status == 'rejected'){
            countRejectedOrders ++;
            html_select_status = `
                <select
                class="inline-block bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5"
                onchange="changeStatus(${value.order_id})"
                id="order-status-${value.order_id}"

                >
                    <option value="ordered">Ordered</option>
                    <option value="shipping" >Shipping</option>
                    <option value="rejected" selected>Rejected</option>
                </select>
            `;
        }
        else{
            countOrderedOrders ++;
            html_select_status = `
                <select
                class="inline-block bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-2.5"
                onchange="changeStatus(${value.order_id})"
                id="order-status-${value.order_id}"
                >
                    <option value="ordered" selected>Ordered</option>
                    <option value="shipping" >Shipping</option>
                    <option value="rejected">Rejected</option>
                </select>
            `;
        }

        html += `
            <div
              class="flex flex-row text-base text-black bg-white p-[20px] border border-[#d1d1d1] rounded-lg mt-2.5 items-center"
              id="order-item-${value.order_id}"
            >
              <div class="min-w-[10%]">${value.order_id}</div>
              <div class="min-w-[17%]">${value.user_name}</div>
              <div class="min-w-[13%]">${value.user_id}</div>
              <div class="min-w-[20%]">${convertTime(value.created_at)}</div>
              <div class="min-w-[15%]">$${value.total_money}</div>
              <div class="min-w-[15%] pr-[24px]">
                ${html_select_status}
              </div>
              <div
                class="min-w-[10%] flex flex-row justify-start items-center text-xl gap-4"
              >
                <a href="./OrderDetailAdmin.html?id=${value.order_id}" class="block">
                    <i class="fa-solid fa-circle-info cursor-pointer"></i>
                </a>
              </div>
            </div>
            `
    })
    totalOrders.innerText = countTotalOrders;
    shippingOrders.innerText = countShippingOrders;
    rejectedOrders.innerText = countRejectedOrders;
    orderedOrders.innerText = countOrderedOrders;
    console.log(html);
    console.log(myOrdersContainer);
    if (html != "") myOrdersContainer.innerHTML = html;

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
        getAllOrders();
    })
    .catch(error => {
        console.error('Error when changing the status:', error);
    });


}