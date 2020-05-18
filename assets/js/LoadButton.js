const $ = require('jquery');

//Stop and starts the spinner on load buttons
const LoadButton = {

    //Event listener for load buttons
    setClickHandler(sectionWrapper) {
        $(sectionWrapper).on('click','.load-btn', function (e) {
            let spinner = $(e.target).children().eq(0);

            $(e.target).toggleClass('disabled');
            spinner.toggleClass('collapse');
        })
    }
};

export {LoadButton};