import {flashbag} from './_flashbag';

const $ = require('jquery');

/*Toggle trick status*/
const statusToggler = {
    toggleForm:'',
    statusElement: '',

    //Sets the event handler for every status-toggler input checkbox
    setEventHandler(displayStatus){
        const statusCheckBox = $('.status-toggler');

        statusCheckBox.on('change', function (){
            //Sets the form element to be serialized
            statusToggler.toggleForm = $(this).parents().eq(1);

            //Sets the status element if an element displays the current status
            if(displayStatus === true){
                statusToggler.statusElement = $(this).parents().eq(3).find('.status-cell');
            }

            statusToggler.makeRequest($(this).attr('data-slug'), displayStatus);
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
                    statusToggler.changeStatus(statusToggler.statusElement);
                }

                //Displays flash message
                flashbag.displayFlashbag('Status of ' + trickSlug + ' has been updated.','notice');
            },
            error: function (xhr) {
                flashbag.displayFlashbag(xhr.responseText,'error');
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

export {statusToggler};