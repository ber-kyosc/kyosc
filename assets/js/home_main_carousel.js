import Carousel from './Carousel';

const homeMainCarousel = document.getElementById('home-main-carousel');
const labelSportCarousel = document.getElementById('label-sport-carousel');
const instanceHomeMainCarousel = new Carousel(homeMainCarousel);
const instanceLabelSportCarousel = new Carousel(labelSportCarousel);

instanceHomeMainCarousel.leftArrowCarousel.addEventListener('click', (e) => {
    clearTimeout(instanceHomeMainCarousel.idAutoScroll);
    clearTimeout(instanceLabelSportCarousel.idAutoScroll);
    instanceHomeMainCarousel.autoScroll(true);
    instanceLabelSportCarousel.autoScroll(true);
    instanceHomeMainCarousel.moveLeft();
    instanceLabelSportCarousel.moveLeft();
});

instanceHomeMainCarousel.rightArrowCarousel.addEventListener('click', (e) => {
    clearTimeout(instanceHomeMainCarousel.idAutoScroll);
    clearTimeout(instanceLabelSportCarousel.idAutoScroll);
    instanceHomeMainCarousel.autoScroll(true);
    instanceLabelSportCarousel.autoScroll(true);
    instanceHomeMainCarousel.moveRight();
    instanceLabelSportCarousel.moveRight();
});

window.addEventListener('resize', () => {
    instanceHomeMainCarousel.responsiveCarousel();
    instanceLabelSportCarousel.responsiveCarousel();
});
