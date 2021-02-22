import Carousel from './Carousel';

const myCarousels = document.querySelectorAll('.my-carousel');
const myCarouselsLen = myCarousels.length;
const objectCarousels = [];

for (let i = 0; i < myCarouselsLen; i += 1) {
    if (!/special-carousel/i.test(myCarousels[i].className)) {
        objectCarousels.push(new Carousel(myCarousels[i]));

        const carousel = objectCarousels[i];

        if (carousel.leftArrowCarousel) {
            carousel.leftArrowCarousel.addEventListener('click', (e) => {
                clearTimeout(carousel.idAutoScroll);
                carousel.autoScroll(true);
                carousel.moveLeft();
            });
        }

        if (carousel.rightArrowCarousel) {
            carousel.rightArrowCarousel.addEventListener('click', (e) => {
                clearTimeout(carousel.idAutoScroll);
                carousel.autoScroll(true);
                carousel.moveRight();
            });
        }
    }
}

const objectCarouselsLen = objectCarousels.length;

window.addEventListener('resize', () => {
    for (let i = 0; i < objectCarouselsLen; i += 1) {
        const carousel = objectCarousels[i];

        carousel.responsiveCarousel();
    }
});
