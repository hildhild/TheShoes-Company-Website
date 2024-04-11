function getMyInformation() {
    const getIn4URL = `http://localhost:8000/user/information`; 

    fetch(getIn4URL, {
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
            displayMyInformation(data.data);
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
}

getMyInformation();
function displayMyInformation(data) {
    const in4 = data[0];
    document.getElementById('account-email').value = in4.email;
    document.getElementById('account-name').value = in4.user_name;
    document.getElementById('account-country').value = in4.country;
    document.getElementById('account-state').value = in4.province;
    document.getElementById('account-city').value = in4.city;
    document.getElementById('account-phone').value = in4.address;
    document.getElementById('account-address').value = in4.phone_number;
    document.getElementById('card-name').value = in4.card_name;
    document.getElementById('card-number').value = in4.card_number;
    document.getElementById('exp-date').value = in4.card_expiration;
    document.getElementById('cvv').value = in4.vcc;
}

function editInformation() {
    var listInput = document.getElementsByName('edit-in4');
    console.log(listInput);
    listInput.forEach((value) => {
        value.removeAttribute('disabled');
    })
    return;
}

function cancelEditInformation() {
    getMyInformation();
    return;
}

function updateInformation() {
    var userID = sessionStorage.getItem('user_id');
    var name = document.getElementById('account-name').value;
    var country = document.getElementById('account-country').value;
    var state = document.getElementById('account-state').value;
    var city = document.getElementById('account-city').value;
    var phone = document.getElementById('account-phone').value;
    var address = document.getElementById('account-address').value;
    var card_name = document.getElementById('card-name').value;
    var card_number = document.getElementById('card-number').value;
    var exp = document.getElementById('exp-date').value;
    var cvv = document.getElementById('cvv').value;
    fetch('http://localhost:8000/user/information', {
        method: 'PUT',
        headers: {
            'Authorization': `Bearer ${sessionStorage.getItem("token")}`,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            user_id: userID,
            user_name: name,
            country: country,
            province: state,
            city: city,
            phone_number: phone,
            address: address,
            card_name: card_name,
            card_number: card_number,
            card_expiration: exp,
            vcc: cvv
        })
    }).then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            console.log(123);
            return response.json();
        })
        .then(data => {
            console.log('change success:', data);
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        })
}
