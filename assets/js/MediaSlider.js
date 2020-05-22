const $ = require('jquery');

const MediaSlider = {
    toggleBtn: $('#media-slider-toggle'),
    mediaSection: $('#media-slider-element'),
    addMediaBlock: $('#add-media'),
    sliderBar: $('#media-slider-bar'),
    leftArrow: $('#slider-left-arrow'),
    rightArrow: $('#slider-right-arrow'),

    //Initializes the slider
    initialize(){
        this.setVisibilityListeners();
        this.setArrowsListeners();
        this.setCarouselListener();
        this.disableYoutubeBtn();
    },

    //Event listeners for media section visibility
    setVisibilityListeners(){
        $(window).on('resize', MediaSlider.displayMediaBarByViewport.bind(MediaSlider));
        $(document).ready(MediaSlider.displayMediaBarByViewport.bind(MediaSlider));
        $(document).ready(MediaSlider.setHorizontalAlignment());
        $(document).ready(MediaSlider.displayArrows.bind(MediaSlider));
        MediaSlider.toggleBtn.on('click',MediaSlider.displayMediaBarByToggleBtn.bind(MediaSlider))
    },

    //Adds an event listener for the arrows
    setArrowsListeners() {
        this.leftArrow.on('click', this.moveSliderRight.bind(this));
        this.rightArrow.on('click', this.moveSliderLeft.bind(this));
    },

    //Changes the "active" class in the Carousel to match the thumbnail that is clicked
    setCarouselListener() {
        const thumbnails = $('.media-slider-thumbnail');

        thumbnails.on('click',function () {
            MediaSlider.setCarouselActive(thumbnails.index($(this)));
        });
    },

    //Creates a transparent div above the media thumbnail to prevent iframe default event on click
    disableYoutubeBtn(){

        $(window).on('load', function () {
            let thumbnail = $('.embed-thumbnail');
            let antiClick = $('<div></div>').addClass('anti-click');

            thumbnail.css({'position':'relative'});
            thumbnail.prepend(antiClick);

            antiClick.innerWidth(thumbnail.find('iframe').innerWidth());
            antiClick.innerHeight(thumbnail.find('iframe').innerHeight());
            antiClick.css({
                'position': 'absolute',
                'top':'0',
                'left':'0',
                'z-index':'99'
            });
        });
    },

    //Sets the horizontal alignment of the media bar depending on the number of thumbnails.
    setHorizontalAlignment(){
        let overflow = this.getLeftOverflowElements();

        if(overflow.length > 0){
            this.sliderBar.css({'justify-content':'flex-start'});
        }
    },

    //Define Carousel's active image
    setCarouselActive(thumbnailIndex){
        //gets the carousel image collection
        const carouselElements = $('.carousel-item');

        //unsets previous active elements
        carouselElements.each(function () {
            if($(this).hasClass('active')){
               $(this).removeClass('active');
            }
        });

        //sets the active image
        $(carouselElements.get(thumbnailIndex)).addClass('active');
    },

    //Displays media section depending on the window size
    displayMediaBarByViewport() {
        if ($('body').outerWidth() < 576) {
            this.mediaSection.addClass('hidden');

            //checks if the edit mode "addMediaBlock" exists before
            if(this.addMediaBlock !== null){
                this.addMediaBlock.addClass('hidden');
            }
            this.toggleBtn.removeClass('hidden');
        } else {
            this.mediaSection.removeClass('hidden');
            //checks if the edit mode "addMediaBlock" exists before
            if(this.addMediaBlock !== null){
                this.addMediaBlock.removeClass('hidden');
            }
            this.toggleBtn.addClass('hidden');
        }
    },

    //Displays media section depending on the toggle button
    displayMediaBarByToggleBtn() {
        this.mediaSection.toggleClass('hidden');

        //checks if the edit mode "addMediaBlock" exists before
        if(this.addMediaBlock !== null){
            this.addMediaBlock.toggleClass('hidden');
        }

        //Toggles the button for display / hide the mediabar
        if (this.mediaSection.hasClass('hidden')) {
            this.toggleBtn.children().first().text('Voir les medias');
        } else {
            this.toggleBtn.children().first().text('Masquer les medias');
            this.displayArrows();
        }
    },

    //Gets the overflowing elements on the right
    getRightOverflowElements() {

        let outsideRightElements = [];

        //Counts the number of thumbnails overflowing the slider element on the right
        this.sliderBar.find('.media-slider-thumbnail').each(function(){
            if($(this).offset().left + $(this).innerWidth() > MediaSlider.sliderBar.offset().left + MediaSlider.sliderBar.innerWidth()){
                outsideRightElements.push($(this));
            }
        });

        return outsideRightElements;
    },

    //Gets the overflowing elements on the left
    getLeftOverflowElements() {
        let outsideLeftElements = [];

        //Counts the number of thumbnails overflowing the slider element on the left
        this.sliderBar.find('.media-slider-thumbnail').each(function(){
            if($(this).offset().left < MediaSlider.sliderBar.offset().left){
                outsideLeftElements.push($(this));
            }
        });

        return outsideLeftElements;
    },

    //Moves the slider thumbnails to the left
    moveSliderLeft() {

        const firstThumbnail = this.sliderBar.children().first();

        //Changes the margin left of the first element
        if (this.getRightOverflowElements().length > 0) {

            //Sets the number of items to move left
            let nbItemsLeft = this.getLeftOverflowElements().length;

            //Sets the margin that should be offset to hide the element
            let marginOffset = parseInt(firstThumbnail.outerWidth(), 10) +
                2 * parseInt(firstThumbnail.css('margin-right'), 10);

            //Sets the first thumbnail new margin-left
            firstThumbnail.css('margin-left','-' + ((nbItemsLeft + 1) * marginOffset) + 'px');

            firstThumbnail.on('transitionend',this.displayArrows);
        }
    },

    //Moves the slider thumbnails to the left
    moveSliderRight() {

        const firstThumbnail = this.sliderBar.children().first();

        //Changes the margin left of the first element
        if (this.getLeftOverflowElements().length > 0) {

            //Sets the number of items to move right
            let nbItemsLeft = this.getLeftOverflowElements().length;

            //Sets the margin that should be offset to hide the element
            let marginOffset = parseInt(firstThumbnail.outerWidth(), 10) +
                2 * parseInt(firstThumbnail.css('margin-right'), 10);

            //Sets the first thumbnail new margin-left
            firstThumbnail.css('margin-left', '-' + ((nbItemsLeft - 1) * marginOffset) + 'px');

            firstThumbnail.on('transitionend',this.displayArrows);
        }
    },

    //Displays the arrows depending on the overflow
    displayArrows() {
        if(MediaSlider.getLeftOverflowElements().length === 0){
            MediaSlider.leftArrow.children().first().addClass('hidden');
        }
        else {
            MediaSlider.leftArrow.children().first().removeClass('hidden');
        }

        if(MediaSlider.getRightOverflowElements().length === 0){
            MediaSlider.rightArrow.children().first().addClass('hidden');
        }
        else {
            MediaSlider.rightArrow.children().first().removeClass('hidden');
        }
    }
};

export {MediaSlider};