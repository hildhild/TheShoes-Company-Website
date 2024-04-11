const navBtns = document.getElementsByClassName("slider__btn");
const slides = document.getElementsByClassName("slider__container");
var currentSlide = 0;
const chooseSlider = (num) => {
  for (let i = 0; i < navBtns.length; ++i) {
    navBtns[i].classList.remove("active");
    if (!slides[i].classList.contains("my-hidden")) {
      slides[i].classList.add("my-hidden");
    }
  }
  if (!navBtns[num].classList.contains("active")) {
    navBtns[num].classList.add("active");
    slides[num].classList.remove("my-hidden");
    currentSlide = num;
  }
  clearInterval(autoChange);
  autoChange = setInterval(() => {
    next.click();
  }, 3000);
};

for (let i = 0; i < navBtns.length; ++i) {
  navBtns[i].addEventListener("click", () => chooseSlider(i));
}

const prev = document.getElementsByClassName("slider__prev")[0];
const next = document.getElementsByClassName("slider__next")[0];

prev.addEventListener("click", () => {
  if (currentSlide === 0) {
    currentSlide = navBtns.length - 1;
  } else {
    currentSlide--;
  }
  chooseSlider(currentSlide);
});

next.addEventListener("click", () => {
  if (currentSlide === navBtns.length - 1) {
    currentSlide = 0;
  } else {
    currentSlide++;
  }
  chooseSlider(currentSlide);
});

var autoChange = setInterval(() => {
  next.click();
}, 3000);
