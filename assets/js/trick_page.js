import '../css/front/tricks/trick_page.scss';

const $ = require('jquery');

const mediaSlider = {
    toggleBtn: document.getElementById('media-slider-toggle'),
    mediaSection: document.getElementById('media-slider-element'),
    addMediaBlock: document.getElementById('add-media'),
    sliderBar: document.getElementById('media-slider-bar'),
    leftArrow: document.getElementById('slider-left-arrow'),
    rightArrow: document.getElementById('slider-right-arrow'),
    thumbnails: document.getElementsByClassName('media-slider-thumbnail'),

    //Adds an event listener for the arrows
    setArrowsListeners() {
        this.leftArrow.addEventListener('click', this.moveSliderRight.bind(this));
        this.rightArrow.addEventListener('click', this.moveSliderLeft.bind(this));

        for(let i = 0; i < this.thumbnails.length; i++)
        {
            this.thumbnails[i].addEventListener('click',function () {
                mediaSlider.setCarouselActive(i);
            });
        }
    },

    //Define Carousel's active image
    setCarouselActive(childIndex){
        //gets the carousel image collection
        const carouselElements = document.getElementsByClassName('carousel-item');

        //unsets previous active elements
        for (const element of carouselElements){
            if(element.classList.contains('active')){
                element.classList.remove('active');
            }
        }

        //sets the active image
        carouselElements[childIndex].classList.add('active');
    },

    //Displays media section depending on the window size
    displayMediaBarByViewport() {
        if (document.body.clientWidth < 576) {
            this.mediaSection.classList.add('hidden');

            //checks if the edit mode "addMediaBlock" exists before
            if(this.addMediaBlock !== null){
                this.addMediaBlock.classList.add('hidden');
            }
            this.toggleBtn.classList.remove('hidden');
        } else {
            this.mediaSection.classList.remove('hidden');
            //checks if the edit mode "addMediaBlock" exists before
            if(this.addMediaBlock !== null){
                this.addMediaBlock.classList.remove('hidden');
            }
            this.toggleBtn.classList.add('hidden');
        }
    },

    //Displays media section depending on the toggle button
    displayMediaBarByToggleBtn() {
        this.mediaSection.classList.toggle('hidden');

        //checks if the edit mode "addMediaBlock" exists before
        if(this.addMediaBlock !== null){
            this.addMediaBlock.classList.toggle('hidden');
        }

        if (this.mediaSection.classList.contains('hidden')) {
            this.toggleBtn.firstElementChild.textContent = 'Voir les medias ';
        } else {
            this.toggleBtn.firstElementChild.textContent = 'Masquer les medias ';
            this.displayArrows();
        }
    },

    //Gets the overflowing elements on the right
    getRightOverflowElements() {
        let outsideRightElements = [];

        //Counts the number of thumbnails overflowing the slider element on the right
        for (let i = 0; i < this.sliderBar.childElementCount; i++) {
            if (this.sliderBar.children[i].offsetLeft + this.sliderBar.children[i].offsetWidth >
                this.sliderBar.offsetLeft + this.sliderBar.offsetWidth) {
                outsideRightElements.push(this.sliderBar.children[i]);
            }
        }

        return outsideRightElements;
    },

    //Gets the overflowing elements on the left
    getLeftOverflowElements() {
        let outsideLeftElements = [];

        //Counts the number of thumbnails overflowing the slider element on the left
        for (let i = 0; i < this.sliderBar.childElementCount; i++) {
            if (this.sliderBar.children[i].offsetLeft < this.sliderBar.offsetLeft) {
                outsideLeftElements.push(this.sliderBar.children[i]);
            }
        }

        return outsideLeftElements;
    },

    //Moves the slider thumbnails to the left
    moveSliderLeft() {
        //Changes the margin left of the first element
        if (this.getRightOverflowElements().length > 0) {

            //Sets the number of items to move left
            let nbItemsLeft = this.getLeftOverflowElements().length;

            //Sets the margin that should be offset to hide the element
            let marginOffset = parseInt(this.sliderBar.children[0].offsetWidth, 10) +
                2 * parseInt(window.getComputedStyle(this.sliderBar.children[0]).getPropertyValue('margin-right'), 10);

            //Sets the first thumbnail new margin-left
            this.sliderBar.children[0].style.marginLeft = '-' + ((nbItemsLeft + 1) * marginOffset) + 'px';

            this.displayArrows();

        }
        this.displayArrows();
    },

    //Moves the slider thumbnails to the left
    moveSliderRight() {

        //Changes the margin left of the first element
        if (this.getLeftOverflowElements().length > 0) {

            //Sets the number of items to move right
            let nbItemsLeft = this.getLeftOverflowElements().length;

            //Sets the margin that should be offset to hide the element
            let marginOffset = parseInt(this.sliderBar.children[0].offsetWidth, 10) +
                2 * parseInt(window.getComputedStyle(this.sliderBar.children[0]).getPropertyValue('margin-right'), 10);

            //Sets the first thumbnail new margin-left
            this.sliderBar.children[0].style.marginLeft = '-' + ((nbItemsLeft - 1) * marginOffset) + 'px';

            this.displayArrows();
        }
        this.displayArrows();

    },

    //Displays the arrows depending on the overflow
    displayArrows() {
        if(this.getLeftOverflowElements().length === 0){
            this.leftArrow.children[0].classList.add('hidden');
        }
        else {
            this.leftArrow.children[0].classList.remove('hidden');
        }

        if(this.getRightOverflowElements().length === 0){
            this.rightArrow.children[0].classList.add('hidden');
        }
        else {
            this.rightArrow.children[0].classList.remove('hidden');
        }
    }
};

/*Ajax object to update comments list*/
const commentLister = {

    trickSlug: '',
    page: '',

    makeRequest(form){
        //Sets the values of the filter and limit
        const blockNav = $('#pagination-nav');
        this.trickSlug = blockNav.attr('data-reference');

        //Sets the ajax object
        $.ajax({
            url: '/ajax/trick-comments/' + commentLister.trickSlug + '/' + commentLister.page,
            method: 'POST',
            data: form.serialize(),
            success: function (data) {
                //Replaces the old list by the new one
                $('#comments-list').replaceWith(data);
            }
        });
    },
};

//Permanent object to intercept events
const commentWrapper = $('#comments');

//Gets the page number and sets it into the Ajax object
commentWrapper.on('click','.page-link', function () {
    commentLister.page = $(this).attr('data-page');
});

//Prevents the form to be submitted and triggers ajax call
commentWrapper.on('submit','#pagination',function (e){
    e.preventDefault();
    commentLister.makeRequest($(this));
});

//Event listeners for media section visibility
window.addEventListener("resize", mediaSlider.displayMediaBarByViewport.bind(mediaSlider));
window.addEventListener('load', mediaSlider.displayMediaBarByViewport.bind(mediaSlider));
window.addEventListener('load', mediaSlider.displayArrows.bind(mediaSlider));

mediaSlider.toggleBtn.addEventListener('click',mediaSlider.displayMediaBarByToggleBtn.bind(mediaSlider));

//Event listeners for the media slider arrows
mediaSlider.setArrowsListeners();










