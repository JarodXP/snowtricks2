import {trickLister} from "./AJAX/_tricks_grid_lister";
import {trickRemover} from "./AJAX/_trickRemover";

const $ = require('jquery');

/*Sets a 'click' listener on the cards to redirect to the corresponding trick page*/
const delegateSelector = $('#section-trick');

delegateSelector.on('click','.card',function () {

    //Gets the link located in the trick name anchor
    let trickLink = $(this).find('.trick-name').children().attr('href');
    window.location.replace(trickLink);
});

//Sets the event handler for the grid lister
trickLister.setEventHandler();

//Sets the event handler for the trick remover and the <card> (parents[3]) as parent wrapper
trickRemover.setEventHandler('#section-trick',2);

