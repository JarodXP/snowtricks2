const $ = require('jquery');

/*Ajax object to update comments list*/
const CommentLister = {

    trickSlug: '',
    page: '',

    setEventsHandler(){
        //Permanent object to intercept events
        const commentWrapper = $('#comments');

        //Gets the page number and sets it into the Ajax object
        commentWrapper.on('click','.page-link', function () {
            CommentLister.page = $(this).attr('data-page');
        });

        //Prevents the form to be submitted and triggers ajax call
        commentWrapper.on('submit','#pagination',function (e){
            e.preventDefault();
            CommentLister.makeRequest($(this));
        });
    },

    makeRequest(form){
        //Sets the values of the filter and limit
        const blockNav = $('#pagination-nav');
        this.trickSlug = blockNav.attr('data-reference');

        //Sets the ajax object
        $.ajax({
            url: '/ajax/trick-comments/' + CommentLister.trickSlug + '/' + CommentLister.page,
            method: 'POST',
            data: form.serialize(),
            success: function (data) {
                //Replaces the old list by the new one
                $('#comments-list').replaceWith(data);
            }
        });
    },
};

export {CommentLister};


