import {Flashbag} from './Flashbag';

const $ = require('jquery');

/*Toggle trick status*/
const StatusToggler = {
    toggleForm:'',
    statusElement: '',

    //Sets the event handler for every status-toggler input checkbox
    setEventHandler(displayStatus){
        const statusCheckBox = $('.status-toggler');

        statusCheckBox.on('change', function (){
            //Sets the form element to be serialized
            StatusToggler.toggleForm = $(this).parents().eq(1);

            //Sets the status element if an element displays the current status
            if(displayStatus === true){
                StatusToggler.statusElement = $(this).parents().eq(3).find('.status-cell');
            }

            StatusToggler.makeRequest($(this).attr('data-slug'), displayStatus);
        });
    },

    makeRequest(trickSlug, displayStatus){

        //Sets the ajax object
        $.ajax({
            url: '/ajax/trick-status/' + trickSlug,
            method: 'POST',
            data: this.toggleForm.serialize(),
            success: function () {

                if(displayStatus === true){
                    //Changes status
                    StatusToggler.changeStatus(StatusToggler.statusElement);
                }

                //Displays flash message
                Flashbag.displayFlashbag('Status of ' + trickSlug + ' has been updated.','notice');
            },
            error: function (xhr) {
                Flashbag.displayFlashbag(xhr.responseText,'error');
            }
        })
    },

    //Toggles HTML trick status
    changeStatus(statusElement){
        let status = statusElement.text();

        if(status === "1"){
            status = "0";
        }else {
            status = "1";
        }

        statusElement.text(status);
    }
};

export {StatusToggler};