const $ = require('jquery');

import '../css/front/tricks/trick_page.scss';
import {CommentLister} from "./AJAX/CommentLister";
import {MediaSlider} from "./MediaSlider";

/*Ajax object to update comments list*/
CommentLister.setEventsHandler();

/*Media slider object for thumbnails*/
MediaSlider.initialize();

/*Alert before removal*/
$('form[name^="trick_form"]').find('.btn-danger').on('click', function (e) {

    let confirmation = confirm('You are about to remove this trick, do you wish to continue?');

    if (!confirmation){
        e.preventDefault();
        e.stopPropagation();
    }
});










