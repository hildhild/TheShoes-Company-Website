function getMyOrders() {
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
            displayMyOrders(data?.data?.order);
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
}

getMyOrders();
function displayMyOrders(orders) {
    const myOrdersContainer = document.getElementById('my-orders-container');
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
        var subtotal = 0;
        value.items.forEach((value) => {
            subtotal += value.total_money*value.quantity;
        });
        if (subtotal > 200) value.total_money  = subtotal;
        else value.total_money = subtotal + 30;
        countTotalOrders ++;
        if (value.order_status == 'shipping') {
            countShippingOrders ++;
        }
        else if (value.order_status == 'rejected') {
            countRejectedOrders ++;
        }
        else {
            countOrderedOrders ++;
        }
        console.log(value.items[0])
        html += `
            <div class="flex flex-row text-base text-black bg-white p-[20px] border border-[#d1d1d1] rounded-lg mt-2.5 items-center">
                <div class="min-w-[10%]">${value.order_id}</div>
                <div class="min-w-[30%]">${value.created_at}</div>
                <div class="min-w-[20%]">$${value.total_money}</div>
                <div class="min-w-[20%]">${value.order_status}</div>
                <div class="min-w-[20%] flex flex-row justify-start items-center text-xl gap-4">
                    <a href="./OrderDetail.html?id=${value.order_id}" class="block">
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