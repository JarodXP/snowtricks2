import {Flashbag} from './Flashbag';

const $ = require('jquery');

const TrickRemover = {
    trickSlug:'',
    trickWrapper:'',//Parent element to be removed on success
    tokenValue:'',

    //Sets the event handler for every remove-btn
    setEventHandler(delegateSelector, wrapperElementNodeLevel){
        const delegate = $(delegateSelector);

        delegate.on('click', 'form[name^="remove-form"]',function (e){
            e.preventDefault();
            e.stopPropagation();
            $(this).submit()
        });

        delegate.on('submit', 'form[name^="remove-form"]',function (e){
            e.preventDefault();
            e.stopPropagation();

            TrickRemover.makeRequest($(this), $(this).parents().eq(wrapperElementNodeLevel))
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
                TrickRemover.trickWrapper.remove();
                Flashbag.displayFlashbag('The trick ' + TrickRemover.trickSlug + ' has been removed','notice');
            },
            error: function (xhr) {
                Flashbag.displayFlashbag(xhr.responseText,'error');
            }
        })
    }
};

export {TrickRemover};
