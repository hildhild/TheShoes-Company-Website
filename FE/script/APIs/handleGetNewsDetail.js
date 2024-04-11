var newsId = window.location.search.slice(4);
function convertTime(utcTimestamp) {
    const dateUtc = new Date(utcTimestamp);
    
    const utcMilliseconds = dateUtc.getTime();
    const gmtPlus7Offset = 7 * 60 * 60 * 1000;
    const gmtPlus7Milliseconds = utcMilliseconds + gmtPlus7Offset;
    const dateGmtPlus7 = new Date(gmtPlus7Milliseconds);
    console.log(dateGmtPlus7.toLocaleString())
    return dateGmtPlus7.toLocaleString();
}
function getNewsDetail() {
    const getNewsURL = "http://localhost:8000/news/";
    fetch(getNewsURL + newsId)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log(data);
            displayNewsDetail(data.data[0], data.commnents);
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
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

    const URL = `http://localhost:8000/news/${newsId}/comment`;
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

function displayNewsDetail(data, comments) {
    const newsDetailContainer = document.getElementById("News-Detail__container");
    newsDetailContainer.innerHTML = `
        <img src="${data.image_url}" alt="" class="w-full">
        <div>
            <strong class="md:text-[25px] text-[20px]">${data.title}</strong><br><br>
            <p class="text-[12px]">${data.created_at}</p><br>
            <p class="text-justify">
                ${data.content}
            </p>
        </div>
        <div id="comments" class="pb-[20px]">
            <h2 class="text-2xl font-semibold mt-[20px] flex">Comments (${comments.length})</h2>
            <div class="my-[20px]">
                    <textarea name="comment-content" id="comment-content" rows="3" class="w-full border-gray border-2 rounded-lg p-[8px]"></textarea>
                    <button id="submit-add-cmt" type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded w-1/8">
                        Write a Comment
                    </button>
            </div>
        </div>
    `;
    console.log(comments)
    let html = "";
    comments.forEach((value, index) => {
        html += `
        <div class="comment-item py-[20px]">
            <div class="comment-info flex">
                <p class="comment-own text-base font-semibold mt-[5px] mr-[10px]">${value.user_name}</p>
                <p class="comment-date text-base font-semibold mt-[5px]">- ${convertTime(value.created_at)}</p>
            </div>
            <p class="text-sm text-justify mt-[5px]">
                ${value.content}
            </p>
        </div>
        `
    })
    newsDetailContainer.innerHTML += html;
    document.getElementById("submit-add-cmt").addEventListener('click', () => {
        const commentContent = document.getElementById("comment-content").value;
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


getNewsDetail();