const $ = require('jquery');

/*Ajax object to update home trick list*/
const TrickGridLister = {

    trickGroup : '',
    limit: '',

    setEventHandler(){
        const delegateSelector = $('#section-trick');

        delegateSelector.on('submit','form[name="home_filter_form"]',function (e){
            e.preventDefault();
            TrickGridLister.makeRequest($(this));

        });

        delegateSelector.on('submit','form[name="home_limit_form"]',function (e){
            e.preventDefault();
            TrickGridLister.makeRequest($(this));
        });
    },

    makeRequest(form){

        console.log(form.serialize());

        //Sets the ajax object
        $.ajax({
            url: '/ajax/home-tricks',
            method: 'POST',
            data: form.serialize(),
            success: function (data) {
                TrickGridLister.updateList(data);
                TrickGridLister.resetLoadBtns(form);
            }
        });
    },

    //Replaces the old list by the new one
    updateList(data){
        $('#trick-list').replaceWith(data);
    },

    //Stops the spinner in the load buttons
    resetLoadBtns(form){
        form.find('.load-btn').trigger('click');
    },
};

export {TrickGridLister};