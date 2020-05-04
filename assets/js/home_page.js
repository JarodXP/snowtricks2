import {trickRemover} from "./AJAX/_trickRemover";

const $ = require('jquery');

/*Sets a 'click' listener on the cards to redirect to the corresponding trick page*/
const cards = $('.card');

cards.on('click',function () {

    //Gets the link located in the trick name anchor
    let trickLink = $(this).find('.trick-name').children().attr('href');
    window.location.replace(trickLink);
});

/*Ajax object to update home trick list*/
const trickLister = {

    trickGroup : '',
    limit: '',

    makeRequest(form){
        //Sets the values of the filter and limit
        this.trickGroup = $('#home_list_form_trickGroup').val();
        this.limit = $('#home_list_form_limit').val();

        //Sets the ajax object
        $.ajax({
            url: '/ajax/home-tricks',
            method: 'POST',
            data: form.serialize(),
            success: function (data) {
                trickLister.updateList(data);
                trickLister.resetLoadBtns(form);
                trickLister.updateFormFields(form);
            }
        });
    },

    //Replaces the old list by the new one
    updateList(data){
        $('#trick-list').replaceWith(data);
    },

    //Updates the filter and limit values
    updateFormFields(form){
        $('#home_limit_form_trickGroup').attr('value', this.trickGroup);

        if(form.attr('name') === 'home_limit_form'){
            this.limit = parseInt(this.limit) + 5;
        }

        $('#home_limit_form_limit').attr('value', this.limit);
        $('#home_list_form_limit').attr('value', this.limit);
    },

    //Stops the spinner in the load buttons
    resetLoadBtns(form){
        form.find('.modify-list').toggleClass('disabled');
        form.find('.modify-list>span').toggleClass('collapse');
    },
};

const sectionTrick = $('#section-trick');
const listForms = [sectionTrick.find("form[name='home_list_form']"),sectionTrick.find("form[name='home_limit_form']")];

for(let form of listForms) {
    form.on('submit',function (e){
        e.preventDefault();
        trickLister.makeRequest(form);
    })
}

//Sets the event handler and the <card> (parents[3]) as parent wrapper
trickRemover.setEventHandler('#section-trick',2);

