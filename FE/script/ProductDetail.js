const btns = document.getElementsByClassName("product__slider-btn");
const slides = document.getElementsByClassName("product__img");
const prev = document.getElementsByClassName("product__slider-prev");
const next = document.getElementsByClassName("product__slider-next");
const maxSlides = 4;
var currentSlide1 = 0;
var currentSlide2 = 4;
var currentSlide3 = 8;

const chooseSlider1 = (num) => {
    for (let i = 0; i < 4; i++) {
        btns[i].classList.remove("active");
        if (!slides[i].classList.contains("my-hidden")) {
            slides[i].classList.add("my-hidden")
        }
    }
    if (!btns[num].classList.contains("active")) {
        btns[num].classList.add("active");
        slides[num].classList.remove("my-hidden");
        currentSlide1 = num;
    }
    clearInterval(autoChange1);
    autoChange1 = setInterval(() => {
        next[0].click();
    }, 3000)
}



prev[0].addEventListener('click', () => {
    if (currentSlide1 === 0) {
        currentSlide1 = maxSlides - 1;
    }
    else {
        currentSlide1--;
    }
    chooseSlider1(currentSlide1);
})


next[0].addEventListener("click", () => {
    if (currentSlide1 === maxSlides - 1) {
        currentSlide1 = 0;
    }
    else {
        currentSlide1++;
    }
    chooseSlider1(currentSlide1);
})

for (let i = 0; i < 4; i++) {
    btns[i].addEventListener('click', () => {
        chooseSlider1(i);
    })
}


var autoChange1 = setInterval(() => {
    next[0].click();
}, 3000)