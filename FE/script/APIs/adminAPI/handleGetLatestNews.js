function getLatestNews() {
  const getNewsURL = "http://localhost:8000/news";
  fetch(getNewsURL)
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .then((data) => {
      console.log(data.data);
      displayLatestNews(data.data);
    })
    .catch((error) => {
      console.error("There was a problem with the fetch operation:", error);
    });
}

getLatestNews();
function displayLatestNews(data) {
    const latestNewsContainer = document.getElementById('latest-news-container');
    let html = "";
    data.slice(0, 3).forEach((value, index) => {
        
        html += `
        <div class="w-full lg:w-1/3 flex">
            <a href="./NewsDetailAdmin.html?id=${value.news_id}" class="block">
                <div class="mb-2">
                    <img src="${value.image_url}" alt="">
                </div>
                    <p class="text-base font-semibold">${value.title}</p>
                    <p class="text-sm">${value.content.substring(0,200)}...</p>

            </a>
        </div>
        `
    })
    latestNewsContainer.innerHTML = html;
}
