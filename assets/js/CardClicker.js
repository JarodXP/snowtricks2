const $ = require('jquery');

/*Sets a 'click' listener on the cards to redirect to the corresponding trick page*/
const CardClicker = {
    setClickHandler(){
        const delegateSelector = $('#section-trick');

        delegateSelector.on('click','.card',function () {

            //Gets the link located in the trick name anchor
            let trickLink = $(this).find('.trick-name').children().attr('href');
            window.location.replace(trickLink);
        });
    }
};

export {CardClicker};