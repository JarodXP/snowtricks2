import {flashbag} from './_flashbag';

const $ = require('jquery');

const trickRemover = {
    trickSlug:'',
    trickWrapper:'',//Parent element to be removed on success
    tokenValue:'',

    //Sets the event handler for every remove-btn
    setEventHandler(delegateSelector, wrapperElementNodeLevel){
        const delegate = $(delegateSelector);
        delegate.on('submit', 'form[name^="remove-form"]',function (e){
            e.preventDefault();
            e.stopPropagation();

            trickRemover.makeRequest($(this), $(this).parents().eq(wrapperElementNodeLevel))
        });
    },

    makeRequest(form, trickWrapper){
        this.trickWrapper = trickWrapper;
        this.trickSlug = form.find('.remove-btn').attr('data-trick-slug');
        this.tokenValue = form.find("input[name='remove_token']").attr('value');

        //Sets the ajax object
        $.ajax({
            url: '/ajax/remove-trick/' + this.trickSlug,
            method: 'POST',
            data: 'remove_token='+encodeURIComponent(this.tokenValue),
            success: function () {
                trickRemover.trickWrapper.remove();
                flashbag.displayFlashbag('The trick ' + trickRemover.trickSlug + ' has been removed','notice');
            },
            error: function (xhr) {
                flashbag.displayFlashbag(xhr.responseText,'error');
            }
        })
    }
};

export {trickRemover};
