import {Flashbag} from './Flashbag';

const $ = require('jquery');

const ElementRemover = {
    ajaxUrl: '',
    dataAttributeName:'',
    elementIdentifier:'',
    elementWrapper:'',//Parent element to be removed on success
    tokenValue:'',

    initialize(delegateSelector,wrapperElementNodeLevel,dataAttributeName, ajaxUrl){
        this.dataAttributeName = dataAttributeName;
        this.ajaxUrl = ajaxUrl;
        this.setEventHandler(delegateSelector,wrapperElementNodeLevel);
    },

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

            ElementRemover.makeRequest($(this), $(this).parents().eq(wrapperElementNodeLevel))
        });
    },

    makeRequest(form, trickWrapper){
        this.elementWrapper = trickWrapper;
        this.elementIdentifier = form.find('.remove-btn').attr(ElementRemover.dataAttributeName);
        this.tokenValue = form.find("input[name='remove_token']").attr('value');

        //Sets the ajax object
        $.ajax({
            url: ElementRemover.ajaxUrl + this.elementIdentifier,
            method: 'POST',
            data: 'remove_token='+encodeURIComponent(this.tokenValue),
            success: function () {
                ElementRemover.elementWrapper.remove();
                Flashbag.displayFlashbag('The element ' + ElementRemover.elementIdentifier + ' has been removed','notice');
            },
            error: function (xhr) {
                Flashbag.displayFlashbag(xhr.responseText,'error');
            }
        })
    }
};

export {ElementRemover};
