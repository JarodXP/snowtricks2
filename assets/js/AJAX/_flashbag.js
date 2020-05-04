const $ = require('jquery');

const flashbag = {

    displayFlashbag(message, type){
        let bodyElt = $('body');
        let currentFlashbag = bodyElt.find('[class^="flash-"]');
        let newFlashbag = '<div class="flash-' + type + '">' + message + '</div>';

        if(currentFlashbag.length !== 0){
            currentFlashbag.replaceWith(newFlashbag);
        }else {
            bodyElt.prepend(newFlashbag);
        }
    }
};

export {flashbag};