class Carousel {
    constructor(carousel) {
        this.carousel = carousel;
        this.carousel.style.position = 'relative';
        this.leftArrowCarousel = document.querySelector(`#${this.carousel.id} .left-arrow-carousel`);
        this.rightArrowCarousel = document.querySelector(`#${this.carousel.id} .right-arrow-carousel`);
        this.slideCarousel = document.querySelector(`#${this.carousel.id} .slide-carousel`);
        this.slideCarousel.style.position = 'relative';
        this.autoScroll();
        this.imagesCarousel = document.querySelectorAll(`#${this.carousel.id} > .slide-carousel > *`);
        this.imagesCarouselLen = this.imagesCarousel.length;
        this.backupImagesCarousel = this.imagesCarousel;
        this.shiftImages();
        this.setIdImages();
        this.setNbImageCarousel();
        this.resizeSlideCarousel();
        this.resizeImages();
        this.duplicatedImages = [];
        this.duplicateImages();
        this.placeImages();
        this.displayNoneImages();
        this.transitionDuration = 500;
        this.ifOnlyOneImage();
        this.ifNoImages();
        this.canClick = true;
    }

    displayNoneImages() {
        if (this.imagesCarouselLen > 1) {
            for (let i = 0; i < this.imagesCarouselLen; i += 1) {
                this.imagesCarousel[i].style.display = 'none';
            }
        }
    }

    setNbImageCarousel() {
        const classCarousel = this.carousel.className;
        const bodyWidth = document.body.offsetWidth;
        let nbImage = null;

        if (/nbIma?ge?:?([0-9]+)/i.test(classCarousel)) {
            nbImage = parseInt(RegExp.$1, 10);
        }

        if (/nbIma?ge?:sm:?([0-9]+)/i.test(classCarousel) && (bodyWidth >= 576)) {
            nbImage = parseInt(RegExp.$1, 10);
        }

        if (/nbIma?ge?:md:?([0-9]+)/i.test(classCarousel) && (bodyWidth >= 768)) {
            nbImage = parseInt(RegExp.$1, 10);
        }

        if (/nbIma?ge?:lg:?([0-9]+)/i.test(classCarousel) && (bodyWidth >= 992)) {
            nbImage = parseInt(RegExp.$1, 10);
        }

        if (/nbIma?ge?:xl:?([0-9]+)/i.test(classCarousel) && (bodyWidth >= 1200)) {
            nbImage = parseInt(RegExp.$1, 10);
        }

        if (!nbImage) {
            this.nbImage = 1;
        } else {
            this.nbImage = nbImage;

            if (this.imagesCarousel.length < this.nbImage) {
                this.nbImage = this.imagesCarousel.length;
            }
        }
    }

    carouselCssProperties() {
        return {
            width: parseInt(this.carousel.offsetWidth, 10),
            height: parseInt(this.carousel.offsetHeight, 10),
            borderLeftWidth: parseInt(getComputedStyle(this.carousel).borderLeftWidth.replace('px', ''), 10),
            borderRightWidth: parseInt(getComputedStyle(this.carousel).borderRightWidth.replace('px', ''), 10),
            borderTopWidth: parseInt(getComputedStyle(this.carousel).borderTopWidth.replace('px', ''), 10),
            borderBottomWidth: parseInt(getComputedStyle(this.carousel).borderBottomWidth.replace('px', ''), 10),
        };
    }

    resizeSlideCarousel() {
        const carouselCssProperties = this.carouselCssProperties();

        this.slideCarousel.style.width = `${carouselCssProperties.width - (carouselCssProperties.borderLeftWidth + carouselCssProperties.borderRightWidth)}px`;
        this.slideCarousel.style.height = `${carouselCssProperties.height - (carouselCssProperties.borderTopWidth + carouselCssProperties.borderBottomWidth)}px`;
        this.slideCarousel.style.border = 'none';
    }

    removeTransition() {
        const duplicatedImagesLen = this.duplicatedImages.length;

        for (let i = 0; i < duplicatedImagesLen; i += 1) {
            this.duplicatedImages[i].image.style.transition = 'none';
        }
    }

    resizeImages() {
        const imagesCarouselLen = this.imagesCarousel.length;
        const carouselCssProperties = this.carouselCssProperties();

        for (let i = 0; i < imagesCarouselLen; i += 1) {
            this.imagesCarousel[i].style.position = 'absolute';
            this.imagesCarousel[i].style.height = `${carouselCssProperties.height - (carouselCssProperties.borderTopWidth + carouselCssProperties.borderBottomWidth)}px`;
            this.imagesCarousel[i].style.width = `${((carouselCssProperties.width - (carouselCssProperties.borderLeftWidth + carouselCssProperties.borderRightWidth)) / this.nbImage) + 1}px`;
        }
    }

    resizeDuplicatedImages() {
        const duplicatedImagesLen = this.duplicatedImages.length;
        const carouselCssProperties = this.carouselCssProperties();

        for (let i = 0; i < duplicatedImagesLen; i += 1) {
            this.duplicatedImages[i].image.style.position = 'absolute';
            this.duplicatedImages[i].image.style.transition = 'none';
            this.duplicatedImages[i].image.style.height = `${carouselCssProperties.height - (carouselCssProperties.borderTopWidth + carouselCssProperties.borderBottomWidth)}px`;
            this.duplicatedImages[i].image.style.width = `${((carouselCssProperties.width - (carouselCssProperties.borderLeftWidth + carouselCssProperties.borderRightWidth)) / this.nbImage) + 1}px`;
            this.duplicatedImages[i].left = this.duplicatedImages[i].image.offsetWidth * (i - 2);
            this.duplicatedImages[i].image.style.left = `${this.duplicatedImages[i].left}px`;
        }
    }

    reorganizeDuplicatedImages() {
        if (this.imagesCarouselLen > 1) {
            const indexFirstDuplicatedImage = this.duplicatedImages[0].image.dataset.index;
            const duplicatedImagesLen = this.duplicatedImages.length;
            const carouselCssProperties = this.carouselCssProperties();

            for (let i = 0; i < duplicatedImagesLen; i += 1) {
                if (this.duplicatedImages[i]) {
                    this.slideCarousel.removeChild(this.duplicatedImages[i].image);
                }
            }

            this.duplicatedImages = [];

            for (
                let i = 0, indexImage = indexFirstDuplicatedImage, j = -2;
                i < this.nbImage + 4;
                i += 1, indexImage = parseInt(indexImage, 10) + 1, j += 1
            ) {
                let leftPosition = carouselCssProperties.width;
                leftPosition -= carouselCssProperties.borderLeftWidth;
                leftPosition -= carouselCssProperties.borderRightWidth;
                leftPosition /= this.nbImage;
                leftPosition += 1;
                leftPosition *= j;

                if (indexImage < 0) {
                    indexImage = this.imagesCarouselLen + indexImage;
                } else if (indexImage >= this.imagesCarouselLen) {
                    indexImage = 0;
                }

                this.duplicatedImages[i] = {
                    index: indexImage,
                    image: this.imagesCarousel[indexImage].cloneNode(true),
                    left: leftPosition,
                };

                this.duplicatedImages[i].image.style.display = 'block';
                this.duplicatedImages[i].image.style.left = `${this.duplicatedImages[i].left}px`;
                this.slideCarousel.appendChild(this.duplicatedImages[i].image);
            }
        }
    }

    setIdImages() {
        for (let i = 0; i < this.imagesCarouselLen; i += 1) {
            this.imagesCarousel[i].dataset.index = i;
        }
    }

    ifNoImages() {
        if (!this.imagesCarouselLen) {
            if (this.leftArrowCarousel) {
                this.leftArrowCarousel.style.display = 'none';
            }

            if (this.rightArrowCarousel) {
                this.rightArrowCarousel.style.display = 'none';
            }

            this.slideCarousel.innerHTML = '<p class="message-carousel-no-images">No images</p>';
        }
    }

    ifOnlyOneImage() {
        if (this.imagesCarouselLen === 1) {
            this.leftArrowCarousel.style.display = 'none';
            this.rightArrowCarousel.style.display = 'none';

            const carouselCssProperties = this.carouselCssProperties();

            this.imagesCarousel[0].style.display = 'block';
            this.imagesCarousel[0].style.position = 'absolute';
            this.imagesCarousel[0].style.left = '0px';
            this.imagesCarousel[0].style.top = '0px';
            this.imagesCarousel[0].style.height = `${carouselCssProperties.height - (carouselCssProperties.borderTopWidth + carouselCssProperties.borderBottomWidth)}px`;
            this.imagesCarousel[0].style.width = `${((carouselCssProperties.width - (carouselCssProperties.borderLeftWidth + carouselCssProperties.borderRightWidth)) / this.nbImage) + 1}px`;
        }
    }

    duplicateImages() {
        if (this.imagesCarouselLen > 1) {
            const carouselCssProperties = this.carouselCssProperties();

            this.duplicatedImages = [];

            for (
                let i = 0, j = -2, indexImage = -2;
                i < this.nbImage + 4;
                i += 1, j += 1, indexImage += 1
            ) {
                let leftPosition = carouselCssProperties.width;
                leftPosition -= carouselCssProperties.borderLeftWidth;
                leftPosition -= carouselCssProperties.borderRightWidth;
                leftPosition /= this.nbImage;
                leftPosition += 1;
                leftPosition *= j;

                if (indexImage < 0) {
                    indexImage = this.imagesCarouselLen + indexImage;
                } else if (indexImage >= this.imagesCarouselLen) {
                    indexImage = 0;
                }

                this.duplicatedImages[i] = {
                    index: indexImage,
                    image: this.imagesCarousel[indexImage].cloneNode(true),
                    left: leftPosition,
                };
            }
        }
    }

    placeImages() {
        const duplicatedImagesLen = this.duplicatedImages.length;

        for (let i = 0; i < duplicatedImagesLen; i += 1) {
            this.slideCarousel.appendChild(this.duplicatedImages[i].image);
            this.duplicatedImages[i].image.style.transition = `all ease-in-out ${this.transitionDuration}ms`;
            this.duplicatedImages[i].image.style.left = `${this.duplicatedImages[i].left}px`;
        }
    }

    enableClickAgain() {
        setTimeout(() => {
            this.canClick = true;
        }, this.transitionDuration / 2);
    }

    moveLeft() {
        if (this.imagesCarouselLen > 1) {
            if (this.canClick) {
                this.canClick = false;

                this.enableClickAgain();

                const duplicatedImagesLen = this.duplicatedImages.length;
                const imageToRemove = this.duplicatedImages[this.duplicatedImages.length - 1].image;

                this.slideCarousel.removeChild(imageToRemove);

                for (let i = duplicatedImagesLen - 1; i >= 0; i -= 1) {
                    if (this.duplicatedImages[i - 1]) {
                        this.duplicatedImages[i - 1].image.style.transition = `all ease-in-out ${this.transitionDuration}ms`;
                        this.duplicatedImages[i - 1].image.style.left = `${this.duplicatedImages[i].left}px`;
                        this.duplicatedImages[i].image = this.duplicatedImages[i - 1].image;
                        const datasetIndex = this.duplicatedImages[i - 1].image.dataset.index;
                        this.duplicatedImages[i].index = datasetIndex;
                    } else {
                        let indexImage = parseInt(this.duplicatedImages[i].index, 10) - 1;

                        if (indexImage === -1) {
                            indexImage = this.imagesCarouselLen - 1;
                        }

                        this.duplicatedImages[i].index = indexImage;
                        const clonedImage = this.imagesCarousel[indexImage].cloneNode(true);
                        this.duplicatedImages[i].image = clonedImage;
                        this.duplicatedImages[i].image.style.display = 'block';
                        this.duplicatedImages[i].image.style.transition = `all ease-in-out ${this.transitionDuration}ms`;
                        this.duplicatedImages[i].image.style.left = `${this.duplicatedImages[i].left}px`;
                        this.slideCarousel.appendChild(this.duplicatedImages[i].image);
                    }
                }
            }
        }
    }

    moveRight() {
        if (this.imagesCarouselLen > 1) {
            if (this.canClick) {
                this.canClick = false;

                this.enableClickAgain();

                const duplicatedImagesLen = this.duplicatedImages.length;

                this.slideCarousel.removeChild(this.duplicatedImages[0].image);

                for (let i = 0; i < duplicatedImagesLen; i += 1) {
                    if (this.duplicatedImages[i + 1]) {
                        this.duplicatedImages[i].image = this.duplicatedImages[i + 1].image;
                        this.duplicatedImages[i].image.style.transition = `all ease-in-out ${this.transitionDuration}ms`;
                        this.duplicatedImages[i].image.style.left = `${this.duplicatedImages[i].left}px`;
                        const datasetIndex = this.duplicatedImages[i].image.dataset.index;
                        this.duplicatedImages[i].index = datasetIndex;
                    } else {
                        let indexImage = parseInt(this.duplicatedImages[i].index, 10) + 1;

                        if (indexImage >= this.imagesCarouselLen) {
                            indexImage = 0;
                        }

                        this.duplicatedImages[i].index = indexImage;
                        const clonedImage = this.imagesCarousel[indexImage].cloneNode(true);
                        this.duplicatedImages[i].image = clonedImage;
                        this.duplicatedImages[i].image.style.display = 'block';
                        this.duplicatedImages[i].image.style.transition = `all ease-in-out ${this.transitionDuration}ms`;
                        this.duplicatedImages[i].image.style.left = `${this.duplicatedImages[i].left}px`;
                        this.slideCarousel.appendChild(this.duplicatedImages[i].image);
                    }
                }
            }
        }
    }

    shiftImages() {
        if (this.imagesCarouselLen) {
            this.imagesCarousel = this.backupImagesCarousel;

            const newImagesOrder = [];
            const classCarousel = this.carousel.className;
            const bodyWidth = document.body.offsetWidth;
            let shiftImages = null;

            if (/shift:?(-?[0-9]+)/i.test(classCarousel)) {
                shiftImages = parseInt(RegExp.$1, 10);
            }

            if (/shift:sm:?(-?[0-9]+)/i.test(classCarousel) && (bodyWidth >= 576)) {
                shiftImages = parseInt(RegExp.$1, 10);
            }

            if (/shift:md:?(-?[0-9]+)/i.test(classCarousel) && (bodyWidth >= 768)) {
                shiftImages = parseInt(RegExp.$1, 10);
            }

            if (/shift:lg:?(-?[0-9]+)/i.test(classCarousel) && (bodyWidth >= 992)) {
                shiftImages = parseInt(RegExp.$1, 10);
            }

            if (/shift:xl:?(-?[0-9]+)/i.test(classCarousel) && (bodyWidth >= 1200)) {
                shiftImages = parseInt(RegExp.$1, 10);
            }

            if (shiftImages) {
                while (shiftImages < 0 || shiftImages >= this.imagesCarouselLen) {
                    if (shiftImages < 0) {
                        shiftImages = this.imagesCarouselLen + shiftImages;
                    } else if (shiftImages >= this.imagesCarouselLen) {
                        shiftImages -= this.imagesCarouselLen;
                    }
                }

                for (let i = shiftImages; i < this.imagesCarouselLen; i += 1) {
                    newImagesOrder.push(this.imagesCarousel[i]);
                }

                for (let i = 0; i < shiftImages; i += 1) {
                    newImagesOrder.push(this.imagesCarousel[i]);
                }

                this.imagesCarousel = newImagesOrder;
            }
        }
    }

    responsiveCarousel() {
        this.shiftImages();
        this.setIdImages();
        this.setNbImageCarousel();
        this.removeTransition();
        this.carouselCssProperties();
        this.resizeSlideCarousel();
        this.resizeImages();
        this.displayNoneImages();
        this.reorganizeDuplicatedImages();
        this.resizeDuplicatedImages();
    }

    autoScroll(userClick = false) {
        let countDownAutoScroll = 6000;

        if (this.imagesCarousel && !userClick) {
            this.moveRight();
        }

        if (userClick) {
            countDownAutoScroll = 10000;
        }

        this.idAutoScroll = setTimeout(
            this.autoScroll.bind(this),
            countDownAutoScroll,
        );
    }
}

export default Carousel;
