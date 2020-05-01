const $ = require('jquery');

const trickRemover = {
    trickSlug:'',
    trickRow:'',
    tokenValue:'',

    makeRequest(button, trickSlug){
        this.trickSlug = trickSlug;
        this.trickRow = button.parents().eq(2);
        this.tokenValue = button.parent().find("input[name='remove_token']").attr('value');

        //Sets the ajax object
        $.ajax({
            url: '/ajax/remove-trick/' + this.trickSlug,
            method: 'POST',
            data: 'remove_token='+encodeURIComponent(this.tokenValue),
            success: function () {
                trickRemover.trickRow.remove();
                alert('The trick ' + trickRemover.trickSlug + ' has been removed');
            },
            error: function () {
                alert('A problem has raised.');
            }
        })
    }
};

//Sets the event handler for every remove-btn
removeBtns = $('.remove-btn');
removeBtns.on('click', function (e){
    e.preventDefault();
    e.stopPropagation();
    trickRemover.makeRequest($(this), $(this).attr('data-trick-slug'))
});