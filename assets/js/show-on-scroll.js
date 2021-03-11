// https://cssanimation.rocks/scroll-animations/
// Detect request animation frame
const scroll = window.requestAnimationFrame
    || function frame(callback) { window.setTimeout(callback, 1000 / 60); };
const elementsToShow = document.querySelectorAll('.show-on-scroll');

// Helper function from: http://stackoverflow.com/a/7557433/274826
function isElementInViewport(el) {
    // special bonus for those using jQuery
    // eslint-disable-next-line no-undef
    if (typeof jQuery === 'function' && el instanceof jQuery) {
        // eslint-disable-next-line no-param-reassign,prefer-destructuring
        el = el[0];
    }
    const rect = el.getBoundingClientRect();
    return (
        (rect.top <= 0
            && rect.bottom >= 0)
        || (rect.bottom >= (window.innerHeight || document.documentElement.clientHeight)
            && rect.top <= (window.innerHeight || document.documentElement.clientHeight))
        || (rect.top >= 0
            && rect.bottom <= (window.innerHeight || document.documentElement.clientHeight))
    );
}

function loop() {
    Array.prototype.forEach.call(elementsToShow, (element) => {
        if (isElementInViewport(element)) {
            element.classList.add('is-visible');
        }
    });

    scroll(loop);
}

// Call the loop for the first time
loop();
