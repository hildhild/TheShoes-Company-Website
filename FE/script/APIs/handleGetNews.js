function getNews() {
    const getNewsURL = "http://localhost:8000/news";
    fetch(getNewsURL)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log(data.data);
            displayNews(data.data);
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
}

getNews();
function displayNews(data) {
    const newsContainer = document.getElementById('news__container');
    let html = "";
    data.forEach((value, index) => {
        if (index % 3 === 0) {
            html += `<div class="flex flex-col lg:flex-row mb-[32px] lg:mx-[-16px]">`;
        }
        html += `
        <div class="w-full lg:w-1/3 lg:mb-0 mb-[32px] lg:px-4">
            <a href="./NewsDetail.html?id=${value.news_id}" class="block w-full">
                <div class="mb-2 block w-full">
                    <img src=${value.image_url} alt="" class="block w-full">
                </div>
                <p class="text-base font-semibold">${value.title}</p>
            </a>
        </div>
        `
        if ((index + 1) % 3 === 0) {
            html += "</div>";
        }
    })
    newsContainer.innerHTML = html;
}